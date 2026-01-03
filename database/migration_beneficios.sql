-- ============================================================================
-- MIGRACIÓN DE BENEFICIOS EXISTENTES
-- Coordicanarias CMS
-- ============================================================================
-- Este script migra los 24 beneficios existentes desde los archivos HTML
-- de las áreas a la tabla 'beneficios' de la base de datos.
--
-- Ejecutar en phpMyAdmin o cliente MySQL después de crear el schema.
-- ============================================================================

USE coordica_crc;

-- Deshabilitar verificación de claves foráneas temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- Limpiar tabla de beneficios (solo si se quiere reiniciar)
-- TRUNCATE TABLE beneficios;

-- ============================================================================
-- ÁREA 1: EMPLEO (4 beneficios)
-- ============================================================================

INSERT INTO beneficios (area_id, titulo, descripcion, icono, orden, activo) VALUES
(1, 'Desarrollo profesional', 'Potenciamos tus capacidades y competencias profesionales para que puedas acceder a empleos de calidad acordes a tu formación y expectativas.', 'fas fa-chart-line', 1, 1),
(1, 'Independencia económica', 'El acceso al empleo te permite alcanzar autonomía económica, mejorando tu calidad de vida y tu capacidad para tomar decisiones sobre tu futuro.', 'fas fa-star', 2, 1),
(1, 'Inclusión social', 'El empleo es una herramienta fundamental para la inclusión social plena, facilitando la participación activa en la comunidad y el desarrollo de redes sociales.', 'fas fa-users', 3, 1),
(1, 'Apoyo continuo', 'Te acompañamos en todo el proceso, desde la orientación inicial hasta el seguimiento en el puesto de trabajo, garantizando tu éxito profesional.', 'fas fa-hands-helping', 4, 1);

-- ============================================================================
-- ÁREA 2: FORMACIÓN E INNOVACIÓN (4 beneficios)
-- ============================================================================

INSERT INTO beneficios (area_id, titulo, descripcion, icono, orden, activo) VALUES
(2, 'Desarrollo de capacidades', 'Adquieres nuevas habilidades y competencias que amplían tus posibilidades de participación activa en la sociedad y el mercado laboral.', 'fas fa-graduation-cap', 1, 1),
(2, 'Mejora de la autonomía personal', 'Los programas están diseñados para potenciar tu independencia y capacidad de gestión en diferentes áreas de tu vida cotidiana.', 'fas fa-user-check', 2, 1),
(2, 'Adaptación a las nuevas tecnologías', 'Te familiarizas con herramientas tecnológicas accesibles que facilitan tu comunicación, aprendizaje y acceso a servicios y oportunidades.', 'fas fa-laptop', 3, 1),
(2, 'Redes de apoyo', 'Participar en nuestros programas te conecta con otras personas en situaciones similares, creando vínculos de apoyo mutuo y enriquecimiento personal.', 'fas fa-users', 4, 1);

-- ============================================================================
-- ÁREA 3: ATENCIÓN INTEGRAL (4 beneficios)
-- ============================================================================

INSERT INTO beneficios (area_id, titulo, descripcion, icono, orden, activo) VALUES
(3, 'Mejora de la autonomía', 'Desarrollamos habilidades que permiten a las personas gestionar su vida diaria con mayor independencia y confianza.', 'fas fa-check-circle', 1, 1),
(3, 'Bienestar emocional', 'Proporcionamos herramientas para la gestión emocional, reducción del estrés y mejora de la autoestima.', 'fas fa-heart', 2, 1),
(3, 'Inclusión social', 'Facilitamos la participación activa en la comunidad y el desarrollo de redes de apoyo social significativas.', 'fas fa-users', 3, 1),
(3, 'Apoyo integral', 'Ofrecemos un acompañamiento completo que aborda todas las dimensiones del bienestar personal y familiar.', 'fas fa-hand-holding-heart', 4, 1);

-- ============================================================================
-- ÁREA 4: IGUALDAD Y PROMOCIÓN DE LA MUJER (4 beneficios)
-- ============================================================================

