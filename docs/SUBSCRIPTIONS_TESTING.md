# Guía de Testing - Sistema de Suscripciones (Socios)
## Coordicanarias - 5€/mes

**Fecha:** 2026-01-10
**Estado:** ✅ Implementación completada - Listo para testing

---

## 📋 Resumen de Implementación

Se ha completado el sistema de suscripciones recurrentes para socios de Coordicanarias:

### ✅ Archivos Creados

1. **Base de Datos:**
   - `/database/create_table_socios.sql` - Tabla de socios (✅ Ejecutado en phpMyAdmin)

2. **Backend Stripe:**
   - `/stripe/create-subscription-session.php` - Crea sesiones de suscripción
   - `/stripe/subscription-success.php` - Página de bienvenida al socio
   - `/stripe/subscription-cancel.php` - Página de cancelación
   - `/stripe/webhook.php` - Actualizado para manejar eventos de suscripciones
   - `/stripe/manage-subscription.php` - Portal de gestión para socios
   - `/stripe/create-portal-session.php` - Crea sesión del Billing Portal de Stripe

3. **Frontend:**
   - `/index.php` - Modal "Hacerte Socio" agregado en sección Colabora

4. **Panel Admin:**
   - `/admin/socios.php` - Visualizar y gestionar socios
   - `/admin/guardar-notas-socio.php` - Guardar notas admin

5. **Configuración:**
   - Producto en Stripe: "Socio de Coordicanarias" - 5€/mes
   - Price ID: `price_1SoAfyLhc0iibDcCLkcC0VcG`

---

## 🧪 Plan de Testing

### Fase 1: Verificación de Base de Datos

#### 1.1 Verificar Tabla Socios
```sql
USE coordica_crc;
SHOW TABLES LIKE 'socios';
DESCRIBE socios;
SELECT * FROM socios LIMIT 5;
```

**Resultado esperado:**
- Tabla existe con 17 columnas
- Campos clave: `id`, `stripe_customer_id`, `stripe_subscription_id`, `nombre`, `email`, `estado`, `fecha_inicio`, `fecha_proximo_cobro`

---

### Fase 2: Testing Frontend

#### 2.1 Verificar Botón "Asóciate ahora"
1. Abrir `https://coordicanarias.com/index.php#colabora`
2. Scroll hasta la sección "Tu contribución, nuestro impulso"
3. Localizar la tarjeta "Socios" con "5 € mensuales"
4. ✅ Verificar botón "Asóciate ahora" está presente

#### 2.2 Probar Modal de Suscripción
1. Hacer clic en "Asóciate ahora"
2. ✅ Verificar que se abre el modal
3. ✅ Verificar contenido del modal:
   - Título: "Hacerte Socio de Coordicanarias"
   - Precio destacado: "5 €" con "al mes"
   - Lista de ventajas de ser socio
   - Campo "Nombre completo" (obligatorio)
   - Campo "Email" (obligatorio)
   - Campo "Teléfono" (opcional)
   - Botón "Continuar al pago"

#### 2.3 Validaciones del Formulario
Probar cada validación:

1. **Nombre vacío:**
   - Dejar nombre vacío
   - Clic en "Continuar al pago"
   - ✅ Debe mostrar: "Por favor, ingresa tu nombre completo"

2. **Email vacío:**
   - Dejar email vacío
   - ✅ Debe mostrar: "Por favor, ingresa tu email"

3. **Email inválido:**
   - Ingresar "test" (sin @)
   - ✅ Debe mostrar: "Por favor, ingresa un email válido"

4. **Email duplicado (socio existente):**
   - Ingresar email de un socio ya activo
   - ✅ Debe mostrar: "Este email ya tiene una suscripción activa"

---

### Fase 3: Testing de Suscripción (Stripe Test Mode)

#### 3.1 Crear Suscripción de Prueba

**Datos de prueba:**
```
Nombre: Test Socio
Email: test.socio@coordicanarias.com
Teléfono: 922 123 456 (opcional)
```

**Pasos:**
1. Completar el formulario con los datos de arriba
2. Hacer clic en "Continuar al pago"
3. ✅ Verificar que aparece el spinner de carga
4. ✅ Verificar redirección a Stripe Checkout

#### 3.2 Probar en Stripe Checkout

URL de Stripe Checkout debe ser algo como:
```
https://checkout.stripe.com/c/pay/cs_test_...
```

**Verificar que el checkout muestra:**
- ✅ Producto: "Socio de Coordicanarias"
- ✅ Precio: 5,00 € / mes
- ✅ Email pre-rellenado

