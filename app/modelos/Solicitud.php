<?php
class Solicitud {
    private $db;
    private $conexion_exitosa = false;

    public function __construct(){
        try {
            $this->db = (new Conexion)->conectar();
            $this->conexion_exitosa = true;
        } catch (PDOException $e) {
            error_log("FALLO CRÍTICO CONEXIÓN MODELO SOLICITUD: " . $e->getMessage());
            $this->conexion_exitosa = false;
        }
    }

    public function agregarSolicitud($datos){
        if (!$this->conexion_exitosa) { return false; }
        
        $estado_inicial = 'pendiente';
        $fecha_ahora_peru = date('Y-m-d H:i:s'); 

        $sql = "INSERT INTO solicitudes 
                    (nombres, apellidos, telefono, documento_identidad, email, 
                     departamento, provincia, distrito, direccion_calle, referencia, location_link,
                     id_plan_interesado, estado_solicitud, id_cliente_fk, tipo_solicitud, fecha_solicitud)
                VALUES 
                    (:nombres, :apellidos, :telefono, :documento, :email,
                     :departamento, :provincia, :distrito, :direccion_calle, :referencia, :location_link,
                     :id_plan, :estado, :id_cliente, :tipo, :fecha_solicitud)";
        try {
            $stmt = $this->db->prepare($sql);

            // 🔹 LA SOLUCIÓN: Buscamos el ID del plan usando ambos nombres posibles
            $id_plan = !empty($datos['id_plan_interesado']) ? $datos['id_plan_interesado'] : (!empty($datos['plan_seleccionado']) ? $datos['plan_seleccionado'] : null);
            
            $email = !empty($datos['email']) ? $datos['email'] : null;
            $referencia = !empty($datos['referencia']) ? $datos['referencia'] : null;
            $id_cliente = !empty($datos['id_cliente_fk']) ? $datos['id_cliente_fk'] : null;
            $tipo_sol = !empty($datos['tipo_solicitud']) ? $datos['tipo_solicitud'] : 'Instalacion';
            
            $calle = !empty($datos['direccion_calle']) ? $datos['direccion_calle'] : 'Revisar en Sistema';
            $doc = !empty($datos['documento_identidad']) ? $datos['documento_identidad'] : null;
            $link = !empty($datos['location_link']) ? $datos['location_link'] : null;

            // Vinculación de parámetros
            $stmt->bindParam(':nombres', $datos['nombres'], PDO::PARAM_STR);
            $stmt->bindParam(':apellidos', $datos['apellidos'], PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
            $stmt->bindParam(':documento', $doc, $doc === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, $email === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':departamento', $datos['departamento'], PDO::PARAM_STR);
            $stmt->bindParam(':provincia', $datos['provincia'], PDO::PARAM_STR);
            $stmt->bindParam(':distrito', $datos['distrito'], PDO::PARAM_STR);
            $stmt->bindParam(':direccion_calle', $calle, PDO::PARAM_STR);
            $stmt->bindParam(':referencia', $referencia, $referencia === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':location_link', $link, $link === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id_plan', $id_plan, $id_plan === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado_inicial, PDO::PARAM_STR);
            $stmt->bindParam(':id_cliente', $id_cliente, $id_cliente === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(':tipo', $tipo_sol, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_solicitud', $fecha_ahora_peru, PDO::PARAM_STR);

            return $stmt->execute(); 
        } catch (PDOException $e) {
             error_log("Error PDO en agregarSolicitud: " . $e->getMessage());
             return false;
        }
    }

    public function obtenerSolicitudes() {
        if (!$this->conexion_exitosa) { return []; }
        $sql = "SELECT s.*, p.nombre_plan, p.precio_mensual
                FROM solicitudes s
                LEFT JOIN planes p ON s.id_plan_interesado = p.id_plan
                ORDER BY s.estado_solicitud ASC, s.fecha_solicitud DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (Throwable $e) {
            error_log("Error en obtenerSolicitudes: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerSolicitudPorId($id_solicitud) {
        if (!$this->conexion_exitosa) { return false; }
        $sql = "SELECT * FROM solicitudes WHERE id_solicitud = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id_solicitud, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (Throwable $e) {
            error_log("Error en obtenerSolicitudPorId: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarEstado($id, $estado){
        if (!$this->conexion_exitosa) { return false; }
        $sql = "UPDATE solicitudes SET estado_solicitud = :estado WHERE id_solicitud = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Throwable $e) {
            error_log("Error en actualizarEstado Solicitud: " . $e->getMessage());
            return false;
        }
    }
}
?>