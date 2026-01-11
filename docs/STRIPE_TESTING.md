# Guía de Testing - Sistema de Donaciones Stripe
## Coordicanarias

**Fecha:** 2026-01-10
**Estado:** ✅ Implementación completada - Listo para testing

---

## 📋 Resumen de Implementación

Se ha completado la integración de Stripe para el sistema de donaciones:

### ✅ Archivos Creados

1. **Base de Datos:**
   - `/database/create_table_donaciones.sql` - Tabla de donaciones (✅ Ejecutado)

2. **Backend Stripe:**
   - `/stripe/create-checkout-session.php` - Crea sesiones de pago
   - `/stripe/success.php` - Página de confirmación
   - `/stripe/cancel.php` - Página de cancelación
   - `/stripe/webhook.php` - Procesa eventos de Stripe

3. **Frontend:**
   - `/index.php` - Sección "Colabora" activada con modal de donación

4. **Panel Admin:**
   - `/admin/donaciones.php` - Visualizar y gestionar donaciones

5. **Configuración:**
   - `/php/config.php` - Claves de API de Stripe configuradas

---

## 🧪 Plan de Testing

### Fase 1: Verificación de Configuración

#### 1.1 Verificar Base de Datos
```sql
-- Ejecutar en MySQL
USE coordica_crc;
SHOW TABLES LIKE 'donaciones';
DESCRIBE donaciones;
```

**Resultado esperado:** La tabla debe existir con 22 columnas

#### 1.2 Verificar Archivos PHP
```bash
ls -la /Users/aquiles/Documents/coordi/stripe/
ls -la /Users/aquiles/Documents/coordi/admin/donaciones.php
```

**Resultado esperado:** Todos los archivos deben existir

#### 1.3 Verificar Claves de API
Abrir `/php/config.php` y verificar que:
- `STRIPE_TEST_PUBLISHABLE_KEY` está configurado
- `STRIPE_TEST_SECRET_KEY` está configurado
- `STRIPE_MODE` está en `'test'`

---

### Fase 2: Testing Frontend

#### 2.1 Verificar Sección "Colabora"
1. Abrir `https://coordicanarias.com/index.php`
2. Hacer scroll hasta la sección "Tu contribución, nuestro impulso"
3. Verificar que se muestran 4 opciones:
   - ✅ Donaciones (con botón "Dona ahora")
   - Colaboraciones
   - Socios (5€/mes)
   - Voluntariado

#### 2.2 Probar Modal de Donación
1. Hacer clic en el botón **"Dona ahora"**
2. Verificar que se abre el modal correctamente
3. Verificar que aparecen:
   - ✅ 4 botones de importe (10€, 25€, 50€, 100€)
   - ✅ Campo de importe personalizado
   - ✅ Campo Nombre
   - ✅ Campo Email
   - ✅ Campo Mensaje (opcional)
   - ✅ Checkbox "Donación anónima"
   - ✅ Información de métodos de pago (Tarjeta y Bizum)

#### 2.3 Validaciones del Formulario
Probar cada validación:

1. **Importe vacío:**
   - Dejar el importe vacío
   - Clic en "Proceder al pago"
   - ✅ Debe mostrar: "Por favor, ingresa un importe válido (mínimo 1€)"

2. **Importe menor a 1€:**
   - Ingresar 0.50
   - ✅ Debe mostrar error

3. **Importe mayor a 10,000€:**
   - Ingresar 15000
   - ✅ Debe mostrar: "El importe máximo permitido es 10,000€"

4. **Email inválido:**
   - Ingresar "test" (sin @)
   - ✅ El navegador debe mostrar validación HTML5

5. **Campos obligatorios vacíos:**
   - Dejar nombre o email vacíos
   - ✅ Debe mostrar: "Por favor, completa todos los campos obligatorios"

---

### Fase 3: Testing de Pago (Stripe Test Mode)

#### 3.1 Crear Donación de Prueba

**Datos de prueba:**
```
Importe: 25€
Nombre: Test Usuario
Email: test@coordicanarias.com
Mensaje: Donación de prueba
Anónimo: No
```

**Pasos:**
1. Completar el formulario con los datos de arriba
2. Hacer clic en "Proceder al pago"
3. ✅ Verificar que aparece el spinner de carga
4. ✅ Verificar redirección a Stripe Checkout

#### 3.2 Probar en Stripe Checkout

URL de Stripe Checkout debe ser algo como:
```
https://checkout.stripe.com/c/pay/cs_test_...
```

**Probar diferentes tarjetas de prueba:**

