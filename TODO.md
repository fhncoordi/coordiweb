# TODO - Sistema CMS Coordicanarias

Plan de implementación del sistema de administración de contenido con MySQL + PHP puro.

**Plan completo:** `/Users/aquiles/.claude/plans/pure-wiggling-duckling.md`

---

## 📊 PROGRESO GENERAL

- [x] **FASE 1:** Infraestructura Base ✅ COMPLETADA (Días 1-5)
- [ ] **FASE 2:** Módulos CRUD (Días 6-12)
- [ ] **FASE 3:** Migración de Datos (Días 13-15)
- [ ] **FASE 4:** Conversión Frontend (Días 16-20)
- [ ] **FASE 5:** Pruebas y Deploy (Días 21-22)

---

## FASE 1: Infraestructura Base ✅ COMPLETADA

### Día 1-2: Base de datos
- [x] Crear base de datos MySQL remota en hosting (`coordica_crc`)
- [x] Crear `/database/schema.sql` con esquema completo
- [x] Ejecutar schema.sql en la BD
- [x] Configurar credenciales en `/php/config.php`
- [x] Crear `/php/db/connection.php` con conexión PDO Singleton
- [x] Probar conexión ✅ FUNCIONA

**Commit:** `52d4c09` - Base de datos creada y conexión PDO implementada

### Día 3-4: Autenticación ✅ COMPLETADO
- [x] Crear `/php/core/auth.php` (login/logout/sesiones/CSRF)
- [x] Crear `/php/core/security.php` (funciones de validación/sanitización)
- [x] Crear `/admin/login.php` con formulario (diseño del sitio)
- [x] Crear `/admin/logout.php`
- [x] Crear `/admin/index.php` temporal para probar
- [x] Implementar detección automática de rutas (BASE_PATH)
- [x] Crear `/admin/.htaccess` para evitar redirecciones de WordPress
- [x] Probar login con usuario `admin` en servidor ✅ FUNCIONA

**Archivos creados:**
- `/php/core/auth.php` - Sistema de autenticación completo
- `/php/core/security.php` - Funciones de seguridad
- `/admin/login.php` - Formulario de login
- `/admin/logout.php` - Cierre de sesión
- `/admin/index.php` - Dashboard temporal
- `/admin/.htaccess` - Protección contra redirecciones WP
- `/php/config.php` - Detección automática de BASE_PATH (actualizado)

**Seguridad implementada:**
- ✅ Password hashing (`password_hash()`)
- ✅ Sesiones seguras (httponly, samesite)
- ✅ Protección CSRF con tokens
- ✅ Protección session hijacking (IP + User Agent)
- ✅ Timeout de sesión (4 horas)
- ✅ Registro de actividad en BD
- ✅ Detección automática de rutas (funciona en /new/ y producción)

### Día 5: Panel base ✅ COMPLETADO
- [x] Crear `/admin/index.php` (dashboard con estadísticas)
- [x] Crear `/admin/includes/header.php` (navbar admin)
- [x] Crear `/admin/includes/footer.php`
- [x] Crear `/admin/includes/sidebar.php` (menú lateral)
- [x] Crear `/admin/assets/css/admin.css`

**Archivos creados:**
- `/admin/includes/header.php` - Navbar común con usuario y menú
- `/admin/includes/footer.php` - Footer común con scripts
- `/admin/includes/sidebar.php` - Menú lateral de navegación
- `/admin/assets/css/admin.css` - Estilos completos del panel (590 líneas)
- `/admin/index.php` - Dashboard con estadísticas reales de BD

