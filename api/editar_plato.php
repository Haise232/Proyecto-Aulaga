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
$activo = intval($data->activo);
// Procesar alérgenos
$alergenos = isset($data->alergenos) ? json_encode($data->alergenos) : '[]';

try {
    // 🚨 MODIFICACIÓN: Agregamos 'alergenos' a la consulta UPDATE
    $sql = "UPDATE platos SET tipo = ?, nombre = ?, descripcion = ?, imagen = ?, activo = ?, alergenos = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$tipo, $nombre, $descripcion, $imagen, $activo, $alergenos, $id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => "Plato actualizado correctamente."]);
    } else {
        // Si no se modificó nada (mismos datos) o ID no existe
        echo json_encode(["success" => "Plato actualizado (sin cambios detectados o ID no encontrado)."]);
    }

} catch (PDOException $e) {
    http_response_code(500); 
    error_log("Error de BD en editar_plato: " . $e->getMessage());
    echo json_encode(["error" => "Error al actualizar el plato: " . $e->getMessage()]);
}
?>