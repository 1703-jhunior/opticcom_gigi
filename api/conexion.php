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
    private $host = '192.168.1.128';
    private $usuario = 'mariadb'; 
    private $password = 'root1234'; 
    private $db = 'default';
    private $dbh;

    public function conectar(){
        $dsn = 'mysql:host=' . $this->host . ';port=3306;dbname=' . $this->db . ';charset=utf8mb4';
        $opciones = [
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            $this->dbh = new PDO($dsn, $this->usuario, $this->password, $opciones);
            return $this->dbh;
        } catch (PDOException $e) {
            die(json_encode(["success" => false, "mensaje" => "Error BD: " . $e->getMessage()]));
        }
    }
}
?>
