<?php
require_once '../config/db.php'; 
require_once '../config/auth.php'; 

// 🚨 Proteger este endpoint de acceso público
protect_api();

header('Content-Type: application/json');

try {
    $sql = "SELECT id, nombre, email, telefono, fecha, hora, personas, fecha_creacion 
            FROM reservas 
            ORDER BY fecha DESC, hora DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($reservas);

} catch (PDOException $e) {
    http_response_code(500); 
    echo json_encode(["error" => "Error al obtener las reservas: " . $e->getMessage()]);
}
?>