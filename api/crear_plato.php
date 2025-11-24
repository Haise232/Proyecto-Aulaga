<?php
require_once '../config/db.php'; 
require_once '../config/auth.php'; 

// Proteger este endpoint de acceso público
protect_api();

header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

// Validación de datos
if (empty($data->nombre) || empty($data->tipo) || empty($data->descripcion) || empty($data->imagen)) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan datos obligatorios."]);
    exit;
}

$nombre = trim($data->nombre);
$tipo = trim($data->tipo);
$descripcion = trim($data->descripcion);
$imagen = trim($data->imagen);
// Por defecto, un plato nuevo se crea como activo (1)
$activo = 1; 
// Procesar alérgenos
$alergenos = isset($data->alergenos) ? json_encode($data->alergenos) : '[]';

try {
    $sql = "INSERT INTO platos (tipo, nombre, descripcion, imagen, activo, alergenos) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$tipo, $nombre, $descripcion, $imagen, $activo, $alergenos]);

    echo json_encode(["success" => "Plato creado correctamente.", "id" => $pdo->lastInsertId()]);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Error de BD en crear_plato: " . $e->getMessage());
    echo json_encode(["error" => "Error al crear el plato: " . $e->getMessage()]);
}
?>