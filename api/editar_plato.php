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

// 🚨 MODIFICACIÓN: Ahora 'activo' es un campo obligatorio
if (empty($data->id) || empty($data->nombre) || empty($data->tipo) || empty($data->descripcion) || empty($data->imagen) || !isset($data->activo)) {
    http_response_code(400); 
    echo json_encode(["error" => "Faltan datos obligatorios o el estado 'activo'."]);
    exit;
}

$id = (int)$data->id;
$nombre = trim($data->nombre);
$tipo = trim($data->tipo);
$descripcion = trim($data->descripcion);
$imagen = trim($data->imagen);
$activo = (int)$data->activo; // 0 o 1

try {
    // 🚨 MODIFICACIÓN: Agregamos 'activo = ?' a la consulta
    $sql = "UPDATE platos SET tipo = ?, nombre = ?, descripcion = ?, imagen = ?, activo = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$tipo, $nombre, $descripcion, $imagen, $activo, $id]);

    http_response_code(200);
    echo json_encode(["success" => "Plato '{$nombre}' actualizado correctamente.", "id" => $id]);

} catch (PDOException $e) {
    http_response_code(500);
    error_log("Error de BD en editar_plato: " . $e->getMessage());
    echo json_encode(["error" => "Error al actualizar el plato: " . $e->getMessage()]);
}
?>