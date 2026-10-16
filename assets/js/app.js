// Pide confirmación antes de eliminar; si se cancela, el formulario no se envía
document.querySelectorAll('.form-eliminar').forEach(function (form) {
    form.addEventListener('submit', function (ev) {
        var detalle = form.dataset.descripcion || 'esta incidencia';
        var ok = window.confirm('¿Seguro que deseas eliminar la incidencia ' + detalle + '?\n\nEsta acción no se puede deshacer.');
        if (!ok) {
            ev.preventDefault();
        }
    });
});

// Validación en el navegador: marca en rojo los campos obligatorios vacíos
// (el servidor valida de nuevo, esto solo ahorra un viaje al usuario)
document.querySelectorAll('form.needs-validation').forEach(function (form) {
    form.addEventListener('submit', function (ev) {
        var invalido = false;
        form.querySelectorAll('[required]').forEach(function (campo) {
            if (campo.value.trim() === '') {
                campo.classList.add('is-invalid');
                invalido = true;
            } else {
                campo.classList.remove('is-invalid');
            }
        });
        if (invalido) {
            ev.preventDefault();   // detiene el envío hasta corregir
        }
    });
});
