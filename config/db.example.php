<?php
/**
 * Configuración de Conexión a Base de Datos - EJEMPLO
 * 
 * Este es un archivo de ejemplo. Copia este archivo como config.php
 * y actualiza los valores según tu configuración de servidor.
 * 
 * PASOS:
 * 1. Copia este archivo: cp config.example.php config.php
 * 2. Actualiza los valores de conexión
 * 3. El archivo config.php está en .gitignore y no se subirá al repositorio
 * 
 * @package Restaurante-Aulaga
 * @author  Joaquín
 */

// 1. Parámetros de Conexión
define('DB_HOST', 'localhost');           // Servidor de base de datos
define('DB_NAME', 'restaurante_aulaga');  // Nombre de la base de datos
define('DB_USER', 'root');                // Usuario de MySQL
define('DB_PASS', '');                    // Contraseña del usuario

// 2. Configuración de Puerto (opcional)
// define('DB_PORT', '3306');             // Puerto MySQL (por defecto 3306)

try {
    // 3. Crear la Conexión PDO
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
    // 4. Manejo de Errores de Conexión
    error_log("Error de conexión a la BD: " . $e->getMessage());
    die("<h1>Error al conectar con la base de datos.</h1><p>Por favor, compruebe que la base de datos 'restaurante_aulaga' existe y que MySQL/MariaDB está activo.</p>");
}

// $pdo está ahora disponible para ser incluido en cualquier otro archivo.
?>
