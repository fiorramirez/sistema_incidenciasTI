<?php
// Registrar una incidencia nueva (CREATE). Es pública: no pide login.
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$titulo = 'Reportar incidencia';
$errores = [];
$datos = ['reportante' => '', 'laboratorio' => '', 'equipo' => '', 'descripcion' => '', 'prioridad' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verificar();
    [$datos, $errores] = validar_incidencia($_POST);

    // Solo se guarda si la validación no encontró errores
    if (!$errores) {
        // Consulta preparada: los datos nunca se pegan al SQL (evita inyección)
        $stmt = $conn->prepare(
            'INSERT INTO incidencias (reportante, laboratorio, equipo, descripcion, prioridad)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('sssss',
            $datos['reportante'], $datos['laboratorio'], $datos['equipo'],
            $datos['descripcion'], $datos['prioridad']
        );
        $stmt->execute();
        $stmt->close();

        // Avisa y vuelve al listado (estado y fecha se asignan solos en la BD)
        flash_set('success', 'La incidencia fue registrada correctamente.');
        header('Location: index.php');
        exit;
    }
}

// Datos para el formulario compartido
$accion = 'crear.php';
$textoBoton = 'Registrar incidencia';
$id = null;

require __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <h1 class="h3 mb-3">Reportar una incidencia</h1>
        <div class="card card-body">
            <?php require __DIR__ . '/includes/form_incidencia.php'; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
