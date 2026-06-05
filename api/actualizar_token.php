<?php
// Ubicación física: /api/actualizar_token.php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit(0); }

// 🔹 USAMOS LA RUTA QUE YA SABEMOS QUE FUNCIONA EN TU OTRO SCRIPT
// Se encuentra en la misma carpeta /api
$rutaConexion = __DIR__ . '/conexion.php';

if (!file_exists($rutaConexion)) {
    // Intento con mayúscula por si acaso
    $rutaConexion = __DIR__ . '/Conexion.php';
}

if (!file_exists($rutaConexion)) {
    echo json_encode(["success" => false, "mensaje" => "Error: No se encontró conexion.php en la carpeta /api"]);
    exit;
}

require_once $rutaConexion;

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if(!isset($data['id_usuario']) || !isset($data['fcm_token'])) { 
    echo json_encode(["success" => false, "mensaje" => "Faltan datos obligatorios"]); 
    exit; 
}

$id_usuario = trim($data['id_usuario']);
$token = trim($data['fcm_token']);

try {
    // Instanciamos la clase según tu estructura
    $db = new Conexion();
    $conn = $db->conectar();
    
    // Asegúrate de que la columna fcm_token exista en tu tabla usuarios
    $sql = "UPDATE usuarios SET fcm_token = :token WHERE id_usuario = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':token', $token);
    $stmt->bindParam(':id', $id_usuario);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "mensaje" => "Token guardado correctamente"]);
    } else {
        echo json_encode(["success" => false, "mensaje" => "Error al ejecutar el UPDATE"]);
    }

} catch (Exception $e) {
    echo json_encode(["success" => false, "mensaje" => "Error del Servidor: " . $e->getMessage()]);
}
?>