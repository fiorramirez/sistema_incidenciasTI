<?php
// Funciones y constantes compartidas por todo el sistema.
if (session_status() === PHP_SESSION_NONE) {
    session_start();   // las sesiones guardan el login y los mensajes
}

// Listas permitidas: se usan en formularios, filtros y validación
const LABORATORIOS = ['Laboratorio 1', 'Laboratorio 2', 'Laboratorio 3', 'Laboratorio 4', 'Laboratorio 5'];
const PRIORIDADES  = ['Baja', 'Media', 'Alta'];
const ESTADOS      = ['Pendiente', 'En proceso', 'Resuelta'];

// Escapa texto antes de mostrarlo en HTML (evita XSS)
function e($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

//  Mensajes flash se muestran una sola vez 

// Guarda un mensaje para la siguiente página (tipo: success, danger, warning, info)
function flash_set(string $tipo, string $mensaje): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

// Devuelve el mensaje pendiente y lo borra para que no se repita
function flash_get(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

//  Token CSRF: evita que otro sitio envíe formularios por el usuario 

// Crea el token una vez por sesión y lo reutiliza
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

// Campo oculto que se pega dentro de cada formulario POST
function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

// Compara el token enviado con el de la sesión; si no coincide, corta la petición
function csrf_verificar(): void
{
    $enviado = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $enviado)) {
        http_response_code(403);
        die('Solicitud no válida (token CSRF incorrecto).');
    }
}

// Estilos de las etiquetas (las clases viven en estilos.css) 

function badge_estado(string $estado): string
{
    return match ($estado) {
        'Pendiente'  => 'estado-pendiente',
        'En proceso' => 'estado-en-proceso',
        'Resuelta'   => 'estado-resuelta',
        default      => 'estado-pendiente',
    };
}

function badge_prioridad(string $prioridad): string
{
    return match ($prioridad) {
        'Alta'  => 'prioridad-alta',
        'Media' => 'prioridad-media',
        'Baja'  => 'prioridad-baja',
        default => 'prioridad-baja',
    };
}

// Validación

// Limpia y valida el formulario de incidencia
function validar_incidencia(array $post): array
{
    // trim() quita espacios sobrantes al inicio y al final
    $datos = [
        'reportante'  => trim($post['reportante'] ?? ''),
        'laboratorio' => trim($post['laboratorio'] ?? ''),
        'equipo'      => trim($post['equipo'] ?? ''),
        'descripcion' => trim($post['descripcion'] ?? ''),
        'prioridad'   => trim($post['prioridad'] ?? ''),
    ];
    $errores = [];

    // Reportante: obligatorio y máximo 100 caracteres
    if ($datos['reportante'] === '') {
        $errores[] = 'El nombre de quien reporta es obligatorio.';
    } elseif (mb_strlen($datos['reportante']) > 100) {
        $errores[] = 'El nombre no puede superar 100 caracteres.';
    }

    // Laboratorio: obligatorio y debe estar en la lista permitida
    if ($datos['laboratorio'] === '') {
        $errores[] = 'Debes seleccionar un laboratorio.';
    } elseif (!in_array($datos['laboratorio'], LABORATORIOS, true)) {
        $errores[] = 'El laboratorio seleccionado no es válido.';
    }

    // Equipo: obligatorio y máximo 100 caracteres
    if ($datos['equipo'] === '') {
        $errores[] = 'El equipo afectado es obligatorio.';
    } elseif (mb_strlen($datos['equipo']) > 100) {
        $errores[] = 'El equipo no puede superar 100 caracteres.';
    }

    // Descripción: obligatoria
    if ($datos['descripcion'] === '') {
        $errores[] = 'La descripción del problema es obligatoria.';
    }

    // Prioridad: obligatoria y debe estar en la lista permitida
    if ($datos['prioridad'] === '') {
        $errores[] = 'Debes seleccionar una prioridad.';
    } elseif (!in_array($datos['prioridad'], PRIORIDADES, true)) {
        $errores[] = 'La prioridad seleccionada no es válida.';
    }

    return [$datos, $errores];
}
