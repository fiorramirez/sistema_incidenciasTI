<?php
// Formulario compartido por crear.php y editar.php.
// Recibe: $datos, $errores, $accion (destino), $textoBoton e $id (solo al editar).
?>
<?php if (!empty($errores)): ?>
    <!-- Errores de validación del servidor -->
    <div class="alert alert-danger">
        <strong>Corrige los siguientes errores:</strong>
        <ul class="mb-0">
            <?php foreach ($errores as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= e($accion) ?>" novalidate class="needs-validation">
    <?= csrf_field() ?>
    <?php if (!empty($id)): ?>
        <!-- Solo al editar: indica qué incidencia se actualiza -->
        <input type="hidden" name="id" value="<?= (int)$id ?>">
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-md-6">
            <label for="reportante" class="form-label">Nombre de quien reporta <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="reportante" name="reportante" maxlength="100"
                   value="<?= e($datos['reportante']) ?>" required>
        </div>

        <div class="col-md-6">
            <label for="laboratorio" class="form-label">Laboratorio <span class="text-danger">*</span></label>
            <select class="form-select" id="laboratorio" name="laboratorio" required>
                <option value="">-- Seleccione --</option>
                <?php foreach (LABORATORIOS as $lab): ?>
                    <option value="<?= e($lab) ?>" <?= $datos['laboratorio'] === $lab ? 'selected' : '' ?>><?= e($lab) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-6">
            <label for="equipo" class="form-label">Equipo afectado <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="equipo" name="equipo" maxlength="100"
                   placeholder="Ej: PC-05, proyector, teclado, router..."
                   value="<?= e($datos['equipo']) ?>" required>
        </div>

        <div class="col-md-6">
            <label for="prioridad" class="form-label">Prioridad <span class="text-danger">*</span></label>
            <select class="form-select" id="prioridad" name="prioridad" required>
                <option value="">-- Seleccione --</option>
                <?php foreach (PRIORIDADES as $p): ?>
                    <option value="<?= e($p) ?>" <?= $datos['prioridad'] === $p ? 'selected' : '' ?>><?= e($p) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12">
            <label for="descripcion" class="form-label">Descripción del problema <span class="text-danger">*</span></label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required><?= e($datos['descripcion']) ?></textarea>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary"><?= e($textoBoton) ?></button>
        <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
