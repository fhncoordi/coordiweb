-- ============================================================================
-- DATOS INICIALES DE CONFIGURACIÓN
-- Coordicanarias CMS
-- ============================================================================
-- Este script inserta los valores iniciales de configuración del sitio
-- Ejecutar en phpMyAdmin después de crear la tabla configuracion
-- ============================================================================

USE coordica_crc;

-- Insertar configuraciones iniciales (si no existen)
INSERT INTO configuracion (clave, valor, descripcion, tipo)
SELECT * FROM (
    SELECT 'nombre_sitio' as clave, 'Coordicanarias' as valor, 'Nombre del sitio web' as descripcion, 'texto' as tipo
    UNION ALL
    SELECT 'descripcion_sitio', 'Centro de Recuperación de Canarias', 'Descripción de la organización', 'texto'
    UNION ALL
    SELECT 'slogan', 'Impulsando la inclusión social', 'Slogan del sitio', 'texto'
    UNION ALL
    SELECT 'contacto_telefono', '928 123 456', 'Teléfono de contacto', 'tel'
    UNION ALL
    SELECT 'contacto_email', 'info@coordicanarias.com', 'Email de contacto', 'email'
    UNION ALL
    SELECT 'contacto_direccion', 'Calle Ejemplo, 123, Las Palmas de Gran Canaria', 'Dirección física', 'texto'
    UNION ALL
    SELECT 'contacto_horario', 'Lunes a Viernes: 8:00 - 15:00', 'Horario de atención', 'texto'
    UNION ALL
    SELECT 'redes_facebook', '', 'URL de Facebook', 'url'
    UNION ALL
    SELECT 'redes_twitter', '', 'URL de Twitter', 'url'
    UNION ALL
    SELECT 'redes_instagram', '', 'URL de Instagram', 'url'
    UNION ALL
    SELECT 'redes_linkedin', '', 'URL de LinkedIn', 'url'
    UNION ALL
    SELECT 'redes_youtube', '', 'URL de YouTube', 'url'
) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM configuracion WHERE clave = tmp.clave
);

-- ============================================================================
-- VERIFICACIÓN
-- ============================================================================

-- Ver configuraciones insertadas
SELECT
    id,
    clave,
    valor,
    tipo,
    descripcion
FROM configuracion
ORDER BY
    CASE
        WHEN clave LIKE 'nombre_%' THEN 1
        WHEN clave LIKE 'contacto_%' THEN 2
        WHEN clave LIKE 'redes_%' THEN 3
        ELSE 4
    END,
    clave;

-- Contar configuraciones
SELECT COUNT(*) as total_configuraciones FROM configuracion;

-- ============================================================================
-- NOTAS IMPORTANTES
-- ============================================================================
--
-- 1. CONFIGURACIONES POR CATEGORÍA:
--
--    General:
--      - nombre_sitio: Nombre de la organización
--      - descripcion_sitio: Descripción breve
--      - slogan: Frase descriptiva
--
--    Contacto:
--      - contacto_telefono: Teléfono principal
--      - contacto_email: Email de contacto
--      - contacto_direccion: Dirección física
--      - contacto_horario: Horarios de atención
--
--    Redes Sociales:
--      - redes_facebook: URL de Facebook
--      - redes_twitter: URL de Twitter/X
--      - redes_instagram: URL de Instagram
--      - redes_linkedin: URL de LinkedIn
--      - redes_youtube: URL de YouTube
--
-- 2. PERMISOS:
--    - Solo admin puede modificar la configuración
--    - Se usa en el footer y página de contacto del sitio público
--
-- 3. PERSONALIZACIÓN:
--    - Actualiza estos valores con la información real de tu organización
--    - Las URLs de redes sociales están vacías por defecto
--    - Completa solo las redes sociales que uses activamente
--
-- ============================================================================
-- RESULTADO ESPERADO:
-- - 12 configuraciones insertadas
-- - Valores por defecto listos para personalizar
-- - Sistema de configuración funcional
-- ============================================================================
