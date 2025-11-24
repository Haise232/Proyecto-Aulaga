<?php
// api/menu.php
// ESTE ARCHIVO ES PARA EL USUARIO PÚBLICO Y MUESTRA EL MENÚ DE LA SEMANA ACTUAL

require_once '../config/db.php'; 
header('Content-Type: application/json');

try {
    // Calcular el lunes y domingo de la semana actual
    $hoy = new DateTime();
    $diaSemana = $hoy->format('N'); // 1 (lunes) a 7 (domingo)
    
    // Calcular el lunes de esta semana
    $diasHastaLunes = $diaSemana - 1;
    $lunes = clone $hoy;
    $lunes->modify("-{$diasHastaLunes} days");
    
    // Calcular el domingo
    $domingo = clone $lunes;
    $domingo->modify('+6 days');
    
    $semana_inicio = $lunes->format('Y-m-d');
    $semana_fin = $domingo->format('Y-m-d');
    
    // Obtener platos asignados a esta semana
    $stmt = $pdo->prepare("SELECT p.id, p.tipo, p.nombre, p.descripcion, p.imagen, p.alergenos
                           FROM platos p
                           INNER JOIN menu_semanal ms ON p.id = ms.plato_id
                           WHERE ms.semana_inicio = ? AND ms.semana_fin = ?
                           AND p.activo = 1
                           ORDER BY FIELD(p.tipo, 'primero', 'segundo', 'postre'), p.id");
    $stmt->execute([$semana_inicio, $semana_fin]);
    $platos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($platos);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al obtener el menú: " . $e->getMessage()]);
}
?>