-- ============================================================================
-- MIGRACIÓN DE TESTIMONIOS
-- Coordicanarias CMS
-- ============================================================================
-- Este script crea la tabla de testimonios e inserta 2 testimonios iniciales
-- para la página principal (index.html)
--
-- Ejecutar en phpMyAdmin o cliente MySQL después de crear el schema.
-- ============================================================================

USE coordica_crc;

-- Deshabilitar verificación de claves foráneas temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- CREAR TABLA DE TESTIMONIOS
-- ============================================================================

CREATE TABLE IF NOT EXISTS `testimonios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `profesion` varchar(200) NOT NULL,
  `texto` text NOT NULL,
  `foto` varchar(500) DEFAULT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT 5 COMMENT 'Valoración de 1 a 5 estrellas',
  `orden` int(11) NOT NULL DEFAULT 0,
  `destacado` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = Se muestra en index.html',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_activo` (`activo`),
  KEY `idx_destacado` (`destacado`),
  KEY `idx_orden` (`orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- INSERTAR TESTIMONIOS INICIALES (2 testimonios)
-- ============================================================================

-- Limpiar testimonios existentes (solo si se quiere reiniciar)
-- TRUNCATE TABLE testimonios;

INSERT INTO testimonios (nombre, profesion, texto, foto, rating, orden, destacado, activo) VALUES
(
    'Carmen López Rodríguez',
    'Auxiliar Administrativa',
    'Gracias al programa de empleo de Coordicanarias encontré un trabajo que se adapta perfectamente a mis necesidades. El equipo me acompañó en todo el proceso, desde la orientación laboral hasta el seguimiento en mi puesto. Ahora tengo la independencia económica que siempre busqué.',
    NULL,
    5,
    1,
    1,
    1
),
(
    'Miguel Hernández Sánchez',
    'Técnico en Informática',
    'Los cursos de formación en nuevas tecnologías me abrieron puertas que creía cerradas. El enfoque personalizado y las herramientas accesibles que me proporcionaron me permitieron desarrollar habilidades que hoy aplico en mi trabajo diario. Coordicanarias cambió mi perspectiva profesional.',
    NULL,
    5,
    2,
    1,
    1
);

-- Habilitar verificación de claves foráneas
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- VERIFICACIÓN DE DATOS INSERTADOS
-- ============================================================================

-- Ver todos los testimonios insertados
SELECT
    id,
    nombre,
    profesion,
    LEFT(texto, 50) as testimonio_preview,
    rating,
    orden,
    destacado,
    activo
FROM testimonios
ORDER BY orden ASC;

-- Contar testimonios totales
SELECT COUNT(*) as total_testimonios FROM testimonios;

-- Contar testimonios destacados (los que se muestran en index.html)
SELECT COUNT(*) as testimonios_destacados FROM testimonios WHERE destacado = 1 AND activo = 1;

-- ============================================================================
-- RESULTADO ESPERADO:
-- - Tabla 'testimonios' creada con estructura completa
-- - 2 testimonios insertados
-- - Carmen López Rodríguez (mujer) - Auxiliar Administrativa - 5 estrellas
-- - Miguel Hernández Sánchez (hombre) - Técnico en Informática - 5 estrellas
-- - Ambos marcados como destacados (aparecerán en index.html)
-- - Ambos activos
-- - Rating de 5 estrellas cada uno
-- ============================================================================
