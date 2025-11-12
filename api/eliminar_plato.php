<?php
require_once '../config/db.php'; 
require_once '../config/auth.php'; 

// 🚨 Proteger este endpoint de acceso público
protect_api();

header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id) || !is_numeric($data->id)) {
    http_response_code(400);
    echo json_encode(["error" => "ID de plato inválido o no proporcionado."]);
    exit;
}

$plato_id = $data->id;

try {
    $sql = "DELETE FROM platos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$plato_id]);

    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode(["success" => "Plato eliminado correctamente.", "id" => $plato_id]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "No se encontró el plato con ID " . $plato_id]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error de la base de datos al eliminar: " . $e->getMessage()]);
}
?>