-- ============================================================
-- Base de datos del Sistema de Gestión de Incidencias Tecnológicas
-- Cómo usarlo: phpMyAdmin > Importar > elegir este archivo.
-- (o por consola: mysql -u root -p < basedatosincidencias.sql)
-- ============================================================

-- Crea la base de datos con soporte completo de acentos y ñ
CREATE DATABASE IF NOT EXISTS incidencias_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE incidencias_db;

-- ------------------------------------------------------------
-- usuarios: administradores que pueden editar y eliminar
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
  id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario   VARCHAR(50)  NOT NULL,
  password  VARCHAR(255) NOT NULL,          -- hash de password_hash(), nunca texto plano
  nombre    VARCHAR(100) NOT NULL,
  creado_en TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_usuarios_usuario (usuario)  -- no permite dos usuarios iguales
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- incidencias: reportes de fallas en los laboratorios
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS incidencias (
  id                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  reportante          VARCHAR(100) NOT NULL,   -- quién reporta
  laboratorio         VARCHAR(50)  NOT NULL,
  equipo              VARCHAR(100) NOT NULL,   -- equipo afectado
  descripcion         TEXT         NOT NULL,
  prioridad           ENUM('Baja','Media','Alta') NOT NULL DEFAULT 'Media',
  estado              ENUM('Pendiente','En proceso','Resuelta') NOT NULL DEFAULT 'Pendiente',
  fecha_registro      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,   -- se llena sola al crear
  fecha_actualizacion DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,   -- se actualiza sola al editar
  PRIMARY KEY (id),
  -- Índices: aceleran los filtros del listado
  KEY idx_incidencias_laboratorio (laboratorio),
  KEY idx_incidencias_estado (estado),
  KEY idx_incidencias_prioridad (prioridad)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Administrador por defecto: admin / admin123
-- (la contraseña ya va cifrada con bcrypt)
-- ------------------------------------------------------------
INSERT INTO usuarios (usuario, password, nombre) VALUES
('admin', '$2y$10$xEPUT5BjCbdQ./rIDF.giuo7ZZNCNX/YjqDyEE6ET/qBfGkRcbJC2', 'Administrador')
ON DUPLICATE KEY UPDATE usuario = usuario;   -- si ya existe, no hace nada

-- ------------------------------------------------------------
-- Datos de ejemplo para la demostración (se pueden borrar)
-- ------------------------------------------------------------
INSERT INTO incidencias (reportante, laboratorio, equipo, descripcion, prioridad, estado, fecha_registro) VALUES
('María Rodríguez', 'Laboratorio 1', 'PC-05', 'La computadora no enciende, no da señal de energía.', 'Alta', 'Pendiente', NOW() - INTERVAL 3 DAY),
('Carlos Jiménez', 'Laboratorio 2', 'Teclado PC-12', 'Varias teclas del teclado no responden.', 'Baja', 'En proceso', NOW() - INTERVAL 2 DAY),
('Ana Vargas', 'Laboratorio 3', 'Router principal', 'Se interrumpe la conexión a Internet cada pocos minutos.', 'Alta', 'En proceso', NOW() - INTERVAL 1 DAY),
('Luis Mora', 'Laboratorio 1', 'Proyector', 'El proyector muestra la imagen con tonos verdes.', 'Media', 'Resuelta', NOW() - INTERVAL 5 DAY);
