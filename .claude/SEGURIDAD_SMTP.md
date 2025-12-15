# 🚨 PROBLEMA DE SEGURIDAD: Credenciales SMTP Expuestas en GitHub

**Fecha del incidente:** 15 de diciembre 2024, 14:49 UTC
**Detectado por:** GitGuardian
**Severidad:** ALTA - Acción inmediata requerida

---

## 📋 ¿Qué ocurrió?

Las credenciales SMTP de Google Workspace fueron subidas al repositorio público de GitHub en texto plano.

**Archivo afectado:** `php/enviar_correo.php`
**Línea problemática:**
```php
define('SMTP_PASS', 'vwudvopgcixmcsen');  // ← VISIBLE PÚBLICAMENTE
```

**Repositorio:** https://github.com/fhncoordi/coordiweb
**Commits afectados:** d08a8b2, edb2e1b

---

## ⚠️ Riesgos

1. **Cualquiera puede enviar correos** usando noreply@coordicanarias.com
2. **Posible uso para spam** dañando la reputación del dominio
3. **Violación de seguridad** de Google Workspace
4. **Acceso no autorizado** al sistema de correo

---

## ✅ SOLUCIÓN PASO A PASO

### PASO 1: REVOCAR CREDENCIALES (URGENTE - Hacer PRIMERO)

1. Ir a: https://myaccount.google.com/apppasswords
2. Iniciar sesión con: **noreply@coordicanarias.com**
3. Contraseña: **ul1N0rl@y**
4. Buscar la contraseña de aplicación creada
5. **ELIMINAR/REVOCAR** inmediatamente
6. Esto invalida `vwudvopgcixmcsen` en todos lados

### PASO 2: CREAR SISTEMA DE CONFIGURACIÓN SEGURO

#### 2.1. Crear archivo de configuración (NO se sube a git)

**Crear:** `php/config.php`
```php
<?php
/**
 * Archivo de configuración - NUNCA subir a git
 * Contiene credenciales sensibles
 */

// Configuración SMTP
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'noreply@coordicanarias.com');
define('SMTP_PASS', 'NUEVA_CONTRASEÑA_AQUI');  // Nueva contraseña de aplicación
define('SMTP_FROM_NAME', 'Coordicanarias - Formulario Web');

// Configuración de emails por área
$emails_por_area = array(
    'inicio'              => 'fhn@coordicanarias.com',
    'transparencia'       => 'fhn@coordicanarias.com',
    'formacion'           => 'fhn@coordicanarias.com',
    'empleo'              => 'fhn@coordicanarias.com',
    'accesibilidad'       => 'fhn@coordicanarias.com',
    'ocio'                => 'fhn@coordicanarias.com',
    'igualdad'            => 'fhn@coordicanarias.com',
    'aintegral'           => 'fhn@coordicanarias.com',
    'alegal'              => 'fhn@coordicanarias.com',
    'participacion'       => 'fhn@coordicanarias.com',
    'politica-cookies'    => 'fhn@coordicanarias.com',
    'politica-privacidad' => 'fhn@coordicanarias.com',
    'default'             => 'fhn@coordicanarias.com'
);
?>
```

#### 2.2. Crear archivo de ejemplo (SÍ se sube a git)

**Crear:** `php/config.example.php`
```php
<?php
/**
 * Archivo de ejemplo de configuración
 * Copiar a config.php y completar con credenciales reales
 */

// Configuración SMTP
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'noreply@coordicanarias.com');
define('SMTP_PASS', 'TU_CONTRASEÑA_DE_APLICACION');  // Generar en Google
define('SMTP_FROM_NAME', 'Coordicanarias - Formulario Web');

// Configuración de emails por área
$emails_por_area = array(
    'inicio'              => 'destino@ejemplo.com',
    // ... resto de áreas
    'default'             => 'destino@ejemplo.com'
);
?>
```

#### 2.3. Actualizar .gitignore

**Agregar a:** `.gitignore`
```
# Archivos de configuración con credenciales
php/config.php

# Archivos temporales
php/verificar_servidor.php
```

#### 2.4. Modificar enviar_correo.php

**Al inicio del archivo, después de los require de PHPMailer:**
```php
// Cargar configuración (credenciales NO en git)
require_once 'config.php';
```

**Eliminar las líneas:**
```php
// ============================================
// CONFIGURACIÓN DE SMTP (Google Workspace)
// ============================================
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'noreply@coordicanarias.com');
define('SMTP_PASS', 'vwudvopgcixmcsen');  // ← ELIMINAR ESTO
define('SMTP_FROM_NAME', 'Coordicanarias - Formulario Web');

// ============================================
// CONFIGURACIÓN DE EMAILS POR ÁREA
// ============================================
$emails_por_area = array(...);  // ← MOVER A config.php
```

### PASO 3: GENERAR NUEVA CONTRASEÑA

1. Ir a: https://myaccount.google.com/apppasswords
2. Seleccionar "Correo" → "Otro dispositivo"
3. Nombre: "Formulario Web v2"
4. Copiar la contraseña generada (16 caracteres)
5. Pegarla en `php/config.php` en `SMTP_PASS`

### PASO 4: LIMPIAR HISTORIAL DE GIT

**Opción A: Reescribir historial (avanzado)**
```bash
# Eliminar archivo del historial completo
git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch php/enviar_correo.php" \
  --prune-empty --tag-name-filter cat -- --all

# Forzar push
git push origin --force --all
```

**Opción B: Commit nuevo (más simple)**
```bash
# Hacer los cambios descritos arriba
git add .gitignore php/config.example.php php/enviar_correo.php
git commit -m "Mover credenciales SMTP a archivo de configuración separado"
git push
```

**IMPORTANTE:** Con la opción B, las credenciales antiguas quedan en el historial pero YA FUERON REVOCADAS, así que no hay riesgo.

### PASO 5: DESPLIEGUE AL SERVIDOR

1. Subir todos los archivos EXCEPTO `php/config.php`
2. En el servidor, crear `php/config.php` manualmente con la nueva contraseña
3. Configurar permisos: `chmod 600 php/config.php` (solo lectura para el owner)
4. Probar formulario

---

## 📚 Buenas Prácticas (Para el Futuro)

### ✅ HACER:
- Usar archivos de configuración separados
- Agregar archivos sensibles a .gitignore
- Usar variables de entorno cuando sea posible
- Revisar código antes de hacer commit
- Usar herramientas como git-secrets o pre-commit hooks

### ❌ NO HACER:
- Subir contraseñas a git
- Hardcodear credenciales en el código
- Compartir archivos de configuración en repositorios públicos
- Ignorar alertas de seguridad

---

## 🔗 Enlaces Útiles

- **Google App Passwords:** https://myaccount.google.com/apppasswords
- **GitGuardian Dashboard:** https://dashboard.gitguardian.com/
- **GitHub Security:** https://github.com/fhncoordi/coordiweb/security

---

## 📝 Checklist de Verificación

- [ ] Contraseña antigua revocada en Google
- [ ] Nueva contraseña generada
- [ ] Archivo config.php creado (local)
- [ ] Archivo config.example.php creado
- [ ] .gitignore actualizado
- [ ] enviar_correo.php modificado
- [ ] Commit y push realizados
- [ ] config.php subido al servidor (manualmente)
- [ ] Formulario probado y funcionando
- [ ] verificar_servidor.php eliminado del servidor

---

**Responsable:** Claude Code (Asistente IA)
**Usuario:** coordicanasser
**Próxima sesión:** Completar TODOS los pasos antes de cualquier otra tarea