**Características del Dashboard:**
- ✅ Estadísticas en tiempo real (proyectos, áreas, servicios, testimonios)
- ✅ Últimos proyectos creados
- ✅ Registro de actividad reciente (solo admin)
- ✅ Acciones rápidas para gestión de contenido
- ✅ Diseño responsive con sidebar colapsable
- ✅ Navegación por teclado y accesible
- ✅ Usa colores del sitio (#243659, #667eea)

---

## FASE 2: Módulos CRUD

### Día 8: Áreas ✅ COMPLETADO
- [x] Crear `/php/models/Area.php`
- [x] Crear `/admin/areas.php` (CRUD completo)
- [x] Implementar subida de imagen banner
- [x] Toggle activo/inactivo
- [x] Reordenar menú (Áreas como primer item)

**Archivos creados:**
- `/php/models/Area.php` - Modelo con métodos getAll, getById, update, validación
- `/admin/areas.php` - Vista CRUD (solo editar áreas existentes, no crear/eliminar)
- `/uploads/areas/` - Directorio para imágenes banner

**Características:**
- ✅ Edición de 6 áreas temáticas fijas
- ✅ Subida de imagen banner con validación
- ✅ Color picker para tema del área
- ✅ Slug auto-generado (editable)
- ✅ Campo orden para organización
- ✅ Toggle activo/inactivo

### Módulo Adicional: Noticias ✅ COMPLETADO
- [x] Agregar tabla `noticias` al schema.sql
- [x] Crear `/php/models/Noticia.php`
- [x] Crear `/admin/noticias.php` (CRUD completo)
- [x] Restaurar Testimonios en menú (coexisten)

**Archivos creados:**
- `database/schema.sql` - Tabla noticias agregada (9 tablas totales)
- `/php/models/Noticia.php` - Modelo CRUD completo con destacadas
- `/admin/noticias.php` - Vista CRUD: crear, editar, eliminar
- `/uploads/noticias/` - Directorio para imágenes destacadas

**Características:**
- ✅ CRUD completo (crear, editar, eliminar)
- ✅ Noticias destacadas (para homepage)
- ✅ Categorías reutilizables
- ✅ Slug auto-generado desde título
- ✅ Fecha de publicación y autor
- ✅ Soft delete (mantiene registros)

### Día 6-7: Proyectos (PRIORIDAD ALTA) ✅ COMPLETADO
- [x] Crear `/php/models/Proyecto.php` (getAll, getById, create, update, delete)
- [x] Crear `/admin/proyectos.php`:
  - [x] Vista de listado con tabla
  - [x] Formulario de creación
  - [x] Formulario de edición
  - [x] Función de eliminación (soft delete)
  - [x] Sistema de subida de imágenes
- [x] Crear directorio `/uploads/proyectos/` con permisos 755
- [x] Migrar 16 proyectos existentes desde HTML a BD

**Archivos creados:**
- `/php/models/Proyecto.php` - Modelo CRUD completo con validación
- `/admin/proyectos.php` - Vista CRUD: crear, editar, eliminar, toggle
- `/database/migration_proyectos.sql` - Migración de 16 proyectos
- `/uploads/proyectos/` - Directorio para imágenes

**Características:**
- ✅ CRUD completo (crear, editar, eliminar soft delete)
- ✅ Toggle activo/inactivo y destacado
- ✅ Subida de imágenes con validación (JPG, PNG, GIF, WEBP, max 5MB)
- ✅ Selector de área temática
- ✅ Categorías para filtros (comma-separated)
- ✅ Campo orden para organización
- ✅ 16 proyectos migrados desde HTML

**Commit:** `048c8e4` - Módulo CRUD de Proyectos completado

### Día 8: Áreas
- [ ] Crear `/php/models/Area.php`
- [ ] Crear `/admin/areas.php` (CRUD completo)

### Día 9: Servicios
- [ ] Crear `/php/models/Servicio.php`
- [ ] Crear `/admin/servicios.php` (CRUD con selector de área)

### Día 10: Beneficios
- [ ] Crear `/php/models/Beneficio.php`
- [ ] Crear `/admin/beneficios.php` (CRUD con selector de área)

### Día 11: Testimonios
- [ ] Crear `/php/models/Testimonio.php`
- [ ] Crear `/admin/testimonios.php` (CRUD con subida de foto)

### Día 12: Configuración y Usuarios
- [ ] Crear `/php/models/Configuracion.php`
- [ ] Crear `/admin/configuracion.php` (editar contacto)
- [ ] Crear `/php/models/Usuario.php`
- [ ] Crear `/admin/usuarios.php` (CRUD, solo rol admin)

---

## FASE 3: Migración de Datos

### Día 13-14: Script de migración
- [x] Crear `/database/migration_proyectos.sql` con INSERT de 16 proyectos desde HTML ✅
- [x] Ejecutar migration script de proyectos en BD ✅
- [ ] Crear `/database/migration_servicios.sql` con INSERT de servicios de las 6 áreas
- [ ] Crear `/database/migration_beneficios.sql` con INSERT de beneficios de las 6 áreas
- [ ] Crear `/database/migration_testimonios.sql` con INSERT de testimonios
- [ ] Crear `/database/migration_configuracion.sql` con INSERT de configuración de contacto

### Día 15: Migrar imágenes
- [ ] Copiar `/images/portfolio/*` a `/uploads/proyectos/`
- [ ] Actualizar rutas en BD si es necesario
- [ ] Crear `/uploads/testimonios/` y `/uploads/areas/`

---

## FASE 4: Conversión Frontend

### Día 16: index.php
- [ ] Backup: `cp index.html index.html.backup`
- [ ] Renombrar: `mv index.html index.php`
- [ ] Agregar includes PHP al inicio
- [ ] Reemplazar sección de proyectos (líneas 593-750)
- [ ] Reemplazar información de contacto (línea 1159+)
- [ ] Probar accesibilidad con lector de pantalla

### Día 17-19: Páginas de áreas
- [ ] Convertir `empleo.html` → `empleo.php`
- [ ] Convertir `forminno.html` → `forminno.php`
- [ ] Convertir `aintegral.html` → `aintegral.php`
- [ ] Convertir `igualdadpm.html` → `igualdadpm.php`
- [ ] Convertir `ocio.html` → `ocio.php`
- [ ] Convertir `participaca.html` → `participaca.php`

### Día 20: .htaccess y pruebas
- [ ] Crear `.htaccess` con:
  - [ ] Redirección .html → .php (compatibilidad)
  - [ ] Protección de archivos sensibles
  - [ ] Compresión y cache
- [ ] Actualizar enlaces internos si es necesario
- [ ] Pruebas completas de navegación

---

## FASE 5: Pruebas y Deploy

### Día 21: Pruebas finales
- [ ] Probar todos los CRUDs (crear, leer, actualizar, eliminar)
- [ ] Probar sistema de login/logout
- [ ] Probar roles (admin vs editor)
- [ ] Probar subida de imágenes
- [ ] Probar frontend dinámico (proyectos, servicios, beneficios)
- [ ] Verificar que formulario de contacto sigue funcionando
- [ ] Verificar accesibilidad WCAG 2.2 AA con validador
- [ ] Pruebas de seguridad (intentar SQL injection, XSS)
- [ ] Pruebas en Chrome, Firefox, Safari, Edge

### Día 22: Deploy a producción
- [ ] Backup completo del sitio actual
- [ ] Subir archivos vía FTP/SFTP
- [ ] Verificar permisos de archivos
- [ ] Cambiar contraseña del usuario admin
- [ ] Crear usuario adicional para editor
- [ ] Monitorear logs de errores
- [ ] Probar en producción

---

## 📝 NOTAS IMPORTANTES

### Archivos críticos ya creados:
- ✅ `/database/schema.sql` - Esquema de BD con 8 tablas
- ✅ `/php/db/connection.php` - Conexión PDO Singleton segura
- ✅ `/php/config.php` - Credenciales BD + SMTP (NO en git)
- ✅ `/php/core/auth.php` - Sistema de autenticación
- ✅ `/php/core/security.php` - Funciones de seguridad
- ✅ `/admin/login.php` - Formulario de login
- ✅ `/admin/logout.php` - Cierre de sesión
- ✅ `/admin/index.php` - Dashboard temporal

### Estructura de BD:
- `usuarios` - Roles: admin, editor
- `areas` - 6 áreas temáticas
- `proyectos` - Proyectos destacados
- `servicios` - Servicios por área
- `beneficios` - Beneficios por área
- `testimonios` - Casos de éxito
- `configuracion` - Config general del sitio
- `registro_actividad` - Log de acciones admin

### Seguridad implementada:
- ✅ PDO con prepared statements
- ✅ Singleton pattern para conexión
- ✅ Credenciales en archivo no trackeado
- ✅ CSRF tokens implementados
- ✅ Password hashing implementado
- ✅ Sesiones seguras implementadas
- ✅ Detección automática de rutas (BASE_PATH)
- ✅ Protección contra redirecciones de WordPress

---

## 🎯 PRÓXIMO PASO

**Continuar con Fase 2 - Día 9: Módulo CRUD de Servicios**

Archivos a crear:
1. `/php/models/Servicio.php` - Modelo con métodos CRUD
2. `/admin/servicios.php` - Vista CRUD completa:
   - Listado con tabla agrupada por área
   - Formulario de creación con selector de área
   - Formulario de edición
   - Sistema de iconos (Font Awesome)
   - Soft delete (activo = 0)
   - Ordenamiento manual por área

**Características a implementar:**
- ✅ Selector de área (foreign key)
- ✅ Campo icono para Font Awesome (ej: "fa-briefcase")
- ✅ Campo orden para organización dentro del área
- ✅ Toggle activo/inactivo
- ✅ Validación de área existente

**Opciones:**
- Podemos migrar servicios existentes desde HTMLs de áreas después de crear el CRUD
- O crear el CRUD primero y luego hacer la migración

---

*Última actualización: 2026-01-03 - Módulos completados: Áreas, Noticias, Proyectos*
