<?php
// api/admin_platos.php
// Este archivo es SÓLO para el ADMIN y trae TODOS los platos.

require_once '../config/db.php'; 
header('Content-Type: application/json');

try {
    // CAMBIO CLAVE: Ordenamos por la función FIELD para forzar el orden:
    // 1. primero
    // 2. segundo
    // 3. postre
    $stmt = $pdo->prepare("SELECT id, tipo, nombre, descripcion, imagen, activo, alergenos 
                          FROM platos 
                          ORDER BY FIELD(tipo, 'primero', 'segundo', 'postre'), id");
    $stmt->execute();
    $platos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($platos);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al obtener platos para admin: " . $e->getMessage()]);
}
?>