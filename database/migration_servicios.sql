-- ============================================================================
-- MIGRACIÓN DE SERVICIOS EXISTENTES
-- Coordicanarias CMS
-- ============================================================================
-- Este script migra los 38 servicios existentes desde los archivos HTML
-- de las áreas a la tabla 'servicios' de la base de datos.
--
-- Ejecutar en phpMyAdmin o cliente MySQL después de crear el schema.
-- ============================================================================

USE coordica_crc;

-- Deshabilitar verificación de claves foráneas temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- Limpiar tabla de servicios (solo si se quiere reiniciar)
-- TRUNCATE TABLE servicios;

-- ============================================================================
-- ÁREA 1: EMPLEO (6 servicios)
-- ============================================================================

INSERT INTO servicios (area_id, titulo, descripcion, icono, orden, activo) VALUES
(1, 'Orientación laboral', 'Asesoramiento personalizado para identificar tus capacidades, definir tu perfil profesional y diseñar un itinerario de inserción laboral adaptado a tus necesidades y objetivos.', 'fas fa-compass', 1, 1),
(1, 'Formación para el empleo', 'Programas formativos especializados que desarrollan competencias profesionales y habilidades transversales necesarias para acceder al mercado laboral con garantías.', 'fas fa-graduation-cap', 2, 1),
(1, 'Intermediación laboral', 'Conectamos a personas con discapacidad con empresas comprometidas con la diversidad, facilitando procesos de selección adaptados y oportunidades reales de empleo.', 'fas fa-handshake', 3, 1),
(1, 'Preparación de entrevistas', 'Talleres y simulaciones para desarrollar habilidades comunicativas y estrategias efectivas que aumenten tus posibilidades de éxito en procesos de selección.', 'fas fa-comments', 4, 1),
(1, 'Seguimiento en el puesto', 'Seguimiento y apoyo continuado durante los primeros meses de incorporación laboral, asegurando una adaptación plena al entorno de trabajo.', 'fas fa-clipboard-check', 5, 1),
(1, 'Asesoría a empresas', 'Orientamos a empresas sobre contratación de personas con discapacidad, adaptaciones del puesto, ayudas disponibles y buenas prácticas de inclusión laboral.', 'fas fa-briefcase', 6, 1);

-- ============================================================================
-- ÁREA 2: FORMACIÓN E INNOVACIÓN (6 servicios)
-- ============================================================================

INSERT INTO servicios (area_id, titulo, descripcion, icono, orden, activo) VALUES
(2, 'Formación para la autonomía', 'Talleres y cursos orientados a desarrollar habilidades de vida independiente, gestión personal y toma de decisiones para aumentar la autonomía en el día a día.', 'fas fa-user-check', 1, 1),
(2, 'Competencias digitales', 'Programas de alfabetización digital y uso de tecnologías adaptadas que facilitan la comunicación, el acceso a información y la participación en la sociedad digital.', 'fas fa-laptop', 2, 1),
(2, 'Habilidades sociales', 'Talleres de comunicación efectiva, trabajo en equipo y relaciones interpersonales que potencian la integración social y comunitaria.', 'fas fa-user-friends', 3, 1),
(2, 'Formación profesional', 'Cursos especializados que desarrollan competencias técnicas y transversales necesarias para mejorar la empleabilidad y el acceso a oportunidades laborales.', 'fas fa-certificate', 4, 1),
(2, 'Formación a familias', 'Programas educativos para familiares que proporcionan herramientas, estrategias y recursos para apoyar mejor a sus seres queridos con discapacidad.', 'fas fa-chalkboard-teacher', 5, 1),
(2, 'Capacitación de voluntarios', 'Formación especializada para voluntarios que desean colaborar con la organización, proporcionando conocimientos sobre discapacidad, apoyo y buenas prácticas.', 'fas fa-hands-helping', 6, 1);

-- ============================================================================
-- ÁREA 3: ATENCIÓN INTEGRAL (8 servicios)
-- ============================================================================

INSERT INTO servicios (area_id, titulo, descripcion, icono, orden, activo) VALUES
(3, 'Asistencia personal', 'Busca favorecer la vida independiente de las personas beneficiarias. Se ofrece apoyo para realizar actividades de la vida diaria e incrementar sus posibilidades de ocio y participación social.', 'fas fa-hands-helping', 1, 1),
(3, 'Trabajo social', 'Orientación e información sobre recursos sociales, ayudas, prestaciones y servicios disponibles para personas con discapacidad y sus familias.', 'fas fa-user-md', 2, 1),
(3, 'Fisioterapia', 'Tratamientos personalizados de fisioterapia para mejorar la movilidad, reducir el dolor y potenciar las capacidades físicas de cada persona.', 'fas fa-hand-holding-medical', 3, 1),
(3, 'Apoyo psicológico', 'Atención psicológica individualizada y grupal para el bienestar emocional, desarrollo de estrategias de afrontamiento y mejora de la autoestima.', 'fas fa-brain', 4, 1),
(3, 'Promoción de la salud', 'Programas de hábitos saludables, autocuidado y prevención de problemas de salud, adaptados a las necesidades específicas de cada persona.', 'fas fa-heartbeat', 5, 1),
(3, 'Autonomía personal', 'Entrenamiento en habilidades de la vida diaria para aumentar la independencia en el hogar, la comunidad y el entorno social.', 'fas fa-walking', 6, 1),
(3, 'Apoyo familiar', 'Acompañamiento y orientación a familias, ofreciendo herramientas para mejorar la dinámica familiar y el apoyo mutuo.', 'fas fa-users', 7, 1),
(3, 'Préstamos de ayudas técnicas', 'Servicio de préstamo de productos de apoyo y ayudas técnicas que facilitan la autonomía personal y mejoran la calidad de vida en el hogar y la comunidad.', 'fas fa-wheelchair', 8, 1);

