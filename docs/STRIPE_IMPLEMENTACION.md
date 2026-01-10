# Implementación de Stripe para Donaciones - Coordicanarias

**Versión:** 1.0
**Fecha:** 2026-01-04
**Autor:** Claude AI
**Para:** Sistema de donaciones y membresías mensuales

---

## 📖 Índice

1. [Introducción](#introducción)
2. [¿Por qué Stripe?](#por-qué-stripe)
3. [Comparativa de Pasarelas](#comparativa-de-pasarelas)
4. [Precios en España](#precios-en-españa)
5. [Cómo Habilitar Bizum](#cómo-habilitar-bizum)
6. [Requisitos Previos](#requisitos-previos)
7. [Tutorial de Implementación](#tutorial-de-implementación)
8. [Código Completo](#código-completo)
9. [Configuración de Webhooks](#configuración-de-webhooks)
10. [Testing](#testing)
11. [Paso a Producción](#paso-a-producción)
12. [Migración de Cuenta Bancaria](#migración-de-cuenta-bancaria)
13. [FAQ - Preguntas Frecuentes](#faq---preguntas-frecuentes)
14. [Troubleshooting](#troubleshooting)
15. [Consideraciones Legales](#consideraciones-legales)

---

## Introducción

Este documento describe la implementación completa de **Stripe** como pasarela de pago para el sistema de donaciones de Coordicanarias.

### ¿Qué vamos a implementar?

La sección **"Colabora"** del sitio web (actualmente comentada en `index.php` líneas 1120-1280) incluye:

1. **Donaciones únicas** - Importes predefinidos (10€, 25€, 50€, 100€) o personalizado
2. **Socios mensuales** - Membresía recurrente de 5€/mes
3. **Colaboraciones empresariales** - Contacto directo (no requiere pago online)
4. **Voluntariado** - Formulario de contacto (no requiere pago online)

**Alcance de esta implementación:**
- ✅ Donaciones únicas con Stripe Checkout
- ✅ Base de datos para registrar donaciones
- ✅ Emails de confirmación automáticos
- ✅ Panel admin para ver donaciones
- 🔜 **Fase 2** (futuro): Membresías recurrentes con Stripe Subscriptions

---

## ¿Por qué Stripe?

### ✅ Ventajas

1. **Fácil implementación** - API moderna y bien documentada
2. **Seguridad PCI DSS** - Stripe maneja los datos de tarjeta (tú nunca los tocas)
3. **Checkout alojado** - Página de pago en dominio de Stripe (menos responsabilidad)
4. **Sin cuotas mensuales** - Solo pagas por transacción
5. **Dashboard completo** - Panel web para ver todas las transacciones
6. **Webhooks automáticos** - Notificaciones en tiempo real de pagos
7. **Modo test/live** - Ambiente de pruebas completo sin necesidad de dinero real
8. **Métodos de pago** - Tarjetas, Bizum, SEPA Direct Debit, Google Pay, Apple Pay
9. **Sin periodo de permanencia** - Puedes cancelar cuando quieras
10. **Bizum integrado** - 38% de españoles prefieren Bizum, transacciones en <10 segundos

### ❌ Desventajas

1. **Comisiones relativamente altas** - 1.5% + 0.25€ por transacción en EEA
2. **Pagos en USD si no configuras bien** - Hay que configurar EUR como divisa
3. **Requiere verificación de cuenta** - Puede tardar 1-3 días

---

## Comparativa de Pasarelas

| Característica | Stripe | PayPal | Redsys | TPV Bancario |
|----------------|--------|--------|--------|--------------|
| **Comisión tarjeta** | 1.5% + 0.25€ | 2.99% + 0.35€ | 1.0-1.5% + 0.25€ | 0.5-1.5% |
| **Bizum** | ✅ Sí | ❌ No | ✅ Sí | ✅ Sí |
| **Comisión Bizum** | 1.5% + 0.25€ | N/A | 1.0-1.5% + 0.25€ | 0.5-1.5% |
| **SEPA** | ✅ 0.35€ fijo | ❌ No | ❌ No | ✅ Variable |
| **Setup** | Fácil | Muy fácil | Complejo | Muy complejo |
| **Cuota mensual** | ❌ Ninguna | ❌ Ninguna | ✅ 20-50€/mes | ✅ 30-100€/mes |
| **Tiempo activación** | 1-3 días | Inmediato | 1-2 semanas | 2-4 semanas |
| **Checkout alojado** | ✅ Sí | ✅ Sí | ❌ No (iframe) | ❌ No |
| **Webhooks** | ✅ Excelentes | ✅ Básicos | ⚠️ Limitados | ⚠️ Complejos |
| **Modo test** | ✅ Completo | ⚠️ Sandbox | ❌ No | ❌ No |
| **API moderna** | ✅ REST/JSON | ✅ REST/JSON | ❌ XML/SOAP | ❌ Propietaria |

### Recomendación

**Para Coordicanarias: Stripe con Bizum es la mejor opción** ✅

**Razones:**
- ✅ **Sin cuotas mensuales** - Ideal para asociaciones sin ánimo de lucro
- ✅ **Bizum incluido** - 38% de españoles lo prefieren como método de pago
- ✅ **Múltiples métodos de pago** - Tarjetas, Bizum, SEPA, Google Pay, Apple Pay
- ✅ **Fácil implementación** - API moderna REST/JSON
- ✅ **Modo test completo** - Pruebas sin dinero real
- ✅ **Checkout alojado** - Máxima seguridad PCI DSS
- ✅ **Webhooks excelentes** - Automatización de confirmaciones

**Implementación recomendada:**
1. **Ahora:** Stripe con Bizum + Tarjetas para donaciones únicas
2. **Futuro:** Membresías recurrentes con Stripe Subscriptions

---

## Precios en España

### Stripe Pricing (Métodos de pago en España)

**Tarjetas estándar europeas:**
- **1.5% + 0.25€** por transacción exitosa

**Bizum:**
- **1.5% + 0.25€** por transacción exitosa (misma tarifa que tarjetas)
- ⚡ Transacciones completadas en menos de 10 segundos
- 📱 Preferido por 38% de compradores españoles

**Transferencias SEPA Direct Debit:**
- **0.35€** fijo por transacción (ideal para donaciones grandes)

**Ejemplos de comisiones:**
- Donación de 10€ → Comisión: 0.40€ → Recibes: **9.60€**
- Donación de 25€ → Comisión: 0.63€ → Recibes: **24.37€**
- Donación de 50€ → Comisión: 1.00€ → Recibes: **49.00€**
- Donación de 100€ → Comisión: 1.75€ → Recibes: **98.25€**

**Descuentos para nonprofits:**
- Stripe NO tiene descuento público para ONGs/asociaciones en España
- Puedes intentar contactar con `sales@stripe.com` para negociar tarifas especiales
- En EEUU ofrecen Stripe for Nonprofits (1.0% + 0.25€), pero no disponible en España aún

### PayPal Pricing (comparativa)

**PayPal para ONGs:**
- **1.5% + 0.35€** (con cuenta verificada de ONG)
- Sin cuenta ONG: 2.99% + 0.35€

**Nota:** PayPal es más caro para donaciones pequeñas que Stripe.

---

## Cómo Habilitar Bizum

### ¿Por qué Bizum es importante?

Bizum es el método de pago instantáneo más popular en España:
- 📊 **95% de las transferencias instantáneas** en España se hacen con Bizum
- 👥 **38% de los compradores españoles** prefieren pagar con Bizum
- ⚡ **Transacciones en menos de 10 segundos**
- 📱 **86% de abandono de carrito** si no está disponible el método preferido
- 🏦 **Más de 30 millones de usuarios** en España (2026)

### Configuración en Stripe Dashboard

Stripe ofrece Bizum como método de pago a través de **Open Bank S.A.** (filial de Banco Santander).

#### Paso 1: Crear cuenta de Stripe
1. Ve a https://dashboard.stripe.com/register
2. Completa los datos de Coordicanarias (CIF, dirección, etc.)
3. Verifica tu cuenta (puede tardar 1-3 días)

#### Paso 2: Habilitar Bizum en Payment Methods
1. Inicia sesión en tu Dashboard de Stripe
2. Ve a **Settings** → **Payment methods**
3. En la sección **Wallets and bank redirects**, busca **Bizum**
4. Click en **Turn on** para activar Bizum
5. Acepta los términos y condiciones de Bizum

#### Paso 3: Configurar en el código (Checkout)
Al crear una sesión de Stripe Checkout, agrega `'bizum'` en los métodos de pago permitidos:

```php
$checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card', 'bizum'], // ⬅️ Agregar 'bizum' aquí
    'line_items' => [[
        'price_data' => [
            'currency' => 'eur',
            'product_data' => [
                'name' => 'Donación a Coordicanarias',
            ],
            'unit_amount' => $importe * 100, // En céntimos
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => 'https://coordicanarias.com/donacion-exitosa.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => 'https://coordicanarias.com/donacion-cancelada.php',
]);
```

#### Paso 4: Probar en modo Test
Stripe proporciona números de prueba para Bizum:
- **Pago exitoso:** Usar cualquier número de teléfono español válido en modo test
- El Dashboard mostrará la transacción como "test mode"

### Requisitos técnicos para Bizum

✅ **Requisitos obligatorios:**
- Cuenta de Stripe verificada en España
- HTTPS habilitado en tu sitio web
- EUR como moneda (Bizum solo funciona en euros)
- Dirección de negocio en España, Andorra, Portugal o Italia

❌ **Limitaciones:**
- Solo disponible para clientes con bancos españoles compatibles
- No funciona para pagos recurrentes/subscripciones (solo pagos únicos)
- Límites de Bizum aplicables (máx. 1000€ por transacción para profesionales)

### Bancos compatibles (principales)

✅ Los siguientes bancos soportan Bizum profesional para recibir donaciones:
- Banco Santander
- BBVA
- CaixaBank
- Banco Sabadell
- Bankia (ahora CaixaBank)
- ING
- Openbank
- Unicaja
- Ibercaja
- Kutxabank
- Abanca
- Cajamar

**Nota:** En total hay más de 40 bancos en el sistema Bizum, pero los 12 principales (incluyendo los listados arriba) soportan cuentas profesionales.

### Ventajas de usar Bizum con Stripe

1. **Una sola integración** - Stripe maneja tanto tarjetas como Bizum con el mismo código
2. **Checkout alojado** - Página de pago segura en dominio de Stripe
3. **Sin cambio de banco** - Puedes mantener tu cuenta actual de Coordicanarias
4. **Webhooks unificados** - Mismo sistema de notificaciones para todos los métodos de pago
5. **Dashboard único** - Ver todas las donaciones (tarjetas + Bizum) en un solo lugar
6. **Sin cuotas mensuales adicionales** - Misma comisión por transacción (1.5% + 0.25€)

### Experiencia del usuario

Cuando un donante elige Bizum en el checkout:
1. Stripe muestra un código QR
2. El donante escanea el código con su app bancaria
3. Confirma el pago en su móvil (con PIN, huella o Face ID)
4. Pago completado en menos de 10 segundos
5. Redirección automática a tu página de éxito

**Alternativa:** También pueden introducir su número de teléfono si su banco lo permite.

### Referencias oficiales

- [Bizum: What businesses in Spain need to know | Stripe](https://stripe.com/resources/more/bizum-in-depth-guide)
- [Accepting Bizum payments in your store | Stripe](https://stripe.com/resources/more/accepting-bizum-payments-online-and-in-store)
- [Payment Methods in Spain | Stripe](https://stripe.com/resources/more/payment-methods-in-spain)
- [Bizum Payment Terms](https://stripe.com/legal/bizum)

---

## Requisitos Previos

### 1. Servidor

- ✅ PHP 7.4 o superior (tienes 8.x)
- ✅ MySQL 5.7 o superior (tienes coordica_crc)
- ✅ Composer instalado (para instalar librerías PHP)
- ✅ HTTPS habilitado (Stripe requiere SSL en producción)
- ✅ cURL habilitado en PHP (para llamadas a API de Stripe)

### 2. Cuenta de Stripe

- Crear cuenta en: https://dashboard.stripe.com/register
- Verificar email
- Completar datos de la empresa/asociación:
  - Nombre legal: Coordinadora de Discapacidad de Canarias
  - CIF de la asociación
  - Dirección fiscal
  - Representante legal
  - Cuenta bancaria para cobros

### 3. Documentación necesaria

Para verificar la cuenta de Stripe necesitarás:
- CIF de la asociación
- Estatutos de la asociación (PDF)
- Documento de identidad del representante legal
- Justificante bancario (extracto con IBAN visible)

---

## Tutorial de Implementación

El proceso completo se divide en **10 FASES**:

---

## FASE 1: Crear Cuenta de Stripe

### 1.1. Registro inicial

1. Ir a https://dashboard.stripe.com/register
2. Completar formulario:
   - **Email:** Tu email personal de desarrollo (cambiarás luego al de la asociación)
   - **Contraseña:** Segura (mín. 12 caracteres)
   - **País:** España 🇪🇸
3. Verificar email (recibirás link de confirmación)

### 1.2. Configuración de la cuenta

Una vez dentro del Dashboard:

1. **Business details:**
   - Company name: `Coordinadora de Discapacidad de Canarias`
   - Business type: `Non-profit organization`
   - Industry: `Civic and social organizations`

2. **Tax details:**
   - CIF: `G-XXXXXXXX` (el CIF de la asociación)
   - Tax ID type: `VAT/Tax ID`

3. **Bank account** (puede ser temporal):
   - **Ahora:** Puedes poner tu cuenta personal para hacer pruebas
   - **Antes de LIVE:** Cambiar a la cuenta de la asociación (ver Fase 10)

### 1.3. Activar cuenta

Stripe te pedirá verificar la identidad:
- Subir documento de identidad del representante legal
- Subir estatutos de la asociación
- Verificar cuenta bancaria (micro-depósitos)

**Tiempo:** 1-3 días laborables

### 1.4. Mientras tanto: Usar modo TEST

No necesitas esperar la verificación para desarrollar. Stripe tiene un **modo TEST completo** que funciona sin dinero real.

---

## FASE 2: Obtener API Keys

### 2.1. Acceder al Dashboard

1. Login en: https://dashboard.stripe.com/
2. En la esquina superior derecha verás: `🧪 Test mode` / `🔴 Live mode`
3. **Asegúrate de estar en TEST MODE** (switch azul)

### 2.2. Obtener las claves de TEST

1. Click en **Developers** (menú izquierdo)
2. Click en **API keys**
3. Verás 4 claves:

   **Test mode:**
   - `Publishable key`: `pk_test_[TU_CLAVE_PUBLICA_TEST]`
   - `Secret key`: `sk_test_[TU_CLAVE_SECRETA_TEST]` (click "Reveal test key")

   **Live mode:**
   - `Publishable key`: `pk_live_[TU_CLAVE_PUBLICA_LIVE]`
   - `Secret key`: `sk_live_[TU_CLAVE_SECRETA_LIVE]` (solo cuando estés verificado)

### 2.3. ¿Qué hace cada clave?

**Publishable Key (`pk_test_...` o `pk_live_...`):**
- Se usa en el **frontend** (JavaScript)
- Es PÚBLICA (puede estar en el código HTML)
- Solo sirve para crear sesiones de pago
- No puede acceder a datos sensibles

**Secret Key (`sk_test_...` o `sk_live_...`):**
- Se usa en el **backend** (PHP)
- Es PRIVADA (nunca exponerla públicamente)
- Puede hacer CUALQUIER operación en tu cuenta Stripe
- **NUNCA** la pongas en git, JavaScript, o HTML

### 2.4. Guardar las claves (modo seguro)

Copia las claves y guárdalas temporalmente en un archivo **local** (NO en el servidor aún):

```bash
# EN TU COMPUTADORA LOCAL
nano ~/stripe_keys.txt

# Pegar:
pk_test_[TU_CLAVE_PUBLICA_TEST]
sk_test_[TU_CLAVE_SECRETA_TEST]
```

Las usaremos en la Fase 3.

---

## FASE 3: Instalar Stripe en el Servidor

### 3.1. Conectar al servidor vía SSH

```bash
ssh usuario@coordicanarias.com
cd /home/coordica/public_html/new
```

### 3.2. Verificar que tienes Composer

```bash
composer --version
```

**Si NO tienes Composer instalado:**

```bash
# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

### 3.3. Crear composer.json

```bash
cd /home/coordica/public_html/new
nano composer.json
```

Pegar este contenido:

```json
{
    "name": "coordicanarias/website",
    "description": "Sitio web de Coordicanarias con CMS",
    "type": "project",
    "require": {
        "php": ">=7.4",
        "stripe/stripe-php": "^13.0"
    },
    "config": {
        "optimize-autoloader": true
    }
}
```

Guardar: `Ctrl+O`, `Enter`, `Ctrl+X`

### 3.4. Instalar dependencias

```bash
composer install
```

Esto creará:
- Carpeta `/vendor/` con la librería de Stripe
- Archivo `/vendor/autoload.php` para cargar clases automáticamente

### 3.5. Verificar instalación

```bash
ls -la vendor/stripe/
```

Deberías ver:
```
stripe-php/
```

### 3.6. Agregar vendor/ a .gitignore

```bash
nano .gitignore
```

Agregar esta línea si no existe:

```
/vendor/
composer.lock
```

**Importante:** NUNCA subas `/vendor/` a git. Es regenerable con `composer install`.

---

## FASE 4: Crear Tabla de Donaciones

### 4.1. Crear archivo SQL

```bash
cd /home/coordica/public_html/new/database
nano donaciones.sql
```

Pegar este contenido (ver [Código Completo - SQL](#código-sql---tabla-donaciones)):

```sql
-- Tabla para registrar donaciones
CREATE TABLE IF NOT EXISTS donaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- IDs de Stripe
    stripe_session_id VARCHAR(255) UNIQUE NOT NULL,
    stripe_payment_intent VARCHAR(255),
    stripe_customer_id VARCHAR(255),

    -- Datos de la donación
    tipo ENUM('donacion', 'socio') NOT NULL DEFAULT 'donacion',
    monto DECIMAL(10,2) NOT NULL,
    moneda VARCHAR(3) DEFAULT 'EUR',

    -- Datos del donante
    email VARCHAR(255) NOT NULL,
    nombre VARCHAR(255),
    telefono VARCHAR(50),
    mensaje TEXT,

    -- Estado
    estado ENUM('pendiente', 'completado', 'fallido', 'reembolsado') DEFAULT 'pendiente',

    -- Fechas
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_completado TIMESTAMP NULL,
    fecha_actualizado TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Metadata adicional (JSON para flexibilidad)
    metadata JSON,

    -- Índices para búsquedas rápidas
    INDEX idx_email (email),
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha_creacion),
    INDEX idx_stripe_session (stripe_session_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para socios mensuales (fase 2 - futuro)
CREATE TABLE IF NOT EXISTS socios (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- IDs de Stripe
    stripe_customer_id VARCHAR(255) UNIQUE NOT NULL,
    stripe_subscription_id VARCHAR(255) UNIQUE,

    -- Datos del socio
    email VARCHAR(255) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    telefono VARCHAR(50),

    -- Estado de la membresía
    estado ENUM('activo', 'cancelado', 'pausado', 'impagado') DEFAULT 'activo',
    monto_mensual DECIMAL(10,2) DEFAULT 5.00,

    -- Fechas
    fecha_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_cancelacion TIMESTAMP NULL,
    fecha_proximo_pago TIMESTAMP NULL,

    -- Metadata
    metadata JSON,

    INDEX idx_email (email),
    INDEX idx_estado (estado),
    INDEX idx_stripe_customer (stripe_customer_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar configuración para donaciones
INSERT INTO configuracion (clave, valor, descripcion, tipo) VALUES
('stripe_modo', 'test', 'Modo de Stripe: test o live', 'select'),
('stripe_pk_test', '', 'Publishable Key de TEST', 'text'),
('stripe_sk_test', '', 'Secret Key de TEST (encriptada)', 'password'),
('stripe_pk_live', '', 'Publishable Key de LIVE', 'text'),
('stripe_sk_live', '', 'Secret Key de LIVE (encriptada)', 'password'),
('stripe_webhook_secret', '', 'Webhook signing secret', 'password'),
('donaciones_activo', '0', 'Activar sistema de donaciones (0=no, 1=sí)', 'checkbox'),
('email_donaciones', 'info@coordicanarias.com', 'Email para notificaciones de donaciones', 'email')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);
```

### 4.2. Ejecutar SQL en la base de datos

**Opción A: Por línea de comandos**

```bash
mysql -h sql.coordicanarias.com -u coordica_crc -p coordica_crc < donaciones.sql
# Introduce la contraseña cuando te la pida
```

**Opción B: Por phpMyAdmin**

1. Accede a phpMyAdmin de tu hosting
2. Selecciona la base de datos `coordica_crc`
3. Click en "SQL"
4. Pega el contenido de `donaciones.sql`
5. Click en "Ejecutar"

### 4.3. Verificar que se crearon las tablas

```bash
mysql -h sql.coordicanarias.com -u coordica_crc -p coordica_crc -e "SHOW TABLES LIKE 'donaciones';"
```

Deberías ver:
```
+----------------------------------+
| Tables_in_coordica_crc (donaciones) |
+----------------------------------+
| donaciones                       |
| socios                          |
+----------------------------------+
```

---

## FASE 5: Configurar Stripe en PHP

### 5.1. Crear archivo de configuración

```bash
cd /home/coordica/public_html/new/php
nano stripe_config.php
```

Contenido (ver [Código Completo - stripe_config.php](#código-php---stripe_configphp)):

```php
<?php
/**
 * Configuración de Stripe
 *
 * Este archivo carga las credenciales de Stripe desde la BD
 * y configura la librería de Stripe.
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/db/connection.php';

// Obtener configuración desde BD
function getStripeConfig() {
    $db = Database::getInstance()->getConnection();

    $stmt = $db->query("
        SELECT clave, valor
        FROM configuracion
        WHERE clave LIKE 'stripe_%'
    ");

    $config = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $config[$row['clave']] = $row['valor'];
    }

    return $config;
}

// Cargar config
$stripeConfig = getStripeConfig();

// Determinar qué modo usar (test o live)
$modo = $stripeConfig['stripe_modo'] ?? 'test';

// Seleccionar las claves apropiadas
if ($modo === 'live') {
    $publishableKey = $stripeConfig['stripe_pk_live'] ?? '';
    $secretKey = $stripeConfig['stripe_sk_live'] ?? '';
} else {
    $publishableKey = $stripeConfig['stripe_pk_test'] ?? '';
    $secretKey = $stripeConfig['stripe_sk_test'] ?? '';
}

// Configurar Stripe
\Stripe\Stripe::setApiKey($secretKey);
\Stripe\Stripe::setApiVersion('2023-10-16');

// Definir constantes globales
define('STRIPE_MODE', $modo);
define('STRIPE_PUBLISHABLE_KEY', $publishableKey);
define('STRIPE_SECRET_KEY', $secretKey);
define('STRIPE_WEBHOOK_SECRET', $stripeConfig['stripe_webhook_secret'] ?? '');
define('SITE_URL', 'https://coordicanarias.com/new');

// Función helper para verificar si Stripe está configurado
function isStripeConfigured() {
    return !empty(STRIPE_SECRET_KEY) && !empty(STRIPE_PUBLISHABLE_KEY);
}
```

### 5.2. Agregar claves de Stripe a la BD

**Opción A: Manualmente por phpMyAdmin**

1. Accede a phpMyAdmin
2. Abre la tabla `configuracion`
3. Busca las filas con clave `stripe_pk_test` y `stripe_sk_test`
4. Edita y pega las claves que guardaste en la Fase 2

**Opción B: Por línea de comandos (más seguro)**

```bash
mysql -h sql.coordicanarias.com -u coordica_crc -p coordica_crc
```

Dentro de MySQL:

```sql
UPDATE configuracion SET valor = 'pk_test_[TU_CLAVE_PUBLICA_TEST]'
WHERE clave = 'stripe_pk_test';

UPDATE configuracion SET valor = 'sk_test_[TU_CLAVE_SECRETA_TEST]'
WHERE clave = 'stripe_sk_test';

UPDATE configuracion SET valor = 'test'
WHERE clave = 'stripe_modo';

SELECT clave, LEFT(valor, 20) as valor_preview
FROM configuracion
WHERE clave LIKE 'stripe_%';

EXIT;
```

**Importante:** La `Secret Key` se guarda en texto plano en la BD. Asegúrate de que:
- La tabla `configuracion` NO sea accesible vía web
- Hagas backups encriptados de la BD
- Solo usuarios admin puedan ver/editar estas claves

### 5.3. Probar la configuración

Crea un archivo temporal para probar:

```bash
cd /home/coordica/public_html/new
nano test_stripe.php
```

```php
<?php
require_once __DIR__ . '/php/stripe_config.php';

echo "Modo: " . STRIPE_MODE . "\n";
echo "Publishable Key: " . substr(STRIPE_PUBLISHABLE_KEY, 0, 20) . "...\n";
echo "Secret Key configurada: " . (isStripeConfigured() ? 'SÍ' : 'NO') . "\n";

try {
    // Intentar listar los primeros 3 productos (debería devolver array vacío)
    $products = \Stripe\Product::all(['limit' => 3]);
    echo "Conexión a Stripe: ✅ EXITOSA\n";
    echo "Productos encontrados: " . count($products->data) . "\n";
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
```

Ejecutar:

```bash
php test_stripe.php
```

**Resultado esperado:**
```
Modo: test
Publishable Key: pk_test_51JxXxXxXx...
Secret Key configurada: SÍ
Conexión a Stripe: ✅ EXITOSA
Productos encontrados: 0
```

Si ves esto, ¡Stripe está correctamente configurado! 🎉

Elimina el archivo de prueba:

```bash
rm test_stripe.php
```

---

## FASE 6: Crear Página de Donaciones

### 6.1. Crear el archivo HTML/PHP

```bash
cd /home/coordica/public_html/new
nano donaciones.php
```

Ver código completo en: [Código Completo - donaciones.php](#código-php---donacionesphp)

Este archivo incluye:
- Formulario con cantidades predefinidas (10€, 25€, 50€, 100€)
- Input para cantidad personalizada
- Campos para: nombre, email, teléfono, mensaje opcional
- Validación JavaScript antes de enviar
- Botón que envía a `crear_sesion_pago.php`

### 6.2. Probar la página

Abre en el navegador:
```
https://coordicanarias.com/new/donaciones.php
```

Deberías ver:
- Formulario de donación estilizado con Bootstrap
- Botones de cantidades predefinidas
- Campos de contacto
- Botón "Donar ahora" deshabilitado hasta completar campos obligatorios

---

## FASE 7: Crear Endpoint de Pago

### 7.1. Crear archivo de procesamiento

```bash
cd /home/coordica/public_html/new/php
nano crear_sesion_pago.php
```

Ver código completo en: [Código Completo - crear_sesion_pago.php](#código-php---crear_sesion_pagophp)

Este script:
1. Recibe datos del formulario (POST)
2. Valida todos los campos
3. Crea una sesión de Stripe Checkout
4. Guarda la donación en BD con estado "pendiente"
5. Redirige al usuario a la página de pago de Stripe
6. Maneja errores y logs

### 7.2. Probar el flujo completo

1. Abre `https://coordicanarias.com/new/donaciones.php`
2. Selecciona 10€
3. Completa: nombre, email, teléfono
4. Click en "Donar ahora"
5. Deberías ser redirigido a: `https://checkout.stripe.com/c/pay/cs_test_XXXX`
6. Verás la página de Stripe con el formulario de pago

**NO hagas el pago aún** - primero necesitamos configurar webhooks (Fase 8)

---

## FASE 8: Página de Confirmación

### 8.1. Crear gracias.php

```bash
cd /home/coordica/public_html/new
nano gracias.php
```

Ver código completo en: [Código Completo - gracias.php](#código-php---graciasphp)

Este archivo:
- Recibe el `session_id` de Stripe en la URL
- Consulta la sesión a Stripe para obtener detalles
- Muestra mensaje de agradecimiento
- Muestra resumen de la donación
- Incluye botón para volver al inicio

### 8.2. Probar redirección

El flujo completo será:
```
donaciones.php
  → crear_sesion_pago.php
    → checkout.stripe.com
      → gracias.php?session_id=cs_test_XXX
```

---

## FASE 9: Configurar Webhooks

Los **webhooks** son fundamentales porque:
- Confirman que el pago fue exitoso (el usuario podría cerrar la pestaña antes de volver)
- Actualizan el estado en tu BD automáticamente
- Envían emails de confirmación
- Son la única forma **confiable** de saber si se cobró

### 9.1. Crear endpoint de webhook

```bash
cd /home/coordica/public_html/new/php/webhooks
mkdir -p /home/coordica/public_html/new/php/webhooks
nano stripe_webhook.php
```

Ver código completo en: [Código Completo - stripe_webhook.php](#código-php---stripe_webhookphp)

Este script:
1. Recibe eventos de Stripe vía HTTP POST
2. Verifica la firma del webhook (seguridad)
3. Procesa eventos importantes:
   - `checkout.session.completed` - Pago exitoso
   - `payment_intent.payment_failed` - Pago fallido
   - `charge.refunded` - Reembolso
4. Actualiza estado en BD
5. Envía emails de confirmación

### 9.2. Configurar webhook en Stripe Dashboard

1. Login en: https://dashboard.stripe.com/
2. Asegúrate de estar en **Test mode** (switch azul)
3. Click en **Developers** → **Webhooks**
4. Click en **+ Add endpoint**
5. Configurar:
   - **Endpoint URL:** `https://coordicanarias.com/new/php/webhooks/stripe_webhook.php`
   - **Description:** `Webhook para donaciones - Test`
   - **Events to send:** Seleccionar:
     - `checkout.session.completed`
     - `payment_intent.payment_failed`
     - `charge.refunded`
6. Click en **Add endpoint**

### 9.3. Obtener Signing Secret

Después de crear el webhook:

1. Click en el webhook que acabas de crear
2. En la sección "Signing secret" click en **Reveal**
3. Copiar el secreto: `whsec_XXXXXXXXXXXXXXXXXXXXXXXX`
4. Guardarlo en la BD:

```bash
mysql -h sql.coordicanarias.com -u coordica_crc -p coordica_crc
```

```sql
UPDATE configuracion SET valor = 'whsec_XXXXXXXXXXXXXXXXXXXXXXXX'
WHERE clave = 'stripe_webhook_secret';

EXIT;
```

### 9.4. Probar el webhook

**Opción A: Con Stripe CLI (recomendado para desarrollo local)**

```bash
# Instalar Stripe CLI
brew install stripe/stripe-cli/stripe  # macOS
# o descargar desde: https://stripe.com/docs/stripe-cli

# Login
stripe login

# Forward webhooks a tu localhost
stripe listen --forward-to https://coordicanarias.com/new/php/webhooks/stripe_webhook.php

# En otra terminal, hacer un pago de prueba
stripe trigger checkout.session.completed
```

**Opción B: Desde Stripe Dashboard**

1. Ir a: **Developers** → **Webhooks**
2. Click en tu webhook
3. Click en **Send test webhook**
4. Seleccionar evento: `checkout.session.completed`
5. Click en **Send test webhook**

Deberías ver en los logs del webhook:
```json
{
  "status": "success",
  "event": "checkout.session.completed",
  "timestamp": "2026-01-04 18:30:15"
}
```

---

## FASE 10: Testing Completo

Ahora vamos a probar el flujo completo con **tarjetas de prueba** de Stripe.

### 10.1. Tarjetas de prueba de Stripe

Stripe provee tarjetas de prueba que simulan diferentes escenarios:

**Tarjeta de éxito:**
- Número: `4242 4242 4242 4242`
- Fecha: Cualquier fecha futura (ej: 12/25)
- CVC: Cualquier 3 dígitos (ej: 123)
- ZIP: Cualquier 5 dígitos (ej: 12345)

**Tarjeta que requiere autenticación 3D Secure:**
- Número: `4000 0027 6000 3184`
- Fecha: Cualquier fecha futura
- CVC: Cualquier 3 dígitos

**Tarjeta que falla:**
- Número: `4000 0000 0000 0002`
- Fecha: Cualquier fecha futura
- CVC: Cualquier 3 dígitos

**Más tarjetas:** https://stripe.com/docs/testing#cards

### 10.2. Flujo de prueba completo

**Paso 1:** Abrir página de donaciones
```
https://coordicanarias.com/new/donaciones.php
```

**Paso 2:** Completar formulario
- Cantidad: 25€
- Nombre: Juan Pérez
- Email: juan.perez@example.com (usa un email real para recibir confirmación)
- Teléfono: +34 600 123 456
- Mensaje: "Prueba de donación"

**Paso 3:** Click en "Donar ahora"
- Deberías ser redirigido a `checkout.stripe.com`

**Paso 4:** Completar pago en Stripe
- Email: (se autocompletará con el que pusiste)
- Card number: `4242 4242 4242 4242`
- MM/YY: `12/25`
- CVC: `123`
- ZIP: `12345`
- Click en "Pay €25.00"

**Paso 5:** Verificar redirección
- Deberías ser redirigido a `gracias.php?session_id=cs_test_XXX`
- Deberías ver mensaje de agradecimiento con resumen de donación

**Paso 6:** Verificar BD
```bash
mysql -h sql.coordicanarias.com -u coordica_crc -p coordica_crc
```

```sql
SELECT id, nombre, email, monto, estado, fecha_creacion
FROM donaciones
ORDER BY id DESC
LIMIT 1;
```

Deberías ver:
```
+----+-------------+---------------------------+--------+------------+---------------------+
| id | nombre      | email                     | monto  | estado     | fecha_creacion      |
+----+-------------+---------------------------+--------+------------+---------------------+
|  1 | Juan Pérez  | juan.perez@example.com    |  25.00 | completado | 2026-01-04 18:45:32 |
+----+-------------+---------------------------+--------+------------+---------------------+
```

**Paso 7:** Verificar email
- Revisar la bandeja de entrada de `juan.perez@example.com`
- Deberías recibir email de confirmación de Coordicanarias

**Paso 8:** Verificar en Stripe Dashboard
1. Ir a: https://dashboard.stripe.com/test/payments
2. Deberías ver el pago de 25€
3. Click en el pago para ver detalles
4. Verificar que el estado es "Succeeded"

### 10.3. Probar escenarios de error

**Test 1: Tarjeta rechazada**
- Usar tarjeta: `4000 0000 0000 0002`
- El pago debería fallar
- Verificar que en BD el estado es "fallido"

**Test 2: Usuario cancela el pago**
- Llegar hasta Stripe Checkout
- Click en "← Back" (sin pagar)
- Verificar redirección a `donaciones.php?cancelado=1`
- Verificar que en BD el estado es "pendiente"

**Test 3: Webhook no llega**
- Hacer un pago pero desactivar temporalmente el webhook
- El estado en BD se quedará en "pendiente"
- Al reactivar webhook, puedes manualmente triggear eventos pasados

---

## Paso a Producción

**¡IMPORTANTE!** Solo hacer esto cuando:
1. ✅ Todas las pruebas en modo TEST funcionan
2. ✅ La cuenta de Stripe está verificada
3. ✅ La cuenta bancaria de la asociación está configurada en Stripe
4. ✅ El sitio tiene HTTPS habilitado

### 11.1. Obtener claves de producción

1. Login en: https://dashboard.stripe.com/
2. Cambiar de **Test mode** a **Live mode** (switch rojo)
3. Ir a **Developers** → **API keys**
4. Copiar:
   - `Publishable key`: `pk_live_[TU_CLAVE_PUBLICA_LIVE]`
   - `Secret key`: `sk_live_[TU_CLAVE_SECRETA_LIVE]` (click "Reveal live key")

### 11.2. Actualizar claves en BD

```bash
mysql -h sql.coordicanarias.com -u coordica_crc -p coordica_crc
```

```sql
UPDATE configuracion SET valor = 'pk_live_[TU_CLAVE_PUBLICA_LIVE]'
WHERE clave = 'stripe_pk_live';

UPDATE configuracion SET valor = 'sk_live_[TU_CLAVE_SECRETA_LIVE]'
WHERE clave = 'stripe_sk_live';

-- IMPORTANTE: CAMBIAR A MODO LIVE
UPDATE configuracion SET valor = 'live'
WHERE clave = 'stripe_modo';

SELECT clave, LEFT(valor, 20) as valor_preview
FROM configuracion
WHERE clave IN ('stripe_modo', 'stripe_pk_live', 'stripe_sk_live');

EXIT;
```

### 11.3. Configurar webhook de producción

1. Ir a: https://dashboard.stripe.com/ (asegúrate de estar en **Live mode**)
2. **Developers** → **Webhooks**
3. Click en **+ Add endpoint**
4. Configurar:
   - **Endpoint URL:** `https://coordicanarias.com/new/php/webhooks/stripe_webhook.php`
   - **Description:** `Webhook para donaciones - PRODUCCIÓN`
   - **Events to send:**
     - `checkout.session.completed`
     - `payment_intent.payment_failed`
     - `charge.refunded`
5. Click en **Add endpoint**
6. Copiar el **Signing secret** (empieza con `whsec_`)
7. Actualizar en BD:

```sql
UPDATE configuracion SET valor = 'whsec_XXXXXXXXXXXXXXXXXXXXXXXX'
WHERE clave = 'stripe_webhook_secret';
```

### 11.4. Activar donaciones en el sitio

```sql
UPDATE configuracion SET valor = '1'
WHERE clave = 'donaciones_activo';
```

### 11.5. Descomentar sección "Colabora" en index.php

```bash
cd /home/coordica/public_html/new
nano index.php
```

Buscar línea ~1120 y descomentar:

```html
<!-- <section id="colabora" class="section"> -->
```

Cambiar a:

```html
<section id="colabora" class="section">
```

Y al final de la sección (línea ~1280):

```html
<!-- </section> -->
```

Cambiar a:

```html
</section>
```

### 11.6. Actualizar links en la sección Colabora

En `index.php` línea ~1190, actualizar el botón de "Donaciones":

```html
<a href="donaciones.php" class="btn btn-primary">Donar ahora</a>
```

### 11.7. Hacer pago de prueba REAL

**¡ATENCIÓN!** Este pago será REAL y se te cobrará a tu tarjeta.

1. Ir a: `https://coordicanarias.com/new/donaciones.php`
2. Seleccionar la cantidad mínima (10€)
3. Completar con datos reales
4. Usar tarjeta REAL (NO tarjetas de prueba)
5. Completar pago
6. Verificar que:
   - Recibes email de confirmación
   - El dinero aparece en Stripe Dashboard (https://dashboard.stripe.com/payments)
   - El estado en BD es "completado"

### 11.8. Monitorear logs

Primeros días en producción, revisa:

**Logs de Apache:**
```bash
tail -f /var/log/apache2/error.log | grep stripe
```

**Logs de Stripe Dashboard:**
https://dashboard.stripe.com/logs

**Logs de webhooks:**
https://dashboard.stripe.com/webhooks → Click en tu webhook → Ver eventos

---

## Migración de Cuenta Bancaria

### ¿Puedo empezar con mi cuenta personal y luego cambiar?

**Respuesta: SÍ**, Stripe permite cambiar la cuenta bancaria en cualquier momento.

### 12.1. Durante desarrollo (ahora)

**Puedes usar tu cuenta personal para:**
1. Completar el setup inicial de Stripe
2. Hacer pruebas en modo TEST (no se mueve dinero real)
3. Verificar que todo funciona correctamente

**Importante:**
- En modo TEST no importa qué cuenta bancaria tengas configurada
- Los pagos de prueba NO se depositan en ninguna cuenta real
- No hay riesgo fiscal/legal durante el desarrollo

### 12.2. Antes de ir a producción

**DEBES cambiar a la cuenta bancaria de la asociación:**

**Razones legales:**
- Los fondos deben ir directamente a la cuenta de la entidad jurídica (la asociación)
- Usar cuenta personal podría generar problemas fiscales
- Hacienda podría considerar esos ingresos como tuyos, no de la asociación
- La asociación debe tener registro contable de TODAS las donaciones

**Razones prácticas:**
- Stripe genera reportes fiscales con el nombre del titular de la cuenta
- Facilita la contabilidad de la asociación
- Evita tener que hacer transferencias manuales después

### 12.3. Cómo cambiar la cuenta bancaria

**Paso 1:** Ir a Stripe Dashboard

1. Login en: https://dashboard.stripe.com/
2. Asegúrate de estar en **Live mode**
3. Click en **Settings** (icono de engranaje arriba a la derecha)

**Paso 2:** Ir a Bank accounts

1. En el menú izquierdo: **Bank accounts and scheduling**
2. Verás tu cuenta bancaria actual

**Paso 3:** Agregar nueva cuenta

1. Click en **Add bank account**
2. Completar formulario:
   - **Country:** España
   - **Currency:** EUR
   - **IBAN:** ES XX XXXX XXXX XX XXXXXXXXXX (IBAN de la asociación)
   - **Account holder name:** Coordinadora de Discapacidad de Canarias
   - **Account holder type:** Company
3. Click en **Add bank account**

**Paso 4:** Verificar la cuenta

Stripe hará 2 micro-depósitos (0.01€ y 0.02€ aprox) a la cuenta en 1-2 días.

1. Espera a recibir los depósitos
2. Vuelve a Stripe Dashboard → **Bank accounts and scheduling**
3. Click en **Verify** junto a la nueva cuenta
4. Introduce los 2 importes que recibiste
5. Click en **Verify**

**Paso 5:** Establecer como predeterminada

1. Click en **⋮** (tres puntos) junto a la cuenta de la asociación
2. Click en **Make default**
3. Confirmar

**Paso 6:** (Opcional) Eliminar cuenta personal

1. Click en **⋮** junto a tu cuenta personal
2. Click en **Remove**
3. Confirmar

### 12.4. ¿Qué pasa con pagos pendientes?

**Si cambias la cuenta bancaria:**
- Los pagos que ya se procesaron se depositan en la cuenta que estaba configurada en ese momento
- Los pagos futuros se depositarán en la nueva cuenta
- No hay forma de "redirigir" pagos viejos a la nueva cuenta

**Recomendación:**
- Si hiciste algún pago de prueba real en tu cuenta personal
- Haz una transferencia manual de esos fondos a la cuenta de la asociación
- Documenta la transferencia en la contabilidad de la asociación

### 12.5. Timeline recomendado

```
Semana 1-2: Desarrollo
├─ Crear cuenta Stripe con tu email personal
├─ Usar tu cuenta bancaria temporal (solo para setup)
├─ Trabajar en modo TEST exclusivamente
└─ Probar todo el flujo sin dinero real

Semana 3: Pre-producción
├─ Agregar cuenta bancaria de la asociación
├─ Verificar cuenta (esperar micro-depósitos)
├─ Establecer como predeterminada
├─ Actualizar datos fiscales (CIF de la asociación)
└─ Subir documentación (estatutos, etc)

Semana 4: Producción
├─ Cambiar a modo LIVE
├─ Hacer pago de prueba pequeño (10€) con tarjeta real
├─ Verificar que llega a cuenta de la asociación (2-7 días)
└─ Activar sección "Colabora" públicamente
```

### 12.6. Documentos necesarios para cuenta de asociación

Stripe pedirá:

**Para verificar la entidad:**
- CIF de la asociación (G-XXXXXXXX)
- Estatutos de la asociación (PDF)
- Acta de constitución o inscripción en registro

**Para verificar representante legal:**
- DNI/NIE del presidente o tesorero
- Poder notarial (si aplica)

**Para verificar cuenta bancaria:**
- Extracto bancario mostrando:
  - IBAN completo
  - Nombre del titular (debe coincidir con nombre de la asociación)
  - Fecha reciente (últimos 3 meses)

### 12.7. Contactar con Stripe para soporte

Si tienes dudas sobre el cambio de cuenta:

**Email:** support@stripe.com
**Teléfono:** +34 911 23 97 85 (España)
**Chat:** https://dashboard.stripe.com/ → Click en "?" → "Contact support"

**Menciona:**
- "Soy una asociación sin ánimo de lucro"
- "Necesito cambiar la cuenta bancaria de personal a la de la entidad"
- "¿Necesito crear una cuenta nueva o puedo actualizar esta?"

---

## FAQ - Preguntas Frecuentes

### ¿Cuánto tarda Stripe en depositar el dinero?

**Primer pago:** 7-14 días (Stripe retiene el primer pago para verificar)
**Pagos posteriores:** 2-7 días laborables
**Configuración:** Puedes cambiar la frecuencia a diaria, semanal o mensual en Dashboard

### ¿Puedo aceptar donaciones recurrentes (socios mensuales)?

**Sí**, pero requiere configuración adicional:
- Usar Stripe Subscriptions en vez de Checkout Sessions
- Crear productos recurrentes en Stripe
- Gestionar cancelaciones y renovaciones
- Ver tabla `socios` en el SQL (ya está creada)

**Recomendación:** Implementar en **Fase 2** después de validar donaciones únicas.

### ¿Qué pasa si un usuario hace un pago pero cierra el navegador antes de volver a mi sitio?

El **webhook** se encarga de esto. Aunque el usuario no vuelva a `gracias.php`, el webhook de Stripe llamará a tu servidor y:
1. Actualizará el estado a "completado"
2. Enviará el email de confirmación
3. Registrará la donación en BD

Por eso los webhooks son CRÍTICOS.

### ¿Puedo hacer reembolsos?

**Sí**, desde Stripe Dashboard:
1. Ir a: https://dashboard.stripe.com/payments
2. Click en el pago
3. Click en **Refund payment**
4. Introducir importe (puede ser parcial)
5. El webhook `charge.refunded` actualizará tu BD automáticamente

**Importante:** Stripe cobra las comisiones igualmente en reembolsos.

### ¿Cómo veo todas mis donaciones?

**Opción 1: Stripe Dashboard**
- https://dashboard.stripe.com/payments
- Filtrar por estado, fecha, importe, etc.
- Exportar a CSV

**Opción 2: Tu base de datos**
```sql
SELECT id, nombre, email, monto, estado, fecha_creacion
FROM donaciones
WHERE estado = 'completado'
ORDER BY fecha_creacion DESC;
```

**Opción 3: Panel admin (futuro)**
- Crear `/admin/donaciones.php` (Fase 2 del CMS)
- Ver estadísticas, gráficos, exportar, etc.

### ¿Qué pasa si cambio de modo TEST a LIVE accidentalmente?

**No pasa nada grave**, pero:
- Las transacciones de TEST no aparecen en LIVE (son bases de datos separadas)
- Si haces un pago en LIVE por error, será un cargo REAL
- Puedes volver a TEST en cualquier momento desde la BD:

```sql
UPDATE configuracion SET valor = 'test' WHERE clave = 'stripe_modo';
```

### ¿Necesito activar 3D Secure / SCA?

**Stripe lo maneja automáticamente**. Si una tarjeta requiere Strong Customer Authentication (SCA), Stripe muestra la autenticación 3D Secure sin que tengas que hacer nada.

### ¿Puedo cambiar los importes predefinidos (10€, 25€, 50€, 100€)?

**Sí**, edita `/new/donaciones.php` línea ~50:

```html
<button type="button" class="btn-cantidad" data-cantidad="10">10€</button>
<button type="button" class="btn-cantidad" data-cantidad="25">25€</button>
<button type="button" class="btn-cantidad" data-cantidad="50">50€</button>
<button type="button" class="btn-cantidad" data-cantidad="100">100€</button>
```

Cambia los números por lo que quieras.

### ¿Puedo aceptar otras monedas además de EUR?

**Sí**, Stripe soporta 135+ monedas. Para agregar USD por ejemplo:

1. En Stripe Dashboard: **Settings** → **Payment methods** → Enable USD
2. En `crear_sesion_pago.php` línea ~30, cambiar:
   ```php
   'currency' => 'eur',  // Cambiar a 'usd'
   ```

**Importante:** Las comisiones pueden variar según la moneda.

### ¿Stripe envía emails automáticos a los donantes?

**Sí**, Stripe envía:
- Email de recibo del pago (branded con tu logo si lo configuras)
- Email de reembolso (si aplica)

**Adicionalmente**, tu código envía emails personalizados con el branding de Coordicanarias (ver `stripe_webhook.php`).

**Puedes desactivar** los emails de Stripe en: **Settings** → **Emails** → Desmarcar "Customer emails"

### ¿Cómo evito donaciones fraudulentas?

Stripe incluye **Stripe Radar** que detecta fraude automáticamente:
- Machine learning en miles de millones de transacciones
- Bloquea pagos sospechosos
- Protección contra chargebacks

**En Dashboard:**
- **Fraud & risk** → Ver intentos bloqueados
- Configurar reglas personalizadas (ej: bloquear países específicos)

**Adicional en tu código:**
- Limitar donaciones a máximo 5000€ (línea ~25 de `crear_sesion_pago.php`)
- Agregar reCAPTCHA en el formulario (recomendado)

### ¿Qué hago si un donante reporta un chargeback?

**Chargeback** = El donante disputa el cargo con su banco.

**Proceso:**
1. Recibirás email de Stripe notificando el chargeback
2. Tienes 7 días para responder con evidencia
3. Subir evidencia en Dashboard: **Disputes** → Click en disputa → **Submit evidence**
4. Evidencia útil:
   - Email de confirmación que enviaste
   - Screenshot de `gracias.php`
   - Registro de BD mostrando la donación
   - Comunicaciones con el donante

**Importante:** Los chargebacks tienen un cargo de 15€ adicional, ganes o pierdas.

### ¿Cómo personalizo el diseño de Stripe Checkout?

**Opción 1: Branding básico (gratis)**
1. Dashboard → **Settings** → **Branding**
2. Subir logo de Coordicanarias
3. Elegir colores primarios (#243659)
4. El Checkout usará estos colores automáticamente

**Opción 2: Checkout totalmente custom (requiere Stripe Link)**
- Más complejo
- No recomendado para esta fase

### ¿Puedo ver donaciones por área temática?

Actualmente NO, porque la tabla `donaciones` no tiene campo `area_id`.

**Para agregarlo:**

1. Modificar tabla:
```sql
ALTER TABLE donaciones
ADD COLUMN area_id INT NULL,
ADD FOREIGN KEY (area_id) REFERENCES areas(id);
```

2. Modificar `donaciones.php` para agregar selector de área:
```html
<select name="area_id">
  <option value="">General</option>
  <option value="1">Empleo</option>
  <option value="2">Formación e Innovación</option>
  ...
</select>
```

3. Modificar `crear_sesion_pago.php` para guardar `area_id`

---

## Troubleshooting

### Error: "No such customer"

**Causa:** Intentaste acceder a un cliente que no existe en Stripe.

**Solución:**
- Verificar que estás en el modo correcto (TEST vs LIVE)
- Los clientes de TEST no existen en LIVE y viceversa

---

### Error: "Invalid API Key provided"

**Causa:** La Secret Key en la BD es incorrecta o está vacía.

**Solución:**
```sql
SELECT clave, LEFT(valor, 20) FROM configuracion WHERE clave LIKE 'stripe_sk_%';
```

Verificar que:
- `stripe_sk_test` empiece con `sk_test_`
- `stripe_sk_live` empiece con `sk_live_`
- No haya espacios al principio/final

---

### Error: "You cannot use a live publishable key in test mode"

**Causa:** Estás en modo TEST pero usando claves de LIVE (o viceversa).

**Solución:**
```sql
UPDATE configuracion SET valor = 'test' WHERE clave = 'stripe_modo';
```

O verifica que las claves en `stripe_config.php` se seleccionen correctamente según el modo.

---

### Webhook no se ejecuta

**Síntomas:**
- El pago aparece en Stripe pero el estado en BD sigue "pendiente"
- No se envía email de confirmación

**Diagnóstico:**

1. Verificar que el webhook está configurado:
```
https://dashboard.stripe.com/webhooks
```

2. Verificar que la URL es correcta:
```
https://coordicanarias.com/new/php/webhooks/stripe_webhook.php
```

3. Ver logs del webhook:
- Click en el webhook
- Ver "Recent deliveries"
- Si hay errores, verás el código de respuesta

4. Verificar que `stripe_webhook_secret` está en BD:
```sql
SELECT valor FROM configuracion WHERE clave = 'stripe_webhook_secret';
```

**Soluciones:**

**Si el webhook no se llama:**
- Verificar que la URL es accesible públicamente
- Verificar que no hay .htaccess bloqueando POST requests
- Verificar que PHP no tiene `allow_url_fopen` deshabilitado

**Si el webhook falla con 500:**
- Revisar logs de PHP: `tail -f /var/log/apache2/error.log`
- Verificar permisos de archivos
- Verificar que `stripe_config.php` carga correctamente

**Si el webhook falla con "Invalid signature":**
- El `stripe_webhook_secret` es incorrecto
- Copiarlo de nuevo desde Dashboard

---

### Email de confirmación no llega

**Causas:**

1. **El webhook no se ejecutó** (ver arriba)

2. **El email está en spam:**
   - Revisar carpeta de spam
   - Agregar `noreply@coordicanarias.com` a contactos

3. **SMTP está mal configurado:**
```bash
# Probar envío manual
php -r "
require 'php/enviar_correo.php';
enviar_correo(
    'tu-email@example.com',
    'Prueba SMTP',
    'Si recibes esto, SMTP funciona'
);
"
```

4. **Error en el código del webhook:**
- Revisar logs: `tail -f /var/log/apache2/error.log`
- Buscar línea ~150 de `stripe_webhook.php` donde se envía el email

---

### Donación aparece duplicada

**Causa:** El webhook se ejecutó dos veces (Stripe reintenta si no recibe respuesta 200).

**Solución:**

El código ya maneja esto con:
```php
// Verificar que no hayamos procesado este evento antes
$stmt = $db->prepare("SELECT id FROM donaciones WHERE stripe_session_id = ?");
$stmt->execute([$session_id]);
if ($stmt->fetch()) {
    http_response_code(200);
    exit; // Ya procesado
}
```

Si aún así ocurre duplicación:
1. Verificar que tu servidor responda rápido (< 5 segundos)
2. Agregar índice UNIQUE en `stripe_session_id`:
```sql
ALTER TABLE donaciones ADD UNIQUE INDEX (stripe_session_id);
```

---

### El pago se completó pero el usuario ve error

**Causa:** El redirect de Stripe a `gracias.php` falló (timeout, error 500, etc).

**Impacto:**
- ❌ Usuario piensa que falló
- ✅ El pago SÍ se procesó (está en Stripe)
- ✅ El webhook actualizó la BD
- ✅ Se envió email de confirmación

**Solución inmediata:**
- Contactar al usuario y confirmar que el pago fue exitoso
- Mostrarle el email de confirmación que recibió

**Solución a largo plazo:**
- Optimizar `gracias.php` para que cargue rápido
- Agregar logging para detectar estos casos
- Crear página de "Buscar mi donación" donde el usuario pueda introducir su email y ver estado

---

### Error: "Amount must be at least 0.50 eur"

**Causa:** Intentaste crear un pago menor a 0.50€.

**Solución:**
Stripe tiene montos mínimos por moneda:
- EUR: 0.50€
- USD: 0.50$
- GBP: 0.30£

Validar en `crear_sesion_pago.php`:
```php
if ($monto < 0.50) {
    die("El monto mínimo es 0.50€");
}
```

---

### No puedo acceder a Stripe Dashboard

**Problema:** Olvidaste la contraseña.

**Solución:**
1. Ir a: https://dashboard.stripe.com/login/forgot
2. Introducir el email con el que te registraste
3. Seguir instrucciones del email

**Problema:** Cuenta desactivada por seguridad.

**Solución:**
- Contactar a: support@stripe.com
- Explicar la situación
- Proveer documentación de la asociación

---

## Consideraciones Legales

### Protección de Datos (GDPR)

**Datos que guardas:**
- Nombre
- Email
- Teléfono
- IP address (en logs)

**Obligaciones:**

1. **Informar en Política de Privacidad:**
   - Qué datos recopilas
   - Para qué los usas (procesar donaciones)
   - Quién los procesa (tú + Stripe)
   - Cuánto tiempo los guardas
   - Derechos del usuario (acceso, rectificación, supresión)

2. **Consent explícito:**
   Agregar checkbox en `donaciones.php`:
   ```html
   <label>
       <input type="checkbox" name="acepto_privacidad" required>
       He leído y acepto la
       <a href="politica-privacidad.php" target="_blank">Política de Privacidad</a>
   </label>
   ```

3. **Derecho al olvido:**
   Si un donante pide eliminar sus datos:
   ```sql
   -- Anonimizar (NO eliminar, necesitas histórico)
   UPDATE donaciones
   SET nombre = 'Anónimo',
       email = 'anonimo@example.com',
       telefono = NULL,
       mensaje = NULL
   WHERE email = 'donante@example.com';
   ```

4. **Compartir datos con Stripe:**
   - Stripe es el "procesador de datos"
   - Tú eres el "controlador de datos"
   - Stripe tiene DPA (Data Processing Agreement) incluido en sus ToS
   - Ver: https://stripe.com/privacy

### Fiscalidad de Donaciones

**Ley 49/2002 de régimen fiscal de entidades sin fines lucrativos:**

**Si Coordicanarias está acogida a esta ley:**
- ✅ Los donantes pueden deducirse hasta el 80% de los primeros 150€
- ✅ Del resto, 35% (40% si donan 2+ años consecutivos)

**Obligaciones:**

1. **Emitir certificado de donación:**
   - Modificar el email de confirmación para incluir:
   - Nombre completo del donante
   - DNI/CIF (pedir en formulario)
   - Importe donado
   - Fecha
   - Firma del representante legal

2. **Modelo 182:**
   - Presentar anualmente en Hacienda
   - Listado de todos los donantes con importe > 150€/año
   - Plazo: Febrero (año siguiente)

3. **Registros contables:**
   - Guardar TODAS las donaciones en BD (ya lo haces)
   - Backup regular
   - Auditoría anual (si aplica)

### Transparencia

**Ley Orgánica 1/2002 de Asociaciones:**

Las asociaciones deben ser transparentes en el uso de fondos.

**Recomendaciones:**

1. **Publicar memoria anual:**
   - Total de donaciones recibidas
   - Desglose por área temática (si aplica)
   - Proyectos financiados con esos fondos

2. **Crear página de transparencia:**
   ```
   https://coordicanarias.com/new/transparencia.php
   ```
   - Mostrar gráficos de ingresos/gastos
   - Proyectos activos
   - Impacto social (personas beneficiadas, etc)

3. **Agregar a "gracias.php":**
   ```html
   <p>
       Tu donación se destinará a [DESCRIPCIÓN DEL PROYECTO].
       Puedes ver cómo usamos los fondos en nuestra
       <a href="transparencia.php">página de transparencia</a>.
   </p>
   ```

### Términos y Condiciones

Crear página `/terminos-donaciones.php` con:

1. **Política de reembolsos:**
   - "Las donaciones son voluntarias y no reembolsables salvo error en el importe"
   - "Para solicitar reembolso, contactar a: donaciones@coordicanarias.com"

2. **Uso de fondos:**
   - "Todos los fondos se destinan a [MISIÓN DE LA ASOCIACIÓN]"
   - "La junta directiva aprueba el presupuesto anual"

3. **Cancelación de membresías:**
   - "Los socios pueden cancelar en cualquier momento"
   - "No hay reembolso de cuotas ya pagadas"

Agregar link en `donaciones.php`:
```html
<p class="small text-muted">
    Al donar, aceptas nuestros
    <a href="terminos-donaciones.php" target="_blank">Términos y Condiciones</a>.
</p>
```

### Cumplimiento PCI DSS

**¿Necesitas certificarte?**

**NO**, porque:
- Usas Stripe Checkout (alojado en Stripe)
- Nunca tocas datos de tarjeta en tu servidor
- Stripe es PCI DSS Level 1 Compliant (el más alto)

**Importante:**
- NUNCA agregues campos de tarjeta en `donaciones.php`
- NUNCA guardes CVV, número completo de tarjeta, o fecha de expiración
- Siempre usa Checkout alojado de Stripe

Ver: https://stripe.com/docs/security/guide

---

## Código Completo

A continuación se incluye el código completo de todos los archivos necesarios.

---

### Código SQL - Tabla Donaciones

**Archivo:** `/database/donaciones.sql`

```sql
-- =====================================================
-- TABLA DE DONACIONES PARA STRIPE
-- Coordicanarias - Sistema de donaciones
-- =====================================================

-- Tabla para registrar donaciones únicas
CREATE TABLE IF NOT EXISTS donaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- IDs de Stripe (necesarios para reconciliación)
    stripe_session_id VARCHAR(255) UNIQUE NOT NULL COMMENT 'ID de la sesión de Stripe Checkout',
    stripe_payment_intent VARCHAR(255) COMMENT 'ID del PaymentIntent de Stripe',
    stripe_customer_id VARCHAR(255) COMMENT 'ID del cliente en Stripe',

    -- Datos de la donación
    tipo ENUM('donacion', 'socio') NOT NULL DEFAULT 'donacion' COMMENT 'Tipo de contribución',
    monto DECIMAL(10,2) NOT NULL COMMENT 'Importe en EUR',
    moneda VARCHAR(3) DEFAULT 'EUR' COMMENT 'Código de moneda ISO',

    -- Datos del donante
    email VARCHAR(255) NOT NULL COMMENT 'Email del donante',
    nombre VARCHAR(255) COMMENT 'Nombre completo',
    telefono VARCHAR(50) COMMENT 'Teléfono de contacto',
    mensaje TEXT COMMENT 'Mensaje opcional del donante',

    -- Estado de la donación
    estado ENUM('pendiente', 'completado', 'fallido', 'reembolsado') DEFAULT 'pendiente' COMMENT 'Estado del pago',

    -- Fechas de seguimiento
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Cuándo se creó la sesión',
    fecha_completado TIMESTAMP NULL COMMENT 'Cuándo se completó el pago',
    fecha_actualizado TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última modificación',

    -- Metadata adicional (JSON para flexibilidad)
    metadata JSON COMMENT 'Datos adicionales en formato JSON',

    -- Índices para búsquedas rápidas
    INDEX idx_email (email),
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha_creacion),
    INDEX idx_stripe_session (stripe_session_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Registro de donaciones procesadas por Stripe';

-- =====================================================
-- TABLA DE SOCIOS MENSUALES (FASE 2 - FUTURO)
-- =====================================================

CREATE TABLE IF NOT EXISTS socios (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- IDs de Stripe
    stripe_customer_id VARCHAR(255) UNIQUE NOT NULL COMMENT 'ID del cliente en Stripe',
    stripe_subscription_id VARCHAR(255) UNIQUE COMMENT 'ID de la suscripción en Stripe',

    -- Datos del socio
    email VARCHAR(255) NOT NULL COMMENT 'Email del socio',
    nombre VARCHAR(255) NOT NULL COMMENT 'Nombre completo',
    telefono VARCHAR(50) COMMENT 'Teléfono de contacto',

    -- Estado de la membresía
    estado ENUM('activo', 'cancelado', 'pausado', 'impagado') DEFAULT 'activo' COMMENT 'Estado de la suscripción',
    monto_mensual DECIMAL(10,2) DEFAULT 5.00 COMMENT 'Cuota mensual en EUR',

    -- Fechas
    fecha_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de alta como socio',
    fecha_cancelacion TIMESTAMP NULL COMMENT 'Fecha de baja',
    fecha_proximo_pago TIMESTAMP NULL COMMENT 'Próximo cobro programado',

    -- Metadata
    metadata JSON COMMENT 'Datos adicionales',

    -- Índices
    INDEX idx_email (email),
    INDEX idx_estado (estado),
    INDEX idx_stripe_customer (stripe_customer_id),
    INDEX idx_fecha_proximo_pago (fecha_proximo_pago)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Socios con membresía mensual recurrente';

-- =====================================================
-- CONFIGURACIÓN DE STRIPE
-- =====================================================

-- Insertar configuración necesaria para Stripe
INSERT INTO configuracion (clave, valor, descripcion, tipo) VALUES
('stripe_modo', 'test', 'Modo de operación: test (desarrollo) o live (producción)', 'select'),
('stripe_pk_test', '', 'Stripe Publishable Key para TEST mode', 'text'),
('stripe_sk_test', '', 'Stripe Secret Key para TEST mode (mantener privada)', 'password'),
('stripe_pk_live', '', 'Stripe Publishable Key para LIVE mode', 'text'),
('stripe_sk_live', '', 'Stripe Secret Key para LIVE mode (mantener privada)', 'password'),
('stripe_webhook_secret', '', 'Webhook signing secret para verificar eventos de Stripe', 'password'),
('donaciones_activo', '0', 'Activar sistema de donaciones (0=desactivado, 1=activo)', 'checkbox'),
('email_donaciones', 'info@coordicanarias.com', 'Email para recibir notificaciones de donaciones', 'email'),
('donacion_minima', '1.00', 'Importe mínimo de donación en EUR (Stripe requiere min 0.50)', 'number'),
('donacion_maxima', '5000.00', 'Importe máximo de donación en EUR (prevención de fraude)', 'number')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- =====================================================
-- DATOS DE EJEMPLO (SOLO PARA TESTING)
-- =====================================================

-- Insertar donación de ejemplo (comentar antes de producción)
-- INSERT INTO donaciones (
--     stripe_session_id,
--     stripe_payment_intent,
--     tipo,
--     monto,
--     email,
--     nombre,
--     telefono,
--     mensaje,
--     estado,
--     fecha_completado
-- ) VALUES (
--     'cs_test_ejemplo123',
--     'pi_test_ejemplo123',
--     'donacion',
--     25.00,
--     'ejemplo@coordicanarias.com',
--     'Juan Pérez García',
--     '+34 600 123 456',
--     'Donación de prueba para el área de empleo',
--     'completado',
--     NOW()
-- );
```

---

### Código PHP - stripe_config.php

**Archivo:** `/php/stripe_config.php`

```php
<?php
/**
 * Configuración de Stripe para Coordicanarias
 *
 * Este archivo:
 * - Carga la librería de Stripe vía Composer
 * - Lee las credenciales desde la base de datos
 * - Configura el modo (test/live) dinámicamente
 * - Define constantes globales para usar en toda la app
 *
 * @package Coordicanarias
 * @subpackage Stripe
 * @version 1.0
 */

// Cargar autoloader de Composer (librería de Stripe)
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar conexión a BD
require_once __DIR__ . '/db/connection.php';

/**
 * Obtener configuración de Stripe desde la base de datos
 *
 * @return array Configuración con claves stripe_*
 */
function getStripeConfig() {
    try {
        $db = Database::getInstance()->getConnection();

        $stmt = $db->query("
            SELECT clave, valor
            FROM configuracion
            WHERE clave LIKE 'stripe_%' OR clave = 'donaciones_activo'
        ");

        $config = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $config[$row['clave']] = $row['valor'];
        }

        return $config;

    } catch (PDOException $e) {
        error_log("Error al cargar configuración de Stripe: " . $e->getMessage());
        return [];
    }
}

// Cargar configuración
$stripeConfig = getStripeConfig();

// Determinar modo de operación (test o live)
$modo = $stripeConfig['stripe_modo'] ?? 'test';

// Seleccionar las claves API apropiadas según el modo
if ($modo === 'live') {
    $publishableKey = $stripeConfig['stripe_pk_live'] ?? '';
    $secretKey = $stripeConfig['stripe_sk_live'] ?? '';
} else {
    $publishableKey = $stripeConfig['stripe_pk_test'] ?? '';
    $secretKey = $stripeConfig['stripe_sk_test'] ?? '';
}

// Configurar la librería de Stripe
if (!empty($secretKey)) {
    \Stripe\Stripe::setApiKey($secretKey);
    \Stripe\Stripe::setApiVersion('2023-10-16'); // Versión fija para evitar breaking changes
}

// Definir constantes globales
define('STRIPE_MODE', $modo);
define('STRIPE_PUBLISHABLE_KEY', $publishableKey);
define('STRIPE_SECRET_KEY', $secretKey);
define('STRIPE_WEBHOOK_SECRET', $stripeConfig['stripe_webhook_secret'] ?? '');
define('SITE_URL', 'https://coordicanarias.com/new');
define('DONACIONES_ACTIVO', ($stripeConfig['donaciones_activo'] ?? '0') === '1');

/**
 * Verificar si Stripe está correctamente configurado
 *
 * @return bool True si las claves API están presentes
 */
function isStripeConfigured() {
    return !empty(STRIPE_SECRET_KEY) && !empty(STRIPE_PUBLISHABLE_KEY);
}

/**
 * Obtener límites de donación
 *
 * @return array ['min' => float, 'max' => float]
 */
function getDonacionLimites() {
    global $stripeConfig;
    return [
        'min' => floatval($stripeConfig['donacion_minima'] ?? 1.00),
        'max' => floatval($stripeConfig['donacion_maxima'] ?? 5000.00)
    ];
}

/**
 * Formatear importe para Stripe (en centavos)
 *
 * @param float $monto Importe en EUR
 * @return int Importe en centavos
 */
function formatearMontoStripe($monto) {
    return intval($monto * 100);
}

/**
 * Log de eventos de Stripe (desarrollo)
 *
 * @param string $mensaje Mensaje a loguear
 * @param array $contexto Datos adicionales
 */
function logStripe($mensaje, $contexto = []) {
    if (STRIPE_MODE === 'test') {
        error_log("[STRIPE] " . $mensaje . " | " . json_encode($contexto));
    }
}
```

---

### Código PHP - donaciones.php

**Archivo:** `/donaciones.php`

```php
<?php
/**
 * Página de donaciones - Coordicanarias
 *
 * Formulario para procesar donaciones únicas vía Stripe
 */

require_once __DIR__ . '/php/stripe_config.php';
require_once __DIR__ . '/php/core/security.php';

// Verificar que Stripe está configurado
if (!isStripeConfigured()) {
    die("El sistema de donaciones no está configurado. Contacta al administrador.");
}

// Verificar que donaciones están activas
if (!DONACIONES_ACTIVO) {
    die("El sistema de donaciones está temporalmente desactivado. Disculpa las molestias.");
}

// Obtener límites
$limites = getDonacionLimites();

// Mensaje de cancelación si vienen de Stripe
$cancelado = isset($_GET['cancelado']) && $_GET['cancelado'] === '1';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donaciones - Coordicanarias</title>

    <!-- Meta tags SEO -->
    <meta name="description" content="Apoya a Coordicanarias con tu donación. Ayúdanos a mejorar la vida de personas con discapacidad en Canarias.">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="css/style.css">

    <style>
        .donaciones-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
        }

        .btn-cantidad {
            width: 100%;
            padding: 20px;
            font-size: 1.5rem;
            font-weight: bold;
            border: 2px solid #667eea;
            background: white;
            color: #667eea;
            border-radius: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-cantidad:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-cantidad.active {
            background: #667eea;
            color: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
        }

        .cantidad-personalizada {
            display: none;
            margin-top: 15px;
        }

        .cantidad-personalizada.show {
            display: block;
        }

        .form-donacion {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .icono-seguridad {
            color: #28a745;
            font-size: 1.2rem;
            margin-right: 10px;
        }

        .alerta-cancelado {
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="donaciones-header">
        <div class="container">
            <h1><i class="fas fa-heart"></i> Haz una Donación</h1>
            <p class="lead">Tu apoyo hace posible nuestro trabajo en favor de las personas con discapacidad</p>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Alerta de cancelación -->
                <?php if ($cancelado): ?>
                <div class="alert alert-warning alerta-cancelado" role="alert">
                    <i class="fas fa-info-circle"></i>
                    <strong>Donación cancelada.</strong> No te preocupes, puedes intentarlo de nuevo cuando quieras.
                </div>
                <?php endif; ?>

                <!-- Formulario de donación -->
                <div class="form-donacion">
                    <h3 class="mb-4">Selecciona el importe</h3>

                    <form id="form-donacion" method="POST" action="php/crear_sesion_pago.php">

                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?= generarTokenCSRF() ?>">

                        <!-- Cantidades predefinidas -->
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-md-3">
                                <button type="button" class="btn-cantidad" data-cantidad="10">10€</button>
                            </div>
                            <div class="col-6 col-md-3">
                                <button type="button" class="btn-cantidad" data-cantidad="25">25€</button>
                            </div>
                            <div class="col-6 col-md-3">
                                <button type="button" class="btn-cantidad" data-cantidad="50">50€</button>
                            </div>
                            <div class="col-6 col-md-3">
                                <button type="button" class="btn-cantidad" data-cantidad="100">100€</button>
                            </div>
                        </div>

                        <!-- Cantidad personalizada -->
                        <div class="text-center mb-3">
                            <button type="button" class="btn btn-link" id="btn-personalizado">
                                <i class="fas fa-edit"></i> Otra cantidad
                            </button>
                        </div>

                        <div class="cantidad-personalizada" id="cantidad-personalizada">
                            <label for="monto" class="form-label">Importe personalizado (€)</label>
                            <input
                                type="number"
                                class="form-control form-control-lg"
                                id="monto"
                                name="monto"
                                min="<?= $limites['min'] ?>"
                                max="<?= $limites['max'] ?>"
                                step="0.01"
                                placeholder="Ej: 15.00"
                            >
                            <small class="form-text text-muted">
                                Mínimo: <?= $limites['min'] ?>€ - Máximo: <?= $limites['max'] ?>€
                            </small>
                        </div>

                        <hr class="my-4">

                        <h4 class="mb-3">Tus datos</h4>

                        <!-- Nombre -->
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre completo *</label>
                            <input
                                type="text"
                                class="form-control"
                                id="nombre"
                                name="nombre"
                                required
                                maxlength="255"
                                placeholder="Juan Pérez García"
                            >
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                required
                                maxlength="255"
                                placeholder="tu-email@example.com"
                            >
                            <small class="form-text text-muted">
                                Te enviaremos un comprobante de donación
                            </small>
                        </div>

                        <!-- Teléfono (opcional) -->
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono (opcional)</label>
                            <input
                                type="tel"
                                class="form-control"
                                id="telefono"
                                name="telefono"
                                maxlength="50"
                                placeholder="+34 600 123 456"
                            >
                        </div>

                        <!-- Mensaje (opcional) -->
                        <div class="mb-4">
                            <label for="mensaje" class="form-label">Mensaje (opcional)</label>
                            <textarea
                                class="form-control"
                                id="mensaje"
                                name="mensaje"
                                rows="3"
                                maxlength="500"
                                placeholder="Escribe un mensaje si lo deseas..."
                            ></textarea>
                            <small class="form-text text-muted" id="contador-caracteres">0/500 caracteres</small>
                        </div>

                        <!-- Checkbox privacidad -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="acepto_privacidad"
                                    name="acepto_privacidad"
                                    required
                                >
                                <label class="form-check-label" for="acepto_privacidad">
                                    He leído y acepto la
                                    <a href="politica-privacidad.php" target="_blank">Política de Privacidad</a>
                                </label>
                            </div>
                        </div>

                        <!-- Botón de envío -->
                        <button
                            type="submit"
                            class="btn btn-primary btn-lg w-100"
                            id="btn-submit"
                            disabled
                        >
                            <i class="fas fa-lock icono-seguridad"></i>
                            Donar ahora de forma segura
                        </button>

                        <!-- Información de seguridad -->
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt icono-seguridad"></i>
                                Pago seguro procesado por Stripe. No guardamos datos de tu tarjeta.
                            </small>
                        </div>

                    </form>
                </div>

                <!-- Información adicional -->
                <div class="mt-4 p-4 bg-light rounded">
                    <h5><i class="fas fa-info-circle"></i> Sobre las donaciones</h5>
                    <ul class="mb-0">
                        <li>Tu donación es <strong>voluntaria y no reembolsable</strong></li>
                        <li>Recibirás un <strong>comprobante por email</strong></li>
                        <li>Las donaciones se destinan a programas de empleo, formación, ocio y atención integral</li>
                        <li>Si tienes dudas, contacta con nosotros en:
                            <a href="mailto:info@coordicanarias.com">info@coordicanarias.com</a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript personalizado -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnsCantidad = document.querySelectorAll('.btn-cantidad');
            const btnPersonalizado = document.getElementById('btn-personalizado');
            const divPersonalizado = document.getElementById('cantidad-personalizada');
            const inputMonto = document.getElementById('monto');
            const inputMensaje = document.getElementById('mensaje');
            const contadorCaracteres = document.getElementById('contador-caracteres');
            const btnSubmit = document.getElementById('btn-submit');
            const formDonacion = document.getElementById('form-donacion');

            let montoSeleccionado = null;

            // Manejar clicks en cantidades predefinidas
            btnsCantidad.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Desactivar otros botones
                    btnsCantidad.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    // Guardar cantidad
                    montoSeleccionado = parseFloat(this.dataset.cantidad);

                    // Limpiar input personalizado
                    inputMonto.value = '';
                    divPersonalizado.classList.remove('show');

                    // Habilitar submit si cumple requisitos
                    validarFormulario();
                });
            });

            // Mostrar input personalizado
            btnPersonalizado.addEventListener('click', function() {
                divPersonalizado.classList.toggle('show');
                if (divPersonalizado.classList.contains('show')) {
                    inputMonto.focus();
                    // Desactivar botones predefinidos
                    btnsCantidad.forEach(b => b.classList.remove('active'));
                }
            });

            // Manejar cambio en input personalizado
            inputMonto.addEventListener('input', function() {
                montoSeleccionado = parseFloat(this.value) || null;
                validarFormulario();
            });

            // Contador de caracteres del mensaje
            inputMensaje.addEventListener('input', function() {
                const longitud = this.value.length;
                contadorCaracteres.textContent = `${longitud}/500 caracteres`;
            });

            // Validar formulario completo
            function validarFormulario() {
                const nombre = document.getElementById('nombre').value.trim();
                const email = document.getElementById('email').value.trim();
                const privacidad = document.getElementById('acepto_privacidad').checked;

                const esValido = (
                    montoSeleccionado !== null &&
                    montoSeleccionado >= <?= $limites['min'] ?> &&
                    montoSeleccionado <= <?= $limites['max'] ?> &&
                    nombre !== '' &&
                    email !== '' &&
                    privacidad
                );

                btnSubmit.disabled = !esValido;
            }

            // Validar en tiempo real
            document.getElementById('nombre').addEventListener('input', validarFormulario);
            document.getElementById('email').addEventListener('input', validarFormulario);
            document.getElementById('acepto_privacidad').addEventListener('change', validarFormulario);

            // Envío del formulario
            formDonacion.addEventListener('submit', function(e) {
                e.preventDefault();

                // Crear input oculto con el monto
                const inputMontoHidden = document.createElement('input');
                inputMontoHidden.type = 'hidden';
                inputMontoHidden.name = 'monto';
                inputMontoHidden.value = montoSeleccionado;
                formDonacion.appendChild(inputMontoHidden);

                // Desactivar botón (evitar double-submit)
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';

                // Enviar formulario
                formDonacion.submit();
            });
        });
    </script>
</body>
</html>
```

---

### Código PHP - crear_sesion_pago.php

**Archivo:** `/php/crear_sesion_pago.php`

```php
<?php
/**
 * Crear sesión de pago en Stripe
 *
 * Este script:
 * 1. Recibe datos del formulario de donaciones
 * 2. Valida todos los campos
 * 3. Crea una sesión de Stripe Checkout
 * 4. Guarda la donación en BD con estado "pendiente"
 * 5. Redirige al usuario a Stripe para completar el pago
 */

require_once __DIR__ . '/stripe_config.php';
require_once __DIR__ . '/core/security.php';
require_once __DIR__ . '/db/connection.php';

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Método no permitido");
}

// Verificar CSRF token
if (!verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
    die("Token CSRF inválido");
}

// Verificar que Stripe está configurado
if (!isStripeConfigured()) {
    die("El sistema de donaciones no está configurado");
}

try {
    // 1. VALIDAR Y SANITIZAR DATOS

    $monto = floatval($_POST['monto'] ?? 0);
    $nombre = sanitizarTexto($_POST['nombre'] ?? '');
    $email = sanitizarTexto($_POST['email'] ?? '');
    $telefono = sanitizarTexto($_POST['telefono'] ?? '');
    $mensaje = sanitizarTexto($_POST['mensaje'] ?? '');

    // Validar importe
    $limites = getDonacionLimites();
    if ($monto < $limites['min'] || $monto > $limites['max']) {
        die("El importe debe estar entre {$limites['min']}€ y {$limites['max']}€");
    }

    // Validar nombre
    if (empty($nombre) || strlen($nombre) > 255) {
        die("El nombre es obligatorio (máx 255 caracteres)");
    }

    // Validar email
    if (!validarEmail($email)) {
        die("El email no es válido");
    }

    // 2. CREAR SESIÓN DE STRIPE CHECKOUT

    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'customer_email' => $email,
        'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Donación a Coordicanarias',
                    'description' => 'Apoyo a personas con discapacidad en Canarias',
                    'images' => ['https://coordicanarias.com/new/images/logo.png'], // Opcional
                ],
                'unit_amount' => formatearMontoStripe($monto), // En centavos
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => SITE_URL . '/gracias.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => SITE_URL . '/donaciones.php?cancelado=1',
        'metadata' => [
            'nombre' => $nombre,
            'telefono' => $telefono,
            'mensaje' => $mensaje,
            'tipo' => 'donacion',
        ],
    ]);

    logStripe("Sesión de Stripe creada", [
        'session_id' => $session->id,
        'monto' => $monto,
        'email' => $email
    ]);

    // 3. GUARDAR DONACIÓN EN BASE DE DATOS (estado pendiente)

    $db = Database::getInstance()->getConnection();

    $stmt = $db->prepare("
        INSERT INTO donaciones (
            stripe_session_id,
            tipo,
            monto,
            moneda,
            email,
            nombre,
            telefono,
            mensaje,
            estado,
            metadata
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $metadata = json_encode([
        'stripe_mode' => STRIPE_MODE,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
    ]);

    $stmt->execute([
        $session->id,
        'donacion',
        $monto,
        'EUR',
        $email,
        $nombre,
        $telefono,
        $mensaje,
        'pendiente',
        $metadata
    ]);

    $donacion_id = $db->lastInsertId();

    logStripe("Donación guardada en BD", [
        'donacion_id' => $donacion_id,
        'session_id' => $session->id
    ]);

    // 4. REDIRIGIR A STRIPE CHECKOUT

    header("Location: " . $session->url);
    exit;

} catch (\Stripe\Exception\ApiErrorException $e) {
    // Error de Stripe
    error_log("Error de Stripe: " . $e->getMessage());
    die("Error al procesar el pago. Por favor, inténtalo de nuevo más tarde. Código: " . $e->getError()->code);

} catch (PDOException $e) {
    // Error de base de datos
    error_log("Error de BD en crear_sesion_pago: " . $e->getMessage());
    die("Error al guardar la donación. Por favor, contacta con nosotros.");

} catch (Exception $e) {
    // Error genérico
    error_log("Error genérico en crear_sesion_pago: " . $e->getMessage());
    die("Ha ocurrido un error inesperado. Por favor, inténtalo de nuevo.");
}
```

---

### Código PHP - gracias.php

**Archivo:** `/gracias.php`

```php
<?php
/**
 * Página de confirmación de donación
 *
 * Muestra mensaje de agradecimiento después de completar el pago en Stripe
 */

require_once __DIR__ . '/php/stripe_config.php';
require_once __DIR__ . '/php/db/connection.php';

// Obtener session_id de la URL
$session_id = $_GET['session_id'] ?? '';

if (empty($session_id)) {
    header("Location: donaciones.php");
    exit;
}

try {
    // Consultar sesión en Stripe
    $session = \Stripe\Checkout\Session::retrieve($session_id);

    // Obtener datos de la donación desde BD
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM donaciones WHERE stripe_session_id = ?");
    $stmt->execute([$session_id]);
    $donacion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$donacion) {
        throw new Exception("Donación no encontrada");
    }

} catch (Exception $e) {
    error_log("Error en gracias.php: " . $e->getMessage());
    $error = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Gracias por tu donación! - Coordicanarias</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .gracias-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .icono-exito {
            font-size: 5rem;
            color: white;
            margin-bottom: 20px;
            animation: zoomIn 0.5s;
        }

        @keyframes zoomIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .tarjeta-resumen {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-top: -50px;
        }

        .dato-donacion {
            border-left: 4px solid #28a745;
            padding-left: 15px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <?php if (isset($error)): ?>

        <!-- Error al cargar donación -->
        <div class="container mt-5">
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Error</h4>
                <p>No pudimos cargar los detalles de tu donación. Por favor, verifica tu email para el comprobante.</p>
                <hr>
                <a href="index.php" class="btn btn-primary">Volver al inicio</a>
            </div>
        </div>

    <?php else: ?>

        <!-- Header de agradecimiento -->
        <div class="gracias-header">
            <div class="container">
                <div class="icono-exito">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1>¡Gracias por tu donación!</h1>
                <p class="lead">Tu apoyo hace posible nuestro trabajo</p>
            </div>
        </div>

        <!-- Resumen de donación -->
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <div class="tarjeta-resumen">

                        <h3 class="mb-4">Resumen de tu donación</h3>

                        <div class="dato-donacion">
                            <strong>Importe:</strong><br>
                            <span class="h4 text-success"><?= number_format($donacion['monto'], 2) ?>€</span>
                        </div>

                        <div class="dato-donacion">
                            <strong>Nombre:</strong><br>
                            <?= htmlspecialchars($donacion['nombre']) ?>
                        </div>

                        <div class="dato-donacion">
                            <strong>Email:</strong><br>
                            <?= htmlspecialchars($donacion['email']) ?>
                        </div>

                        <div class="dato-donacion">
                            <strong>Fecha:</strong><br>
                            <?= date('d/m/Y H:i', strtotime($donacion['fecha_creacion'])) ?>
                        </div>

                        <?php if (!empty($donacion['mensaje'])): ?>
                        <div class="dato-donacion">
                            <strong>Tu mensaje:</strong><br>
                            <em>"<?= htmlspecialchars($donacion['mensaje']) ?>"</em>
                        </div>
                        <?php endif; ?>

                        <hr class="my-4">

                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-envelope"></i>
                            <strong>Hemos enviado un comprobante a tu email.</strong>
                            Guárdalo para tu declaración de la renta.
                        </div>

                        <div class="text-center mt-4">
                            <a href="index.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-home"></i> Volver al inicio
                            </a>
                        </div>

                    </div>

                    <!-- Información adicional -->
                    <div class="mt-4 p-4 bg-light rounded">
                        <h5><i class="fas fa-info-circle"></i> ¿Qué pasa ahora?</h5>
                        <ul class="mb-0">
                            <li>Tu donación se destinará a nuestros programas de empleo, formación y atención integral</li>
                            <li>Puedes deducir hasta el 80% de los primeros 150€ en tu declaración de la renta</li>
                            <li>Si tienes dudas, contacta con nosotros en:
                                <a href="mailto:info@coordicanarias.com">info@coordicanarias.com</a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>

    <?php endif; ?>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
```

---

### Código PHP - stripe_webhook.php

**Archivo:** `/php/webhooks/stripe_webhook.php`

```php
<?php
/**
 * Webhook de Stripe
 *
 * Recibe eventos de Stripe y los procesa:
 * - checkout.session.completed → Pago exitoso
 * - payment_intent.payment_failed → Pago fallido
 * - charge.refunded → Reembolso
 *
 * IMPORTANTE: Este archivo es llamado directamente por Stripe,
 * no por el usuario. No debe tener ninguna salida HTML.
 */

require_once __DIR__ . '/../stripe_config.php';
require_once __DIR__ . '/../db/connection.php';
require_once __DIR__ . '/../enviar_correo.php';

// Leer el cuerpo de la petición
$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

// Verificar que tenemos el webhook secret
if (empty(STRIPE_WEBHOOK_SECRET)) {
    error_log("Webhook secret no configurado");
    http_response_code(500);
    exit;
}

try {
    // Verificar la firma del webhook (seguridad)
    $event = \Stripe\Webhook::constructEvent(
        $payload,
        $sig_header,
        STRIPE_WEBHOOK_SECRET
    );

} catch (\UnexpectedValueException $e) {
    // Payload inválido
    error_log("Webhook payload inválido: " . $e->getMessage());
    http_response_code(400);
    exit;

} catch (\Stripe\Exception\SignatureVerificationException $e) {
    // Firma inválida
    error_log("Webhook firma inválida: " . $e->getMessage());
    http_response_code(400);
    exit;
}

// Obtener conexión a BD
$db = Database::getInstance()->getConnection();

// Procesar el evento según su tipo
switch ($event->type) {

    // ===================================================
    // PAGO COMPLETADO CON ÉXITO
    // ===================================================
    case 'checkout.session.completed':

        $session = $event->data->object;
        $session_id = $session->id;
        $payment_intent = $session->payment_intent;
        $customer_id = $session->customer;

        logStripe("Webhook recibido: checkout.session.completed", [
            'session_id' => $session_id
        ]);

        try {
            // Actualizar estado en BD
            $stmt = $db->prepare("
                UPDATE donaciones
                SET estado = 'completado',
                    fecha_completado = NOW(),
                    stripe_payment_intent = ?,
                    stripe_customer_id = ?
                WHERE stripe_session_id = ?
            ");

            $stmt->execute([$payment_intent, $customer_id, $session_id]);

            if ($stmt->rowCount() === 0) {
                throw new Exception("Donación no encontrada para session_id: $session_id");
            }

            logStripe("Donación actualizada a completado", ['session_id' => $session_id]);

            // Obtener datos de la donación
            $stmt = $db->prepare("SELECT * FROM donaciones WHERE stripe_session_id = ?");
            $stmt->execute([$session_id]);
            $donacion = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($donacion) {
                // Enviar email de confirmación
                enviarEmailDonacion($donacion);
            }

        } catch (Exception $e) {
            error_log("Error al procesar checkout.session.completed: " . $e->getMessage());
            http_response_code(500);
            exit;
        }

        break;

    // ===================================================
    // PAGO FALLIDO
    // ===================================================
    case 'payment_intent.payment_failed':

        $payment_intent = $event->data->object;
        $payment_intent_id = $payment_intent->id;

        logStripe("Webhook recibido: payment_intent.payment_failed", [
            'payment_intent' => $payment_intent_id
        ]);

        try {
            // Actualizar estado en BD
            $stmt = $db->prepare("
                UPDATE donaciones
                SET estado = 'fallido'
                WHERE stripe_payment_intent = ?
            ");

            $stmt->execute([$payment_intent_id]);

            logStripe("Donación actualizada a fallido", ['payment_intent' => $payment_intent_id]);

        } catch (Exception $e) {
            error_log("Error al procesar payment_intent.payment_failed: " . $e->getMessage());
        }

        break;

    // ===================================================
    // REEMBOLSO
    // ===================================================
    case 'charge.refunded':

        $charge = $event->data->object;
        $payment_intent = $charge->payment_intent;

        logStripe("Webhook recibido: charge.refunded", [
            'payment_intent' => $payment_intent
        ]);

        try {
            // Actualizar estado en BD
            $stmt = $db->prepare("
                UPDATE donaciones
                SET estado = 'reembolsado'
                WHERE stripe_payment_intent = ?
            ");

            $stmt->execute([$payment_intent]);

            logStripe("Donación actualizada a reembolsado", ['payment_intent' => $payment_intent]);

        } catch (Exception $e) {
            error_log("Error al procesar charge.refunded: " . $e->getMessage());
        }

        break;

    // ===================================================
    // OTROS EVENTOS (ignorar por ahora)
    // ===================================================
    default:
        logStripe("Webhook recibido (no procesado)", ['type' => $event->type]);
}

// Responder a Stripe con 200 OK
http_response_code(200);
exit;

// ===================================================
// FUNCIÓN AUXILIAR: Enviar email de confirmación
// ===================================================

function enviarEmailDonacion($donacion) {
    $nombre = htmlspecialchars($donacion['nombre']);
    $email = $donacion['email'];
    $monto = number_format($donacion['monto'], 2);
    $fecha = date('d/m/Y H:i', strtotime($donacion['fecha_completado']));

    $asunto = "Gracias por tu donación de {$monto}€ - Coordicanarias";

    $mensaje = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .footer { background: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; color: #666; }
                .button { background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                table td { padding: 10px; border-bottom: 1px solid #eee; }
                table td:first-child { font-weight: bold; width: 150px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>¡Gracias por tu donación!</h1>
            </div>

            <div class='content'>
                <p>Hola <strong>{$nombre}</strong>,</p>

                <p>Hemos recibido correctamente tu donación. Tu apoyo es fundamental para continuar nuestro trabajo en favor de las personas con discapacidad en Canarias.</p>

                <h3>Detalles de tu donación:</h3>
                <table>
                    <tr>
                        <td>Importe:</td>
                        <td><strong>{$monto}€</strong></td>
                    </tr>
                    <tr>
                        <td>Fecha:</td>
                        <td>{$fecha}</td>
                    </tr>
                    <tr>
                        <td>Referencia:</td>
                        <td>{$donacion['stripe_session_id']}</td>
                    </tr>
                </table>

                <p><strong>Deducción fiscal:</strong> Puedes deducir hasta el 80% de los primeros 150€ en tu declaración de la renta. Guarda este email como comprobante.</p>

                <p>Si necesitas un certificado oficial de donación, contáctanos en: <a href='mailto:info@coordicanarias.com'>info@coordicanarias.com</a></p>

                <a href='https://coordicanarias.com/new' class='button'>Visitar nuestro sitio web</a>

                <p>Un abrazo,<br>
                <strong>Equipo de Coordicanarias</strong></p>
            </div>

            <div class='footer'>
                <p>Coordinadora de Discapacidad de Canarias<br>
                Email: info@coordicanarias.com | Tel: +34 XXX XXX XXX</p>
                <p>Este email fue enviado automáticamente. Por favor, no respondas a este mensaje.</p>
            </div>
        </body>
        </html>
    ";

    try {
        enviarCorreo($email, $asunto, $mensaje, $nombre);
        logStripe("Email de confirmación enviado", ['email' => $email]);

        // También notificar al admin
        $config = getStripeConfig();
        $email_admin = $config['email_donaciones'] ?? 'info@coordicanarias.com';

        $asunto_admin = "Nueva donación recibida: {$monto}€";
        $mensaje_admin = "
            <p>Se ha recibido una nueva donación:</p>
            <ul>
                <li><strong>Nombre:</strong> {$nombre}</li>
                <li><strong>Email:</strong> {$email}</li>
                <li><strong>Importe:</strong> {$monto}€</li>
                <li><strong>Fecha:</strong> {$fecha}</li>
            </ul>
        ";

        enviarCorreo($email_admin, $asunto_admin, $mensaje_admin);

    } catch (Exception $e) {
        error_log("Error al enviar email de donación: " . $e->getMessage());
    }
}
```

---

## Conclusión

Con esta implementación completa de Stripe, Coordicanarias tendrá:

✅ Sistema de donaciones seguro y profesional
✅ Procesamiento de pagos con tarjeta sin necesidad de PCI DSS
✅ Emails automáticos de confirmación
✅ Registro completo de donaciones en base de datos
✅ Panel de Stripe para ver todas las transacciones
✅ Webhooks para garantizar que todos los pagos se registran
✅ Modo TEST para desarrollo sin riesgo
✅ Fácil migración a producción cuando esté listo

**Próximos pasos recomendados:**

1. Implementar membresías recurrentes (socios 5€/mes)
2. Agregar panel admin para ver donaciones en `/admin/donaciones.php`
3. Implementar Bizum vía Redsys (cuando el volumen lo justifique)
4. Agregar gráficos de donaciones por mes/año
5. Certificados de donación PDF automáticos

**¿Preguntas?**

Contacta con el equipo de desarrollo o consulta la documentación oficial de Stripe:
- https://stripe.com/docs
- https://stripe.com/docs/payments/checkout

---

**Documento creado:** 2026-01-04
**Versión:** 1.0
**Mantenido por:** Equipo de desarrollo Coordicanarias
