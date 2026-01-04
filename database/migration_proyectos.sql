-- ============================================================================
-- MIGRACIÓN DE PROYECTOS EXISTENTES
-- Coordicanarias CMS
-- ============================================================================
-- Este script migra los 16 proyectos existentes desde los archivos HTML
-- a la tabla 'proyectos' de la base de datos.
--
-- Ejecutar en phpMyAdmin o cliente MySQL después de crear el schema.
-- ============================================================================

USE coordica_crc;

-- Deshabilitar verificación de claves foráneas temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- Limpiar tabla de proyectos (solo si se quiere reiniciar)
-- TRUNCATE TABLE proyectos;

-- ============================================================================
-- INSERTAR PROYECTOS
-- ============================================================================

-- 1. Promoción de la Autonomía Personal (Atención Integral)
INSERT INTO proyectos (titulo, descripcion, imagen, area_id, categorias, destacado, orden, activo) VALUES
('Promoción de la Autonomía Personal',
'Programa destinado a promover la autonomía personal de las personas con discapacidad intelectual mediante actividades de desarrollo de habilidades sociales, educación para la salud y ocio inclusivo.',
'images/portfolio/autonomia_personal1.jpg',
3,
'integral',
1,
1,
1);

-- 2. TAIDA - Radio Escolar (Participación Ciudadana)
INSERT INTO proyectos (titulo, descripcion, imagen, area_id, categorias, destacado, orden, activo) VALUES
('TAIDA - Radio Escolar',
'Proyecto de radio escolar que fomenta la participación ciudadana de jóvenes a través de la comunicación audiovisual y el periodismo comunitario.',
'images/portfolio/taida_radio.jpg',
6,
'part-cultura-accesible',
1,
2,
1);

-- 3. Magarza Crea (Igualdad y Participación en la Mujer)
INSERT INTO proyectos (titulo, descripcion, imagen, area_id, categorias, destacado, orden, activo) VALUES
('Magarza Crea',
'Programa de empoderamiento femenino que promueve la creatividad, el emprendimiento y la igualdad de oportunidades para mujeres en situación de vulnerabilidad.',
'images/portfolio/magarza_crea.jpg',
4,
'igualdad',
1,
3,
1);

-- 4. Punto Naranja - Boreal (Participación Ciudadana)
INSERT INTO proyectos (titulo, descripcion, imagen, area_id, categorias, destacado, orden, activo) VALUES
('Punto Naranja - Boreal',
'Espacio de sensibilización y prevención de violencias sexuales en espacios de ocio y eventos culturales.',
'images/portfolio/punto_naranja.jpg',
6,
'part-cultura-accesible',
0,
4,
1);

-- 5. Bolsa de Empleo (Empleo)
INSERT INTO proyectos (titulo, descripcion, imagen, area_id, categorias, destacado, orden, activo) VALUES
('Bolsa de Empleo',
'Servicio de intermediación laboral que conecta a personas en búsqueda activa de empleo con empresas que requieren personal cualificado.',
'images/portfolio/bolsa_empleo.jpg',
1,
'empleo',
1,
5,
1);

-- 6. Cuídate (Atención Integral)
INSERT INTO proyectos (titulo, descripcion, imagen, area_id, categorias, destacado, orden, activo) VALUES
('Cuídate',
'Programa de promoción de hábitos saludables, autocuidado y prevención de enfermedades dirigido a personas mayores y colectivos vulnerables.',
'images/portfolio/cuidate.jpg',
3,
'integral',
1,
6,
1);

-- 7. Teleasistencia Avanzada (Formación e Innovación)
INSERT INTO proyectos (titulo, descripcion, imagen, area_id, categorias, destacado, orden, activo) VALUES
('Teleasistencia Avanzada',
'Servicio de teleasistencia domiciliaria con tecnologías avanzadas para la atención y seguimiento de personas mayores y dependientes.',
'images/portfolio/teleasistencia.jpg',
2,
'integral,forma-innova',
1,
7,
1);

-- 8. La Estancia (Atención Integral)
INSERT INTO proyectos (titulo, descripcion, imagen, area_id, categorias, destacado, orden, activo) VALUES
('La Estancia',
'Centro de día para personas con discapacidad intelectual que ofrece actividades de ocio, estimulación cognitiva y habilidades sociales.',
'images/portfolio/la_estancia.jpg',
3,
'integral,ocio',
0,
8,
1);

-- 9. La Voz de las Magarzas (Igualdad y Participación en la Mujer)
INSERT INTO proyectos (titulo, descripcion, imagen, area_id, categorias, destacado, orden, activo) VALUES
('La Voz de las Magarzas',
'Espacio de encuentro y participación para mujeres donde se promueve el empoderamiento, la autoestima y el desarrollo personal.',
'images/portfolio/voz_magarzas.jpg',
4,
'igualdad,part-cultura-accesible',
0,
9,
1);