INSERT INTO beneficios (area_id, titulo, descripcion, icono, orden, activo) VALUES
(4, 'Visibilización', 'Hacemos visible la realidad de las mujeres con discapacidad y la discriminación múltiple que enfrentan en todos los ámbitos de la vida.', 'fas fa-bullhorn', 1, 1),
(4, 'Empoderamiento personal', 'Fortalecemos la autoestima, la autonomía y las capacidades de liderazgo de las mujeres con discapacidad.', 'fas fa-fist-raised', 2, 1),
(4, 'Cambio social', 'Contribuimos a transformar actitudes, eliminar estereotipos y construir una sociedad más igualitaria y justa para todas las personas.', 'fas fa-balance-scale', 3, 1),
(4, 'Defensa de derechos', 'Promovemos políticas públicas que garanticen los derechos de las mujeres con discapacidad y previenen situaciones de violencia y discriminación.', 'fas fa-shield-alt', 4, 1);

-- ============================================================================
-- ÁREA 5: OCIO (4 beneficios)
-- ============================================================================

INSERT INTO beneficios (area_id, titulo, descripcion, icono, orden, activo) VALUES
(5, 'Bienestar y salud', 'Las actividades físicas y recreativas mejoran la salud física y mental, reducen el estrés y aumentan la sensación de bienestar general.', 'fas fa-heart', 1, 1),
(5, 'Inclusión social', 'Participar en actividades de ocio favorece la integración en la comunidad, rompiendo barreras y creando espacios de encuentro inclusivos.', 'fas fa-users', 2, 1),
(5, 'Desarrollo personal', 'El ocio permite descubrir nuevos intereses, desarrollar habilidades y fortalecer la autoestima a través de experiencias positivas.', 'fas fa-rocket', 3, 1),
(5, 'Calidad de vida', 'Disfrutar del tiempo libre de forma plena y significativa es esencial para una vida satisfactoria y equilibrada.', 'fas fa-smile', 4, 1);

-- ============================================================================
-- ÁREA 6: PARTICIPACIÓN Y CULTURA ACCESIBLE (4 beneficios)
-- ============================================================================

INSERT INTO beneficios (area_id, titulo, descripcion, icono, orden, activo) VALUES
(6, 'Empoderamiento', 'Fortalecemos las capacidades de las personas con discapacidad para participar activamente en la sociedad y defender sus derechos.', 'fas fa-fist-raised', 1, 1),
(6, 'Acceso universal a la cultura', 'Eliminamos barreras para que todas las personas puedan disfrutar plenamente de la riqueza cultural de nuestra comunidad.', 'fas fa-universal-access', 2, 1),
(6, 'Democracia inclusiva', 'Promovemos una democracia más participativa donde todas las voces sean escuchadas y tenidas en cuenta en la toma de decisiones.', 'fas fa-vote-yea', 3, 1),
(6, 'Transformación social', 'Contribuimos a construir una sociedad más justa, equitativa e inclusiva a través de la participación activa y el cambio cultural.', 'fas fa-balance-scale', 4, 1);

-- Habilitar verificación de claves foráneas
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- VERIFICACIÓN DE DATOS INSERTADOS
-- ============================================================================

-- Ver todos los beneficios insertados agrupados por área
SELECT
    a.nombre as area,
    b.id,
    b.titulo,
    b.icono,
    b.orden,
    b.activo
FROM beneficios b
LEFT JOIN areas a ON b.area_id = a.id
ORDER BY a.orden ASC, b.orden ASC;

-- Contar beneficios por área
SELECT
    a.nombre as area,
    COUNT(b.id) as total_beneficios
FROM areas a
LEFT JOIN beneficios b ON a.id = b.area_id
GROUP BY a.id, a.nombre
ORDER BY a.orden ASC;

-- Ver total de beneficios
SELECT COUNT(*) as total_beneficios FROM beneficios;

-- ============================================================================
-- RESULTADO ESPERADO:
-- - 24 beneficios insertados en total
-- - Empleo: 4 beneficios
-- - Formación e Innovación: 4 beneficios
-- - Atención Integral: 4 beneficios
-- - Igualdad y Promoción de la Mujer: 4 beneficios
-- - Ocio: 4 beneficios
-- - Participación y Cultura Accesible: 4 beneficios
-- - Todos los beneficios con iconos Font Awesome asignados
-- - Orden secuencial dentro de cada área
-- ============================================================================
