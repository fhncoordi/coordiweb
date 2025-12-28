-- ============================================
-- COORDICANARIAS CMS - ESQUEMA DE BASE DE DATOS
-- ============================================
-- Base de datos: coordica_crc
-- Charset: utf8mb4 (soporte para emojis y caracteres especiales)
-- Motor: InnoDB (soporte para foreign keys y transacciones)
-- ============================================

-- Usar la base de datos
USE coordica_crc;

-- ============================================
-- TABLA: usuarios
-- Sistema de autenticación con roles
-- ============================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(100) NOT NULL,
    rol ENUM('admin', 'editor') DEFAULT 'editor',
    activo TINYINT(1) DEFAULT 1,
    ultimo_acceso DATETIME NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_rol (rol),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- CREAR USUARIO ADMINISTRADOR INICIAL
-- ============================================
-- IMPORTANTE: Después de ejecutar este schema, debes crear el usuario admin manualmente
-- desde el panel de phpMyAdmin o ejecutando este SQL con TU PROPIA contraseña:
--
-- INSERT INTO usuarios (username, email, password_hash, nombre_completo, rol, activo)
-- VALUES (
--     'admin',
--     'admin@coordicanarias.com',
--     '$2y$10$TU_PASSWORD_HASH_AQUI',  -- Generar con: password_hash('tu_password', PASSWORD_DEFAULT)
--     'Administrador',
--     'admin',
--     1
-- );
--
-- Para generar el hash de tu contraseña, ejecuta en PHP:
-- echo password_hash('tu_password_seguro', PASSWORD_DEFAULT);

-- ============================================
-- TABLA: areas
-- 6 áreas temáticas principales de la organización
-- ============================================
CREATE TABLE IF NOT EXISTS areas (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    imagen_banner VARCHAR(255),
    color_tema VARCHAR(7) DEFAULT '#243659',
    orden INT(11) DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_orden (orden),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar las 6 áreas principales
INSERT INTO areas (nombre, slug, descripcion, imagen_banner, orden, activo) VALUES
('Empleo con Apoyo', 'empleo', 'Programas de orientación e inserción laboral para personas con discapacidad', 'images/banners/empleo.jpg', 1, 1),
('Formación e Innovación', 'forminno', 'Formación continua y desarrollo de capacidades', 'images/banners/formacion_Innovacion.jpg', 2, 1),
('Atención Integral', 'aintegral', 'Servicios de apoyo y atención personalizada', 'images/banners/atencion_integral.jpg', 3, 1),
('Igualdad y Promoción de la Mujer', 'igualdadpm', 'Promoción de la igualdad de género y empoderamiento de mujeres con discapacidad', 'images/banners/igual_promo_mujer_discapacidad.jpg', 4, 1),
('Ocio y Tiempo Libre', 'ocio', 'Actividades recreativas y sociales inclusivas', 'images/banners/ocio.jpg', 5, 1),
('Participación y Cultura Accesible', 'participaca', 'Participación ciudadana y accesibilidad cultural', 'images/banners/partycult_accesible.jpg', 6, 1)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- ============================================
-- TABLA: proyectos
-- Proyectos destacados de la organización
-- ============================================
CREATE TABLE IF NOT EXISTS proyectos (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT,
    imagen VARCHAR(255),
    area_id INT(11) UNSIGNED,
    categorias VARCHAR(255) COMMENT 'Categorías separadas por coma para filtros',
    orden INT(11) DEFAULT 0,
    destacado TINYINT(1) DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (area_id) REFERENCES areas(id) ON DELETE SET NULL,
    INDEX idx_area (area_id),
    INDEX idx_orden (orden),
    INDEX idx_destacado (destacado),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: servicios
-- Servicios ofrecidos por cada área (3-6 por área)
-- ============================================
CREATE TABLE IF NOT EXISTS servicios (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    area_id INT(11) UNSIGNED NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT,
    icono VARCHAR(100) COMMENT 'Clase de Font Awesome o emoji',
    orden INT(11) DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (area_id) REFERENCES areas(id) ON DELETE CASCADE,
    INDEX idx_area (area_id),
    INDEX idx_orden (orden),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: beneficios
-- Beneficios de cada área (4-5 por área)
-- ============================================
CREATE TABLE IF NOT EXISTS beneficios (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    area_id INT(11) UNSIGNED NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT,
    icono VARCHAR(100) DEFAULT '✔',
    orden INT(11) DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (area_id) REFERENCES areas(id) ON DELETE CASCADE,
    INDEX idx_area (area_id),
    INDEX idx_orden (orden),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: testimonios
-- Casos de éxito y testimonios
-- ============================================
CREATE TABLE IF NOT EXISTS testimonios (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    profesion VARCHAR(100),
    texto TEXT NOT NULL,
    foto VARCHAR(255),
    rating TINYINT(1) DEFAULT 5 CHECK (rating >= 1 AND rating <= 5),
    orden INT(11) DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_orden (orden),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: configuracion
-- Configuración general del sitio (contacto, etc.)
-- ============================================
CREATE TABLE IF NOT EXISTS configuracion (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(50) NOT NULL UNIQUE,
    valor TEXT,
    descripcion VARCHAR(255),
    tipo ENUM('texto', 'email', 'telefono', 'textarea', 'url') DEFAULT 'texto',
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_clave (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar configuración inicial de contacto
INSERT INTO configuracion (clave, valor, descripcion, tipo) VALUES
('contacto_telefono', '922 21 59 09', 'Teléfono principal de contacto', 'telefono'),
('contacto_email', 'info@coordicanarias.com', 'Email principal de contacto', 'email'),
('contacto_direccion', 'C/ Zurbarán, 7, Los Andenes, La Laguna 38108, Santa Cruz de Tenerife', 'Dirección física de la oficina', 'textarea'),
('contacto_horario', 'Lunes a viernes de 8:00 a 15:00', 'Horario de atención al público', 'texto'),
('site_title', 'Coordicanarias', 'Título del sitio web', 'texto'),
('site_description', 'Coordinadora de Personas con Discapacidad Física de Canarias', 'Descripción del sitio web', 'textarea')
ON DUPLICATE KEY UPDATE clave = clave;

-- ============================================
-- TABLA: registro_actividad
-- Log de acciones administrativas para auditoría
-- ============================================
CREATE TABLE IF NOT EXISTS registro_actividad (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT(11) UNSIGNED,
    accion VARCHAR(100) NOT NULL COMMENT 'login, logout, crear, actualizar, eliminar',
    tabla_afectada VARCHAR(50),
    registro_id INT(11),
    detalles TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_fecha (fecha_hora),
    INDEX idx_accion (accion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- FIN DEL ESQUEMA
-- ============================================

-- Verificar que todas las tablas se crearon correctamente
SHOW TABLES;
