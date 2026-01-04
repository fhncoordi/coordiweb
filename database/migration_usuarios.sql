-- ============================================================================
-- MIGRACIÓN DE USUARIOS Y SISTEMA DE ROLES
-- Coordicanarias CMS
-- ============================================================================
-- Este script actualiza la tabla de usuarios existente para agregar:
-- - Columna area_id (para coordinadores de área)
-- - Rol 'coordinador' al ENUM de roles
-- - Foreign key a la tabla areas
--
-- Roles disponibles:
-- - admin: Acceso total a todo el sistema
-- - coordinador: Solo puede gestionar contenido de su área asignada
-- - editor: Puede ver y editar contenido de todas las áreas (sin gestión de usuarios)
--
-- Ejecutar en phpMyAdmin o cliente MySQL.
-- ============================================================================

USE coordica_crc;

-- Deshabilitar verificación de claves foráneas temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- ACTUALIZAR TABLA DE USUARIOS EXISTENTE
-- ============================================================================

-- 1. Agregar columna area_id (ignorar si ya existe)
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'usuarios'
     AND COLUMN_NAME = 'area_id') = 0,
    'ALTER TABLE usuarios ADD COLUMN area_id INT(11) UNSIGNED DEFAULT NULL COMMENT "Área asignada (solo para coordinadores)" AFTER rol',
    'SELECT "Column area_id already exists" AS message'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 2. Modificar el ENUM de rol para agregar 'coordinador'
ALTER TABLE usuarios
MODIFY COLUMN rol ENUM('admin', 'coordinador', 'editor') NOT NULL DEFAULT 'editor';

-- 3. Agregar índice para area_id (ignorar si ya existe)
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'usuarios'
     AND INDEX_NAME = 'idx_area_id') = 0,
    'ALTER TABLE usuarios ADD INDEX idx_area_id (area_id)',
    'SELECT "Index idx_area_id already exists" AS message'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 4. Agregar foreign key a areas (ignorar si ya existe)
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'usuarios'
     AND CONSTRAINT_NAME = 'fk_usuario_area') = 0,
    'ALTER TABLE usuarios ADD CONSTRAINT fk_usuario_area FOREIGN KEY (area_id) REFERENCES areas(id) ON DELETE SET NULL',
    'SELECT "FK fk_usuario_area already exists" AS message'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- ============================================================================
-- INSERTAR USUARIO ADMINISTRADOR INICIAL (SI NO EXISTE)
-- ============================================================================

-- Contraseña por defecto: Admin2025!
-- IMPORTANTE: Cambiar esta contraseña después del primer login
-- Hash generado con password_hash('Admin2025!', PASSWORD_DEFAULT)

INSERT INTO usuarios (username, email, password_hash, nombre_completo, rol, area_id, activo)
SELECT * FROM (SELECT
    'admin' as username,
    'admin@coordicanarias.com' as email,
    '$2y$12$0OBlUlgB.FTdp0ywn8qhAOwFO7y48iA8/I.Sna7M889IfhkTjzE9S' as password_hash,
    'Administrador del Sistema' as nombre_completo,
    'admin' as rol,
    NULL as area_id,
    1 as activo
) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM usuarios WHERE username = 'admin'
) LIMIT 1;

-- ============================================================================
-- CREAR USUARIOS COORDINADORES PARA LAS 6 ÁREAS
-- ============================================================================
-- Usuario: coordinador_<area>
-- Email: fhn@coordicanarias.com (mismo para todos)
-- Contraseñas: <nombre_area>_2025!

-- Coordinador de Empleo con Apoyo (area_id = 1)
-- Password: empleo_2025!
INSERT INTO usuarios (username, email, password_hash, nombre_completo, rol, area_id, activo)
SELECT * FROM (SELECT
    'coordinador_empleo' as username,
    'empleo@coordicanarias.com' as email,
    '$2y$12$DXQCCIHEkH8L/zWIzB6t/eZjdBaH97yk2LlBnd1jCF3x4tB4XufmS' as password_hash,
    'Coordinador de Empleo con Apoyo' as nombre_completo,
    'coordinador' as rol,
    1 as area_id,
    1 as activo
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE username = 'coordinador_empleo') LIMIT 1;

