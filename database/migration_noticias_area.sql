-- ============================================================================
-- MIGRACIÓN: Agregar area_id a tabla noticias
-- Coordicanarias CMS
-- ============================================================================
-- Ejecutar este script en phpMyAdmin para agregar el campo area_id a noticias
-- y permitir que cada noticia pertenezca a un área específica
-- ============================================================================

USE coordica_crc;

-- Agregar columna area_id (si no existe)
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'noticias'
     AND COLUMN_NAME = 'area_id') = 0,
    'ALTER TABLE noticias ADD COLUMN area_id INT(11) UNSIGNED DEFAULT NULL COMMENT "Área a la que pertenece la noticia" AFTER id',
    'SELECT "Column area_id already exists" AS message'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar índice para area_id (si no existe)
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'noticias'
     AND INDEX_NAME = 'idx_area_id') = 0,
    'ALTER TABLE noticias ADD INDEX idx_area_id (area_id)',
    'SELECT "Index idx_area_id already exists" AS message'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar foreign key a areas (si no existe)
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'noticias'
     AND CONSTRAINT_NAME = 'fk_noticia_area') = 0,
    'ALTER TABLE noticias ADD CONSTRAINT fk_noticia_area FOREIGN KEY (area_id) REFERENCES areas(id) ON DELETE SET NULL',
    'SELECT "FK fk_noticia_area already exists" AS message'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- ============================================================================
-- VERIFICACIÓN
-- ============================================================================

-- Ver estructura actualizada de la tabla
DESCRIBE noticias;

-- Ver noticias existentes (si las hay)
SELECT
    id,
    titulo,
    area_id,
    fecha_publicacion,
    activo
FROM noticias
ORDER BY fecha_publicacion DESC
LIMIT 10;

-- ============================================================================
-- NOTAS IMPORTANTES
-- ============================================================================
--
-- 1. NOTICIAS POR ÁREA:
--    - Cada noticia ahora pertenece a un área específica
--    - area_id puede ser NULL para noticias generales de la organización
--
-- 2. PERMISOS POR ROL:
--    Admin:
--      ✓ Crear/editar noticias de cualquier área
--      ✓ Crear noticias generales (area_id = NULL)
--
--    Editor:
--      ✓ Crear/editar noticias de cualquier área
--      ✓ Crear noticias generales (area_id = NULL)
--
--    Coordinador:
--      ✓ Solo crear/editar noticias de SU área
--      ✗ No puede crear noticias generales
--      ✗ No puede ver/editar noticias de otras áreas
--
-- 3. SI SE ELIMINA UN ÁREA:
--    - Las noticias de esa área tendrán area_id = NULL (ON DELETE SET NULL)
--    - No se pierden las noticias, solo quedan "sin área"
--
-- ============================================================================
