<?php
// Ubicación: app/controladores/Ordenes.php

// 🔹 INCLUIMOS NUESTRA HERRAMIENTA DE NOTIFICACIONES
require_once dirname(APP_ROOT) . '/public_html/config/NotificacionFCM.php';

class Ordenes extends Controlador {
    private $ordenModelo;

    public function __construct() {
        if (!isLoggedIn()) {
            flash('mensaje_error', 'Debe iniciar sesión para acceder.', 'alert alert-warning');
            header('location: '. RUTA_URL . '/portal/login'); 
            exit();
        }
        // Solo Admin y Soporte pueden despachar técnicos
        if (!hasRole(['Administrador', 'Soporte'])) { 
            flash('mensaje_error', 'Acceso no autorizado al panel de despacho.', 'alert alert-danger');
            header('location: ' . RUTA_URL . '/dashboard');
            exit();
        }

        $this->ordenModelo = $this->modelo('OrdenTrabajo');
    }

    // Muestra la lista de todas las órdenes (El "Monitor de Campo")
    public function index() {
        $busqueda = '';
        
        // 🔹 LÓGICA DEL BUSCADOR INTEGRADA
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['busqueda'])) {
            $busqueda = trim($_POST['busqueda']);
            // Pasamos la búsqueda al modelo (Asegúrate de que tu modelo lo soporte)
            $ordenes = $this->ordenModelo->obtenerTodasLasOrdenes($busqueda) ?? [];
        } else {
            $ordenes = $this->ordenModelo->obtenerTodasLasOrdenes() ?? [];
        }

        $tecnicos = $this->ordenModelo->obtenerTecnicos() ?? []; 
        
        $datos = [
            'titulo'   => 'Monitor de Despachos y Órdenes',
            'ordenes'  => $ordenes,
            'tecnicos' => $tecnicos,
            'busqueda' => $busqueda // Para mantener el texto en la caja de búsqueda
        ];

        $this->vista('ordenes/inicio', $datos);
    }

    // Función para que el despachador asigne la orden a un técnico de campo
    public function reasignarOrden() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $id_orden         = trim($_POST['id_orden'] ?? '');
            $id_tecnico_real  = trim($_POST['id_tecnico'] ?? '');
            $fecha_real       = trim($_POST['fecha_programada'] ?? '');

            if (empty($id_orden) || empty($id_tecnico_real) || empty($fecha_real)) {
                flash('mensaje_error', 'Debe seleccionar un técnico y una fecha válida.', 'alert alert-danger');
                header('location: ' . RUTA_URL . '/ordenes');
                exit();
            }

            if ($this->ordenModelo->asignarTecnicoYFecha($id_orden, $id_tecnico_real, $fecha_real)) {
                
                // 🔹 AQUÍ DISPARAMOS LA NOTIFICACIÓN AL REASIGNAR 🔹
                $titulo = "¡Nueva Orden #$id_orden!";
                $cuerpo = "Te han asignado un nuevo trabajo de campo. Revisa los detalles en tu app.";
                NotificacionFCM::enviar($id_tecnico_real, $titulo, $cuerpo);
                
                flash('orden_mensaje', 'Orden asignada con éxito. El técnico ya fue notificado.');
            } else {
                flash('mensaje_error', 'Hubo un error al reasignar la orden.', 'alert alert-danger');
            }
            
            header('location: ' . RUTA_URL . '/ordenes');
            exit();
        }
    }

    // Formulario tradicional para crear una orden manual desde cero
    public function asignar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $datos = [
                'id_cliente'       => trim($_POST['id_cliente'] ?? ''),
                'id_tecnico'       => trim($_POST['id_tecnico'] ?? ''),
                'id_tipo_orden'    => trim($_POST['id_tipo_orden'] ?? ''),
                'prioridad'        => trim($_POST['prioridad'] ?? 'Media'),
                'fecha_programada' => trim($_POST['fecha_programada'] ?? ''),
                'observaciones'    => trim($_POST['observaciones'] ?? '')
            ];

            if (empty($datos['id_cliente']) || empty($datos['id_tecnico']) || empty($datos['id_tipo_orden']) || empty($datos['fecha_programada'])) {
                flash('mensaje_error', 'Por favor, complete todos los campos obligatorios.', 'alert alert-danger');
                header('location: ' . RUTA_URL . '/ordenes/asignar');
                exit();
            }

            $resultado_creacion = $this->ordenModelo->crearOrden($datos);

            if ($resultado_creacion) {
                
                // 🔹 AQUÍ DISPARAMOS LA NOTIFICACIÓN AL CREAR MANUALMENTE 🔹
                $titulo = "¡Nueva Asignación de Trabajo!";
                $cuerpo = "Tienes una nueva instalación o soporte programado para el " . $datos['fecha_programada'] . ".";
                NotificacionFCM::enviar($datos['id_tecnico'], $titulo, $cuerpo);

                flash('orden_mensaje', 'Orden creada y asignada con éxito. El técnico fue notificado.');
                header('location: ' . RUTA_URL . '/ordenes');
                exit();
            } else {
                flash('mensaje_error', 'Error al crear la orden.', 'alert alert-danger');
            }
        } else {
            $datos = [
                'titulo' => 'Asignar Nueva Orden a Técnico',
                'clientes' => $this->ordenModelo->obtenerClientesDisponibles(),
                'tecnicos' => $this->ordenModelo->obtenerTecnicos(),
                'tipos_orden' => $this->ordenModelo->obtenerTiposOrden()
            ];

            $this->vista('ordenes/asignar', $datos);
        }
    }

    public function reporte($id_orden) {
        $reporte = $this->ordenModelo->obtenerReportePorOrden($id_orden);
        $fotos = [];
        if ($reporte) {
            $fotos = $this->ordenModelo->obtenerFotosPorReporte($reporte->id_reporte);
        }

        $datos = [
            'titulo' => 'Reporte de Instalación (App Móvil)',
            'reporte' => $reporte,
            'fotos' => $fotos
        ];

        $this->vista('ordenes/reporte', $datos);
    }
}
?>