-- ============================================================================
-- ÁREA 4: IGUALDAD Y PROMOCIÓN DE LA MUJER (6 servicios)
-- ============================================================================

INSERT INTO servicios (area_id, titulo, descripcion, icono, orden, activo) VALUES
(4, 'Empoderamiento', 'Programas de desarrollo personal, liderazgo y autonomía que fortalecen las capacidades de las mujeres con discapacidad.', 'fas fa-fist-raised', 1, 1),
(4, 'Prevención de violencia', 'Acciones de prevención, detección y atención a mujeres con discapacidad en situación de violencia de género.', 'fas fa-shield-alt', 2, 1),
(4, 'Sensibilización', 'Campañas de visibilización y sensibilización sobre la realidad de las mujeres con discapacidad y la discriminación múltiple.', 'fas fa-bullhorn', 3, 1),
(4, 'Participación política', 'Fomentamos la participación de mujeres con discapacidad en espacios de decisión y representación política y social.', 'fas fa-landmark', 4, 1),
(4, 'Formación', 'Talleres y formaciones sobre igualdad de género, derechos de las mujeres y prevención de discriminación.', 'fas fa-graduation-cap', 5, 1),
(4, 'Incidencia política', 'Trabajamos para que las políticas públicas incorporen la perspectiva de género y discapacidad de forma transversal.', 'fas fa-balance-scale', 6, 1);

-- ============================================================================
-- ÁREA 5: OCIO (6 servicios)
-- ============================================================================

INSERT INTO servicios (area_id, titulo, descripcion, icono, orden, activo) VALUES
(5, 'Deportes adaptados', 'Actividades físicas adaptadas a diferentes capacidades: senderismo, yoga y otras.', 'fas fa-running', 1, 1),
(5, 'Turismo accesible', 'Excursiones y rutas adaptadas que permiten conocer y disfrutar del patrimonio cultural y natural de Canarias de forma inclusiva.', 'fas fa-map-marked-alt', 2, 1),
(5, 'Actividades culturales', 'Talleres creativos, visitas a museos, cine, teatro, música y otros eventos culturales adaptados para todos los gustos.', 'fas fa-palette', 3, 1),
(5, 'Ocio comunitario', 'Actividades grupales que fomentan las relaciones sociales, el compañerismo y la creación de vínculos entre las y los participantes.', 'fas fa-users', 4, 1),
(5, 'Eventos y celebraciones', 'Organización de eventos especiales, celebraciones temáticas y encuentros que generan momentos de convivencia.', 'fas fa-calendar-alt', 5, 1),
(5, 'Ocio digital', 'Actividades online, clubes de lectura virtual, videojuegos accesibles y otras propuestas que aprovechan las tecnologías digitales.', 'fas fa-gamepad', 6, 1);

-- ============================================================================
-- ÁREA 6: PARTICIPACIÓN Y CULTURA ACCESIBLE (6 servicios)
-- ============================================================================

INSERT INTO servicios (area_id, titulo, descripcion, icono, orden, activo) VALUES
(6, 'Accesibilidad cultural', 'Trabajamos para que las rutas turísticas, los museos y teatros, los festivales y demás eventos culturales sean plenamente accesibles para todas las personas.', 'fas fa-universal-access', 1, 1),
(6, 'Participación ciudadana', 'Promovemos la participación de personas con discapacidad en procesos de decisión, consejos ciudadanos y órganos de representación.', 'fas fa-vote-yea', 2, 1),
(6, 'Sensibilización', 'Desarrollamos campañas y acciones de sensibilización para visibilizar las barreras y promover la cultura inclusiva.', 'fas fa-bullhorn', 3, 1),
(6, 'Asesoramiento', 'Orientamos a instituciones culturales y administraciones públicas sobre accesibilidad universal y diseño para todas las personas.', 'fas fa-lightbulb', 4, 1),
(6, 'Materiales adaptados', 'Creamos y distribuimos materiales informativos y culturales en formatos accesibles: lectura fácil, braille, audio y otros.', 'fas fa-book-reader', 5, 1),
(6, 'Formación', 'Impartimos talleres sobre accesibilidad y participación a profesionales de la cultura, la comunicación y la administración pública.', 'fas fa-chalkboard', 6, 1);

-- Habilitar verificación de claves foráneas
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- VERIFICACIÓN DE DATOS INSERTADOS
-- ============================================================================

-- Ver todos los servicios insertados agrupados por área
SELECT
    a.nombre as area,
    s.id,
    s.titulo,
    s.icono,
    s.orden,
    s.activo
FROM servicios s
LEFT JOIN areas a ON s.area_id = a.id
ORDER BY a.orden ASC, s.orden ASC;

-- Contar servicios por área
SELECT
    a.nombre as area,
    COUNT(s.id) as total_servicios
FROM areas a
LEFT JOIN servicios s ON a.id = s.area_id
GROUP BY a.id, a.nombre
ORDER BY a.orden ASC;

-- Ver total de servicios
SELECT COUNT(*) as total_servicios FROM servicios;

-- ============================================================================
-- RESULTADO ESPERADO:
-- - 38 servicios insertados en total
-- - Empleo: 6 servicios
-- - Formación e Innovación: 6 servicios
-- - Atención Integral: 8 servicios
-- - Igualdad y Promoción de la Mujer: 6 servicios
-- - Ocio: 6 servicios
-- - Participación y Cultura Accesible: 6 servicios
-- - Todos los servicios con iconos Font Awesome asignados
-- - Orden secuencial dentro de cada área
-- ============================================================================
