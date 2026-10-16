<?php
// Listado de incidencias (READ) con filtros y resumen por estado.
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$titulo = 'Incidencias';

// ---- Filtros: llegan por la URL (GET) y todos son opcionales ----
$f_lab    = trim($_GET['laboratorio'] ?? '');
$f_estado = trim($_GET['estado'] ?? '');
$f_prio   = trim($_GET['prioridad'] ?? '');
$f_texto  = trim($_GET['q'] ?? '');

// Se arma el WHERE solo con los filtros usados.
// $params guarda los valores y $types sus tipos ('s' = texto) para bind_param.
$where  = [];
$params = [];
$types  = '';

if ($f_lab !== '') {
    $where[] = 'laboratorio = ?';
    $params[] = $f_lab;
    $types .= 's';
}
if ($f_estado !== '') {
    $where[] = 'estado = ?';
    $params[] = $f_estado;
    $types .= 's';
}
if ($f_prio !== '') {
    $where[] = 'prioridad = ?';
    $params[] = $f_prio;
    $types .= 's';
}
// La búsqueda de texto mira tres columnas a la vez (LIKE con comodines %)
if ($f_texto !== '') {
    $where[] = '(reportante LIKE ? OR equipo LIKE ? OR descripcion LIKE ?)';
    $like = '%' . $f_texto . '%';
    array_push($params, $like, $like, $like);
    $types .= 'sss';
}

$sql = 'SELECT id, reportante, laboratorio, equipo, descripcion, prioridad, estado, fecha_registro
        FROM incidencias';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY fecha_registro DESC, id DESC';   // las más recientes primero

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);   // "..." reparte el arreglo como argumentos
}
$stmt->execute();
$resultado = $stmt->get_result();
$incidencias = $resultado->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---- Resumen por estado: cuenta sobre toda la tabla, sin importar los filtros ----
$resumen = ['Pendiente' => 0, 'En proceso' => 0, 'Resuelta' => 0];
$rs = $conn->query('SELECT estado, COUNT(*) AS total FROM incidencias GROUP BY estado');
while ($fila = $rs->fetch_assoc()) {
    $resumen[$fila['estado']] = (int)$fila['total'];
}

$admin = esta_autenticado();   // decide si se muestran las acciones de administrador
$hayFiltros = ($f_lab !== '' || $f_estado !== '' || $f_prio !== '' || $f_texto !== '');
$qs = $_SERVER['QUERY_STRING'] ?? '';   // filtros actuales, para conservarlos al cambiar estado

require __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <div>
        <h1>Incidencias de los laboratorios</h1>
        <p>Consulta qué está pendiente, qué se está atendiendo y qué ya se resolvió.</p>
    </div>
    <a href="crear.php" class="btn btn-primary">Reportar incidencia</a>
</div>

<!-- Resumen: cada celda filtra la tabla por ese estado -->
<?php $totalGeneral = array_sum($resumen);   // total para dibujar la barra proporcional ?>
<section class="status-strip" aria-label="Resumen por estado">
    <div class="status-cells">
        <a class="status-cell cell-pendiente" href="index.php?estado=Pendiente">
            <strong><?= $resumen['Pendiente'] ?></strong><span>Pendientes</span>
        </a>
        <a class="status-cell cell-proceso" href="index.php?estado=En+proceso">
            <strong><?= $resumen['En proceso'] ?></strong><span>En proceso</span>
        </a>
        <a class="status-cell cell-resuelta" href="index.php?estado=Resuelta">
            <strong><?= $resumen['Resuelta'] ?></strong><span>Resueltas</span>
        </a>
    </div>
    <?php if ($totalGeneral > 0): ?>
        <!-- Barra: el ancho de cada tramo (flex) es proporcional a su cantidad -->
        <div class="status-bar" role="img"
             aria-label="<?= $resumen['Pendiente'] ?> pendientes, <?= $resumen['En proceso'] ?> en proceso, <?= $resumen['Resuelta'] ?> resueltas">
            <?php if ($resumen['Pendiente'] > 0): ?><i class="seg-pendiente" style="flex: <?= $resumen['Pendiente'] ?>"></i><?php endif; ?>
            <?php if ($resumen['En proceso'] > 0): ?><i class="seg-proceso" style="flex: <?= $resumen['En proceso'] ?>"></i><?php endif; ?>
            <?php if ($resumen['Resuelta'] > 0): ?><i class="seg-resuelta" style="flex: <?= $resumen['Resuelta'] ?>"></i><?php endif; ?>
        </div>
    <?php else: ?>
        <div class="status-bar is-empty" aria-hidden="true"><i></i></div>
    <?php endif; ?>
</section>

