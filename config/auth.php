<?php
/**
 * Configuración de Autenticación - EJEMPLO
 * 
 * Este archivo contiene el hash de contraseña del administrador.
 * 
 * PASOS PARA GENERAR TU PROPIA CONTRASEÑA:
 * 1. Abre hash_generator.php en tu navegador
 * 2. Copia el hash generado
 * 3. Reemplaza el valor de ADMIN_PASSWORD_HASH con tu hash
 * 
 * IMPORTANTE: Este archivo NO debe tener la contraseña en texto plano
 * y NO debe subirse al repositorio si contiene credenciales reales.
 * 
 * @package Restaurante-Aulaga
 * @author  Joaquín
 */

// 1. Iniciar la sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Parámetros de Autenticación
define('ADMIN_USER', 'admin');

// Hash de la contraseña por defecto "admin123"
// IMPORTANTE: Cambia esto en producción usando hash_generator.php
define('ADMIN_PASSWORD_HASH', '$2y$10$XU4lSn38mKYZsGC7dmxjyuelB2qBmgA66S2aUMtKLIh2lOZHY.adu');

/**
 * Función para verificar si el usuario está logeado
 * @return bool True si el usuario está autenticado, false en caso contrario
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Función para proteger las APIs: Si no está logeado, devuelve un error 401.
 * Usar esta función al inicio de cualquier endpoint que requiera autenticación.
 * 
 * @return void Termina la ejecución si no está autenticado
 */
function protect_api() {
    if (!is_logged_in()) {
        http_response_code(401); 
        echo json_encode(["success" => false, "error" => "Acceso denegado. Se requiere autenticación."]);
        exit;
    }
}
?>