**Probar diferentes tarjetas de prueba:**

1. **Suscripción exitosa:**
   ```
   Tarjeta: 4242 4242 4242 4242
   Fecha: Cualquier fecha futura (ej: 12/34)
   CVC: Cualquier 3 dígitos (ej: 123)
   ZIP: Cualquier código postal
   ```
   - ✅ Debe redirigir a `/stripe/subscription-success.php`
   - ✅ Debe mostrar mensaje "¡Bienvenido a la Familia!"
   - ✅ Debe mostrar:
     - Estado: "✓ Activa" o "🎁 En período de prueba"
     - Importe mensual: 5,00 €
     - Próximo cobro: (fecha dentro de 30 días)
     - Email de contacto
   - ✅ Botón "Gestionar mi suscripción"
   - ✅ Verificar en BD: estado = `active` o `trialing`

2. **Suscripción con tarjeta declinada:**
   ```
   Tarjeta: 4000 0000 0000 0002
   ```
   - ✅ Stripe debe mostrar error "Your card was declined"
   - ✅ NO debe crear suscripción activa
   - ✅ BD puede tener registro con estado `incomplete`

3. **Tarjeta que requiere autenticación 3D Secure:**
   ```
   Tarjeta: 4000 0027 6000 3184
   ```
   - ✅ Debe mostrar modal de autenticación
   - ✅ Hacer clic en "Complete authentication"
   - ✅ Debe completarse exitosamente

4. **Cancelación de suscripción:**
   - En Stripe Checkout, hacer clic en "← Back"
   - ✅ Debe redirigir a `/stripe/subscription-cancel.php`
   - ✅ Debe mostrar "Suscripción No Completada"
   - ✅ Botones: "Contactar", "Intentar de nuevo", "Volver al inicio"

---

### Fase 4: Testing de Base de Datos

#### 4.1 Verificar Registro de Suscripción
```sql
SELECT * FROM socios ORDER BY fecha_creacion DESC LIMIT 5;
```

**Verificar que se guardan correctamente:**
- ✅ `stripe_customer_id` (cus_xxxxx)
- ✅ `stripe_subscription_id` (sub_xxxxx)
- ✅ `nombre`, `email`, `telefono`
- ✅ `estado` = 'active' o 'trialing'
- ✅ `fecha_inicio` (fecha actual)
- ✅ `fecha_proximo_cobro` (dentro de 30 días)
- ✅ `fecha_creacion`

---

### Fase 5: Testing del Portal de Gestión

#### 5.1 Acceder al Portal
1. Ir a `https://coordicanarias.com/stripe/manage-subscription.php`
2. ✅ Debe mostrar formulario pidiendo email
3. Ingresar email del socio de prueba
4. ✅ Debe mostrar panel con información del socio:
   - Estado con badge de color
   - Importe mensual: 5,00 €
   - Socio desde: (fecha)
   - Próximo cobro: (fecha)
   - Email

#### 5.2 Acceder al Billing Portal de Stripe
1. Hacer clic en "Acceder al Portal de Gestión"
2. ✅ Debe redirigir al Billing Portal de Stripe
3. ✅ En el portal debe poder:
   - Ver método de pago actual
   - Actualizar tarjeta
   - Ver facturas pasadas
   - Cancelar suscripción

#### 5.3 Cancelar Suscripción
1. En el Billing Portal, hacer clic en "Cancel subscription"
2. Confirmar cancelación
3. ✅ Debe volver a manage-subscription.php
4. ✅ Estado debe cambiar a "✗ Cancelada"
5. ✅ Verificar en BD: `estado = 'canceled'`, `fecha_cancelacion` actualizada
6. ✅ Debe mostrar botón "Volver a suscribirme"

---

### Fase 6: Testing del Panel Admin

#### 6.1 Login Admin
1. Ir a `https://coordicanarias.com/admin/login.php`
2. Usar credenciales de admin
3. ✅ Debe redirigir al dashboard

#### 6.2 Acceder al Panel de Socios
1. Ir a `https://coordicanarias.com/admin/socios.php`
2. ✅ Verificar estadísticas superiores:
   - Total Socios
   - Socios Activos
   - Ingresos Mensuales (activos × 5€)
   - Con Problemas de Pago

3. ✅ Verificar tabla de socios muestra:
   - ID, Fecha Alta, Nombre, Email, Teléfono
   - Estado con badge de color
   - Próximo Cobro
   - Botón "Ver en Stripe"
   - Botón "Notas"

