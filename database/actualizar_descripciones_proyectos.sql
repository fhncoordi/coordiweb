-- =====================================================
-- ACTUALIZAR DESCRIPCIONES COMPLETAS DE PROYECTOS
-- Coordicanarias - Recuperar descripciones de backups
-- =====================================================

-- ÁREA: OCIO Y TIEMPO LIBRE

UPDATE proyectos
SET descripcion = 'Programa innovador de turismo accesible que promueve el conocimiento y disfrute del patrimonio cultural y natural de Canarias. A través de rutas adaptadas, visitas guiadas y actividades sensoriales, acercamos la riqueza de nuestras islas a personas con discapacidad.

Este proyecto fomenta la inclusión social y el derecho al ocio, convirtiendo el turismo en una experiencia enriquecedora y accesible para todas las personas.'
WHERE titulo = 'Sintiendo mi Ciudad'
  AND area_id = (SELECT id FROM areas WHERE slug = 'ocio');

UPDATE proyectos
SET descripcion = 'Programa de actividades deportivas y recreativas adaptadas que promueve la vida activa y saludable. Incluye talleres de actividad física, deportes adaptados, actividades al aire libre y eventos recreativos diseñados para todos los niveles y capacidades.

A través de metodologías inclusivas y adaptadas, favorecemos el bienestar físico y emocional, fortalecemos las relaciones sociales y promovemos hábitos de vida saludables.'
WHERE titulo = 'Activados'
  AND area_id = (SELECT id FROM areas WHERE slug = 'ocio');

-- ÁREA: IGUALDAD Y PROMOCIÓN DE LA MUJER

UPDATE proyectos
SET descripcion = 'Espacio de participación y representación donde las mujeres con discapacidad de Canarias debaten, proponen y defienden políticas públicas que respondan a sus necesidades específicas. El Parlamento es un órgano consultivo que traslada propuestas a las instituciones canarias.

A través de este proyecto, las mujeres con discapacidad ejercen su derecho a participar activamente en las decisiones políticas y sociales que afectan a sus vidas, siendo protagonistas del cambio social.'
WHERE titulo = 'Parlamento Canario de la Mujer'
  AND area_id = (SELECT id FROM areas WHERE slug = 'igualdadpm');

UPDATE proyectos
SET descripcion = 'Programa de empoderamiento y desarrollo creativo para mujeres con discapacidad que combina talleres artísticos, expresión creativa y desarrollo de habilidades personales. A través del arte y la creatividad, las participantes exploran su identidad y fortalecen su autoestima.

Magarza Crea es un espacio seguro donde las mujeres con discapacidad se encuentran, comparten experiencias y desarrollan su potencial creativo, generando redes de apoyo mutuo y promoviendo el bienestar emocional.'
WHERE titulo = 'Magarza Crea'
  AND area_id = (SELECT id FROM areas WHERE slug = 'igualdadpm');

-- ÁREA: PARTICIPACIÓN Y CULTURA ACCESIBLE

UPDATE proyectos
SET descripcion = 'Proyecto innovador que potencia la comunicación, las habilidades sociales y la innovación socioeducativa en 36 Centros Educativos Públicos de Infantil y Primaria del Municipio de Santa Cruz de Tenerife. Utiliza la radio y la técnica del croma como recursos pedagógicos para promover la convivencia escolar y la inclusión educativa.

TAIDA transmite al alumnado los principios de la comunicación a través de talleres de radio escolar, crea canales de YouTube en cada centro participante y edita las intervenciones de los estudiantes. Más de 550 alumnos y alumnas se benefician de este proyecto que convierte la radio en un recurso estratégico para la participación y la inclusión.'
WHERE titulo = 'TAIDA - Radio Escolar'
  AND area_id = (SELECT id FROM areas WHERE slug = 'participaca');

UPDATE proyectos
SET descripcion = 'TENIQUE, que significa "piedra" en lengua guanche, es un proyecto participativo que lleva 6 ediciones acercando el arte urbano a los barrios de Santa Cruz de Tenerife. Involucra a centros educativos, asociaciones vecinales y el colectivo de personas con discapacidad en encuentros de sensibilización y creación colectiva de murales.

