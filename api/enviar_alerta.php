<?php
// Ubicación física: public_html/api/enviar_alerta.php

// 1. RUTAS EXACTAS BASADAS EN TU ESTRUCTURA
// Subimos un nivel desde /api/ y entramos a /config/
$rutaConexion = __DIR__ . '/../config/Conexion.php';

// (Plan B por si tienes la conexión dentro de app/config/)
if (!file_exists($rutaConexion)) {
    $rutaConexion = __DIR__ . '/../app/config/Conexion.php';
}

if (!file_exists($rutaConexion)) {
    die("Error de ruta: No encuentro Conexion.php. Ruta buscada: " . $rutaConexion);
}

require_once $rutaConexion;

// 2. RECIBIR EL ID POR LA URL
$id_tecnico = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_tecnico == 0) {
    die("Por favor, pasa el ID del tecnico en la URL. Ejemplo: ?id=1");
}

// 3. EXTRAER EL TOKEN AUTOMÁTICAMENTE DE LA BD
$db = new Conexion();
$conn = $db->conectar();
$stmt = $conn->prepare("SELECT fcm_token FROM usuarios WHERE id_usuario = :id LIMIT 1");
$stmt->bindParam(':id', $id_tecnico);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario || empty($usuario['fcm_token'])) {
    die("Error: El usuario ID $id_tecnico no existe o no tiene un Token guardado.");
}

$token_destino = $usuario['fcm_token'];

// 4. LEER TU LLAVE MAESTRA DE FIREBASE
$jsonPath = __DIR__ . '/../config/firebase_credentials.json';
if (!file_exists($jsonPath)) {
    die("Error: No se encuentra el archivo firebase_credentials.json en: " . $jsonPath);
}
$cred = json_decode(file_get_contents($jsonPath), true);

// 5. GENERAR PASE VIP PARA GOOGLE (JWT)
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
$response = curl_exec($ch);
curl_close($ch);

$authData = json_decode($response, true);
$google_access_token = $authData['access_token'];

// 6. ¡DISPARAR LA NOTIFICACIÓN!
$url_firebase = 'https://fcm.googleapis.com/v1/projects/' . $cred['project_id'] . '/messages:send';

$mensaje_push = [
    'message' => [
        'token' => $token_destino,
        'notification' => [
            'title' => '¡NUEVO TRABAJO ASIGNADO!',
            'body' => 'Tienes una nueva instalación de OPTICCOM pendiente.'
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
$resultadoFinal = curl_exec($ch2);
curl_close($ch2);

echo "<h3>¡Notificación disparada con éxito!</h3>";
echo "<p>Respuesta de Google: " . $resultadoFinal . "</p>";
?>