#### 6.3 Filtros
1. **Filtrar por estado:**
   - Seleccionar "Activos"
   - Hacer clic en "Filtrar"
   - ✅ Solo debe mostrar socios activos

2. **Filtrar por fecha:**
   - Desde: Hoy
   - Hasta: Hoy
   - ✅ Solo debe mostrar socios dados de alta hoy

3. **Limpiar filtros:**
   - Hacer clic en "Limpiar"
   - ✅ Debe mostrar todos los socios

#### 6.4 Ver en Stripe
1. Hacer clic en "Ver en Stripe"
2. ✅ Debe abrir nueva pestaña con el Dashboard de Stripe
3. ✅ Debe mostrar detalles de la suscripción

#### 6.5 Notas Admin
1. Hacer clic en "Notas"
2. Agregar texto: "Socio de prueba - Testing system"
3. Hacer clic en "Guardar Notas"
4. ✅ Debe guardar correctamente
5. ✅ Recargar página y verificar que las notas persisten

---

### Fase 7: Testing de Webhooks

#### 7.1 Configurar Webhook en Stripe

**IMPORTANTE:** El webhook ya existe de donaciones, solo necesitas agregar eventos de suscripciones.

1. Ir a: https://dashboard.stripe.com/test/webhooks
2. Buscar el endpoint existente: `https://coordicanarias.com/stripe/webhook.php`
3. Hacer clic en "..." → "Update details"
4. Agregar eventos:
   - `customer.subscription.created`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
   - `invoice.payment_succeeded`
   - `invoice.payment_failed`
5. Guardar

#### 7.2 Probar Webhook
1. Hacer una suscripción de prueba
2. Ir a Stripe Dashboard > Webhooks > Ver el webhook
3. ✅ Debe aparecer eventos:
   - `checkout.session.completed` (modo subscription)
   - `customer.subscription.created`
   - `invoice.payment_succeeded`
4. ✅ Todos los eventos deben tener estado "Succeeded"

#### 7.3 Simular Eventos
En Stripe Dashboard > Webhooks > Testing:
1. Seleccionar evento `invoice.payment_succeeded`
2. Hacer clic en "Send test webhook"
3. ✅ Debe responder con HTTP 200
4. ✅ Verificar que actualiza `ultima_factura_pagada` en BD

---

## 🐛 Troubleshooting

### Error: "Could not connect to database"
**Solución:** Verificar credenciales en `/php/config.php`

### Error: "Stripe API key not found"
**Solución:** Verificar que las claves TEST están definidas en config.php

### Modal de suscripción no se abre
**Solución:**
1. Verificar que Bootstrap JS está cargado
2. Abrir consola del navegador (F12) y buscar errores

### Redirección a Stripe falla
**Solución:**
1. Verificar en consola del navegador
2. Verificar que el endpoint `/stripe/create-subscription-session.php` responde con JSON
3. Verificar que el Price ID es correcto: `price_1SoAfyLhc0iibDcCLkcC0VcG`

### Suscripción no se actualiza después del pago
**Causas posibles:**
1. Webhook no configurado → La página `subscription-success.php` también actualiza el estado
2. Error en success.php → Verificar logs de errores de PHP
3. Price ID incorrecto → Verificar en Stripe Dashboard

### Portal de Gestión no funciona
**Solución:**
1. Verificar que el Billing Portal está activado en Stripe
2. Ir a: https://dashboard.stripe.com/test/settings/billing/portal
3. Activar "Customer Portal"
4. Configurar qué acciones pueden hacer los clientes (cancelar, actualizar pago, ver facturas)

---

## ✅ Checklist de Testing

Antes de pasar a producción, completar:

- [ ] Probar al menos 3 suscripciones de prueba exitosas
- [ ] Probar cancelación de suscripción
- [ ] Probar validaciones del formulario de suscripción
- [ ] Verificar que las suscripciones se guardan en BD correctamente
- [ ] Probar el portal de gestión del socio
- [ ] Probar cancelación desde Billing Portal
- [ ] Verificar que la cancelación se refleja en BD
- [ ] Probar el panel admin de socios
- [ ] Probar filtros del panel admin
- [ ] Probar guardar notas admin
- [ ] Configurar webhooks en Stripe
- [ ] Probar eventos de webhooks
- [ ] Verificar que los cobros mensuales funcionan (simular con test clock de Stripe)
- [ ] Documentar cambios realizados

---

## 🚀 Paso a Producción (CUANDO ESTÉ LISTO)

