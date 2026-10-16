<?php
// Control de acceso: decide si hay un administrador con sesión iniciada.
require_once __DIR__ . '/funciones.php';

// true si el usuario ya inició sesión (se guarda su id al hacer login)
function esta_autenticado(): bool
{
    return !empty($_SESSION['usuario_id']);
}

// Protege una página: sin sesión, manda al login y detiene el script
function requiere_login(): void
{
    if (!esta_autenticado()) {
        flash_set('warning', 'Debes iniciar sesión para realizar esta acción.');
        header('Location: login.php');
        exit;
    }
}
