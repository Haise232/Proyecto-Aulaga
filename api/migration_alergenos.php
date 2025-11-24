<?php
require_once '../config/db.php';

try {
    // Verificar si la columna ya existe
    $checkSql = "SHOW COLUMNS FROM platos LIKE 'alergenos'";
    $stmt = $pdo->query($checkSql);
    
    if ($stmt->rowCount() == 0) {
        // Si no existe, la creamos
        $sql = "ALTER TABLE platos ADD COLUMN alergenos TEXT DEFAULT NULL AFTER imagen";
        $pdo->exec($sql);
        echo "✅ Columna 'alergenos' añadida correctamente a la tabla 'platos'.\n";
    } else {
        echo "ℹ️ La columna 'alergenos' ya existe.\n";
    }

} catch (PDOException $e) {
    echo "❌ Error al modificar la base de datos: " . $e->getMessage() . "\n";
}
?>
