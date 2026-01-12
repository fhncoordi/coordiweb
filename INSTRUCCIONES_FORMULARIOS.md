# Instrucciones para Actualizar Formularios HTML

## Archivos que contienen formularios de contacto

Necesitas actualizar estos archivos:
- `index.php` (formulario principal)
- Archivos en `areas/*.php` (cada área tiene su formulario)
- Cualquier otro archivo con formularios de contacto

---

## Cambios Necesarios en Cada Archivo

### 1. Agregar helper PHP al inicio del archivo

Al principio del archivo PHP (después de los require existentes), agregar:

```php
<?php
// Si no existe, agregar al inicio del archivo
require_once __DIR__ . '/php/form_security_helper.php';
// O si estás en una subcarpeta (areas/):
require_once __DIR__ . '/../php/form_security_helper.php';
?>
```

### 2. Agregar scripts en el `<head>`

Dentro del `<head>` del documento, agregar:

```php
<!-- reCAPTCHA v3 -->
<?php echo generar_script_recaptcha(); ?>

<!-- JavaScript de seguridad de formularios -->
<script>
    // Pasar la clave de reCAPTCHA al JavaScript
    window.RECAPTCHA_SITE_KEY = '<?php echo obtener_recaptcha_site_key(); ?>';
</script>
```

### 3. Agregar script de seguridad antes del cierre de `</body>`

Antes del cierre de `</body>`, agregar:

```html
<!-- Script de seguridad de formularios -->
<script src="<?= url('js/form-security.js') ?>"></script>
```

O si no existe la función `url()`, usar ruta relativa:

```html
<!-- Script de seguridad de formularios -->
<script src="/js/form-security.js"></script>
<!-- O en subcarpetas: -->
<script src="../js/form-security.js"></script>
```

### 4. Actualizar el formulario de contacto

#### ANTES (formulario actual):
```html
<form action="<?= url('php/enviar_correo.php') ?>" method="POST" id="contactForm">
    <!-- Campo oculto para identificar el área -->
    <input type="hidden" name="area" value="inicio">

    <label for="fname">Nombre:</label>
    <input type="text" id="fname" name="txtName" placeholder="Tu nombre y apellidos" title="FirstName" required />

    <label for="email">Email:</label>
    <input type="email" id="email" name="txtEmail" placeholder="Tu correo electrónico" title="Email" required />

    <label for="subject">Mensaje:</label>
    <textarea id="subject" name="txtMsg" placeholder="Tu mensaje" title="Message" style="height:200px" required></textarea>

    <input type="submit" value="Enviar">
</form>
```

#### DESPUÉS (formulario con seguridad):
```html
<form action="<?= url('php/enviar_correo.php') ?>" method="POST" id="contactForm">
    <!-- Campo oculto para identificar el área -->
    <input type="hidden" name="area" value="inicio">

    <!-- CAMPOS DE SEGURIDAD ANTI-BOT -->
    <?php echo generar_campos_seguridad(); ?>
    <!-- FIN CAMPOS DE SEGURIDAD -->

    <label for="fname">Nombre:</label>
    <input type="text" id="fname" name="txtName" placeholder="Tu nombre y apellidos" title="FirstName" required />

    <label for="email">Email:</label>
    <input type="email" id="email" name="txtEmail" placeholder="Tu correo electrónico" title="Email" required />

    <label for="subject">Mensaje:</label>
    <textarea id="subject" name="txtMsg" placeholder="Tu mensaje" title="Message" style="height:200px" required></textarea>

    <input type="submit" value="Enviar">
</form>
```

---

## Ejemplo Completo: index.php

Aquí está cómo debería quedar la sección del formulario en `index.php`:

### En el `<head>` (agregar):

```php
<!-- reCAPTCHA v3 -->
<?php echo generar_script_recaptcha(); ?>

<!-- Configuración para JavaScript -->
<script>
    window.RECAPTCHA_SITE_KEY = '<?php echo obtener_recaptcha_site_key(); ?>';
</script>
```

### Formulario de contacto (líneas 1107-1123):

