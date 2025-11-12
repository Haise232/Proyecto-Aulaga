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

// Validación de datos: asumimos que el nuevo plato debe estar activo (activo = 1)
if (empty($data->nombre) || empty($data->tipo) || empty($data->descripcion) || empty($data->imagen)) {
    http_response_code(400); 
    echo json_encode(["error" => "Faltan datos obligatorios."]);
    exit;
}

$nombre = trim($data->nombre);
$tipo = trim($data->tipo);
$descripcion = trim($data->descripcion);
$imagen = trim($data->imagen);

try {
    // 🚨 MODIFICACIÓN: Agregamos 'activo' a la consulta e insertamos el valor '1'
    $sql = "INSERT INTO platos (tipo, nombre, descripcion, imagen, activo) VALUES (?, ?, ?, ?, 1)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$tipo, $nombre, $descripcion, $imagen]);

    http_response_code(201);
    // Devolvemos los datos que JavaScript necesita para actualizar la lista
    echo json_encode([
        "success" => "Plato '{$nombre}' añadido correctamente.", 
        "id" => $pdo->lastInsertId(),
        "tipo" => $tipo,
        "nombre" => $nombre,
        "descripcion" => $descripcion,
        "imagen" => $imagen,
        "activo" => 1 // Importante para que JS lo sepa
    ]);

} catch (PDOException $e) {
    http_response_code(500); 
    error_log("Error de BD en crear_plato: " . $e->getMessage());
    echo json_encode(["error" => "Error al crear el plato: " . $e->getMessage()]);
}
?>