A través de obras realizadas por reconocidos artistas urbanos como Sabotaje al Montaje y Feo Flip, en colaboración con la comunidad, TENIQUE favorece la cohesión social y el empoderamiento. Cada edición regala a los barrios obras artísticas accesibles que reflejan la diversidad y generan mecanismos de sensibilización para la verdadera integración social.'
WHERE titulo = 'TENIQUE 2024'
  AND area_id = (SELECT id FROM areas WHERE slug = 'participaca');

-- ÁREA: EMPLEO

UPDATE proyectos
SET descripcion = 'Proyecto experimental de empleo cofinanciado por el Servicio Canario de Empleo que ofrece itinerarios personalizados de inserción laboral. Incluye orientación especializada, formación en competencias clave y prácticas profesionales en empresas colaboradoras.

DRACAENA ha facilitado la inserción laboral de decenas de personas con discapacidad, convirtiéndose en un modelo de éxito en inclusión laboral en Canarias.'
WHERE titulo = 'DRACAENA 14'
  AND area_id = (SELECT id FROM areas WHERE slug = 'empleo');

UPDATE proyectos
SET descripcion = 'Servicio permanente de intermediación laboral que conecta a personas con discapacidad en búsqueda activa de empleo con empresas que buscan incorporar talento diverso. Gestionamos ofertas laborales y facilitamos procesos de selección adaptados.

Nuestra bolsa de empleo funciona como un puente efectivo entre candidatos cualificados y empresas comprometidas con la inclusión laboral real.'
WHERE titulo = 'Bolsa de Empleo'
  AND area_id = (SELECT id FROM areas WHERE slug = 'empleo');

-- ÁREA: ATENCIÓN INTEGRAL

UPDATE proyectos
SET descripcion = 'Servicio permanente de atención psicológica y trabajo social que ofrece apoyo continuo a personas con discapacidad y sus familias. Brindamos orientación, acompañamiento en procesos vitales y conexión con recursos comunitarios.

Nuestro enfoque personalizado garantiza que cada persona reciba la atención específica que necesita para desarrollar su proyecto de vida.'
WHERE titulo = 'Cuídate'
  AND area_id = (SELECT id FROM areas WHERE slug = 'aintegral');

UPDATE proyectos
SET descripcion = 'Programa de apoyo integral para personas con discapacidad física que fomenta la capacidad de tomar decisiones propias y ejecutar actividades básicas de la vida diaria. Acompañamos en tareas domésticas, relaciones personales, educación, cuidado de la salud y participación comunitaria.

Nuestra intervención preventiva y personalizada busca mantener las capacidades cognitivas, funcionales, sociales y emocionales, evitando patologías derivadas de la inactividad y mejorando la calidad de vida desde el respeto a la dignidad y preferencias de cada persona.'
WHERE titulo = 'Promoción de la Autonomía Personal'
  AND area_id = (SELECT id FROM areas WHERE slug = 'aintegral');

-- ÁREA: FORMACIÓN E INNOVACIÓN

UPDATE proyectos
SET descripcion = 'Proyecto de investigación que evalúa la efectividad de los servicios de teleasistencia avanzada en la mejora de la calidad de vida, autonomía personal y seguridad de las personas usuarias del servicio, analizando su impacto social y económico.

A través de metodología rigurosa, generamos evidencia sobre los beneficios de la teleasistencia y contribuimos a la mejora continua de este servicio esencial para la vida independiente.'
WHERE titulo = 'Teleasistencia Avanzada'
  AND area_id = (SELECT id FROM areas WHERE slug = 'forminno');

-- =====================================================
-- VERIFICAR ACTUALIZACIONES
-- =====================================================

-- Contar proyectos actualizados
SELECT
    a.nombre AS area,
    COUNT(*) AS proyectos_actualizados
FROM proyectos p
INNER JOIN areas a ON p.area_id = a.id
WHERE LENGTH(p.descripcion) > 200
GROUP BY a.nombre
ORDER BY a.nombre;

-- Ver proyectos con descripciones cortas (posible problema)
SELECT
    p.id,
    a.nombre AS area,
    p.titulo,
    LENGTH(p.descripcion) AS longitud_descripcion
FROM proyectos p
INNER JOIN areas a ON p.area_id = a.id
WHERE LENGTH(p.descripcion) < 200
ORDER BY a.nombre, p.titulo;
