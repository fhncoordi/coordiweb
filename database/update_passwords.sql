-- ============================================================================
-- ACTUALIZAR HASHES DE CONTRASEÑAS
-- Coordicanarias CMS
-- ============================================================================
-- Ejecutar este script en phpMyAdmin para corregir los hashes de contraseña
-- ============================================================================

USE coordica_crc;

-- Actualizar contraseña de ADMIN
-- Usuario: admin | Contraseña: Admin2025!
UPDATE usuarios
SET password_hash = '$2y$12$0OBlUlgB.FTdp0ywn8qhAOwFO7y48iA8/I.Sna7M889IfhkTjzE9S'
WHERE username = 'admin';

-- Actualizar contraseña de COORDINADOR DE EMPLEO
-- Usuario: coordinador_empleo | Contraseña: empleo_2025!
UPDATE usuarios
SET password_hash = '$2y$12$DXQCCIHEkH8L/zWIzB6t/eZjdBaH97yk2LlBnd1jCF3x4tB4XufmS'
WHERE username = 'coordinador_empleo';

-- Actualizar contraseña de COORDINADOR DE FORMACIÓN E INNOVACIÓN
-- Usuario: coordinador_forminno | Contraseña: forminno_2025!
UPDATE usuarios
SET password_hash = '$2y$12$fHnQiSCTbYxwtr4s8goINeBy.g3rDv2JKBcZThVbymOIwNCyCfo8m'
WHERE username = 'coordinador_forminno';

-- Actualizar contraseña de COORDINADOR DE ATENCIÓN INTEGRAL
-- Usuario: coordinador_aintegral | Contraseña: aintegral_2025!
UPDATE usuarios
SET password_hash = '$2y$12$tMqbBdjR6QuEvUOaFDgwhOLBkynH5VErBVrwz0z.X3.5Slgb8HDvS'
WHERE username = 'coordinador_aintegral';

-- Actualizar contraseña de COORDINADOR DE IGUALDAD Y PROMOCIÓN DE LA MUJER
-- Usuario: coordinador_igualdadpm | Contraseña: igualdadpm_2025!
UPDATE usuarios
SET password_hash = '$2y$12$lS5BQdyvOF2M6urv9UtFFOwO24skcr6XBB7b6DtanwKOMflj40bU2'
WHERE username = 'coordinador_igualdadpm';

-- Actualizar contraseña de COORDINADOR DE OCIO Y TIEMPO LIBRE
-- Usuario: coordinador_ocio | Contraseña: ocio_2025!
UPDATE usuarios
SET password_hash = '$2y$12$ZNwGL6viUr0xaJRcKVexOuDhq4jBLyLOjzsGSnu7r.UJjy/GS1/ta'
WHERE username = 'coordinador_ocio';

-- Actualizar contraseña de COORDINADOR DE PARTICIPACIÓN Y CULTURA ACCESIBLE
-- Usuario: coordinador_participaca | Contraseña: participaca_2025!
UPDATE usuarios
SET password_hash = '$2y$12$3xd/0upz/A5B2TaBDJ/OROHSgOrMPRL1YOCyap1q2P.d6A3tUl.1q'
WHERE username = 'coordinador_participaca';

-- Actualizar contraseña de EDITOR GENERAL
-- Usuario: editor | Contraseña: editor_2025!
UPDATE usuarios
SET password_hash = '$2y$12$kM6fj55TRBW7aVFqNp2WNO2bK74DBMNP71fZNBLWrEtXQ5vkQh76O'
WHERE username = 'editor';

-- ============================================================================
-- VERIFICAR ACTUALIZACIÓN
-- ============================================================================

SELECT
    username,
    nombre_completo,
    rol,
    SUBSTRING(password_hash, 1, 20) as hash_inicio,
    activo
FROM usuarios
ORDER BY
    CASE rol
        WHEN 'admin' THEN 1
        WHEN 'editor' THEN 2
        WHEN 'coordinador' THEN 3
    END,
    username;

-- ============================================================================
-- RESULTADO ESPERADO: 8 usuarios actualizados
-- ============================================================================
