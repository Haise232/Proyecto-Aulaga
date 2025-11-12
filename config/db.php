<?php

/* */

// 1. Parámetros de Conexión
define('DB_HOST', 'localhost');
define('DB_NAME', 'restaurante_aulaga'); 
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    // 2. Crear la Conexión PDO
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", 
        DB_USER, 
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

} catch (PDOException $e) {
    // 3. Manejo de Errores de Conexión
    error_log("Error de conexión a la BD: " . $e->getMessage());
    die("<h1>Error al conectar con la base de datos.</h1><p>Por favor, compruebe que la base de datos 'restaurante_aulaga' existe y que MySQL/MariaDB está activo.</p>");
}

// $pdo está ahora disponible para ser incluido en cualquier otro archivo.