### 1. Configuración Previa en Stripe
1. Ir a: https://dashboard.stripe.com/settings/billing/portal
2. Activar "Customer Portal" en modo LIVE
3. Configurar opciones:
   - ✅ Permitir cancelar suscripción
   - ✅ Permitir actualizar método de pago
   - ✅ Permitir ver facturas
   - ✅ Configurar mensajes personalizados

### 2. Activar Modo LIVE
En `/php/config.php`:
```php
define('STRIPE_MODE', 'live'); // ← Cambiar de 'test' a 'live'
```

### 3. Habilitar Bizum (Opcional)
1. Ir a: https://dashboard.stripe.com/settings/payment_methods
2. Activar "Bizum"
3. Completar verificación de cuenta española
4. Actualizar `/stripe/create-subscription-session.php`:
   ```php
   'payment_method_types' => ['card', 'sepa_debit'], // Agregar métodos
   ```

### 4. Configurar Webhooks LIVE
1. URL: `https://coordicanarias.com/stripe/webhook.php`
2. Copiar signing secret a `/php/config.php`:
   ```php
   define('STRIPE_LIVE_WEBHOOK_SECRET', 'whsec_tu_secret_aqui');
   ```

### 5. Actualizar Modal en index.php
Cambiar mensaje de métodos de pago (línea ~1534):
```html
<div class="alert alert-success" role="alert">
    <strong>Métodos de pago disponibles:</strong> Tarjeta de crédito/débito, Bizum
</div>
```

### 6. Probar con Suscripción Real Pequeña
- Hacer una suscripción real con tu propia tarjeta
- Verificar que todo funciona correctamente
- Cancelar inmediatamente si es solo prueba

---

## 📊 Métricas a Monitorear

Después del lanzamiento:

1. **Conversión:**
   - ¿Cuántas personas abren el modal de socio?
   - ¿Cuántas completan la suscripción?
   - Tasa de abandono en Stripe Checkout

2. **Retención:**
   - % de socios que permanecen activos después de:
     - 1 mes
     - 3 meses
     - 6 meses
     - 1 año
   - Tasa de cancelación mensual (churn rate)

3. **Pagos:**
   - % de pagos exitosos vs fallidos
   - Socios en estado `past_due` (problema de pago)
   - Tiempo promedio en resolver pagos fallidos

4. **Ingresos:**
   - Ingresos mensuales recurrentes (MRR)
   - Evolución del MRR mes a mes
   - Proyección anual de ingresos

---

## 📝 Diferencias: Donaciones vs Suscripciones

| Característica | Donaciones | Suscripciones (Socios) |
|----------------|------------|------------------------|
| **Tipo** | Pago único | Pago recurrente mensual |
| **Importe** | Variable (1€ - 10,000€) | Fijo (5€/mes) |
| **Stripe Mode** | `payment` | `subscription` |
| **Tabla BD** | `donaciones` | `socios` |
| **Estados** | pending, completed, failed, refunded | active, trialing, past_due, canceled, incomplete, unpaid |
| **Gestión** | Solo admin | Admin + Portal de cliente |
| **Cancelación** | No aplica | Cliente puede cancelar cuando quiera |
| **Facturas** | Una sola | Mensualmente |
| **Webhooks** | payment_intent.*, checkout.session.completed | customer.subscription.*, invoice.* |

---

## 📞 Contacto y Soporte

Si encuentras problemas durante el testing:

1. **Logs de PHP:** Verificar `/var/log/php_errors.log` o error log del hosting
2. **Logs de Stripe:** Dashboard > Developers > Logs
3. **Consola del navegador:** F12 > Console
4. **Estado de Stripe:** https://status.stripe.com/

---

## 🎯 Próximos Pasos Sugeridos (Opcional)

Después de que el sistema funcione correctamente:

1. **Email automático de bienvenida** al nuevo socio
2. **Email de recordatorio** cuando falla un pago
3. **Email de agradecimiento** en cada cobro mensual
4. **Certificado de socio** descargable en PDF
5. **Beneficios exclusivos** para socios en el sitio web
6. **Newsletter mensual** solo para socios
7. **Descuentos en eventos** para socios
8. **Sección "Nuestros Socios"** en el sitio web (con permiso)

---

**Estado del documento:** Actualizado al 2026-01-10
**Próxima revisión:** Después del testing completo

**Archivos relacionados:**
- `STRIPE_TESTING.md` - Guía de testing de donaciones
- `create_table_socios.sql` - Schema de la tabla socios
- Código fuente en `/stripe/` y `/admin/socios.php`
