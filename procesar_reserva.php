<?php
// Incluir la conexión a la base de datos
require_once 'config/db.php';

// 1. Verificar si la solicitud es de tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Recoger y sanear (limpiar) los datos del formulario
    $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $telefono = htmlspecialchars(trim($_POST['telefono'] ?? ''));
    $fecha = htmlspecialchars(trim($_POST['fecha'] ?? ''));
    $hora = htmlspecialchars(trim($_POST['hora'] ?? ''));
    $personas = htmlspecialchars(trim($_POST['personas'] ?? ''));

    // 3. Validación (comprobación simple de campos obligatorios)
    if (empty($nombre) || empty($email) || empty($fecha) || empty($hora) || empty($personas)) {
        // Redirigir de vuelta con un mensaje de error
        header("Location: reservas.php?status=error&message=Por favor, complete todos los campos obligatorios.");
        exit;
    }

    try {
        // 4. Insertar la reserva en la base de datos
        $sql = "INSERT INTO reservas (nombre, email, telefono, fecha, hora, personas) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $email, $telefono, $fecha, $hora, $personas]);

        // 5. Redirigir con mensaje de éxito
        header("Location: reservas.php?status=success&message=¡Reserva realizada con éxito! Nos pondremos en contacto pronto.");
        exit;

    } catch (PDOException $e) {
        // 6. Error de base de datos
        error_log("Error al guardar reserva: " . $e->getMessage());
        header("Location: reservas.php?status=error&message=Error al procesar la reserva. Inténtelo de nuevo.");
        exit;
    }

} else {
    // Si alguien intenta acceder a este archivo directamente sin enviar el formulario
    header("Location: reservas.php?status=error&message=Acceso no permitido.");
    exit;
}
?>