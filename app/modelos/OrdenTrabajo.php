<?php
// Ubicación: app/modelos/OrdenTrabajo.php

class OrdenTrabajo {
    private $db;
    private $conexion_exitosa = false;

    public function __construct() {
        try {
            $conexion = new Conexion();
            $this->db = $conexion->conectar();
            $this->conexion_exitosa = true;
        } catch (PDOException $e) {
            error_log("❌ Error de conexión en OrdenTrabajo: " . $e->getMessage());
            $this->conexion_exitosa = false;
        }
    }

    // 🔹 OBTENER TODAS LAS ÓRDENES (AHORA CON BUSCADOR INTEGRADO)
    public function obtenerTodasLasOrdenes($busqueda = '') {
        if (!$this->conexion_exitosa) return [];
        
        // Empezamos la consulta base
        $sql = "SELECT ot.*, 
                       c.nombre AS cliente_nombre, c.apellido AS cliente_apellido, c.direccion_calle,
                       d.nombre AS distrito_nombre,
                       u.nombre AS tecnico_nombre,
                       t.nombre_tipo AS tipo_orden_nombre
                FROM ordenes_trabajo ot
                INNER JOIN clientes c ON ot.id_cliente_fk = c.id_cliente
                LEFT JOIN distritos d ON c.id_distrito_fk = d.id_distrito
                INNER JOIN usuarios u ON ot.id_tecnico_fk = u.id_usuario
                INNER JOIN tipos_orden t ON ot.id_tipo_orden_fk = t.id_tipo_orden
                WHERE ot.estado_registro = '1'";

        // Si hay una palabra de búsqueda, agregamos los filtros
        if (!empty($busqueda)) {
            $sql .= " AND (ot.id_orden LIKE :busqueda 
                           OR c.nombre LIKE :busqueda 
                           OR c.apellido LIKE :busqueda 
                           OR u.nombre LIKE :busqueda 
                           OR ot.estado_orden LIKE :busqueda)";
        }

        // Siempre ordenamos por fecha programada (los más antiguos primero)
        $sql .= " ORDER BY ot.fecha_programada ASC";
        
        $stmt = $this->db->prepare($sql);
        
        // Si hay búsqueda, empaquetamos la variable con los comodines %
        if (!empty($busqueda)) {
            $busquedaLike = '%' . $busqueda . '%';
            $stmt->bindParam(':busqueda', $busquedaLike, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Obtener solo técnicos activos (Rol 3 = Técnico)
    public function obtenerTecnicos() {
        if (!$this->conexion_exitosa) return [];
        $sql = "SELECT id_usuario, nombre FROM usuarios WHERE id_rol_fk = 3 AND estado_registro = '1'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Obtener tipos de orden (Instalación, Avería, etc.)
    public function obtenerTiposOrden() {
        if (!$this->conexion_exitosa) return [];
        $sql = "SELECT id_tipo_orden, nombre_tipo FROM tipos_orden WHERE estado_registro = '1'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Obtener clientes que necesitan una instalación o soporte
    public function obtenerClientesDisponibles() {
        if (!$this->conexion_exitosa) return [];
        $sql = "SELECT id_cliente, nombre, apellido, dni, direccion_calle 
                FROM clientes 
                WHERE estado_registro = '1' AND estado_servicio IN ('Pendiente Instalacion', 'Activo', 'Suspendido')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Guardar la orden en la BD (Esto es lo que lee la App Móvil)
    public function crearOrden($datos) {
        if (!$this->conexion_exitosa) return false;

        $sql = "INSERT INTO ordenes_trabajo 
                (id_cliente_fk, id_tecnico_fk, id_tipo_orden_fk, prioridad, estado_orden, fecha_programada, observaciones_despacho, estado_registro) 
                VALUES 
                (:id_cliente, :id_tecnico, :id_tipo_orden, :prioridad, 'Pendiente', :fecha_programada, :observaciones, '1')";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id_cliente' => $datos['id_cliente'],
                ':id_tecnico' => $datos['id_tecnico'],
                ':id_tipo_orden' => $datos['id_tipo_orden'],
                ':prioridad' => $datos['prioridad'],
                ':fecha_programada' => $datos['fecha_programada'],
                ':observaciones' => $datos['observaciones']
            ]);
        } catch (Throwable $e) {
            error_log("Error crearOrden: " . $e->getMessage());
            return false;
        }
    }

    // --- NUEVAS FUNCIONES PARA VER EL REPORTE DE LA APP ---
    
    public function obtenerReportePorOrden($id_orden) {
        if (!$this->conexion_exitosa) return false;
        $sql = "SELECT * FROM reportes_tecnicos WHERE id_orden_fk = :id_orden LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_orden', $id_orden, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function obtenerFotosPorReporte($id_reporte) {
        if (!$this->conexion_exitosa) return [];
        $sql = "SELECT * FROM evidencias_fotos WHERE id_reporte_fk = :id_reporte";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_reporte', $id_reporte, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    // 🔹 NUEVA FUNCIÓN NECESARIA PARA LA MESA DE DESPACHO
    public function asignarTecnicoYFecha($id_orden, $id_tecnico, $fecha_programada) {
        if (!$this->conexion_exitosa) return false;
        $sql = "UPDATE ordenes_trabajo 
                SET id_tecnico_fk = :tecnico, 
                    fecha_programada = :fecha 
                WHERE id_orden = :orden";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':tecnico' => $id_tecnico,
                ':fecha'   => $fecha_programada,
                ':orden'   => $id_orden
            ]);
        } catch (Throwable $e) {
            error_log("Error asignarTecnicoYFecha: " . $e->getMessage());
            return false;
        }
    }
    
}
?>