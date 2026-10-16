<?php
// Encabezado común: carga estilos, muestra el menú y los mensajes flash.
require_once __DIR__ . '/auth.php';
$titulo = $titulo ?? 'Gestión de Incidencias';   // cada página puede definir su título
$flash  = flash_get();                           // mensaje pendiente (si existe)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?> | Incidencias TI</title>
    <!-- Tipografías de Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700&family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap, y después nuestros estilos para poder sobrescribirlo -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/estilos.css" rel="stylesheet">
</head>
<body>
<!-- Menú superior -->
<nav class="navbar navbar-expand-md navbar-dark app-navbar">
    <div class="container">
        <a class="navbar-brand" href="index.php"><span class="brand-mark" aria-hidden="true"></span>Incidencias TI</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Incidencias</a></li>
                <li class="nav-item"><a class="nav-link" href="crear.php">Reportar incidencia</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <?php if (esta_autenticado()): ?>
                    <!-- Administrador con sesión -->
                    <span class="text-white-50 small">Admin: <?= e($_SESSION['usuario_nombre']) ?></span>
                    <a class="btn btn-outline-light btn-sm" href="logout.php">Cerrar sesión</a>
                <?php else: ?>
                    <a class="btn btn-light btn-sm" href="login.php">Acceso administrador</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main class="container py-4">
<?php if ($flash): ?>
    <!-- Mensaje de éxito o error de la acción anterior -->
    <div class="alert alert-<?= e($flash['tipo']) ?> alert-dismissible fade show" role="alert">
        <?= e($flash['mensaje']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
