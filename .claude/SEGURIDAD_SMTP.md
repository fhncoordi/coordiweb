# 🚨 PROBLEMA DE SEGURIDAD: Credenciales SMTP Expuestas en GitHub

**Fecha del incidente:** 15 de diciembre 2024, 14:49 UTC
**Detectado por:** GitGuardian
**Severidad:** ALTA - Acción inmediata requerida

## ✅ ESTADO ACTUAL (15 dic 2024, 15:00)

**PASO 1 COMPLETADO:** Contraseña antigua revocada ✓
- La contraseña expuesta (`vwudvopgcixmcsen`) ha sido revocada
- Ya NO hay riesgo inmediato de uso no autorizado
- Pendiente: Completar pasos 2-5 para solución completa

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

- [x] **Contraseña antigua revocada en Google** ✅ COMPLETADO (15 dic 2024)
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

## 🔄 ACTUALIZACIÓN: Estado Completo (15 dic 2024, 19:00 UTC)

### ✅ REMEDIACIÓN COMPLETADA

**PASOS 1-4 COMPLETADOS:**
- [x] Contraseña antigua revocada
- [x] Sistema de configuración seguro creado (`config.php`)
- [x] Archivo de ejemplo creado (`config.example.php`)
- [x] .gitignore actualizado
- [x] enviar_correo.php modificado para usar config.php
- [x] Historial de git limpiado (force push realizado)
- [x] Commits realizados y pusheados

### 🚨 NUEVO PROBLEMA DESCUBIERTO: Puertos SMTP Bloqueados

**Diagnóstico técnico confirmado:**
```
Puerto 465 (SSL):      ✗ BLOQUEADO (Connection refused - código 111)
Puerto 587 (STARTTLS): ✗ BLOQUEADO (Connection refused - código 111)
mail() nativa:         ⚠️ Ejecuta pero NO entrega emails
```

**Causa:** El firewall del servidor (Alojared) bloquea las conexiones salientes SMTP.

**Impacto:** No es posible usar Google Workspace SMTP desde el servidor actual.

**Acción tomada:** Ticket abierto con Alojared solicitando habilitación de puertos 465/587.

### 🔧 SOLUCIÓN TEMPORAL IMPLEMENTADA

Mientras Alojared habilita los puertos SMTP:

**Configuración actual:**
```php
// En config.php
define('EMAIL_METHOD', 'mail'); // Solo mail() - SMTP bloqueado
```

**Sistema implementado:**
- `enviar_correo.php` ahora soporta 3 métodos: 'smtp', 'mail', 'smtp_with_fallback'
- Actualmente configurado en modo 'mail' (función nativa PHP)
- Cuando Alojared habilite puertos, cambiar a 'smtp' o 'smtp_with_fallback'

**Limitaciones de mail():**
- ⚠️ Emails pueden no llegar (o ir a spam)
- ⚠️ No usa noreply@coordicanarias.com como remitente real
- ⚠️ Depende de configuración del servidor de correo local

---

## 📋 INSTRUCCIONES PARA MAÑANA (Retomar en otro equipo)

### 🔄 Sincronización después del Force Push

**IMPORTANTE:** Se hizo force push al repositorio porque se limpió el historial de git.

#### En tu equipo de la oficina, ejecuta:

```bash
cd /ruta/al/proyecto/coordicanarias

# 1. Guardar cambios locales si hay (opcional)
git stash

# 2. Obtener la última versión del servidor
git fetch origin

# 3. Resetear tu rama local al estado del servidor
git reset --hard origin/main

# 4. Si guardaste cambios, restaurarlos (opcional)
git stash pop

# 5. Verificar que todo está sincronizado
git log --oneline -5
```

**Deberías ver estos commits recientes:**
```
6afa206 Implementar sistema de fallback con mail() nativa de PHP
93f6737 Actualizar verificar_servidor.php para probar puerto 465
86ce279 Cambiar SMTP a puerto 465 (SSL) para evitar bloqueo del hosting
2e9c36c Implementar sistema de configuración segura para credenciales SMTP
d4752ad Actualizar estado: contraseña SMTP revocada
```

### 📁 Archivos Importantes (NO están en git)

**Archivo local que debes tener:**
- `php/config.php` - Contiene la contraseña SMTP (16 caracteres sin espacios)

