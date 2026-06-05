<?php
// Clase para la conexión a la base de datos (Conexión directa a Alpine Linux)
class Conexion {
    // REQUISITO PROFESOR: Conexión directa a la IP de la máquina Alpine Linux
    private $host = 'hy3cw2euykssv6r2hdlcoya5'; // La IP de tu servidor Alpine
    private $usuario = 'mariadb';
    private $password = 'root1234'; 
    private $db = 'default'; 
    private $dbh; // Database Handler

    public function conectar(){
        // Al usar la IP real en el host, la conexión va "directo a Alpine" por el puerto público 3306
        $dsn = 'mysql:host=' . $this->host . ';port=3306;dbname=' . $this->db . ';charset=utf8mb4';
        $opciones = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        try {
            $this->dbh = new PDO($dsn, $this->usuario, $this->password, $opciones);
            return $this->dbh;
        } catch (PDOException $e) {
            die('Error Crítico de Conexión: No se pudo conectar a la base de datos. Verifica la contraseña en el archivo Conexion.php. Detalle: ' . $e->getMessage());
        }
    }
}
