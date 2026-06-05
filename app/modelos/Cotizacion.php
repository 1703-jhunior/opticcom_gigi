<?php
class Cotizacion {
    private $db;
    private $conexion_exitosa = false;

    public function __construct(){
        try {
            $this->db = (new Conexion())->conectar();
            $this->conexion_exitosa = true;
        } catch (PDOException $e) {
            error_log("FALLO CRÍTICO CONEXIÓN MODELO COTIZACION: " . $e->getMessage());
            $this->conexion_exitosa = false;
        }
    }

    /**
     * Agrega una nueva cotización B2B.
     */
    public function agregarCotizacion($datos) {
        if (!$this->conexion_exitosa) return false;

        $estado_inicial = 'pendiente';
        // ❗ INICIO DE LA CORRECCIÓN DE HORA
        $fecha_ahora_peru = date('Y-m-d H:i:s'); // PHP genera la hora de Perú
        // ❗ FIN DE LA CORRECCIÓN DE HORA

        $sql = "INSERT INTO cotizaciones 
                    (razon_social, ruc, persona_contacto, telefono_contacto, email_contacto,
                     departamento, provincia, distrito, direccion_calle, referencia, location_link,
                     mensaje, estado_cotizacion, fecha_solicitud)
                VALUES 
                    (:razon_social, :ruc, :persona_contacto, :telefono_contacto, :email_contacto,
                     :departamento, :provincia, :distrito, :direccion_calle, :referencia, :location_link,
                     :mensaje, :estado, :fecha_solicitud)"; // ❗ Campo de hora añadido
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) {
                 error_log("Error PREPARE agregarCotizacion: " . implode(":", $this->db->errorInfo()));
                 return false;
             }
             
            $link = empty($datos['location_link']) ? null : $datos['location_link'];
            $ref = empty($datos['referencia']) ? null : $datos['referencia'];
            $msg = empty($datos['mensaje']) ? null : $datos['mensaje'];

            $stmt->bindParam(':razon_social', $datos['razon_social'], PDO::PARAM_STR);
            $stmt->bindParam(':ruc', $datos['ruc'], PDO::PARAM_STR);
            $stmt->bindParam(':persona_contacto', $datos['persona_contacto'], PDO::PARAM_STR);
            $stmt->bindParam(':telefono_contacto', $datos['telefono_contacto'], PDO::PARAM_STR);
            $stmt->bindParam(':email_contacto', $datos['email_contacto'], PDO::PARAM_STR);
            $stmt->bindParam(':departamento', $datos['departamento'], PDO::PARAM_STR);
            $stmt->bindParam(':provincia', $datos['provincia'], PDO::PARAM_STR);
            $stmt->bindParam(':distrito', $datos['distrito'], PDO::PARAM_STR);
            $stmt->bindParam(':direccion_calle', $datos['direccion_calle'], PDO::PARAM_STR);
            $stmt->bindParam(':referencia', $ref, $ref === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':location_link', $link, $link === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':mensaje', $msg, $msg === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':estado', $estado_inicial, PDO::PARAM_STR);
            
            // ❗ INICIO DE LA CORRECCIÓN DE HORA
            $stmt->bindParam(':fecha_solicitud', $fecha_ahora_peru, PDO::PARAM_STR);
            // ❗ FIN DE LA CORRECCIÓN DE HORA

            return $stmt->execute();
        } catch (PDOException $e) {
             error_log("Error PDO en agregarCotizacion: [Code ".$e->getCode()."] ".$e->getMessage());
             return false;
        } catch (Throwable $e) {
             error_log("Error Throwable en agregarCotizacion: ".$e->getMessage());
             return false;
        }
    }

    /**
     * Obtiene TODAS las cotizaciones B2B.
     */
    public function obtenerCotizaciones(){
        if (!$this->conexion_exitosa) { return []; }
        $sql = "SELECT * FROM cotizaciones ORDER BY fecha_solicitud DESC";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) { return []; }
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $resultado ? $resultado : [];
        } catch (Throwable $e) {
            error_log("Error Throwable en obtenerCotizaciones: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene UNA cotización B2B por ID.
     */
    public function obtenerCotizacionPorId($id){
        if (!$this->conexion_exitosa) { return false; }
        $sql = "SELECT * FROM cotizaciones WHERE id_cotizacion = :id LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) { return false; }
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            return $resultado ? $resultado : false;
        } catch (Throwable $e) {
            error_log("Error Throwable en obtenerCotizacionPorId ID $id: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza SOLO el estado de una cotización.
     */
    public function actualizarEstado($id, $estado){
        if (!$this->conexion_exitosa) { return false; }
        $sql = "UPDATE cotizaciones SET estado_cotizacion = :estado WHERE id_cotizacion = :id";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) { return false; }
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Throwable $e) {
            error_log("Error Throwable en actualizarEstado Cotizacion ID $id: " . $e->getMessage());
            return false;
        }
    }
}

