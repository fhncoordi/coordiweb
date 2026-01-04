-- ============================================================================
-- ACTUALIZAR CATEGORÍAS DE PROYECTOS
-- Coordicanarias CMS
-- ============================================================================
-- Este script actualiza SOLO el campo 'categorias' de los proyectos existentes
-- para que coincidan con los filtros de MixItUp
-- ============================================================================

USE coordica_crc;

-- Actualizar categorías de proyectos existentes
UPDATE proyectos SET categorias = 'integral' WHERE titulo = 'Promoción de la Autonomía Personal';
UPDATE proyectos SET categorias = 'part-cultura-accesible' WHERE titulo = 'TAIDA - Radio Escolar';
UPDATE proyectos SET categorias = 'igualdad' WHERE titulo = 'Magarza Crea';
UPDATE proyectos SET categorias = 'part-cultura-accesible' WHERE titulo = 'Punto Naranja - Boreal';
UPDATE proyectos SET categorias = 'empleo' WHERE titulo = 'Bolsa de Empleo';
UPDATE proyectos SET categorias = 'integral' WHERE titulo = 'Cuídate';
UPDATE proyectos SET categorias = 'integral,forma-innova' WHERE titulo = 'Teleasistencia Avanzada';
UPDATE proyectos SET categorias = 'integral,ocio' WHERE titulo = 'La Estancia';
UPDATE proyectos SET categorias = 'igualdad,part-cultura-accesible' WHERE titulo = 'La Voz de las Magarzas';
UPDATE proyectos SET categorias = 'igualdad,part-cultura-accesible' WHERE titulo = 'Parlamento Canario de la Mujer';
UPDATE proyectos SET categorias = 'ocio,part-cultura-accesible' WHERE titulo = 'Sintiendo mi Ciudad';
UPDATE proyectos SET categorias = 'igualdad,part-cultura-accesible' WHERE titulo = 'Materiales Informativos Adaptados';
UPDATE proyectos SET categorias = 'empleo,forma-innova' WHERE titulo = 'DRACAENA 14';
UPDATE proyectos SET categorias = 'ocio' WHERE titulo = 'Activados';
UPDATE proyectos SET categorias = 'part-cultura-accesible' WHERE titulo = 'Birmagen';
UPDATE proyectos SET categorias = 'part-cultura-accesible' WHERE titulo = 'TENIQUE 2024';

-- Verificar cambios
SELECT id, titulo, categorias FROM proyectos ORDER BY id;

-- ============================================================================
-- RESULTADO ESPERADO:
-- - 16 proyectos actualizados con categorías válidas
-- - Las categorías coinciden con los filtros de MixItUp:
--   * empleo
--   * forma-innova
--   * integral
--   * igualdad
--   * ocio
--   * part-cultura-accesible
-- ============================================================================
