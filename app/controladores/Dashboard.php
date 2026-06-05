<?php
class Dashboard extends Controlador {
    private $clienteModelo;
    private $planModelo;
    private $db;

    public function __construct() {
        if (!isLoggedIn()) {
            flash('mensaje_error', 'Debe iniciar sesión para acceder.', 'alert alert-warning');
            header('Location: ' . RUTA_URL . '/portal/login');
            exit();
        }
        
        if (!hasRole(['Administrador'])) {
            $this->_redirectSegunRol(currentRole());
        }

        $this->clienteModelo = $this->modelo('Cliente');
        $this->planModelo = $this->modelo('Plan');
        
        // Instanciamos la conexión directa para los reportes Excel
        $this->db = new Conexion();
    }

    public function index() {
        $rol = currentRole();
        
        $totalClientes = method_exists($this->clienteModelo, 'contarClientes') ? $this->clienteModelo->contarClientes() : 0;
        $totalActivos = method_exists($this->clienteModelo, 'contarClientesPorEstado') ? $this->clienteModelo->contarClientesPorEstado('Activo') : 0;
        $totalSuspendidos = method_exists($this->clienteModelo, 'contarClientesPorEstado') ? $this->clienteModelo->contarClientesPorEstado('Suspendido') : 0;
        $totalPlanes = method_exists($this->planModelo, 'contarPlanes') ? $this->planModelo->contarPlanes() : 0;
        $ultimosClientes = method_exists($this->clienteModelo, 'obtenerUltimosClientes') ? $this->clienteModelo->obtenerUltimosClientes(5) : [];

        $datos = [
            'titulo' => 'Dashboard Central',
            'total_clientes' => $totalClientes,
            'total_planes' => $totalPlanes,
            'total_activos' => $totalActivos,
            'total_suspendidos' => $totalSuspendidos,
            'ultimos_clientes' => $ultimosClientes,
            'mensaje' => 'Resumen de operaciones y acceso a reportes.',
            'rol' => $rol
        ];

        $this->vista('dashboard/index', $datos);
    }

    // =================================================================
    // 🔹 MÓDULO DE EXPORTACIÓN A EXCEL (Formatos Nativos HTML)
    // =================================================================

    // 1. REPORTE GENERAL DE CLIENTES
    public function exportarExcelClientes() {
        if (!hasRole(['Administrador'])) { header('Location: ' . RUTA_URL . '/dashboard'); exit(); }

        $conn = $this->db->conectar();
        $sql = "SELECT c.id_cliente, c.nombre, c.apellido, c.dni, c.telefono, c.email, 
                       c.direccion_calle, p.nombre_plan, c.estado_servicio, c.fecha_instalacion
                FROM clientes c
                LEFT JOIN planes p ON c.id_plan_fk = p.id_plan
                WHERE c.estado_registro = '1'
                ORDER BY c.nombre ASC";
                
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $filename = "Base_Clientes_Opticcom_" . date('Ymd') . ".xls";
        $this->_generarCabecerasExcel($filename);

        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="utf-8"></head><body>';
        echo '<table border="1" style="font-family: Arial, sans-serif; border-collapse: collapse;">';
        echo '<thead><tr>';
        echo '<th style="background-color:#0d6efd; color:#ffffff; padding:10px;">ID</th>';
        echo '<th style="background-color:#0d6efd; color:#ffffff; padding:10px;">Cliente</th>';
        echo '<th style="background-color:#0d6efd; color:#ffffff; padding:10px;">DNI</th>';
        echo '<th style="background-color:#0d6efd; color:#ffffff; padding:10px;">Teléfono</th>';
        echo '<th style="background-color:#0d6efd; color:#ffffff; padding:10px;">Dirección</th>';
        echo '<th style="background-color:#0d6efd; color:#ffffff; padding:10px;">Plan</th>';
        echo '<th style="background-color:#0d6efd; color:#ffffff; padding:10px;">Estado</th>';
        echo '</tr></thead><tbody>';

        foreach ($clientes as $row) {
            echo '<tr>';
            echo '<td style="text-align:center;">' . $row['id_cliente'] . '</td>';
            echo '<td>' . htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) . '</td>';
            echo '<td style="mso-number-format:\'@\';">' . $row['dni'] . '</td>'; 
            echo '<td style="mso-number-format:\'@\';">' . $row['telefono'] . '</td>';
            echo '<td>' . htmlspecialchars($row['direccion_calle'] ?? 'N/A') . '</td>';
            echo '<td>' . htmlspecialchars($row['nombre_plan'] ?? 'N/A') . '</td>';
            echo '<td>' . $row['estado_servicio'] . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table></body></html>';
        exit();
    }

