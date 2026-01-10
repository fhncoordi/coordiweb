# Checklist de Deploy a Producción - Coordicanarias

**Versión:** 1.0
**Fecha:** 2026-01-10
**Movimiento:** De `coordicanarias.com/new/` a `coordicanarias.com/`

---

## 📋 Resumen Ejecutivo

Este checklist cubre el proceso completo de movimiento del sitio web de Coordicanarias desde el subdirectorio `/new/` a la raíz del dominio, incluyendo:
- ✅ Sitio público (12 páginas PHP)
- ✅ Panel de administración completo (`/admin/`)
- ✅ Base de datos MySQL (`coordica_crc`)
- ✅ Sistema de emails (formularios de contacto)
- ✅ Panel de accesibilidad (Alto Contraste, Modo Oscuro, Lector de Voz)

**Tiempo estimado:** 2-3 horas
**Nivel de riesgo:** Bajo (el código está diseñado para auto-detectar rutas)

---

## 🎯 FASE 1: PRE-DEPLOY (Antes del movimiento)

### 1.1 Backup Completo

#### 📁 Backup de Archivos
- [ ] Acceder al hosting vía FTP/SFTP o cPanel File Manager
- [ ] Crear carpeta de backup: `backup_YYYYMMDD/` (ej: `backup_20260110/`)
- [ ] **Opción A (Recomendada):** Comprimir y descargar
  ```bash
  # Desde terminal SSH del hosting:
  cd /home/coordica/public_html
  tar -czf backup_$(date +%Y%m%d_%H%M%S).tar.gz .
  # Descargar el archivo .tar.gz a tu ordenador
  ```
- [ ] **Opción B:** Descargar toda la carpeta `/public_html/` vía FTP
- [ ] **Verificar tamaño del backup** (debe ser ~100-200 MB aprox.)
- [ ] **Guardar backup en lugar seguro** (disco externo + nube)

#### 🗄️ Backup de Base de Datos
- [ ] Acceder a phpMyAdmin en tu hosting
- [ ] Seleccionar base de datos: `coordica_crc`
- [ ] Click en pestaña **"Exportar"**
- [ ] Método: **"Rápido"**, Formato: **"SQL"**
- [ ] Click en **"Continuar"** para descargar
- [ ] Guardar archivo: `coordica_crc_backup_20260110.sql`
- [ ] **Verificar que el archivo .sql no esté vacío** (debe pesar varios MB)
- [ ] **Guardar backup en lugar seguro** (disco externo + nube)

**⚠️ IMPORTANTE:** No continúes sin tener ambos backups verificados y guardados.

---

### 1.2 Verificar Configuración

#### ✅ php/config.php
- [ ] Abrir `/new/php/config.php` en un editor
- [ ] **Verificar credenciales de base de datos:**
  ```php
  define('DB_HOST', 'localhost');  // ✅ Correcto
  define('DB_NAME', 'coordica_crc');  // ✅ Verificar que existe
  define('DB_USER', 'coordica_crc');  // ✅ Verificar usuario
  define('DB_PASS', 'e6UTGzCbEgjLkQkL7fn9');  // ✅ Verificar contraseña
  ```
- [ ] **Verificar configuración SMTP:**
  ```php
  define('EMAIL_METHOD', 'mail');  // ✅ OK para Alojared
  define('SMTP_USER', 'noreply@coordicanarias.com');  // ✅ Verificar
  ```
- [ ] **Verificar emails por área** (líneas 36-50):
  - Todos apuntan a `fhn@coordicanarias.com` ✅
- [ ] **Verificar dominios permitidos** (líneas 56-60):
  - `coordicanarias.com` ✅
  - `www.coordicanarias.com` ✅
  - `localhost` ✅ (para pruebas)
- [ ] **AUTO-DETECCIÓN DE RUTAS** (líneas 76-109):
  - ✅ Ya está configurado para funcionar en `/new/` y raíz
  - ❌ NO necesitas cambiar nada aquí

#### ✅ admin/login.php
- [ ] Verificar que tienes las credenciales de acceso al panel admin
- [ ] Usuario admin existe en la base de datos
- [ ] Si no recuerdas la contraseña, resetéala desde phpMyAdmin antes del deploy

---

### 1.3 Crear Punto de Restauración

