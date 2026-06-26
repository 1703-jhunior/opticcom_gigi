<?php
// =======================================================================
// 🛡️ BLOQUE ANTIMAGIA NEGRA (CORS)
// =======================================================================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Content-Type: application/json; charset=UTF-8");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
// =======================================================================

class Conexion {
    // Parámetros de Cloud SQL
    private $usuario = 'root'; 
    private $password = 'j60078609'; // ¡Pon aquí la contraseña de tu Cloud SQL!
    private $db = 'opticcom_db';
    // CORREGIDO: Se añadió el '2a' que faltaba en el ID del proyecto
    private $instance_connection_name = 'project-b94c8741-34bc-4e2a-a4a2a:europe-west1:free-trial-first-project';
    private $dbh;

    public function conectar(){
        // Conexión directa mediante el socket UNIX de Google Cloud
        $dsn = 'mysql:unix_socket=/cloudsql/' . $this->instance_connection_name . ';dbname=' . $this->db . ';charset=utf8mb4';
        
        $opciones = [
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            $this->dbh = new PDO($dsn, $this->usuario, $this->password, $opciones);
            return $this->dbh;
        } catch (PDOException $e) {
            // Devuelve el error en formato JSON para no romper el frontend
            die(json_encode(["success" => false, "mensaje" => "Error BD en Cloud SQL: " . $e->getMessage()]));
        }
    }
}
?>
