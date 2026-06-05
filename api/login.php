<?php
// api/login.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'conexion.php';
$input = json_decode(file_get_contents('php://input'), true);

$email = $input['email'] ?? $_POST['email'] ?? '';
$password = $input['password'] ?? $_POST['password'] ?? '';

if(empty($email) || empty($password)) {
    echo json_encode(["success" => false, "mensaje" => "Faltan datos"]);
    exit;
}

// Función auxiliar para crear el JWT en PHP Puro
function base64UrlEncode($text) {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($text));
}

try {
    $db = new Conexion();
    $conn = $db->conectar();

    $sql = "SELECT u.id_usuario, u.nombre, u.password, r.nombre_rol as rol 
            FROM usuarios u 
            INNER JOIN roles r ON u.id_rol_fk = r.id_rol 
            WHERE u.email = :email AND u.estado_registro = '1' LIMIT 1";
            
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user['password']) || $password == $user['password']) {
            unset($user['password']); // Nunca enviar la contraseña
            
            // ==========================================
            // 🔐 CREACIÓN DEL JWT (JSON Web Token)
            // ==========================================
            $secret_key = 'OpticcomSecret2026_KeySuperSegura!'; // ¡No pierdas esta clave!
            
            // 1. Header
            $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
            $base64UrlHeader = base64UrlEncode($header);

            // 2. Payload (Los datos que viajan en el token)
            $payload = json_encode([
                'id_tecnico' => $user['id_usuario'],
                'nombre' => $user['nombre'],
                'rol' => $user['rol'],
                'exp' => time() + (86400 * 30) // Expira en 30 días
            ]);
            $base64UrlPayload = base64UrlEncode($payload);

            // 3. Signature (La firma digital para que no lo hackeen)
            $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret_key, true);
            $base64UrlSignature = base64UrlEncode($signature);

            // 4. Token Final
            $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
            // ==========================================
            
            echo json_encode([
                "success" => true,
                "mensaje" => "Bienvenido " . $user['nombre'],
                "token"   => $jwt, // 👈 ¡Aquí mandamos el JWT a Flutter!
                "usuario" => $user
            ]);
        } else {
            echo json_encode(["success" => false, "mensaje" => "Contraseña incorrecta"]);
        }
    } else {
        echo json_encode(["success" => false, "mensaje" => "Usuario no encontrado o inactivo"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "mensaje" => "Error Servidor: " . $e->getMessage()]);
}
?>