- [ ] Anotar fecha y hora actual: `____________`
- [ ] Tomar captura de pantalla del sitio actual en `coordicanarias.com`
- [ ] Tomar captura de pantalla del sitio en `coordicanarias.com/new/`
- [ ] Listar archivos actuales en raíz (para saber qué sobrescribirás):
  ```bash
  ls -la /home/coordica/public_html/
  ```
- [ ] **Identificar archivos críticos en raíz que NO debes borrar:**
  - `.htaccess` (del WordPress antiguo si existe)
  - `wp-config.php` (si existe WordPress)
  - Otros archivos críticos: `________________`

---

## 🚀 FASE 2: DEPLOY (Movimiento de archivos)

### 2.1 Preparación

- [ ] **Poner sitio en modo mantenimiento (Opcional):**
  ```php
  // Crear archivo maintenance.php en raíz con:
  <!DOCTYPE html>
  <html lang="es">
  <head>
      <meta charset="UTF-8">
      <title>Mantenimiento - Coordicanarias</title>
      <style>
          body { font-family: Arial; text-align: center; padding: 50px; }
          h1 { color: #333; }
      </style>
  </head>
  <body>
      <h1>Sitio en mantenimiento</h1>
      <p>Estamos actualizando nuestro sitio web. Volvemos en unos minutos.</p>
  </body>
  </html>
  ```
- [ ] Notificar al equipo del inicio del deploy

---

### 2.2 Movimiento de Archivos

**IMPORTANTE:** Existen dos estrategias. Elige la que prefieras:

#### **OPCIÓN A: Mover archivos (Recomendada)**
Mantiene el directorio `/new/` intacto como backup.

```bash
# Desde terminal SSH:
cd /home/coordica/public_html

# Crear backup de archivos actuales en raíz (por si acaso)
mkdir backup_old_root
mv *.php *.html backup_old_root/ 2>/dev/null

# Copiar todo desde /new/ a raíz
cp -r new/* .

# Verificar que se copió correctamente
ls -la
```

- [ ] **Directorios copiados a raíz:**
  - [ ] `/admin/` (14 archivos PHP + includes + .htaccess)
  - [ ] `/areas/` (11 páginas PHP de áreas + institucionales)
  - [ ] `/css/` (todos los archivos CSS)
  - [ ] `/database/` (scripts SQL)
  - [ ] `/docs/` (documentación)
  - [ ] `/fonts/` (fuentes web)
  - [ ] `/images/` (todas las imágenes)
  - [ ] `/js/` (archivos JavaScript)
  - [ ] `/php/` (backend PHP + PHPMailer + config.php)
  - [ ] `/uploads/` (imágenes subidas por admin)
  - [ ] `/webfonts/` (iconos Font Awesome)

- [ ] **Archivos raíz copiados:**
  - [ ] `index.php` (página principal)
  - [ ] `transparencia.php`
  - [ ] `.gitignore` (opcional, no afecta producción)
  - [ ] `README.md` (opcional, no afecta producción)

#### **OPCIÓN B: Eliminar /new/ y mover**
Libera espacio pero pierdes el backup automático.

```bash
# Desde terminal SSH:
cd /home/coordica/public_html

# Backup preventivo
tar -czf backup_before_delete_new.tar.gz new/

# Mover archivos de /new/ a raíz
mv new/* .
mv new/.gitignore . 2>/dev/null

# Eliminar carpeta vacía
rmdir new/
```

- [ ] Ejecutar comandos de OPCIÓN B
- [ ] Verificar que `/new/` ya no existe o está vacío

---

### 2.3 Configurar Permisos

- [ ] **Verificar permisos de directorios:**
  ```bash
  chmod 755 admin/
  chmod 755 areas/
  chmod 755 css/
  chmod 755 database/
  chmod 755 docs/
  chmod 755 fonts/
  chmod 755 images/
  chmod 755 js/
  chmod 755 php/
  chmod 755 uploads/
  chmod 755 webfonts/
  ```

- [ ] **Verificar permisos de archivos PHP:**
  ```bash
  chmod 644 *.php
  chmod 644 areas/*.php
  chmod 644 admin/*.php
  chmod 644 admin/includes/*.php
  chmod 644 php/*.php
  chmod 644 php/core/*.php
  ```

