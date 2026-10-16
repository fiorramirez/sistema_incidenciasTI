<?php
// Eliminar una incidencia (DELETE). Solo administradores, solo por POST.
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

requiere_login();

// Borrar nunca debe ocurrir con un simple enlace (GET)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
csrf_verificar();

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    flash_set('danger', 'Incidencia no válida.');
    header('Location: index.php');
    exit;
}

$stmt = $conn->prepare('DELETE FROM incidencias WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$eliminadas = $stmt->affected_rows;   // 0 si el id ya no existía
$stmt->close();

if ($eliminadas > 0) {
    flash_set('success', 'La incidencia #' . $id . ' fue eliminada.');
} else {
    flash_set('warning', 'La incidencia no existe o ya fue eliminada.');
}
header('Location: index.php');
exit;
