<?php
class Usuario {
    private $db;
    private $conexion_exitosa = false;

    public function __construct(){
        try {
            $conexion = new Conexion();
            $this->db = $conexion->conectar();
            $this->conexion_exitosa = true;
        } catch (PDOException $e) {
            error_log("FALLO CRÍTICO CONEXIÓN MODELO USUARIO: " . $e->getMessage());
            $this->conexion_exitosa = false;
        }
    }

    /**
     * Función de Login para Administradores y Técnicos
     */
    public function login($email, $password){
        if (!$this->conexion_exitosa) { return false; }
        try {
            // Buscamos al usuario por su email
            $usuario = $this->obtenerUsuarioPorEmail($email);
            
            if ($usuario) {
                if (empty($usuario->password)) return false; 
                
                // --- ADAPTACIÓN PARA CONTRASEÑAS ENCRIPTADAS O TEXTO PLANO ---
                // Verifica si la contraseña es un hash (empieza con $2y$)
                if (strpos($usuario->password, '$2y$') === 0) {
                    if (password_verify($password, $usuario->password)) {
                        return $usuario;
                    }
                } else {
                    // Si no es un hash (ej: '123456' como el técnico actual en tu BD)
                    if ($password === $usuario->password) {
                        return $usuario;
                    }
                }
            }
            return false;
        } catch (Throwable $e) { return false; }
    }

    /**
     * Obtiene todos los usuarios (Con JOIN a tabla roles)
     */
    public function obtenerUsuarios(){
        if (!$this->conexion_exitosa) { return []; }
        
        // MODIFICADO: Hacemos JOIN con roles para traer 'nombre_rol'
        $sql = "SELECT u.id_usuario, u.nombre, u.email, r.nombre_rol AS rol, u.fecha_creacion 
                FROM usuarios u
                INNER JOIN roles r ON u.id_rol_fk = r.id_rol
                WHERE u.estado_registro = '1'
                ORDER BY u.nombre ASC";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) { return []; }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (Throwable $e) { return []; }
    }

    /**
     * Agrega un nuevo usuario
     */
    public function agregarUsuario($datos){
        if (!$this->conexion_exitosa) { return false; }
        
        // MODIFICADO: Guardamos id_rol_fk en lugar de texto
        $sql = "INSERT INTO usuarios (nombre, email, id_rol_fk, password, estado_registro) 
                VALUES (:nombre, :email, :id_rol_fk, :password, '1')";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) { return false; }
            $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $datos['email'], PDO::PARAM_STR);
            $stmt->bindParam(':id_rol_fk', $datos['rol'], PDO::PARAM_INT); // Viene del select
            $stmt->bindParam(':password', $datos['password'], PDO::PARAM_STR); 
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error PDO en agregarUsuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un usuario (con o sin contraseña)
     */
    public function actualizarUsuario($datos){
        if (!$this->conexion_exitosa) { return false; }
        
        try {
            if (empty($datos['password'])) {
                $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, id_rol_fk = :id_rol_fk 
                        WHERE id_usuario = :id_usuario";
                $stmt = $this->db->prepare($sql);
                if ($stmt === false) { return false; }
            } else {
                $datos['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, id_rol_fk = :id_rol_fk, password = :password 
                        WHERE id_usuario = :id_usuario";
                $stmt = $this->db->prepare($sql);
                if ($stmt === false) { return false; }
                $stmt->bindParam(':password', $datos['password'], PDO::PARAM_STR);
            }
            
            $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $datos['email'], PDO::PARAM_STR);
            $stmt->bindParam(':id_rol_fk', $datos['rol'], PDO::PARAM_INT);
            $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error PDO en actualizarUsuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Borra un usuario (Borrado Lógico)
     */
    public function borrarUsuario($id_usuario){
        if (!$this->conexion_exitosa) { return false; }
        
        // MODIFICADO: Hacemos borrado lógico para no romper órdenes asignadas
        $sql = "UPDATE usuarios SET estado_registro = '0' WHERE id_usuario = :id_usuario";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) { return false; }
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error PDO en borrarUsuario (Lógico): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca un usuario por email (JOIN con roles)
     */
    public function obtenerUsuarioPorEmail($email){
        if (!$this->conexion_exitosa) { return false; }
        
        $sql = "SELECT u.*, r.nombre_rol AS rol 
                FROM usuarios u 
                INNER JOIN roles r ON u.id_rol_fk = r.id_rol 
                WHERE u.email = :email AND u.estado_registro = '1'";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) { return false; }
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            return $resultado ? $resultado : false;
        } catch (Throwable $e) { return false; }
    }

    /**
     * Busca un usuario por ID (JOIN con roles)
     */
    public function obtenerUsuarioPorId($id_usuario){
        if (!$this->conexion_exitosa) { return false; }
        
        $sql = "SELECT u.id_usuario, u.nombre, u.email, u.id_rol_fk AS rol, u.fecha_creacion 
                FROM usuarios u 
                WHERE u.id_usuario = :id_usuario AND u.estado_registro = '1'";
        try {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) { return false; }
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_OBJ);
            return $resultado ? $resultado : false;
        } catch (Throwable $e) { return false; }
    }
    
    // Nueva función necesaria para los selects en la vista
    public function obtenerRolesActivos() {
        if (!$this->conexion_exitosa) { return []; }
        $sql = "SELECT id_rol, nombre_rol FROM roles WHERE estado_registro = '1'";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (Throwable $e) { return []; }
    }
    // 🔹 NUEVO: Guardar el Token de Firebase (FCM) del celular del técnico
    public function actualizarTokenFCM($id_usuario, $token) {
        $sql = "UPDATE usuarios SET fcm_token = :token WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql); // Asumiendo que usas $this->db como en tus otros modelos
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':id', $id_usuario);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
?>