- [ ] **Proteger archivos sensibles:**
  ```bash
  chmod 600 php/config.php
  chmod 644 admin/.htaccess
  ```

- [ ] **Permisos de escritura para uploads:**
  ```bash
  chmod 755 uploads/
  chmod 755 uploads/proyectos/
  chmod 755 uploads/testimonios/
  chmod 755 uploads/noticias/
  chmod 755 uploads/areas/
  ```

---

### 2.4 Verificar Auto-detección de Rutas

El código tiene auto-detección de rutas en `php/config.php` líneas 76-109. Esto significa que **NO necesitas cambiar nada manualmente**.

- [ ] **Verificar que BASE_PATH se detecta correctamente:**
  ```php
  // Crear archivo temporal: test_base_path.php en raíz
  <?php
  require_once __DIR__ . '/php/config.php';
  echo "BASE_PATH detectado: '" . BASE_PATH . "'<br>";
  echo "Debería ser: '' (cadena vacía)<br>";
  echo "URL generada con url('admin/'): " . url('admin/') . "<br>";
  ?>
  ```
- [ ] Visitar: `https://coordicanarias.com/test_base_path.php`
- [ ] **Resultado esperado:**
  ```
  BASE_PATH detectado: ''
  Debería ser: '' (cadena vacía)
  URL generada con url('admin/'): /admin/
  ```
- [ ] Si el resultado es correcto, eliminar `test_base_path.php`

---

## ✅ FASE 3: TESTING POST-DEPLOY

### 3.1 Testing del Sitio Público

#### 📄 Páginas principales
- [ ] **Homepage:** `https://coordicanarias.com/`
  - [ ] Carga correctamente
  - [ ] Imágenes se ven
  - [ ] CSS aplicado correctamente
  - [ ] JavaScript funciona (menú responsive, etc.)
  - [ ] No hay errores en consola del navegador (F12)

- [ ] **Página de transparencia:** `https://coordicanarias.com/transparencia.php`
  - [ ] Carga correctamente
  - [ ] Banner visible
  - [ ] Enlaces funcionan

#### 🎨 Páginas de áreas (6 páginas)
- [ ] **Empleo:** `https://coordicanarias.com/areas/empleo.php`
- [ ] **Formación e Innovación:** `https://coordicanarias.com/areas/forminno.php`
- [ ] **Atención Integral:** `https://coordicanarias.com/areas/aintegral.php`
- [ ] **Igualdad y Promoción de la Mujer:** `https://coordicanarias.com/areas/igualdadpm.php`
- [ ] **Ocio y Tiempo Libre:** `https://coordicanarias.com/areas/ocio.php`
- [ ] **Participación Ciudadana:** `https://coordicanarias.com/areas/participaca.php`

**Para cada área verificar:**
- [ ] Carga sin errores
- [ ] Banner (jumbotron) visible
- [ ] Servicios se muestran correctamente
- [ ] Beneficios se muestran correctamente
- [ ] Iconos SVG visibles
- [ ] Formulario de contacto presente

#### ⚖️ Páginas legales (3 páginas)
- [ ] **Accesibilidad:** `https://coordicanarias.com/areas/accesibilidad.php`
- [ ] **Aviso Legal:** `https://coordicanarias.com/areas/alegal.php`
- [ ] **Política de Cookies:** `https://coordicanarias.com/areas/politica-cookies.php`
- [ ] **Política de Privacidad:** `https://coordicanarias.com/areas/politica-privacidad.php`

---

### 3.2 Testing del Panel de Accesibilidad

Probar en **TODAS** las páginas (al menos 3 diferentes: index, empleo, transparencia):

#### 🔤 Tamaño de fuente
- [ ] Click en **A+** aumenta tamaño de texto
- [ ] Click en **A-** disminuye tamaño de texto
- [ ] Cambios persisten al recargar página (cookies funcionan)

#### 📖 Fuente legible
- [ ] Click en **Fuente legible** cambia tipografía
- [ ] Icono muestra estado activo (checkmark verde)
- [ ] Cambios persisten al recargar

#### 🔗 Subrayar enlaces
- [ ] Click en **Subrayar enlaces** subraya todos los enlaces
- [ ] Icono muestra estado activo
- [ ] Cambios persisten al recargar