-- Coordinador de Formación e Innovación (area_id = 2)
-- Password: forminno_2025!
INSERT INTO usuarios (username, email, password_hash, nombre_completo, rol, area_id, activo)
SELECT * FROM (SELECT
    'coordinador_forminno' as username,
    'formacion@coordicanarias.com' as email,
    '$2y$12$fHnQiSCTbYxwtr4s8goINeBy.g3rDv2JKBcZThVbymOIwNCyCfo8m' as password_hash,
    'Coordinador de Formación e Innovación' as nombre_completo,
    'coordinador' as rol,
    2 as area_id,
    1 as activo
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE username = 'coordinador_forminno') LIMIT 1;

-- Coordinador de Atención Integral (area_id = 3)
-- Password: aintegral_2025!
INSERT INTO usuarios (username, email, password_hash, nombre_completo, rol, area_id, activo)
SELECT * FROM (SELECT
    'coordinador_aintegral' as username,
    'aintegral@coordicanarias.com' as email,
    '$2y$12$tMqbBdjR6QuEvUOaFDgwhOLBkynH5VErBVrwz0z.X3.5Slgb8HDvS' as password_hash,
    'Coordinador de Atención Integral' as nombre_completo,
    'coordinador' as rol,
    3 as area_id,
    1 as activo
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE username = 'coordinador_aintegral') LIMIT 1;

-- Coordinador de Igualdad y Promoción de la Mujer (area_id = 4)
-- Password: igualdadpm_2025!
INSERT INTO usuarios (username, email, password_hash, nombre_completo, rol, area_id, activo)
SELECT * FROM (SELECT
    'coordinador_igualdadpm' as username,
    'igualdad@coordicanarias.com' as email,
    '$2y$12$lS5BQdyvOF2M6urv9UtFFOwO24skcr6XBB7b6DtanwKOMflj40bU2' as password_hash,
    'Coordinador de Igualdad y Promoción de la Mujer' as nombre_completo,
    'coordinador' as rol,
    4 as area_id,
    1 as activo
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE username = 'coordinador_igualdadpm') LIMIT 1;

-- Coordinador de Ocio y Tiempo Libre (area_id = 5)
-- Password: ocio_2025!
INSERT INTO usuarios (username, email, password_hash, nombre_completo, rol, area_id, activo)
SELECT * FROM (SELECT
    'coordinador_ocio' as username,
    'ocio@coordicanarias.com' as email,
    '$2y$12$ZNwGL6viUr0xaJRcKVexOuDhq4jBLyLOjzsGSnu7r.UJjy/GS1/ta' as password_hash,
    'Coordinador de Ocio y Tiempo Libre' as nombre_completo,
    'coordinador' as rol,
    5 as area_id,
    1 as activo
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE username = 'coordinador_ocio') LIMIT 1;

-- Coordinador de Participación y Cultura Accesible (area_id = 6)
-- Password: participaca_2025!
INSERT INTO usuarios (username, email, password_hash, nombre_completo, rol, area_id, activo)
SELECT * FROM (SELECT
    'coordinador_participaca' as username,
    'participa@coordicanarias.com' as email,
    '$2y$12$3xd/0upz/A5B2TaBDJ/OROHSgOrMPRL1YOCyap1q2P.d6A3tUl.1q' as password_hash,
    'Coordinador de Participación y Cultura Accesible' as nombre_completo,
    'coordinador' as rol,
    6 as area_id,
    1 as activo
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE username = 'coordinador_participaca') LIMIT 1;

-- ============================================================================
-- CREAR USUARIO EDITOR GENERAL
-- ============================================================================
-- Usuario: editor
-- Email: editor@coordicanarias.com
-- Password: editor_2025!

INSERT INTO usuarios (username, email, password_hash, nombre_completo, rol, area_id, activo)
SELECT * FROM (SELECT
    'editor' as username,
    'editor@coordicanarias.com' as email,
    '$2y$12$kM6fj55TRBW7aVFqNp2WNO2bK74DBMNP71fZNBLWrEtXQ5vkQh76O' as password_hash,
    'Editor General' as nombre_completo,
    'editor' as rol,
    NULL as area_id,
    1 as activo
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE username = 'editor') LIMIT 1;

-- ============================================================================
-- AGREGAR FOREIGN KEY A REGISTRO_ACTIVIDAD
-- ============================================================================
-- Ahora que la tabla usuarios está actualizada, agregamos la FK que faltaba

SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
     WHERE TABLE_SCHEMA = DATABASE()
     AND TABLE_NAME = 'registro_actividad'
     AND CONSTRAINT_NAME = 'fk_registro_usuario') = 0,
    'ALTER TABLE registro_actividad ADD CONSTRAINT fk_registro_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL',
    'SELECT "FK fk_registro_usuario already exists" AS message'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Habilitar verificación de claves foráneas
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- VERIFICACIÓN DE DATOS INSERTADOS
-- ============================================================================

