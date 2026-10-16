<?php
// Editar una incidencia (UPDATE). Solo para administradores.
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

requiere_login();

$titulo = 'Editar incidencia';
$errores = [];

// El id llega por URL al abrir la página y por POST al guardar
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) {
    flash_set('danger', 'Incidencia no válida.');
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Guardar cambios
    csrf_verificar();
    [$datos, $errores] = validar_incidencia($_POST);

    if (!$errores) {
        $stmt = $conn->prepare(
            'UPDATE incidencias
                SET reportante = ?, laboratorio = ?, equipo = ?, descripcion = ?, prioridad = ?
              WHERE id = ?'
        );
        $stmt->bind_param('sssssi',
            $datos['reportante'], $datos['laboratorio'], $datos['equipo'],
            $datos['descripcion'], $datos['prioridad'], $id
        );
        $stmt->execute();
        $stmt->close();

        flash_set('success', 'La incidencia #' . $id . ' fue actualizada.');
        header('Location: index.php');
        exit;
    }
} else {
    // Primera carga: trae los datos actuales para rellenar el formulario
    $stmt = $conn->prepare(
        'SELECT reportante, laboratorio, equipo, descripcion, prioridad, estado, fecha_registro
           FROM incidencias WHERE id = ?'
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Si el id no existe, regresa al listado
    if (!$fila) {
        flash_set('danger', 'La incidencia no existe.');
        header('Location: index.php');
        exit;
    }
    $datos = $fila;
}

// Datos para el formulario compartido
$accion = 'editar.php';
$textoBoton = 'Guardar cambios';

require __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <h1 class="h3 mb-3">Editar incidencia #<?= (int)$id ?></h1>
        <div class="card card-body">
            <?php require __DIR__ . '/includes/form_incidencia.php'; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