```html
<div class="contact-form">
    <form action="<?= url('php/enviar_correo.php') ?>" method="POST" id="contactForm">
        <!-- Campo oculto para identificar el área -->
        <input type="hidden" name="area" value="inicio">

        <!-- CAMPOS DE SEGURIDAD ANTI-BOT -->
        <?php echo generar_campos_seguridad(); ?>
        <!-- FIN CAMPOS DE SEGURIDAD -->

        <label for="fname">Nombre:</label>
        <input type="text" id="fname" name="txtName" placeholder="Tu nombre y apellidos" title="FirstName" required />

        <label for="email">Email:</label>
        <input type="email" id="email" name="txtEmail" placeholder="Tu correo electrónico" title="Email" required />

        <label for="subject">Mensaje:</label>
        <textarea id="subject" name="txtMsg" placeholder="Tu mensaje" title="Message" style="height:200px" required></textarea>

        <input type="submit" value="Enviar">
    </form>
</div>
```

### Antes del cierre de `</body>` (agregar):

```html
<!-- Script de seguridad de formularios -->
<script src="<?= url('js/form-security.js') ?>"></script>
```

---

## Para archivos en `areas/*.php`

Si estás en un subdirectorio (como `areas/accesibilidad.php`), ajusta las rutas:

### Helper PHP:
```php
<?php
require_once __DIR__ . '/../php/form_security_helper.php';
?>
```

### Script JS:
```html
<script src="../js/form-security.js"></script>
```

---

## Verificación

Para verificar que todo funciona correctamente:

1. **Inspecciona el HTML generado** - Deberías ver:
   - Un campo honeypot invisible (`name="website"`)
   - Un campo `form_timestamp`
   - Un campo `csrf_token`
   - Un campo `recaptcha_token`

2. **Revisa la consola del navegador** - No debería haber errores de JavaScript

3. **Prueba el formulario** - Debería:
   - Mostrar "Verificando..." al enviar
   - Enviar correctamente si eres humano
   - Bloquear si intentas enviar muy rápido (<3 segundos)

---

## Resumen de Cambios por Archivo

### index.php
- [ ] Agregar `require_once` del helper al inicio
- [ ] Agregar script de reCAPTCHA en el `<head>`
- [ ] Agregar `<?php echo generar_campos_seguridad(); ?>` en el formulario
- [ ] Agregar `<script src="js/form-security.js"></script>` antes de `</body>`

### areas/*.php (cada archivo)
- [ ] Agregar `require_once` del helper al inicio (con `../ `para subir un nivel)
- [ ] Agregar script de reCAPTCHA en el `<head>`
- [ ] Agregar `<?php echo generar_campos_seguridad(); ?>` en el formulario
- [ ] Agregar `<script src="../js/form-security.js"></script>` antes de `</body>`

---

## Notas Importantes

1. **El helper genera 4 campos ocultos**:
   - `website` (honeypot - trampa para bots)
   - `form_timestamp` (para validar tiempo de envío)
   - `csrf_token` (protección contra CSRF)
   - `recaptcha_token` (se llenará con JavaScript)

2. **No es necesario cambiar `enviar_correo.php`** - Las validaciones se aplicarán automáticamente

3. **Compatibilidad**: Si reCAPTCHA no está configurado, el sistema seguirá funcionando con las otras capas de seguridad

4. **Testing**: Después de aplicar los cambios, prueba enviando un formulario para verificar que funciona

---

## Solución de Problemas

**Error: "Call to undefined function generar_campos_seguridad()"**
- Verifica que agregaste el `require_once` del helper al inicio del archivo

**El formulario se envía pero no aparece reCAPTCHA**
- Verifica que agregaste el script de reCAPTCHA en el `<head>`
- Revisa la consola del navegador por errores

**Todos los envíos son bloqueados**
- Revisa `php/temp/spam_attempts.log` para ver el motivo del bloqueo
- Verifica que el directorio `php/temp` tenga permisos de escritura

---

## ¿Necesitas Ayuda?

Si prefieres, puedo actualizar los archivos por ti. Solo dime qué archivos necesitas que actualice.