1. **Pago exitoso:**
   ```
   Tarjeta: 4242 4242 4242 4242
   Fecha: Cualquier fecha futura (ej: 12/34)
   CVC: Cualquier 3 dígitos (ej: 123)
   ZIP: Cualquier código postal
   ```
   - ✅ Debe redirigir a `/stripe/success.php`
   - ✅ Debe mostrar mensaje "¡Pago Completado!"
   - ✅ Debe mostrar detalles de la donación
   - ✅ Verificar en BD que el estado es `completed`

2. **Pago rechazado (tarjeta declinada):**
   ```
   Tarjeta: 4000 0000 0000 0002
   ```
   - ✅ Stripe debe mostrar error "Your card was declined"
   - ✅ NO debe crear entrada en BD o debe quedar en `pending`

3. **Pago con autenticación 3D Secure:**
   ```
   Tarjeta: 4000 0027 6000 3184
   ```
   - ✅ Debe mostrar modal de autenticación
   - ✅ Hacer clic en "Complete authentication"
   - ✅ Debe completarse exitosamente

4. **Cancelación de pago:**
   - En Stripe Checkout, hacer clic en "← Back" (arriba a la izquierda)
   - ✅ Debe redirigir a `/stripe/cancel.php`
   - ✅ Debe mostrar "Pago Cancelado"
   - ✅ Debe mostrar botones "Intentar de nuevo" y "Volver al inicio"

#### 3.3 Probar Bizum (Solo en producción)

⚠️ **IMPORTANTE:** Bizum solo funciona en modo LIVE con cuenta española verificada.

En modo TEST, Bizum NO estará disponible en Stripe Checkout.

---

### Fase 4: Testing de Base de Datos

#### 4.1 Verificar Registro de Donación
```sql
SELECT * FROM donaciones ORDER BY fecha_creacion DESC LIMIT 5;
```

**Verificar que se guardan correctamente:**
- ✅ `stripe_session_id` (cs_test_xxxxx)
- ✅ `nombre`, `email`, `importe`
- ✅ `estado` = 'pending' al crear, 'completed' al pagar
- ✅ `stripe_payment_intent_id` se llena después del pago
- ✅ `metodo_pago` = 'card' (o 'bizum' en producción)
- ✅ `fecha_creacion` y `fecha_completado`
- ✅ `mensaje` y `es_anonimo`

---

### Fase 5: Testing del Panel Admin

#### 5.1 Login
1. Ir a `https://coordicanarias.com/admin/login.php`
2. Usar credenciales:
   ```
   Usuario: admin
   Contraseña: Admin2025!
   ```
3. ✅ Debe redirigir a `/admin/donaciones.php`

#### 5.2 Visualización de Donaciones
1. Verificar estadísticas superiores:
   - ✅ Total Donaciones
   - ✅ Total Recaudado
   - ✅ Completadas, Pendientes, Fallidas

2. Verificar tabla de donaciones:
   - ✅ ID, Fecha, Donante, Email, Importe
   - ✅ Método de pago con badge de color
   - ✅ Estado con badge de color (verde=completed, amarillo=pending)
   - ✅ Botón "Ver mensaje" (si hay mensaje)
   - ✅ Botón "Ver en Stripe" (enlace a dashboard de Stripe)

#### 5.3 Filtros
1. **Filtrar por estado:**
   - Seleccionar "Completadas"
   - Hacer clic en "Filtrar"
   - ✅ Solo debe mostrar donaciones completadas

2. **Filtrar por fecha:**
   - Desde: Hoy
   - Hasta: Hoy
   - ✅ Solo debe mostrar donaciones de hoy

3. **Limpiar filtros:**
   - Hacer clic en "Limpiar"
   - ✅ Debe mostrar todas las donaciones

---

### Fase 6: Testing de Webhooks (Opcional avanzado)

#### 6.1 Configurar Webhook en Stripe

1. Ir a: https://dashboard.stripe.com/test/webhooks
2. Hacer clic en "Add endpoint"
3. Endpoint URL: `https://coordicanarias.com/stripe/webhook.php`
4. Eventos a escuchar:
   - `checkout.session.completed`
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
   - `charge.refunded`
5. Copiar el "Signing secret" (empieza con `whsec_`)
6. Pegarlo en `/php/config.php`:
   ```php
   define('STRIPE_TEST_WEBHOOK_SECRET', 'whsec_tu_secret_aqui');
   ```

#### 6.2 Probar Webhook
1. Hacer una donación de prueba
2. Ir a Stripe Dashboard > Webhooks > Ver el webhook
3. ✅ Debe aparecer el evento `checkout.session.completed`
4. ✅ Estado debe ser "Succeeded"

