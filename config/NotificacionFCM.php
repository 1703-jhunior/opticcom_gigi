<?php
// Ubicación física: public_html/config/NotificacionFCM.php

require_once __DIR__ . '/Conexion.php';

class NotificacionFCM {
    
    public static function enviar($id_tecnico, $titulo, $cuerpo_mensaje) {
        try {
            // 1. Buscar el Token
            $db = new Conexion();
            $conn = $db->conectar();
            $stmt = $conn->prepare("SELECT fcm_token FROM usuarios WHERE id_usuario = :id LIMIT 1");
            $stmt->bindParam(':id', $id_tecnico);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario || empty($usuario['fcm_token'])) {
                return false; 
            }

            $token_destino = $usuario['fcm_token'];

            // 2. Leer credenciales (están en la misma carpeta config/)
            $jsonPath = __DIR__ . '/firebase_credentials.json';
            if (!file_exists($jsonPath)) {
                error_log("Falta el archivo firebase_credentials.json en config/");
                return false;
            }
            $cred = json_decode(file_get_contents($jsonPath), true);

            // 3. Generar JWT para Google
            $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
            $payload = json_encode([
                'iss' => $cred['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => time() + 3600,
                'iat' => time()
            ]);

            $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
            $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
            $signature = '';
            openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $cred['private_key'], OPENSSL_ALGO_SHA256);
            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
            $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $authData = json_decode(curl_exec($ch), true);
            curl_close($ch);

            if (!isset($authData['access_token'])) return false;
            $google_access_token = $authData['access_token'];

            // 4. Enviar Push a FCM
            $url_firebase = 'https://fcm.googleapis.com/v1/projects/' . $cred['project_id'] . '/messages:send';
            $mensaje_push = [
                'message' => [
                    'token' => $token_destino,
                    'notification' => [
                        'title' => $titulo,
                        'body' => $cuerpo_mensaje
                    ]
                ]
            ];

            $ch2 = curl_init();
            curl_setopt($ch2, CURLOPT_URL, $url_firebase);
            curl_setopt($ch2, CURLOPT_POST, true);
            curl_setopt($ch2, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $google_access_token,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($mensaje_push));
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch2);
            curl_close($ch2);

            return true;

        } catch (Exception $e) {
            error_log("Error enviando FCM: " . $e->getMessage());
            return false;
        }
    }
}
?>