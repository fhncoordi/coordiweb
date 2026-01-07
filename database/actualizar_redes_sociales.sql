-- Actualizar URLs de redes sociales en la tabla configuracion
-- Coordicanarias CMS

-- Facebook
UPDATE configuracion
SET valor = 'https://www.facebook.com/CoordiCanarias/'
WHERE clave = 'redes_facebook';

-- Twitter/X
UPDATE configuracion
SET valor = 'https://x.com/coordicanarias'
WHERE clave = 'redes_twitter';

-- Instagram (vacío por ahora - actualizar cuando tengan cuenta)
UPDATE configuracion
SET valor = ''
WHERE clave = 'redes_instagram';

-- LinkedIn (vacío por ahora - actualizar cuando tengan cuenta)
UPDATE configuracion
SET valor = ''
WHERE clave = 'redes_linkedin';

-- YouTube (vacío por ahora - actualizar cuando tengan cuenta)
UPDATE configuracion
SET valor = 'https://www.youtube.com/@CoordiCanarias'
WHERE clave = 'redes_youtube';

-- Verificar cambios
SELECT clave, valor
FROM configuracion
WHERE clave LIKE 'redes_%'
ORDER BY clave;