<!-- Filtros: se envían por GET, así la búsqueda se puede copiar como enlace -->
<form method="get" class="card filter-card mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small mb-1" for="q">Buscar</label>
            <input type="text" class="form-control" id="q" name="q" placeholder="Nombre, equipo o descripción"
                   value="<?= e($f_texto) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1" for="f_lab">Laboratorio</label>
            <select class="form-select" id="f_lab" name="laboratorio">
                <option value="">Todos</option>
                <?php foreach (LABORATORIOS as $lab): ?>
                    <option value="<?= e($lab) ?>" <?= $f_lab === $lab ? 'selected' : '' ?>><?= e($lab) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small mb-1" for="f_estado">Estado</label>
            <select class="form-select" id="f_estado" name="estado">
                <option value="">Todos</option>
                <?php foreach (ESTADOS as $est): ?>
                    <option value="<?= e($est) ?>" <?= $f_estado === $est ? 'selected' : '' ?>><?= e($est) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small mb-1" for="f_prio">Prioridad</label>
            <select class="form-select" id="f_prio" name="prioridad">
                <option value="">Todas</option>
                <?php foreach (PRIORIDADES as $p): ?>
                    <option value="<?= e($p) ?>" <?= $f_prio === $p ? 'selected' : '' ?>><?= e($p) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark flex-fill">Filtrar</button>
            <?php if ($hayFiltros): ?>
                <a href="index.php" class="btn btn-outline-secondary" title="Limpiar filtros">&#10005;</a>
            <?php endif; ?>
        </div>
    </div>
</form>

<!-- Tabla de incidencias -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Fecha de registro</th>
                    <th>Reporta</th>
                    <th>Laboratorio</th>
                    <th>Equipo</th>
                    <th>Descripción</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <?php if ($admin): ?><th class="text-end">Acciones</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php if (!$incidencias): ?>
                <!-- Sin resultados: el mensaje cambia si hay filtros activos -->
                <tr>
                    <td colspan="<?= $admin ? 9 : 8 ?>" class="empty-state">
                        <?php if ($hayFiltros): ?>
                            <strong>Ninguna incidencia coincide con estos filtros</strong>
                            Prueba con otro laboratorio o estado, o <a href="index.php">quita los filtros</a>.
                        <?php else: ?>
                            <strong>Todavía no hay incidencias</strong>
                            Cuando algo falle en un laboratorio, <a href="crear.php">repórtalo aquí</a>.
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach ($incidencias as $inc): ?>
                <!-- Una fila por incidencia; la clase prio-* pinta la franja de prioridad -->
                <tr class="prio-<?= e(strtolower($inc['prioridad'])) ?>">
                    <td class="col-id"><?= (int)$inc['id'] ?></td>
                    <td class="col-fecha"><?= e(date('d/m/Y H:i', strtotime($inc['fecha_registro']))) ?></td>
                    <td><?= e($inc['reportante']) ?></td>
                    <td class="text-nowrap"><?= e($inc['laboratorio']) ?></td>
                    <td class="col-equipo"><?= e($inc['equipo']) ?></td>
                    <td class="desc-cell" title="<?= e($inc['descripcion']) ?>"><?= e($inc['descripcion']) ?></td>
                    <td><span class="pill <?= badge_prioridad($inc['prioridad']) ?>"><?= e($inc['prioridad']) ?></span></td>
                    <td>
                        <?php if ($admin): ?>
                            <!-- Admin: el selector envía el cambio de estado al elegir una opción -->
                            <form method="post" action="estado.php" class="d-flex gap-1">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int)$inc['id'] ?>">
                                <input type="hidden" name="volver" value="<?= e($qs) ?>">
                                <select name="estado" class="form-select form-select-sm estado-select" onchange="this.form.submit()"
                                        aria-label="Cambiar estado">
                                    <?php foreach (ESTADOS as $est): ?>
                                        <option value="<?= e($est) ?>" <?= $inc['estado'] === $est ? 'selected' : '' ?>><?= e($est) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <noscript><button class="btn btn-sm btn-outline-primary">OK</button></noscript>
                            </form>
                        <?php else: ?>
                            <!-- Visitante: solo puede ver el estado -->
                            <span class="pill <?= badge_estado($inc['estado']) ?>"><?= e($inc['estado']) ?></span>
                        <?php endif; ?>
                    </td>
                    <?php if ($admin): ?>
                        <!-- Acciones: eliminar usa POST y app.js pide confirmación -->
                        <td class="text-end text-nowrap">
                            <a href="editar.php?id=<?= (int)$inc['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                            <form method="post" action="eliminar.php" class="d-inline form-eliminar"
                                  data-descripcion="#<?= (int)$inc['id'] ?> - <?= e($inc['equipo']) ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= (int)$inc['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex flex-wrap justify-content-between gap-2 small text-muted">
        <span>
            <?= count($incidencias) ?> incidencia<?= count($incidencias) === 1 ? '' : 's' ?>
            <?= $hayFiltros ? 'con los filtros aplicados' : 'en total' ?>
        </span>
        <span class="table-legend">
            Prioridad:
            <span><i style="background: var(--prio-alta)"></i>Alta</span>
            <span><i style="background: var(--prio-media)"></i>Media</span>
            <span><i style="background: var(--prio-baja)"></i>Baja</span>
        </span>
    </div>
</div>

<?php if (!$admin): ?>
    <!-- Pista para visitantes: las acciones de edición requieren login -->
    <p class="text-muted small mt-3">
        Para editar, eliminar o cambiar el estado de una incidencia, <a href="login.php">inicia sesión como administrador</a>.
    </p>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
