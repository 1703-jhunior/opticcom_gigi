<?php
// Clase para la conexión a la base de datos (Adaptada para Variables de Entorno en Cloud Run)
class Conexion {
    private $usuario;
    private $password;
    private $db; 
    private $instance_connection_name = 'project-b94c8741-34bc-4e2a-a4a:us-central1:free-trial-first-project';
    private $dbh; // Database Handler

    public function __construct() {
        // Lee las variables de entorno configuradas en tu servicio de Cloud Run (por defecto usa Jhunior)
        $this->usuario = getenv('DB_USER') ?: 'Jhunior';
        $this->password = getenv('DB_PASS') ?: 'Jh60078609#';
        $this->db = getenv('DB_NAME') ?: 'mi_proyecto_db';
    }

    public function conectar(){
        // Conexión directa mediante el socket UNIX de Google Cloud especificando puerto 3306
        $dsn = 'mysql:unix_socket=/cloudsql/' . $this->instance_connection_name . ';port=3306;dbname=' . $this->db . ';charset=utf8mb4';
        
        $opciones = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        try {
            $this->dbh = new PDO($dsn, $this->usuario, $this->password, $opciones);
            return $this->dbh;
        } catch (PDOException $e) {
            die('Error Crítico de Conexión: No se pudo conectar a la base de datos en Cloud SQL. Detalle: ' . $e->getMessage());
        }
    }
}
?>