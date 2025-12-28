# TODO - Sistema CMS Coordicanarias

Plan de implementación del sistema de administración de contenido con MySQL + PHP puro.

**Plan completo:** `/Users/aquiles/.claude/plans/pure-wiggling-duckling.md`

---

## 📊 PROGRESO GENERAL

- [x] **FASE 1:** Infraestructura Base ✅ COMPLETADA
- [ ] **FASE 2:** Módulos CRUD
- [ ] **FASE 3:** Migración de Datos
- [ ] **FASE 4:** Conversión Frontend
- [ ] **FASE 5:** Pruebas y Deploy

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
- [ ] Probar login con usuario `admin` en servidor

**Archivos creados:**
- `/php/core/auth.php` - Sistema de autenticación completo
- `/php/core/security.php` - Funciones de seguridad
- `/admin/login.php` - Formulario de login
- `/admin/logout.php` - Cierre de sesión
- `/admin/index.php` - Dashboard temporal

**Seguridad implementada:**
- ✅ Password hashing (`password_hash()`)
- ✅ Sesiones seguras (httponly, samesite)
- ✅ Protección CSRF con tokens
- ✅ Protección session hijacking (IP + User Agent)
- ✅ Timeout de sesión (4 horas)
- ✅ Registro de actividad en BD

### Día 5: Panel base
- [ ] Crear `/admin/index.php` (dashboard con estadísticas)
- [ ] Crear `/admin/includes/header.php` (navbar admin)
- [ ] Crear `/admin/includes/footer.php`
- [ ] Crear `/admin/includes/sidebar.php` (menú lateral)
- [ ] Crear `/admin/assets/css/admin.css`

---

## FASE 2: Módulos CRUD

### Día 6-7: Proyectos (PRIORIDAD ALTA)
- [ ] Crear `/php/models/Proyecto.php` (getAll, getById, create, update, delete)
- [ ] Crear `/admin/proyectos.php`:
  - [ ] Vista de listado con tabla
  - [ ] Formulario de creación
  - [ ] Formulario de edición
  - [ ] Función de eliminación (soft delete)
  - [ ] Sistema de subida de imágenes
- [ ] Crear directorio `/uploads/proyectos/` con permisos 755

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
- [ ] Crear `/database/migration_data.sql` con:
  - [ ] INSERT de 23 proyectos desde index.html
  - [ ] INSERT de servicios de las 6 áreas (extraer de HTMLs)
  - [ ] INSERT de beneficios de las 6 áreas
  - [ ] INSERT de 2 testimonios actuales
  - [ ] INSERT de configuración de contacto
- [ ] Ejecutar migration script en BD

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
- ⏳ CSRF tokens (próxima fase)
- ⏳ Password hashing (próxima fase)
- ⏳ Sesiones seguras (próxima fase)

---

## 🎯 PRÓXIMO PASO

**Continuar con Fase 1 - Día 3-4: Sistema de Autenticación**

Archivos a crear:
1. `/php/auth.php`
2. `/php/security.php`
3. `/admin/login.php`
4. `/admin/logout.php`

---

*Última actualización: 2025-12-28 - Sistema de autenticación completado*
