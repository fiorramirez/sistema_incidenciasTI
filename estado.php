<?php
// Cambiar el estado de una incidencia. Solo administradores, solo por POST.
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

requiere_login();

// Si alguien entra escribiendo la URL (GET), no hay nada que procesar
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
csrf_verificar();

$id     = (int)($_POST['id'] ?? 0);
$estado = trim($_POST['estado'] ?? '');
// 'volver' conserva los filtros activos al regresar al listado
$volver = 'index.php' . (!empty($_POST['volver']) ? '?' . $_POST['volver'] : '');

// El estado debe ser uno de los permitidos
if ($id <= 0 || !in_array($estado, ESTADOS, true)) {
    flash_set('danger', 'Datos no válidos para cambiar el estado.');
    header('Location: ' . $volver);
    exit;
}

$stmt = $conn->prepare('UPDATE incidencias SET estado = ? WHERE id = ?');
$stmt->bind_param('si', $estado, $id);
$stmt->execute();
$stmt->close();

flash_set('success', 'La incidencia #' . $id . ' ahora está "' . $estado . '".');
header('Location: ' . $volver);
exit;
