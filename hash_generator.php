<?php
// Contraseña a hashear: 'admin123'
$password = 'admin123';

// Generar el hash seguro
$hash = password_hash($password, PASSWORD_DEFAULT);

echo $hash; 
?>