-- Ver todos los usuarios insertados
SELECT
    u.id,
    u.username,
    u.email,
    u.nombre_completo,
    u.rol,
    a.nombre as area_asignada,
    u.activo,
    u.fecha_creacion
FROM usuarios u
LEFT JOIN areas a ON u.area_id = a.id
ORDER BY
    CASE u.rol
        WHEN 'admin' THEN 1
        WHEN 'editor' THEN 2
        WHEN 'coordinador' THEN 3
    END,
    a.orden ASC;

-- Contar usuarios por rol
SELECT
    rol,
    COUNT(*) as total_usuarios
FROM usuarios
WHERE activo = 1
GROUP BY rol
ORDER BY
    CASE rol
        WHEN 'admin' THEN 1
        WHEN 'editor' THEN 2
        WHEN 'coordinador' THEN 3
    END;

-- Ver total de usuarios
SELECT COUNT(*) as total_usuarios FROM usuarios;

-- ============================================================================
-- NOTAS IMPORTANTES
-- ============================================================================
--
-- 1. CONTRASEÑA POR DEFECTO: 'Admin2025!'
--    - Cambiar inmediatamente después del primer login
--    - Usar contraseñas fuertes para todos los usuarios
--
-- 2. ROLES:
--    - admin: Puede gestionar todo (usuarios, áreas, configuración, todo el contenido)
--    - coordinador: Solo gestiona contenido de su área (proyectos, servicios, beneficios)
--    - editor: Puede editar contenido de todas las áreas (sin gestión de usuarios)
--
-- 3. PERMISOS POR ROL:
--    Admin:
--      ✓ Gestionar usuarios
--      ✓ Gestionar áreas
--      ✓ Gestionar todo el contenido
--      ✓ Configuración del sistema
--      ✓ Ver registro de actividad
--
--    Coordinador:
--      ✓ Ver/editar proyectos de su área
--      ✓ Ver/editar servicios de su área
--      ✓ Ver/editar beneficios de su área
--      ✗ No puede gestionar usuarios
--      ✗ No puede gestionar áreas
--      ✗ No puede ver contenido de otras áreas
--
--    Editor:
--      ✓ Ver/editar proyectos de todas las áreas
--      ✓ Ver/editar servicios de todas las áreas
--      ✓ Ver/editar beneficios de todas las áreas
--      ✓ Ver/editar noticias
--      ✗ No puede gestionar usuarios
--      ✗ No puede gestionar áreas
--      ✗ No puede eliminar contenido (solo editar)
--
-- 4. ÁREA ASIGNADA:
--    - Solo relevante para rol 'coordinador'
--    - Admin y editor tienen area_id = NULL
--    - Si se elimina un área, los coordinadores de esa área tendrán area_id = NULL
--
-- ============================================================================
-- RESULTADO ESPERADO:
-- - Tabla 'usuarios' actualizada con columna area_id
-- - Rol 'coordinador' agregado al ENUM
-- - Foreign key a tabla areas establecida
-- - Usuario admin creado/verificado (username: admin, password: Admin2025!)
-- - FK agregada a registro_actividad
-- ============================================================================

-- Resumen Completo de Usuarios:

--  Admin (Acceso Total)

--  | Username | Email                    | Contraseña |
--  |----------|--------------------------|------------|
--  | admin    | admin@coordicanarias.com | Admin2025! |

--  Coordinadores (por Área)

--  | Área                              | Username                | Email                  | Contraseña        |
--  |-----------------------------------|-------------------------|------------------------|-------------------|
--  | Empleo con Apoyo                  | coordinador_empleo      | fhn@coordicanarias.com | empleo_2025!      |
--  | Formación e Innovación            | coordinador_forminno    | fhn@coordicanarias.com | forminno_2025!    |
--  | Atención Integral                 | coordinador_aintegral   | fhn@coordicanarias.com | aintegral_2025!   |
--  | Igualdad y Promoción de la Mujer  | coordinador_igualdadpm  | fhn@coordicanarias.com | igualdadpm_2025!  |
--  | Ocio y Tiempo Libre               | coordinador_ocio        | fhn@coordicanarias.com | ocio_2025!        |
--  | Participación y Cultura Accesible | coordinador_participaca | fhn@coordicanarias.com | participaca_2025! |

-- Editor General

--  | Username | Email                  | Contraseña   |
--  |----------|------------------------|--------------|
--  | editor   | fhn@coordicanarias.com | editor_2025! |
