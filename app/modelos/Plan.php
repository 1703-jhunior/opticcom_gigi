<?php
class Plan {
    private $db;
    private $conexion_exitosa = false;

    public function __construct(){
        try {
            $conexion = new Conexion();
            $this->db = $conexion->conectar();
            $this->conexion_exitosa = true;
        } catch (PDOException $e) {
            error_log("❌ Error de conexión en Plan: " . $e->getMessage());
            $this->conexion_exitosa = false;
        }
    }

    /* =========================================================
     * LISTADOS
     * ========================================================= */

    // Para el admin: TODOS los planes (Corregido el error de fecha_creacion)
    public function obtenerPlanes(){
        if (!$this->conexion_exitosa) return [];
        // Ordenamos por precio en lugar de fecha_creacion
        $sql = "SELECT * FROM planes ORDER BY precio_mensual ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Para la web pública / portal: solo activos
    public function obtenerPlanesActivos(){
        if (!$this->conexion_exitosa) return [];
        // Adaptado: estado = 'activo' ahora es estado_registro = '1'
        $sql = "SELECT * FROM planes WHERE estado_registro = '1' ORDER BY precio_mensual ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /* =========================================================
     * CRUD
     * ========================================================= */

    public function agregarPlan($datos){
        if (!$this->conexion_exitosa) return false;
        // Adaptado: Usamos estado_registro y '1' por defecto
        $sql = "INSERT INTO planes (nombre_plan, velocidad, precio_mensual, descripcion, estado_registro)
                VALUES (:nombre, :velocidad, :precio, :descripcion, '1')";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre', $datos['nombre_plan']);
        $stmt->bindParam(':velocidad', $datos['velocidad']);
        $stmt->bindParam(':precio', $datos['precio_mensual']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        return $stmt->execute();
    }

    public function obtenerPlanPorId($id_plan){
        if (!$this->conexion_exitosa) return false;
        $sql = "SELECT * FROM planes WHERE id_plan = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id_plan, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function actualizarPlan($datos){
        if (!$this->conexion_exitosa) return false;
        $sql = "UPDATE planes
                SET nombre_plan = :nombre,
                    velocidad = :velocidad,
                    precio_mensual = :precio,
                    descripcion = :descripcion
                WHERE id_plan = :id_plan";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre', $datos['nombre_plan']);
        $stmt->bindParam(':velocidad', $datos['velocidad']);
        $stmt->bindParam(':precio', $datos['precio_mensual']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':id_plan', $datos['id_plan'], PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminarPlan($id_plan){
        if (!$this->conexion_exitosa) return false;
        // se usará solo si no tiene clientes
        $sql = "DELETE FROM planes WHERE id_plan = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id_plan, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /* =========================================================
     * CONTROL DE ESTADO
     * ========================================================= */

    public function desactivarPlan($id_plan){
        if (!$this->conexion_exitosa) return false;
        // Adaptado a eliminación lógica: estado_registro = '0'
        $sql = "UPDATE planes SET estado_registro = '0' WHERE id_plan = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id_plan, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function activarPlan($id_plan){
        if (!$this->conexion_exitosa) return false;
        // Adaptado a activación lógica: estado_registro = '1'
        $sql = "UPDATE planes SET estado_registro = '1' WHERE id_plan = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id_plan, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /* =========================================================
     * VALIDACIONES
     * ========================================================= */

    // ¿Hay clientes usando este plan?
    public function tieneClientes($id_plan){
        if (!$this->conexion_exitosa) return false;
        $sql = "SELECT COUNT(*) AS total FROM clientes WHERE id_plan_fk = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id_plan, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return ($row && (int)$row->total > 0);
    }

    public function contarPlanes(){
        if (!$this->conexion_exitosa) return 0;
        $sql = "SELECT COUNT(*) as total FROM planes";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row ? $row->total : 0;
    }
}
?>