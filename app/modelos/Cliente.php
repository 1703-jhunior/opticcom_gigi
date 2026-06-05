<?php
class Cliente {
    private $db;
    private $conexion_exitosa = false;

    public function __construct() {
        try {
            $conexion = new Conexion();
            $this->db = $conexion->conectar();
            $this->conexion_exitosa = true;
        } catch (PDOException $e) {
            error_log("❌ Error de conexión en Cliente: " . $e->getMessage());
            $this->conexion_exitosa = false;
        }
    }

    /* ============================================================
     * RECURSOS ADICIONALES (NORMALIZACIÓN)
     * ============================================================ */
    public function obtenerDistritos() {
        if (!$this->conexion_exitosa) return [];
        $sql = "SELECT d.id_distrito, d.nombre AS distrito, p.nombre AS provincia, dep.nombre AS departamento 
                FROM distritos d
                INNER JOIN provincias p ON d.id_provincia_fk = p.id_provincia
                INNER JOIN departamentos dep ON p.id_departamento_fk = dep.id_departamento
                WHERE d.estado_registro = '1'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function obtenerTiposPago() {
        if (!$this->conexion_exitosa) return [];
        $sql = "SELECT * FROM tipos_pago WHERE estado_registro = '1'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /* ============================================================
     * CLIENTES - CRUD GENERAL
     * ============================================================ */
    public function obtenerClientes($busqueda = null) {
        if (!$this->conexion_exitosa) return [];

        // 🔹 AQUÍ ESTÁ LA MAGIA: Agregamos los LEFT JOIN para traer Provincia y Departamento
        $sql = "SELECT c.*, p.nombre_plan, p.velocidad, p.precio_mensual AS precio_plan,
                       d.nombre AS distrito,
                       prov.nombre AS provincia,
                       dep.nombre AS departamento
                FROM clientes c
                LEFT JOIN planes p ON c.id_plan_fk = p.id_plan
                LEFT JOIN distritos d ON c.id_distrito_fk = d.id_distrito
                LEFT JOIN provincias prov ON d.id_provincia_fk = prov.id_provincia
                LEFT JOIN departamentos dep ON prov.id_departamento_fk = dep.id_departamento
                WHERE c.estado_registro = '1'";

        if (!empty($busqueda)) {
            $sql .= " AND (c.nombre LIKE :b OR c.apellido LIKE :b OR c.dni LIKE :b)";
        }

        $sql .= " ORDER BY c.id_cliente DESC";

        $stmt = $this->db->prepare($sql);
        if (!empty($busqueda)) {
            $b = '%' . $busqueda . '%';
            $stmt->bindParam(':b', $b);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function obtenerClientePorDni($dni) {
        if (!$this->conexion_exitosa) return false;
        $sql = "SELECT * FROM clientes WHERE dni = :dni ORDER BY id_cliente DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':dni', $dni, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function obtenerClientePorId($id) {
        if (!$this->conexion_exitosa) return false;

        // 🔹 CORRECCIÓN: Se agregaron los JOIN de provincias y departamentos
        $sql = "SELECT c.*, p.nombre_plan, p.precio_mensual AS precio_plan, p.velocidad,
                       d.nombre AS distrito_nombre,
                       d.nombre AS distrito,
                       prov.nombre AS provincia,
                       dep.nombre AS departamento
                FROM clientes c
                LEFT JOIN planes p ON p.id_plan = c.id_plan_fk
                LEFT JOIN distritos d ON c.id_distrito_fk = d.id_distrito
                LEFT JOIN provincias prov ON d.id_provincia_fk = prov.id_provincia
                LEFT JOIN departamentos dep ON prov.id_departamento_fk = dep.id_departamento
                WHERE c.id_cliente = :id AND c.estado_registro = '1'
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function agregarCliente($datos) {
        if (!$this->conexion_exitosa) return false;

        $sql = "INSERT INTO clientes 
                (nombre, apellido, dni, telefono, email, 
                 id_distrito_fk, direccion_calle, referencia, location_link, 
                 id_plan_fk, fecha_instalacion, estado_servicio, estado_pago, estado_registro)
                VALUES 
                (:nombre, :apellido, :dni, :telefono, :email, 
                 :id_distrito_fk, :direccion_calle, :referencia, :location_link, 
                 :id_plan, :fecha_instalacion, :estado_servicio, 'Pendiente', '1')";
        try {
            $stmt = $this->db->prepare($sql);
            
            // 🔹 EL BLINDAJE: Si un dato viene vacío (''), lo forzamos a ser NULL 
            // para que MySQL no arroje errores de tipo de dato o de llaves foráneas.
            return $stmt->execute([
                ':nombre'            => $datos['nombre'],
                ':apellido'          => empty($datos['apellido']) ? null : $datos['apellido'],
                ':dni'               => $datos['documento_identidad'],
                ':telefono'          => $datos['telefono'],
                ':email'             => empty($datos['email']) ? null : $datos['email'],
                ':id_distrito_fk'    => empty($datos['distrito']) ? 1 : $datos['distrito'],
                ':direccion_calle'   => $datos['direccion_calle'],
                ':referencia'        => empty($datos['referencia']) ? null : $datos['referencia'],
                ':location_link'     => empty($datos['location_link']) ? null : $datos['location_link'],
                ':id_plan'           => empty($datos['id_plan']) ? null : $datos['id_plan'],
                ':fecha_instalacion' => empty($datos['fecha_instalacion']) ? null : $datos['fecha_instalacion'],
                ':estado_servicio'   => $datos['estado_servicio']
            ]);
        } catch (Throwable $e) {
            // ⚠️ Guardamos el error real en el archivo de registro (log) de Hostinger para saber exactamente por qué falla
            error_log("❌ Error fatal en agregarCliente: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarCliente($datos) {
        if (!$this->conexion_exitosa) return false;

        $sql = "UPDATE clientes SET
                    nombre = :nombre, 
                    apellido = :apellido, 
                    dni = :dni,
                    telefono = :telefono, 
                    email = :email, 
                    id_distrito_fk = :id_distrito_fk,
                    direccion_calle = :direccion_calle,
                    referencia = :referencia,
                    location_link = :location_link,
                    id_plan_fk = :id_plan,
                    fecha_instalacion = :fecha_instalacion,
                    estado_servicio = :estado_servicio
                WHERE id_cliente = :id_cliente";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nombre' => $datos['nombre'],
                ':apellido' => $datos['apellido'],
                ':dni' => $datos['documento_identidad'],
                ':telefono' => $datos['telefono'],
                ':email' => $datos['email'],
                ':id_distrito_fk' => $datos['distrito'],
                ':direccion_calle' => $datos['direccion_calle'],
                ':referencia' => $datos['referencia'],
                ':location_link' => $datos['location_link'],
                ':id_plan' => $datos['id_plan'],
                ':fecha_instalacion' => $datos['fecha_instalacion'],
                ':estado_servicio' => $datos['estado_servicio'],
                ':id_cliente' => $datos['id_cliente']
            ]);
        } catch (Throwable $e) {
            error_log("Error actualizarCliente: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarPlanCliente($id_cliente, $id_plan_nuevo) {
        if (!$this->conexion_exitosa) return false;
        $sql = "UPDATE clientes SET id_plan_fk = :id_plan WHERE id_cliente = :id_cliente";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id_plan' => $id_plan_nuevo, ':id_cliente' => $id_cliente]);
        } catch (Throwable $e) {
            return false;
        }
    }

    public function borrarCliente($id) {
        $sql = "UPDATE clientes SET estado_registro = '0' WHERE id_cliente = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /* ============================================================
     * PAGOS DETALLADOS
     * ============================================================ */
    public function registrarPagoDetallado($id_cliente, $datos_pago) {
        if (!$this->conexion_exitosa) return false;
        try {
            $sql = "INSERT INTO pagos (id_cliente_fk, fecha_pago, monto_pagado, mes_correspondiente, id_tipo_pago_fk, id_estado_pago_fk, id_usuario_registro)
                    VALUES (:id, :fecha, :monto, :mes, :id_tipo_pago, 2, :id_usuario)"; 
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id_cliente,
                ':fecha' => $datos_pago['fecha_pago'],
                ':monto' => $datos_pago['monto_pagado'],
                ':mes' => $datos_pago['mes_correspondiente'],
                ':id_tipo_pago' => $datos_pago['id_tipo_pago'],
                ':id_usuario' => $_SESSION['id_usuario'] ?? null
            ]);
        } catch (Throwable $e) {
            error_log("Error registrarPagoDetallado: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerHistorialPagos($id_cliente) {
        $sql = "SELECT p.*, u.nombre AS nombre_usuario_registro, t.nombre_tipo AS metodo_pago, ep.nombre_estado AS estado_pago
                FROM pagos p
                LEFT JOIN usuarios u ON p.id_usuario_registro = u.id_usuario
                LEFT JOIN tipos_pago t ON p.id_tipo_pago_fk = t.id_tipo_pago
                LEFT JOIN estados_pago ep ON p.id_estado_pago_fk = ep.id_estado_pago
                WHERE p.id_cliente_fk = :id
                ORDER BY p.fecha_pago DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id_cliente);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /* ============================================================
     * PORTAL CLIENTE & RECIBOS
     * ============================================================ */
    public function loginCliente($dni, $password) {
        if (!$this->conexion_exitosa) return false;
        $sql = "SELECT * FROM clientes WHERE dni = :dni AND estado_registro = '1' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':dni', $dni);
        $stmt->execute();
        $cliente = $stmt->fetch(PDO::FETCH_OBJ);

        if ($cliente && !empty($cliente->password) && password_verify($password, $cliente->password)) {
            return $cliente;
        }
        return false;
    }

    public function crearPasswordCliente($id_cliente, $password_plana) {
        $hash = password_hash($password_plana, PASSWORD_DEFAULT);
        $sql = "UPDATE clientes SET password = :p WHERE id_cliente = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':p' => $hash, ':id' => $id_cliente]);
    }

    public function guardarRecibo($id_cliente, $nombre_archivo) {
        $sql = "INSERT INTO recibos (id_cliente_fk, nombre_archivo, fecha_subida) VALUES (:id_cliente, :nombre, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_cliente' => $id_cliente, ':nombre' => $nombre_archivo]);
    }
    
    public function obtenerRecibosPorCliente($id_cliente) {
        $sql = "SELECT id_recibo, nombre_archivo, fecha_subida FROM recibos WHERE id_cliente_fk = :id_cliente ORDER BY fecha_subida DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function eliminarReciboPorNombre($id_cliente, $nombre_archivo) {
        $sql = "DELETE FROM recibos WHERE id_cliente_fk = :id_cliente AND nombre_archivo = :nombre_archivo";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_cliente' => $id_cliente, ':nombre_archivo' => $nombre_archivo]);
    }

    // --- FUNCIONES DE ESTADO Y PORTAL ---

    public function obtenerPlanPorCliente($id_cliente) {
        if (!$this->conexion_exitosa) return null;
        $sql = "SELECT p.* FROM planes p 
                INNER JOIN clientes c ON c.id_plan_fk = p.id_plan 
                WHERE c.id_cliente = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_cliente]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function obtenerPagosPorCliente($id_cliente, $limite = 5) {
        if (!$this->conexion_exitosa) return [];
        $sql = "SELECT p.*, t.nombre_tipo AS metodo_pago 
                FROM pagos p 
                LEFT JOIN tipos_pago t ON p.id_tipo_pago_fk = t.id_tipo_pago
                WHERE p.id_cliente_fk = :id 
                ORDER BY p.fecha_pago DESC 
                LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id_cliente, PDO::PARAM_INT);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function actualizarEstadoPago($id_cliente, $estado) {
        if (!$this->conexion_exitosa) return false;
        $sql = "UPDATE clientes SET estado_pago = :estado WHERE id_cliente = :id_cliente";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':estado' => $estado, ':id_cliente' => $id_cliente]);
        } catch (Throwable $e) {
            error_log("Error en actualizarEstadoPago: " . $e->getMessage());
            return false;
        }
    }
    // ========================================================
    // FUNCIONES DE CONTEO PARA EL DASHBOARD
    // ========================================================
    public function contarClientes() {
        if (!$this->conexion_exitosa) return 0;
        $sql = "SELECT COUNT(id_cliente) as total FROM clientes WHERE estado_registro = '1'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_OBJ);
        return $resultado ? $resultado->total : 0;
    }

    public function contarClientesPorEstado($estado) {
        if (!$this->conexion_exitosa) return 0;
        $sql = "SELECT COUNT(id_cliente) as total FROM clientes WHERE estado_servicio = :estado AND estado_registro = '1'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':estado' => $estado]);
        $resultado = $stmt->fetch(PDO::FETCH_OBJ);
        return $resultado ? $resultado->total : 0;
    }
}