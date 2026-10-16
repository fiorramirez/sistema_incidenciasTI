<?php
// Conexión a MySQL con mysqli. Ajusta los datos si tu XAMPP es distinto.
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';              // XAMPP trae root sin contraseña
$DB_NAME = 'incidencias_db';

// Los errores de MySQL lanzan excepciones, para poder capturarlos abajo
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    $conn->set_charset('utf8mb4');   // acentos y ñ se guardan bien
} catch (mysqli_sql_exception $e) {
    // Mensaje en lugar del error técnico de PHP
    http_response_code(500);
    die('<h3>No se pudo conectar a la base de datos.</h3>'
      . '<p>Verifica que MySQL esté encendido en XAMPP y que hayas importado <code>basedatosincidencias.sql</code>.</p>');
}