**Si no tienes este archivo en el otro equipo:**
1. Copia `php/config.example.php` → `php/config.php`
2. Edita `php/config.php` y configura:
   ```php
   define('EMAIL_METHOD', 'mail'); // Temporal hasta que Alojared habilite SMTP
   define('SMTP_PASS', 'qdrwydhemyrmdhuo'); // Sin espacios
   ```

### 🧹 Archivos de Test en el Servidor (ELIMINAR)

**Elimina estos archivos del servidor por seguridad:**
- `php/test_email.php`
- `php/test_formulario.php`
- `php/test_smtp_directo.php`
- `php/verificar_servidor.php`

Puedes eliminarlos vía FTP/SFTP o desde cPanel → Administrador de Archivos.

---

## 🎫 SEGUIMIENTO TICKET ALOJARED

**Estado:** Abierto - Esperando respuesta

**Ticket enviado:** 15 diciembre 2024

**Solicitud:**
- Habilitar puertos SMTP salientes: 465 (SSL) o 587 (STARTTLS)
- Destino: smtp.gmail.com
- Motivo: Envío de emails corporativos desde noreply@coordicanarias.com

**Tiempo estimado de respuesta:** 1-3 días hábiles

### Cuando Alojared responda:

#### ✅ Si habilitan los puertos:

1. Edita `php/config.php`:
   ```php
   define('EMAIL_METHOD', 'smtp'); // Cambiar de 'mail' a 'smtp'
   ```

2. Sube `php/config.php` al servidor

3. Prueba el formulario - debería funcionar perfectamente con Google Workspace

#### ❌ Si NO pueden habilitar los puertos:

**Opciones alternativas:**

**A) Configurar servidor de correo local correctamente**
- Solicitar configuración de SPF/DKIM en el dominio
- Configurar sendmail/exim correctamente
- Riesgo: Emails pueden ir a spam de todas formas

**B) Usar servicio de relay SMTP externo**
- SendGrid (gratis hasta 100 emails/día): https://sendgrid.com
- Mailgun (gratis hasta 5,000 emails/mes): https://mailgun.com
- Configurar API key en lugar de SMTP

**C) Cambiar de hosting**
- Hostings que permiten SMTP: SiteGround, DigitalOcean, AWS, etc.

---

## 📝 Checklist Actualizado

### Seguridad SMTP
- [x] Contraseña antigua revocada ✅
- [x] Nueva contraseña generada (qdrwydhemyrmdhuo) ✅
- [x] Archivo config.php creado ✅
- [x] Archivo config.example.php creado ✅
- [x] .gitignore actualizado ✅
- [x] enviar_correo.php modificado ✅
- [x] Historial de git limpiado ✅
- [x] Commits realizados ✅
- [x] index.html corregido (comentarios HTML) ✅

### Configuración Servidor
- [x] config.php subido al servidor ✅
- [x] Diagnóstico de puertos SMTP realizado ✅
- [x] Ticket abierto con Alojared ✅
- [ ] **PENDIENTE:** Respuesta de Alojared sobre puertos SMTP
- [ ] **PENDIENTE:** Eliminar archivos de test del servidor
- [ ] **PENDIENTE:** Verificar funcionamiento completo del formulario

### Formularios
- [x] Sistema de fallback implementado ✅
- [x] Formularios cargando en páginas de /areas/ ✅
- [ ] **PENDIENTE:** Confirmar que emails llegan (aunque sea a spam)

---

## 🔐 Información Sensible (Solo para uso interno)

**Credenciales Google Workspace:**
- Usuario: noreply@coordicanarias.com
- Contraseña cuenta: ul1N0rl@y
- Contraseña aplicación actual: qdrwydhemyrmdhuo (16 caracteres sin espacios)

**⚠️ IMPORTANTE:**
- NO compartir estas credenciales
- NO subirlas a git
- NO incluirlas en capturas de pantalla públicas

---

**Responsable:** Claude Code (Asistente IA)
**Usuario:** Aquiles (coordicanarias)
**Última actualización:** 15 diciembre 2024, 19:00 UTC
**Próxima sesión:**
1. Sincronizar repositorio en equipo de oficina
2. Verificar respuesta de Alojared
3. Eliminar archivos de test del servidor
4. Probar formularios
