<?php
require_once '../config/auth.php'; 

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "error" => "Método no permitido."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->password)) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Falta la contraseña."]);
    exit;
}

$input_password = $data->password;

// DEBUG: Mostrar información
error_log("=== DEBUG LOGIN ===");
error_log("Password ingresado: " . $input_password);
error_log("Hash esperado: " . ADMIN_PASSWORD_HASH);
error_log("Resultado verify: " . (password_verify($input_password, ADMIN_PASSWORD_HASH) ? 'TRUE' : 'FALSE'));

// 1. Verificar la contraseña con el hash de auth.php
if (password_verify($input_password, ADMIN_PASSWORD_HASH)) {
    
    // 2. Credenciales correctas: Crear la sesión
    $_SESSION['user_id'] = ADMIN_USER;
    
    // 3. Devolver éxito
    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Inicio de sesión exitoso."]);
    
} else {
    // 4. Credenciales incorrectas
    http_response_code(401);
    echo json_encode(["success" => false, "error" => "Contraseña incorrecta, en caso de tener problemas contacte al administrador.  "]);
}
?>