    // 2. REPORTE FINANCIERO (Pagos y Deudas)
    public function exportarExcelPagos() {
        if (!hasRole(['Administrador'])) { header('Location: ' . RUTA_URL . '/dashboard'); exit(); }

        $conn = $this->db->conectar();
        $sql = "SELECT c.id_cliente, c.nombre, c.apellido, c.telefono, p.nombre_plan, p.precio_mensual, c.estado_pago,
                       (SELECT MAX(fecha_pago) FROM pagos WHERE id_cliente_fk = c.id_cliente AND estado_registro = '1') as ultimo_pago
                FROM clientes c
                LEFT JOIN planes p ON c.id_plan_fk = p.id_plan
                WHERE c.estado_registro = '1' AND c.estado_servicio != 'Suspendido'
                ORDER BY FIELD(c.estado_pago, 'Vencido', 'Pendiente', 'Al día'), c.nombre ASC";
                
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $finanzas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $filename = "Reporte_Pagos_Opticcom_" . date('Y-F') . ".xls";
        $this->_generarCabecerasExcel($filename);

        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="utf-8"></head><body>';
        echo '<h2>Reporte Financiero y Morosidad - ' . date('d/m/Y') . '</h2>';
        echo '<table border="1" style="font-family: Arial, sans-serif; border-collapse: collapse;">';
        echo '<thead><tr>';
        echo '<th style="background-color:#198754; color:#ffffff; padding:10px;">Cliente</th>';
        echo '<th style="background-color:#198754; color:#ffffff; padding:10px;">Teléfono</th>';
        echo '<th style="background-color:#198754; color:#ffffff; padding:10px;">Plan (S/)</th>';
        echo '<th style="background-color:#198754; color:#ffffff; padding:10px;">ESTADO</th>';
        echo '<th style="background-color:#198754; color:#ffffff; padding:10px;">Último Pago Registrado</th>';
        echo '</tr></thead><tbody>';

        foreach ($finanzas as $row) {
            $color_estado = '#ffffff'; 
            if ($row['estado_pago'] === 'Vencido') $color_estado = '#ffcdd2'; 
            if ($row['estado_pago'] === 'Pendiente') $color_estado = '#fff9c4'; 
            if ($row['estado_pago'] === 'Al día') $color_estado = '#c8e6c9'; 

            echo '<tr style="background-color: ' . $color_estado . ';">';
            echo '<td>' . htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) . '</td>';
            echo '<td style="mso-number-format:\'@\';">' . $row['telefono'] . '</td>';
            echo '<td>' . htmlspecialchars($row['nombre_plan'] ?? 'N/A') . ' (S/ ' . number_format($row['precio_mensual'] ?? 0, 2) . ')</td>';
            echo '<td style="font-weight:bold;">' . strtoupper($row['estado_pago']) . '</td>';
            echo '<td>' . ($row['ultimo_pago'] ? date('d/m/Y', strtotime($row['ultimo_pago'])) : 'Sin registros') . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table></body></html>';
        exit();
    }

    // 3. REPORTE DE ATENCIÓN (Solicitudes de la Web) - CORREGIDO
    public function exportarExcelSolicitudes() {
        if (!hasRole(['Administrador'])) { header('Location: ' . RUTA_URL . '/dashboard'); exit(); }

        $conn = $this->db->conectar();
        
        // 🔹 CORRECCIÓN: Se cambió 's.fecha_creacion' por el campo real 's.fecha_solicitud'
        $sql = "SELECT s.id_solicitud, c.nombre, c.apellido, c.telefono, s.tipo_solicitud, s.estado_solicitud, s.fecha_solicitud
                FROM solicitudes s
                INNER JOIN clientes c ON s.id_cliente_fk = c.id_cliente
                ORDER BY s.fecha_solicitud DESC";
                
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $filename = "Reporte_Solicitudes_Opticcom_" . date('Ymd') . ".xls";
        $this->_generarCabecerasExcel($filename);

        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="utf-8"></head><body>';
        echo '<table border="1" style="font-family: Arial, sans-serif; border-collapse: collapse;">';
        echo '<thead><tr>';
        echo '<th style="background-color:#ffc107; color:#000000; padding:10px;">ID Ticket</th>';
        echo '<th style="background-color:#ffc107; color:#000000; padding:10px;">Cliente</th>';
        echo '<th style="background-color:#ffc107; color:#000000; padding:10px;">Teléfono</th>';
        echo '<th style="background-color:#ffc107; color:#000000; padding:10px;">Tipo de Solicitud</th>';
        echo '<th style="background-color:#ffc107; color:#000000; padding:10px;">Estado</th>';
        echo '<th style="background-color:#ffc107; color:#000000; padding:10px;">Fecha Emisión</th>';
        echo '</tr></thead><tbody>';

        foreach ($solicitudes as $row) {
            echo '<tr>';
            echo '<td style="text-align:center;">#' . $row['id_solicitud'] . '</td>';
            echo '<td>' . htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) . '</td>';
            echo '<td style="mso-number-format:\'@\';">' . $row['telefono'] . '</td>';
            echo '<td>' . htmlspecialchars($row['tipo_solicitud']) . '</td>';
            echo '<td>' . strtoupper($row['estado_solicitud']) . '</td>';
            // 🔹 CORRECCIÓN: Se mapeó a la clave del arreglo vinculada a la columna real
            echo '<td>' . date('d/m/Y H:i', strtotime($row['fecha_solicitud'])) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table></body></html>';
        exit();
    }

    // Helpers
    private function _generarCabecerasExcel($filename) {
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");
    }

    private function _redirectSegunRol($rol) {
        $rol_normalizado = normalizeRole($rol); 
        $redirect_url = match ($rol_normalizado) {
            'Administrador' => RUTA_URL . '/dashboard',
            'Ventas'        => RUTA_URL . '/ventas',
            'Pagos'         => RUTA_URL . '/clientes',
            'Soporte'       => RUTA_URL . '/clientes',
            'Cliente'       => RUTA_URL . '/portal/inicio',
            default         => RUTA_URL . '/portal/login', 
        };
        header('Location: ' . $redirect_url);
        exit();
    }
}
?>