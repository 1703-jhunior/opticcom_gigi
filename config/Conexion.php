<?php
// Clase para la conexión a la base de datos (Adaptada para Google Cloud SQL)
class Conexion {
    // Parámetros de Cloud SQL
    private $usuario = 'root';
    private $password = 'j60078609'; // ¡Pon tu contraseña aquí!
    private $db = 'opticcom_db'; 
    private $instance_connection_name = 'project-b94c8741-34bc-4e2a-a4a:europe-west1:free-trial-first-project';
    private $dbh; // Database Handler

    public function conectar(){
        // Conexión directa mediante el socket UNIX de Google Cloud
        $dsn = 'mysql:unix_socket=/cloudsql/' . $this->instance_connection_name . ';dbname=' . $this->db . ';charset=utf8mb4';
        
        $opciones = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        try {
            $this->dbh = new PDO($dsn, $this->usuario, $this->password, $opciones);
            return $this->dbh;
        } catch (PDOException $e) {
            die('Error Crítico de Conexión: No se pudo conectar a la base de datos en Cloud SQL. Verifica la contraseña. Detalle: ' . $e->getMessage());
        }
    }
}
?>