#### 6.3 Simular Eventos
En Stripe Dashboard > Webhooks > Testing:
1. Seleccionar evento `checkout.session.completed`
2. Hacer clic en "Send test webhook"
3. ✅ Debe responder con HTTP 200

---

## 🐛 Troubleshooting

### Error: "Could not connect to database"
**Solución:** Verificar credenciales en `/php/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'coordica_crc');
define('DB_USER', 'coordica_crc');
define('DB_PASS', 'tu_password');
```

### Error: "Stripe API key not found"
**Solución:** Verificar que las claves están definidas en `/php/config.php`

### Modal no se abre al hacer clic en "Dona ahora"
**Solución:** Verificar que Bootstrap JS está cargado:
```html
<script src="js/bootstrap.bundle.min.js"></script>
```

### Redirección a Stripe falla
**Solución:**
1. Abrir consola del navegador (F12)
2. Buscar errores en la pestaña "Console" o "Network"
3. Verificar que el endpoint `/stripe/create-checkout-session.php` responde con JSON

### Donación no se marca como "completed" después del pago
**Causas posibles:**
1. Webhook no configurado → Solución: La página `success.php` también actualiza el estado
2. Error en success.php → Verificar logs de errores de PHP

---

## ✅ Checklist Final

Antes de pasar a producción:

- [ ] Probar al menos 3 donaciones de prueba exitosas
- [ ] Probar cancelación de pago
- [ ] Probar validaciones del formulario
- [ ] Verificar que las donaciones se guardan en BD correctamente
- [ ] Probar el panel admin
- [ ] Probar filtros del panel admin
- [ ] Cambiar contraseña de admin en `/admin/login.php`
- [ ] Configurar webhook en Stripe
- [ ] Documentar cambios realizados

---

## 🚀 Paso a Producción (CUANDO ESTÉ LISTO)

### 1. Obtener Claves LIVE de Stripe
1. Ir a: https://dashboard.stripe.com/account/apikeys
2. Copiar:
   - Publishable key: `pk_live_...`
   - Secret key: `sk_live_...`

### 2. Actualizar config.php
```php
define('STRIPE_LIVE_PUBLISHABLE_KEY', 'pk_live_tu_clave_aqui');
define('STRIPE_LIVE_SECRET_KEY', 'sk_live_tu_clave_aqui');
define('STRIPE_MODE', 'live'); // ← Cambiar de 'test' a 'live'
```

### 3. Habilitar Bizum en Stripe
1. Ir a: https://dashboard.stripe.com/settings/payment_methods
2. Activar "Bizum"
3. Completar verificación de cuenta española

### 4. Configurar Webhook LIVE
- URL: `https://coordicanarias.com/stripe/webhook.php`
- Copiar signing secret a `STRIPE_LIVE_WEBHOOK_SECRET`

### 5. Probar con Donación Real Pequeña
- Hacer una donación de 1€ real
- Verificar que todo funciona correctamente
- Reembolsar si es necesario

---

## 📊 Métricas a Monitorear

Después del lanzamiento:

1. **Conversión:**
   - ¿Cuántas personas abren el modal?
   - ¿Cuántas completan la donación?
   - Tasa de abandono en Stripe Checkout

2. **Métodos de pago:**
   - % Tarjeta vs % Bizum
   - Identificar el método preferido

3. **Importes:**
   - Donación promedio
   - Importes más populares (10€, 25€, 50€, 100€, personalizado)

4. **Errores:**
   - Pagos fallidos (tarjetas rechazadas)
   - Errores técnicos en logs de PHP

---

## 📞 Contacto y Soporte

Si encuentras problemas durante el testing:

1. **Logs de PHP:** Verificar `/var/log/php_errors.log` o similar
2. **Logs de Stripe:** Dashboard > Developers > Logs
3. **Consola del navegador:** F12 > Console
4. **Estado de Stripe:** https://status.stripe.com/

---

## 📝 Notas Adicionales

### Consideraciones de Seguridad
✅ Las claves de Stripe están en `config.php` (no versionado en git)
✅ Las tarjetas son procesadas por Stripe (nunca tocan tu servidor)
✅ Validaciones tanto client-side como server-side
✅ Prepared statements para prevenir SQL injection

### Consideraciones de Accesibilidad
✅ Modal accesible con roles ARIA
✅ Labels asociados a inputs
✅ Mensajes de error descriptivos
✅ Navegación por teclado funcional

### Consideraciones de UX
✅ Botones de importe predefinidos para facilitar selección
✅ Validación en tiempo real
✅ Spinner de carga mientras redirige
✅ Mensajes claros de éxito/error
✅ Opción de donación anónima

---

**Estado del documento:** Actualizado al 2026-01-10
**Próxima revisión:** Después del testing completo
