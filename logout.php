<?php
// Cerrar sesión del administrador.
require_once __DIR__ . '/includes/funciones.php';

// Vacía los datos y borra la cookie de sesión del navegador
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

// Nueva sesión solo para poder mostrar el aviso de despedida
session_start();
flash_set('info', 'Sesión cerrada correctamente.');
header('Location: index.php');
exit;
