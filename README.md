# Sistema de Gestión de Incidencias Tecnológicas
Parcial 1
Estudiante: Anyel Fiorelha Barrantes Ramirez
Curso: Programación Avanzada

Aplicación web desarrollada con **PHP, MySQL (mysqli), HTML, CSS, JavaScript y Bootstrap 5** para registrar y dar seguimiento a las incidencias tecnológicas de los laboratorios de computación de la universidad.

Evaluación práctica · Programación Avanzada · UISIL.

## Funcionalidades

- Registrar incidencias (quien reporta, laboratorio, equipo, descripción, prioridad).
- Listado de todas las incidencias en una tabla, con fecha de registro.
- Búsqueda y filtros por laboratorio, estado y prioridad (más búsqueda por texto).
- Sección administrativa con inicio de sesión (usuario y contraseña).
- Solo administradores autenticados pueden: **editar**, **cambiar el estado** (Pendiente / En proceso / Resuelta) y **eliminar** (con confirmación).
- Validación de campos obligatorios (en el navegador y en el servidor).

## Seguridad aplicada

- Consultas preparadas (`mysqli::prepare`) contra inyección SQL.
- Contraseñas guardadas con `password_hash()` y verificadas con `password_verify()`.
- Escape de salida con `htmlspecialchars()` contra XSS.
- Token CSRF en todos los formularios POST.
- Editar, cambiar estado y eliminar validan sesión en el servidor (no solo ocultan botones).

## Estructura del proyecto

```
incidencias/
├── assets/
│   ├── css/estilos.css
│   └── js/app.js
├── config/
│   └── db.php              # conexión mysqli
├── includes/
│   ├── auth.php            # control de acceso
│   ├── funciones.php       # utilidades, constantes y validaciones
│   ├── header.php
│   ├── footer.php
│   └── form_incidencia.php # formulario reutilizable
├── index.php               # listado + filtros
├── crear.php               # registrar incidencia
├── editar.php              # editar incidencia (admin)
├── estado.php              # cambiar estado (admin)
├── eliminar.php            # eliminar incidencia (admin)
├── login.php
├── logout.php
├── basedatosincidencias.sql # estructura de la base de datos
└── README.md
```

## Requisitos

- [XAMPP](https://www.apachefriends.org/) con **PHP 8.0 o superior**, Apache y MySQL/MariaDB.
- Conexión a Internet (Bootstrap se carga desde CDN).
- Visual Studio Code (opcional, recomendado: extensión *PHP Intelephense*).

## Instalación y ejecución (XAMPP + VS Code)

1. **Copiar el proyecto** a la carpeta `htdocs` de XAMPP:
   - Windows: `C:\xampp\htdocs\incidencias`
   - macOS: `/Applications/XAMPP/htdocs/incidencias`
   - Linux: `/opt/lampp/htdocs/incidencias`
2. **Abrir la carpeta en VS Code**: `Archivo > Abrir carpeta...` y elegir `incidencias`.
3. **Iniciar Apache y MySQL** desde el panel de control de XAMPP.
4. **Crear la base de datos**:
   - Entrar a <http://localhost/phpmyadmin>.
   - Pestaña **Importar** → seleccionar el archivo `basedatosincidencias.sql` → **Importar**.
   - Esto crea la base `incidencias_db`, las tablas y el usuario administrador.
5. **Revisar la conexión** en `config/db.php` (por defecto: host `localhost`, usuario `root`, sin contraseña).
6. **Abrir la aplicación**: <http://localhost/incidencias/>

## Credenciales de administrador

| Usuario | Contraseña |
|---------|------------|
| `admin` | `admin123` |

## Modelo de datos

**usuarios**: `id`, `usuario` (único), `password` (hash), `nombre`, `creado_en`.

**incidencias**: `id`, `reportante`, `laboratorio`, `equipo`, `descripcion`, `prioridad` (Baja/Media/Alta), `estado` (Pendiente/En proceso/Resuelta), `fecha_registro`, `fecha_actualizacion`.



y abrir <http://localhost:8000>.

