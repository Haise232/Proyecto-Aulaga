<?php
require_once '../config/db.php';
require_once '../config/auth.php';

// Proteger este endpoint
protect_api();

header('Content-Type: application/json');

// Obtener la fecha solicitada o usar la fecha actual
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

try {
    // Calcular el lunes y domingo de la semana
    $fechaObj = new DateTime($fecha);
    $diaSemana = $fechaObj->format('N'); // 1 (lunes) a 7 (domingo)
    
    // Calcular el lunes de esa semana
    $diasHastaLunes = $diaSemana - 1;
    $lunes = clone $fechaObj;
    $lunes->modify("-{$diasHastaLunes} days");
    
    // Calcular el domingo
    $domingo = clone $lunes;
    $domingo->modify('+6 days');
    
    $semana_inicio = $lunes->format('Y-m-d');
    $semana_fin = $domingo->format('Y-m-d');
    
    // Obtener platos asignados a esta semana
    $sql = "SELECT p.id, p.tipo, p.nombre, p.descripcion, p.imagen, p.alergenos
            FROM platos p
            INNER JOIN menu_semanal ms ON p.id = ms.plato_id
            WHERE ms.semana_inicio = ? AND ms.semana_fin = ?
            AND p.activo = 1
            ORDER BY FIELD(p.tipo, 'primero', 'segundo', 'postre'), p.id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$semana_inicio, $semana_fin]);
    $platos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'semana_inicio' => $semana_inicio,
        'semana_fin' => $semana_fin,
        'platos' => $platos
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener menú semanal: ' . $e->getMessage()]);
}
?>