#### ⚫⚪ Alto Contraste
- [ ] Click en **Alto Contraste** activa modo:
  - Fondo negro
  - Texto blanco
  - Enlaces amarillos
  - Logo Coordicanarias cambia a blanco
  - Logo Gobierno de Canarias cambia a blanco
  - Iconos SVG en blanco
  - Overlay oscuro en jumbotrons
- [ ] Valores en negrita (strong) se ven en amarillo
- [ ] Desactiva Modo Oscuro automáticamente si estaba activo
- [ ] Cambios persisten al recargar

#### 🌙 Modo Oscuro
- [ ] Click en **Modo Oscuro** activa modo:
  - Fondo gris oscuro (#1a1a1a)
  - Texto gris claro (#e0e0e0)
  - Enlaces en color tema
  - Logo Coordicanarias cambia a blanco
  - Overlay oscuro en jumbotrons
- [ ] Valores en negrita (strong) se ven en dorado
- [ ] Desactiva Alto Contraste automáticamente si estaba activo
- [ ] Cambios persisten al recargar

#### 🔊 Lector de Voz
- [ ] Click en **Lector de Voz** activa síntesis de voz
- [ ] Al pasar mouse sobre títulos (h1, h2, h3) lee el texto en español
- [ ] Al pasar mouse sobre párrafos lee el texto
- [ ] Al pasar mouse sobre botones lee el texto o aria-label
- [ ] Al pasar mouse sobre enlaces lee el texto o aria-label
- [ ] Al pasar mouse sobre valores con <strong> lee correctamente
- [ ] Voz se cancela al salir del elemento (mouseleave)
- [ ] Cambios persisten al recargar

#### 🔄 Reset
- [ ] Click en **Reset** desactiva todas las personalizaciones
- [ ] Vuelve a tamaño de fuente original
- [ ] Desactiva fuente legible
- [ ] Desactiva subrayado de enlaces
- [ ] Desactiva alto contraste
- [ ] Desactiva modo oscuro
- [ ] Desactiva lector de voz
- [ ] Elimina todas las cookies de accesibilidad

---

### 3.3 Testing de Formularios de Contacto

Probar formularios en **al menos 2 páginas diferentes** (ej: index.php y empleo.php):

#### 📧 Envío exitoso
- [ ] Rellenar formulario con datos válidos:
  - Nombre: `Prueba Deploy`
  - Email: `tu_email@coordicanarias.com`
  - Teléfono: `922123456`
  - Mensaje: `Esto es una prueba del formulario tras el deploy a producción`
- [ ] Click en **"Enviar mensaje"**
- [ ] **Verificar mensaje de éxito** en pantalla
- [ ] **Verificar que llega email** a `fhn@coordicanarias.com`
- [ ] **Verificar contenido del email:**
  - Asunto incluye área correcta
  - Datos del formulario presentes
  - Origen indica coordicanarias.com (NO /new/)

#### ❌ Validación de errores
- [ ] Intentar enviar formulario vacío → debe mostrar errores
- [ ] Intentar enviar con email inválido → debe mostrar error
- [ ] Intentar enviar con teléfono inválido → debe mostrar error

---

### 3.4 Testing del Panel de Administración

#### 🔐 Login
- [ ] Ir a: `https://coordicanarias.com/admin/`
- [ ] Debe redirigir a: `https://coordicanarias.com/admin/login.php`
- [ ] **Login con credenciales de admin:**
  - Usuario: `admin` (o el que tengas configurado)
  - Contraseña: `___________` (tu contraseña)
- [ ] Click en **"Iniciar sesión"**
- [ ] **Debe redirigir al dashboard:** `https://coordicanarias.com/admin/index.php`
- [ ] Verificar que aparece nombre de usuario en navbar

#### 📊 Dashboard
- [ ] **Estadísticas visibles:**
  - [ ] Número de proyectos
  - [ ] Número de servicios
  - [ ] Número de beneficios
  - [ ] Número de testimonios
  - [ ] Número de áreas
  - [ ] Número de usuarios
- [ ] **Gráficos funcionan** (si los hay)
- [ ] **Últimas actividades** se muestran (si las hay)

#### 🗂️ Módulos CRUD (verificar acceso)
- [ ] **Proyectos:** `https://coordicanarias.com/admin/proyectos.php`
  - [ ] Tabla de proyectos se carga
  - [ ] Botón "Nuevo proyecto" visible
  - [ ] Acciones (editar, eliminar) visibles

- [ ] **Servicios:** `https://coordicanarias.com/admin/servicios.php`
  - [ ] Tabla de servicios se carga
  - [ ] Selector de área funciona

- [ ] **Beneficios:** `https://coordicanarias.com/admin/beneficios.php`
  - [ ] Tabla de beneficios se carga

- [ ] **Testimonios:** `https://coordicanarias.com/admin/testimonios.php`
  - [ ] Tabla de testimonios se carga

- [ ] **Áreas:** `https://coordicanarias.com/admin/areas.php`
  - [ ] Tabla de 6 áreas se carga

- [ ] **Noticias:** `https://coordicanarias.com/admin/noticias.php`
  - [ ] Tabla de noticias se carga

- [ ] **Configuración:** `https://coordicanarias.com/admin/configuracion.php`
  - [ ] Formulario de configuración carga
  - [ ] Datos de contacto visibles

- [ ] **Usuarios:** `https://coordicanarias.com/admin/usuarios.php`
  - [ ] Solo accesible para rol admin
  - [ ] Tabla de usuarios se carga

- [ ] **Perfil:** `https://coordicanarias.com/admin/perfil.php`
  - [ ] Datos del usuario actual visibles
  - [ ] Formulario de cambio de contraseña visible

#### 🚪 Logout
- [ ] Click en **"Cerrar sesión"** en navbar
- [ ] Debe redirigir a login
- [ ] Intentar acceder a `https://coordicanarias.com/admin/` sin login
- [ ] Debe redirigir a login (protección funcionando)

---

### 3.5 Testing de Base de Datos

- [ ] Acceder a phpMyAdmin
- [ ] Seleccionar base de datos `coordica_crc`
- [ ] **Verificar tablas existentes:**
  - [ ] `proyectos`
  - [ ] `servicios`
  - [ ] `beneficios`
  - [ ] `testimonios`
  - [ ] `areas` (debe tener 6 registros)
  - [ ] `noticias`
  - [ ] `configuracion`
  - [ ] `usuarios` (al menos 1 usuario admin)
  - [ ] `registro_actividad` (log de acciones)
- [ ] **Verificar contenido** de al menos una tabla (ej: `areas`)
- [ ] **Probar una consulta:**
  ```sql
  SELECT * FROM areas WHERE activo = 1 ORDER BY orden;
  ```
- [ ] Debe devolver 6 áreas

---

### 3.6 Testing Cross-Browser

Probar al menos la homepage y una página de área en:

- [ ] **Google Chrome** (versión actual)
  - [ ] Sitio carga correctamente
  - [ ] Panel de accesibilidad funciona
  - [ ] Formulario funciona

- [ ] **Mozilla Firefox** (versión actual)
  - [ ] Sitio carga correctamente
  - [ ] Panel de accesibilidad funciona

- [ ] **Safari** (macOS/iOS)
  - [ ] Sitio carga correctamente
  - [ ] Panel de accesibilidad funciona

- [ ] **Microsoft Edge**
  - [ ] Sitio carga correctamente
  - [ ] Panel de accesibilidad funciona

---

### 3.7 Testing Responsive (Mobile)

Probar en al menos 2 dispositivos o usar Chrome DevTools (F12 → Toggle device toolbar):

- [ ] **iPhone (375px)**
  - [ ] Menú hamburguesa funciona
  - [ ] Panel de accesibilidad se ve correctamente
  - [ ] Texto legible
  - [ ] Imágenes se adaptan

- [ ] **iPad (768px)**
  - [ ] Layout tablet funciona
  - [ ] Panel de accesibilidad accesible

- [ ] **Android (360px)**
  - [ ] Sitio funciona correctamente

---

### 3.8 Testing de Seguridad Básica

- [ ] **Intentar acceder a archivos protegidos:**
  - [ ] `https://coordicanarias.com/php/config.php` → Debe mostrar código PHP vacío o error 403
  - [ ] `https://coordicanarias.com/database/schema.sql` → Debe dar error 403 o no encontrar
  - [ ] `https://coordicanarias.com/admin/.htaccess` → Debe dar error 403

- [ ] **Verificar headers de seguridad:**
  - Abrir Chrome DevTools (F12) → Network
  - Recargar página
  - Click en primer request (document)
  - Tab "Headers"
  - [ ] `X-Content-Type-Options: nosniff` presente
  - [ ] `X-Frame-Options: SAMEORIGIN` presente
  - [ ] `X-XSS-Protection: 1; mode=block` presente

- [ ] **HTTPS activo:**
  - [ ] Candado verde en navegador
  - [ ] Certificado SSL válido
  - [ ] No hay contenido mixto (HTTP en página HTTPS)

---

### 3.9 Testing de Rendimiento

- [ ] **Google PageSpeed Insights:** https://pagespeed.web.dev/
  - Analizar: `https://coordicanarias.com/`
  - [ ] Score Mobile > 70
  - [ ] Score Desktop > 80
  - [ ] No hay errores críticos

- [ ] **Tiempo de carga aceptable:**
  - [ ] Homepage carga en < 3 segundos
  - [ ] Imágenes optimizadas (no > 500KB cada una)

---

## 🔧 FASE 4: POST-DEPLOY

### 4.1 Verificación de URLs

- [ ] **Google Search Console** (si está configurado):
  - Verificar que no hay errores 404 nuevos
  - Solicitar re-indexación de homepage

- [ ] **Google Analytics** (si está configurado):
  - Verificar que el tracking code funciona
  - Ver "Tiempo real" → debe aparecer tu visita

- [ ] **Redes sociales:**
  - [ ] Verificar que links en Facebook apuntan a coordicanarias.com (no /new/)
  - [ ] Verificar que links en Twitter/X apuntan a coordicanarias.com
  - [ ] Verificar que links en LinkedIn apuntan a coordicanarias.com
  - [ ] Actualizar biografías si mencionan URL antigua

---

### 4.2 Limpieza (Opcional)

- [ ] **Si usaste OPCIÓN A (copiar archivos):**
  - [ ] Evaluar si eliminar `/new/` para liberar espacio
  - [ ] Si decides eliminarlo:
    ```bash
    # Backup final antes de eliminar
    cd /home/coordica/public_html
    tar -czf backup_new_before_delete.tar.gz new/
    # Eliminar
    rm -rf new/
    ```

- [ ] **Eliminar archivos de prueba:**
  - [ ] `test_base_path.php` (si lo creaste)
  - [ ] `maintenance.php` (si lo creaste)

- [ ] **Limpiar backups antiguos del servidor:**
  - Mantener solo 2-3 backups más recientes
  - Mover backups antiguos a tu ordenador

---

### 4.3 Documentación

- [ ] **Actualizar documentación interna:**
  - [ ] URLs en documentos internos (si los hay)
  - [ ] Manuales de usuario (si mencionan /new/)
  - [ ] Procedimientos internos

- [ ] **Comunicación:**
  - [ ] Notificar al equipo que el deploy se completó exitosamente
  - [ ] Enviar email a stakeholders (opcional)
  - [ ] Publicar en redes sociales (opcional): "¡Renovamos nuestra web!"

---

## 🆘 TROUBLESHOOTING

### Problema: "La página no se ve, sale en blanco"

**Solución:**
1. Verificar logs de errores PHP:
   ```bash
   tail -f /home/coordica/logs/error_log
   ```
2. Verificar permisos de archivos PHP (deben ser 644)
3. Verificar que `php/config.php` tiene las credenciales correctas
4. Verificar que la base de datos está accesible

---

### Problema: "Las imágenes no se ven (error 404)"

**Solución:**
1. Verificar que la carpeta `/images/` se copió correctamente
2. Verificar permisos de `/images/` (debe ser 755)
3. Verificar que las rutas en HTML son relativas (no absolutas con /new/)
4. Verificar que no hay `.htaccess` en `/images/` bloqueando acceso

---

### Problema: "El panel de accesibilidad no funciona"

**Solución:**
1. Abrir Chrome DevTools (F12) → Consola
2. Buscar errores de JavaScript
3. Verificar que `/js/main.js` se cargó correctamente
4. Verificar que la librería `js.cookie.min.js` se cargó
5. Limpiar caché del navegador y recargar (Ctrl+Shift+R)

---

### Problema: "Formulario de contacto no envía emails"

**Solución:**
1. Verificar configuración en `php/config.php`:
   - `EMAIL_METHOD` debe ser `'mail'`
   - `$emails_por_area` debe tener emails válidos
2. Verificar logs de PHP en hosting
3. Verificar que no hay restricciones de `mail()` en el servidor
4. Probar enviar email de prueba desde cPanel

---

### Problema: "No puedo acceder al panel admin"

**Solución:**
1. Verificar que la URL es correcta: `https://coordicanarias.com/admin/`
2. Verificar credenciales en base de datos:
   ```sql
   SELECT * FROM usuarios WHERE username = 'admin';
   ```
3. Verificar que la sesión funciona (comprobar que cookies están habilitadas)
4. Verificar `.htaccess` en `/admin/` se copió correctamente
5. Resetear contraseña si es necesario:
   ```sql
   UPDATE usuarios
   SET password_hash = '$2y$10$...'
   WHERE username = 'admin';
   ```
   (Generar hash con https://bcrypt-generator.com/)

---

### Problema: "Error de base de datos: Access denied"

**Solución:**
1. Verificar credenciales en `php/config.php`
2. Verificar que el usuario de BD existe en phpMyAdmin
3. Verificar que el usuario tiene permisos sobre la base de datos
4. Contactar con soporte del hosting si el problema persiste

---

### Problema: "El sitio funciona pero /new/ también sigue funcionando"

**Esto es normal si usaste OPCIÓN A (copiar archivos).**

**Soluciones:**
1. **Opción recomendada:** Dejar `/new/` como backup temporal (1 semana)
2. **Opción alternativa:** Eliminar `/new/` completamente (ver sección 4.2)
3. **Opción redirect:** Crear `.htaccess` en `/new/` que redirija a raíz:
   ```apache
   RewriteEngine On
   RewriteRule ^(.*)$ https://coordicanarias.com/$1 [R=301,L]
   ```

---

## ✅ CHECKLIST FINAL

Antes de dar por terminado el deploy, verificar:

- [ ] ✅ Homepage carga correctamente en coordicanarias.com
- [ ] ✅ Al menos 3 páginas diferentes probadas y funcionan
- [ ] ✅ Panel de accesibilidad funciona (al menos 3 funciones probadas)
- [ ] ✅ Formulario de contacto envía emails correctamente
- [ ] ✅ Panel admin accesible y funcional
- [ ] ✅ Base de datos conectada y funcionando
- [ ] ✅ No hay errores en consola del navegador
- [ ] ✅ Sitio responsive en mobile
- [ ] ✅ HTTPS activo con candado verde
- [ ] ✅ Backups guardados en lugar seguro
- [ ] ✅ Equipo notificado del deploy exitoso

---

## 📊 RESUMEN DE TIEMPOS

| Fase | Tiempo estimado |
|------|-----------------|
| 1. Pre-deploy (backups + verificación) | 30-45 min |
| 2. Deploy (movimiento de archivos) | 15-30 min |
| 3. Testing post-deploy | 60-90 min |
| 4. Post-deploy (limpieza + docs) | 15-30 min |
| **TOTAL** | **2-3 horas** |

**Nota:** Tiempo puede variar según familiaridad con el hosting y herramientas.

---

## 📞 CONTACTOS DE EMERGENCIA

En caso de problemas críticos durante el deploy:

- **Soporte del Hosting (Alojared):** [Número o email de soporte]
- **Backup de contacto técnico:** [Tu email o teléfono]
- **Tiempo de respuesta esperado:** [Indicar horario]

---

## 🎉 ¡DEPLOY COMPLETADO!

Si llegaste hasta aquí y todos los checks están marcados:

**🎊 ¡FELICIDADES! El sitio está oficialmente en producción en coordicanarias.com 🎊**

**Próximos pasos:**
1. Monitorear el sitio durante las próximas 24-48 horas
2. Revisar emails de formularios de contacto
3. Preparar implementación de Stripe + Bizum (siguiente fase)

---

**Última actualización:** 2026-01-10
**Versión del checklist:** 1.0
**Preparado por:** Claude AI para Coordicanarias