-- 10. Parlamento Canario de la Mujer (Igualdad y Participación en la Mujer)
INSERT INTO proyectos (titulo, descripcion, imagen, area_id, categorias, destacado, orden, activo) VALUES
('Parlamento Canario de la Mujer',
'Iniciativa que promueve la participación política de las mujeres canarias y su representación en espacios de toma de decisiones.',
'images/portfolio/parlamento_mujer.jpg',
4,
'igualdad,part-cultura-accesible',
1,
10,
1);

-- 11. Sintiendo mi Ciudad (Ocio)
INSERT INTO proyectos (titulo, descripcion, imagen, area_id, categorias, destacado, orden, activo) VALUES
('Sintiendo mi Ciudad',
'Programa de ocio inclusivo que fomenta el conocimiento del patrimonio cultural y natural de las ciudades canarias mediante actividades adaptadas.',
'images/portfolio/sintiendo_ciudad.jpg',
5,
'ocio,part-cultura-accesible',
1,
11,
1);

-- 12. Materiales Informativos Adaptados (Igualdad y Participación en la Mujer)
INSERT INTO proyectos (titulo, descripcion, imagen, area_id, categorias, destacado, orden, activo) VALUES
('Materiales Informativos Adaptados',
'Creación de materiales informativos en lectura fácil y formatos accesibles sobre derechos de la mujer, igualdad y prevención de violencias.',
'images/portfolio/materiales_adaptados.jpg',
4,
'igualdad,part-cultura-accesible',
0,
12,
1);

-- 13. DRACAENA 14 (Empleo)
INSERT INTO proyectos (titulo, descripcion, imagen, area_id, categorias, destacado, orden, activo) VALUES
('DRACAENA 14',
'Programa de inserción laboral para personas con discapacidad que incluye formación, intermediación y seguimiento en el empleo.',
'images/portfolio/dracaena14.jpg',
1,
'empleo,forma-innova',
1,
13,
1);

-- 14. Activados (Ocio)
INSERT INTO proyectos (titulo, descripcion, imagen, area_id, categorias, destacado, orden, activo) VALUES
('Activados',
'Programa de actividades deportivas y recreativas adaptadas que promueve la vida activa, el deporte inclusivo y los hábitos saludables.',
'images/portfolio/activados.jpg',
5,
'ocio',
1,
14,
1);

-- 15. Birmagen (Participación Ciudadana)
INSERT INTO proyectos (titulo, descripcion, imagen, area_id, categorias, destacado, orden, activo) VALUES
('Birmagen',
'Proyecto de comunicación audiovisual y alfabetización digital que empodera a jóvenes en situación de vulnerabilidad mediante la creación de contenidos.',
'images/portfolio/birmagen.jpg',
6,
'part-cultura-accesible',
0,
15,
1);

-- 16. TENIQUE 2024 (Participación Ciudadana)
INSERT INTO proyectos (titulo, descripcion, imagen, area_id, categorias, destacado, orden, activo) VALUES
('TENIQUE 2024',
'Encuentro anual de jóvenes participantes que promueve el intercambio de experiencias, el trabajo en red y la participación ciudadana juvenil.',
'images/portfolio/tenique2024.jpg',
6,
'part-cultura-accesible',
1,
16,
1);

-- Habilitar verificación de claves foráneas
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- VERIFICACIÓN DE DATOS INSERTADOS
-- ============================================================================

-- Ver todos los proyectos insertados
SELECT
    p.id,
    p.titulo,
    a.nombre as area,
    p.categorias,
    p.destacado,
    p.orden,
    p.activo
FROM proyectos p
LEFT JOIN areas a ON p.area_id = a.id
ORDER BY p.orden;

-- Contar proyectos por área
SELECT
    a.nombre as area,
    COUNT(p.id) as total_proyectos,
    SUM(p.destacado) as destacados
FROM areas a
LEFT JOIN proyectos p ON a.id = p.area_id
GROUP BY a.id, a.nombre
ORDER BY a.id;

-- Ver proyectos destacados
SELECT
    p.titulo,
    a.nombre as area,
    p.imagen
FROM proyectos p
LEFT JOIN areas a ON p.area_id = a.id
WHERE p.destacado = 1
ORDER BY p.orden;

-- ============================================================================
-- RESULTADO ESPERADO:
-- - 16 proyectos insertados
-- - Empleo: 2 proyectos (2 destacados)
-- - Formación e Innovación: 1 proyecto (1 destacado)
-- - Atención Integral: 3 proyectos (2 destacados)
-- - Igualdad y Participación en la Mujer: 4 proyectos (2 destacados)
-- - Ocio: 2 proyectos (2 destacados)
-- - Participación Ciudadana: 4 proyectos (2 destacados)
-- - Total proyectos destacados: 11
-- ============================================================================
