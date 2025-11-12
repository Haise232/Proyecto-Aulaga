<?php
// Incluir el archivo de conexión a la BD
require_once '../config/db.php'; 

header('Content-Type: application/json');

try {
    // Consulta para seleccionar todos los platos activos (si no se especifica lo contrario)
    $stmt = $pdo->prepare("SELECT id, tipo, nombre, descripcion, imagen FROM platos WHERE activo = 1 ORDER BY tipo, id");
    $stmt->execute();
    $platos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver los platos en formato JSON
    echo json_encode($platos);

} catch (PDOException $e) {
    // Devolver un error JSON si falla la consulta
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Error al obtener el menú: " . $e->getMessage()]);
}
?>