<?php
// Incluir la conexión a la base de datos y la autenticación
require_once '../config/db.php'; 
require_once '../config/auth.php'; 

// 🚨 Proteger este endpoint de acceso público
protect_api();

// Establecer cabeceras para una API JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido. Use POST."]);
    exit;
}

// Obtener los datos JSON enviados por JavaScript
$data = json_decode(file_get_contents("php://input"));

// 1. Validación de datos
if (!isset($data->id) || !is_numeric($data->id)) {
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(["error" => "ID de reserva inválido o no proporcionado."]);
    exit;
}

$reserva_id = $data->id;

try {
    // 2. Preparar la consulta SQL para eliminar (DELETE)
    $sql = "DELETE FROM reservas WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$reserva_id]);

    // 3. Verificar si se eliminó alguna fila
    if ($stmt->rowCount() > 0) {
        http_response_code(200); // Éxito
        echo json_encode(["success" => "Reserva ID {$reserva_id} eliminada correctamente."]);
    } else {
        http_response_code(404); // No encontrado
        echo json_encode(["error" => "No se encontró la reserva con ID " . $reserva_id]);
    }

} catch (PDOException $e) {
    // 4. Manejo de errores de la BD
    http_response_code(500);
    echo json_encode(["error" => "Error de la base de datos al eliminar la reserva: " . $e->getMessage()]);
}
?>