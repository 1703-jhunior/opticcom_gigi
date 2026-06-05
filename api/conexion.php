<?php
// =======================================================================
// 🛡️ BLOQUE ANTIMAGIA NEGRA (CORS)
// =======================================================================
// Permite acceso desde cualquier origen (localhost, celular, web, etc.)
header("Access-Control-Allow-Origin: *");
// Permite los headers que Flutter suele enviar
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
// Permite los métodos que usas
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
// Define que siempre respondes JSON
header("Content-Type: application/json; charset=UTF-8");

// IMPORTANTE: Manejo de la petición "OPTIONS" (Pre-flight)
// Cuando el navegador pregunta "¿Puedo conectarme?", respondemos "SÍ" y cortamos aquí.
// Si no hacemos esto, el script intenta conectarse a la BD y falla el chequeo de seguridad.
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
// =======================================================================

class Conexion {
    // ⚠️ TUS CREDENCIALES REALES DE HOSTINGER
    private $host = 'localhost';
    private $usuario = 'u467606377_RoySuperOPTC'; 
    private $password = 'RoyHqazx159@.'; 
    private $db = 'u467606377_opticcom_db';
    private $dbh;

    public function conectar(){
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db . ';charset=utf8';
        $opciones = [
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            $this->dbh = new PDO($dsn, $this->usuario, $this->password, $opciones);
            return $this->dbh;
        } catch (PDOException $e) {
            // Devuelve error en JSON válido para que la App no explote
            die(json_encode(["success" => false, "mensaje" => "Error BD: " . $e->getMessage()]));
        }
    }
}
?>