# Instrucciones de Integración - Sistema Anti-Bot

## Paso 1: Integrar validaciones en enviar_correo.php

### 1.1 Agregar require del sistema de seguridad

Después de la línea `require_once 'config.php';` (línea 19), agregar:

```php
// Cargar sistema de seguridad anti-bot
require_once 'security_antibot.php';
```

### 1.2 Agregar validaciones anti-bot

Después de la línea `if (!verificar_origen($dominios_permitidos)) {` (alrededor de línea 81), agregar este bloque ANTES de `// Detectar el área desde el formulario`:

```php
// ============================================
// VALIDACIONES ANTI-BOT
// ============================================

// Preparar datos del formulario para validación anti-bot
$datos_antibot = [
    'nombre' => $_POST['txtName'] ?? '',
    'email' => $_POST['txtEmail'] ?? '',
    'mensaje' => $_POST['txtMsg'] ?? '',
    'website' => $_POST['website'] ?? '',  // Honeypot
    'timestamp' => $_POST['form_timestamp'] ?? '',  // Tiempo de carga
    'csrf_token' => $_POST['csrf_token'] ?? '',  // Token CSRF
    'recaptcha_token' => $_POST['recaptcha_token'] ?? ''  // reCAPTCHA v3
];

// Ejecutar todas las validaciones anti-bot
$resultado_antibot = validar_antibot($datos_antibot);

// Si las validaciones anti-bot fallan, bloquear y registrar
if (!$resultado_antibot['valido']) {
    $errores_encoded = urlencode('Mensaje bloqueado por seguridad. Si crees que es un error, contacta por teléfono.');

    // Determinar la página de origen
    $pagina_origen = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.html';
    $pagina_origen = basename(parse_url($pagina_origen, PHP_URL_PATH));

    // Ajustar ruta si viene de areas/
    if (strpos($_SERVER['HTTP_REFERER'], '/areas/') !== false) {
        $pagina_origen = '../areas/' . $pagina_origen;
    } else {
        $pagina_origen = '../' . $pagina_origen;
    }

    // Log detallado del intento bloqueado (para debugging)
    error_log("Formulario bloqueado por anti-bot: " . json_encode($resultado_antibot));

    header("Location: $pagina_origen?error=" . $errores_encoded . "#contact");
    exit;
}
```

### 1.3 Agregar badge de seguridad en el email

En la sección donde se construye `$cuerpo_email`, después de la línea que contiene `<span class='area-badge'>` (alrededor de línea 224), agregar:

```php
// Agregar badge de seguridad si reCAPTCHA está activo
if (isset($resultado_antibot['scores']['recaptcha'])) {
    $score = $resultado_antibot['scores']['recaptcha'];
    $cuerpo_email .= "<span class='security-badge'>✓ Verificado (Score: " . number_format($score, 2) . ")</span>";
}
```

Y agregar este estilo CSS en el `<style>` del email (después del `.area-badge`):

```css
.security-badge {
    display: inline-block;
    background-color: #28a745;
    color: white;
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 11px;
    margin-left: 10px;
}
```

### 1.4 Agregar IP en el footer del email

En el footer del email (alrededor de línea 247), cambiar:

```php
Email recibido desde formulario de contacto | " . date('d/m/Y H:i:s') . "
```

Por:

```php
Email recibido desde formulario de contacto | " . date('d/m/Y H:i:s') . "<br>
IP: " . obtener_ip_cliente() . "
```

### 1.5 Limpiar rate limit tras envío exitoso

Después de `$email_enviado = true;` (líneas 298 y 334), agregar al final antes de la redirección:

```php
// Si el email se envió exitosamente, limpiar el rate limiter
if ($email_enviado) {
    limpiar_rate_limit_exitoso();
}
```

### 1.6 Desactivar modo debug de SMTP

Cambiar las líneas 265-266:

```php
// Activar modo debug (TEMPORAL - quitar después)
$mail->SMTPDebug = 3; // 0=sin debug, 1=cliente, 2=cliente+servidor, 3=detallado
$mail->Debugoutput = 'html'; // Mostrar en HTML formateado
```

Por:

```php
// Desactivar modo debug en producción
$mail->SMTPDebug = 0; // 0=sin debug, 1=cliente, 2=cliente+servidor, 3=detallado
```

---

## Paso 2: Crear directorio temporal

Crear el directorio para archivos temporales del rate limiter:

```bash
mkdir php/temp
chmod 755 php/temp
```

---

## Resumen de Archivos Creados

1. ✅ `php/security_antibot.php` - Sistema completo de validaciones
2. ✅ `php/form_security_helper.php` - Helper para generar campos en formularios
3. ✅ `js/form-security.js` - JavaScript para reCAPTCHA v3 y validaciones
4. ✅ `php/enviar_correo.php.backup` - Backup del archivo original
5. ✅ Este archivo de instrucciones

---

## Paso 3: Configurar reCAPTCHA v3

1. Ve a https://www.google.com/recaptcha/admin
2. Crea un nuevo sitio reCAPTCHA v3
3. Anota las claves (Site Key y Secret Key)
4. Edita `php/security_antibot.php` y actualiza las líneas 18-19:

```php
define('RECAPTCHA_SITE_KEY', 'TU_SITE_KEY_AQUI'); // PÚBLICO
define('RECAPTCHA_SECRET_KEY', 'TU_SECRET_KEY_AQUI'); // SECRETO
```

**IMPORTANTE**: Si no quieres usar reCAPTCHA todavía, puedes dejar las claves vacías. El sistema funcionará con las otras capas de seguridad (honeypot, rate limiting, etc.)

---

## Próximos Pasos

- [ ] Aplicar cambios en `enviar_correo.php` siguiendo este documento
- [ ] Crear directorio `php/temp`
- [ ] Configurar claves de reCAPTCHA (opcional pero recomendado)
- [ ] Actualizar formularios HTML (siguiente archivo de instrucciones)
- [ ] Probar el sistema

---

## Soporte

Si encuentras problemas:
1. Revisa los logs en `php/temp/spam_attempts.log`
2. Verifica que el directorio `php/temp` tenga permisos de escritura
3. Comprueba que todas las funciones estén siendo llamadas correctamente
