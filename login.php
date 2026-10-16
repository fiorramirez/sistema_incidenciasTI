<?php
// Inicio de sesión del administrador.
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

// Si ya hay sesión, no tiene sentido ver el login
if (esta_autenticado()) {
    header('Location: index.php');
    exit;
}

$titulo = 'Iniciar sesión';
$error = '';
$usuario = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verificar();
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($usuario === '' || $password === '') {
        $error = 'Debes ingresar usuario y contraseña.';
    } else {
        // Busca al usuario (consulta preparada)
        $stmt = $conn->prepare('SELECT id, nombre, password FROM usuarios WHERE usuario = ?');
        $stmt->bind_param('s', $usuario);
        $stmt->execute();
        $u = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // password_verify compara contra el hash guardado en la BD
        if ($u && password_verify($password, $u['password'])) {
            session_regenerate_id(true);   // nuevo id de sesión: evita robo de sesión
            $_SESSION['usuario_id'] = (int)$u['id'];
            $_SESSION['usuario_nombre'] = $u['nombre'];
            flash_set('success', 'Bienvenido, ' . $u['nombre'] . '.');
            header('Location: index.php');
            exit;
        }
        // Mismo mensaje si falla el usuario o la clave, para no dar pistas
        $error = 'Usuario o contraseña incorrectos.';
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card auth-card">
            <span class="brand-mark" aria-hidden="true"></span>
            <h1 class="h3 mb-1">Acceso administrador</h1>
            <p class="text-muted small">Solo el personal autorizado puede editar, eliminar o cambiar el estado de las incidencias.</p>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post" novalidate>
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="usuario" name="usuario"
                           value="<?= e($usuario) ?>" autofocus required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
