<?php
require_once '../config/db.php';
require_once '../config/auth.php';

// Proteger este endpoint - solo admin
protect_api();

header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'));

if (empty($data->semana_inicio) || !isset($data->plato_ids) || !is_array($data->plato_ids)) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos: semana_inicio y plato_ids son obligatorios.']);
    exit;
}

$semana_inicio = $data->semana_inicio;

try {
    // Calcular semana_fin (domingo)
    $fechaObj = new DateTime($semana_inicio);
    $domingo = clone $fechaObj;
    $domingo->modify('+6 days');
    $semana_fin = $domingo->format('Y-m-d');
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    // 1. Eliminar asignaciones existentes para esta semana
    $sqlDelete = "DELETE FROM menu_semanal WHERE semana_inicio = ? AND semana_fin = ?";
    $stmtDelete = $pdo->prepare($sqlDelete);
    $stmtDelete->execute([$semana_inicio, $semana_fin]);
    
    // 2. Insertar nuevas asignaciones
    if (count($data->plato_ids) > 0) {
        $sqlInsert = "INSERT INTO menu_semanal (plato_id, semana_inicio, semana_fin) VALUES (?, ?, ?)";
        $stmtInsert = $pdo->prepare($sqlInsert);
        
        foreach ($data->plato_ids as $plato_id) {
            $stmtInsert->execute([$plato_id, $semana_inicio, $semana_fin]);
        }
    }
    
    // Confirmar transacción
    $pdo->commit();
    
    echo json_encode([
        'success' => 'Menú semanal actualizado correctamente.',
        'semana_inicio' => $semana_inicio,
        'semana_fin' => $semana_fin,
        'platos_asignados' => count($data->plato_ids)
    ]);

} catch (Exception $e) {
    // Revertir en caso de error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    error_log('Error en asignar_menu_semanal: ' . $e->getMessage());
    echo json_encode(['error' => 'Error al asignar menú semanal: ' . $e->getMessage()]);
}
?>
