<?php
// Ubicación: app/controladores/Api.php

class Api extends Controlador {
    private $usuarioModelo;

    public function __construct(){
        // Cargamos el modelo de Usuario
        $this->usuarioModelo = $this->modelo('Usuario'); 
    }

    // 🔹 ENDPOINT: Recibe el Token desde Flutter
    public function actualizarToken() {
        // 1. Cabeceras de seguridad para permitir conexión desde la App (CORS)
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");
        header('Content-Type: application/json; charset=utf-8');

        // Si es una petición de verificación (preflight), la aprobamos y salimos
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit(0); }

        // 2. Capturar el JSON que nos manda Flutter
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        // 3. Validar que vengan los datos
        if(!isset($data['id_usuario']) || !isset($data['fcm_token'])) { 
            echo json_encode(["success" => false, "mensaje" => "Faltan datos obligatorios"]); 
            return; 
        }

        $id_usuario = trim($data['id_usuario']);
        $token = trim($data['fcm_token']);

        // 4. Guardar en la Base de Datos
        if ($this->usuarioModelo->actualizarTokenFCM($id_usuario, $token)) {
            echo json_encode(["success" => true, "mensaje" => "Token guardado correctamente en la BD"]);
        } else {
            echo json_encode(["success" => false, "mensaje" => "Error al guardar en la BD"]);
        }
    }
}