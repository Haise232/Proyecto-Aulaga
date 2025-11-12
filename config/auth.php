<?php
/**
 * Script para gestionar la sesión, autenticación y proteger APIs
 */

// 1. Iniciar la sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Parámetros de Autenticación
define('ADMIN_USER', 'admin');
define('ADMIN_PASSWORD_HASH', '$2y$10$XU4lSn38mKYZsGC7dmxjyuelB2qBmgA66S2aUMtKLIh2lOZHY.adu');

/**
 * Función para verificar si el usuario está logeado
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Función para proteger las APIs: Si no está logeado, devuelve un error 401.
 */
function protect_api() {
    if (!is_logged_in()) {
        http_response_code(401); 
        echo json_encode(["success" => false, "error" => "Acceso denegado. Se requiere autenticación."]);
        exit;
    }
}
?>