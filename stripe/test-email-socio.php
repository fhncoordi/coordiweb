<?php
/**
 * Test de envío de email de socio
 * TEMPORAL: Eliminar después de probar
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/db/connection.php';
require_once __DIR__ . '/../php/emails_donaciones.php';

// Obtener el último socio de la BD
$socio = fetchOne("SELECT * FROM socios ORDER BY id DESC LIMIT 1");

if (!$socio) {
    echo "<h1>❌ No hay socios en la base de datos</h1>";
    echo "<p>Por favor, realiza una suscripción de prueba primero.</p>";
    echo "<p><a href='../hazte-socio.html'>Hacerse socio</a></p>";
    exit;
}

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Test Email Socio</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; }
        pre { background: #f9f9f9; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🧪 Test de Email de Socio</h1>

        <div class='info'>
            <strong>Último socio encontrado:</strong><br>
            ID: {$socio['id']}<br>
            Nombre: " . htmlspecialchars($socio['nombre']) . "<br>
            Email: " . htmlspecialchars($socio['email']) . "<br>
            Estado: {$socio['estado']}<br>
            Stripe Customer ID: " . ($socio['stripe_customer_id'] ?? 'NULL') . "<br>
            Stripe Subscription ID: " . ($socio['stripe_subscription_id'] ?? 'NULL') . "<br>
            Fecha creación: {$socio['fecha_creacion']}<br>
            Fecha inicio: " . ($socio['fecha_inicio'] ?? 'NULL') . "
        </div>";

// Verificar si tiene stripe_subscription_id
if (empty($socio['stripe_subscription_id'])) {
    echo "<div class='warning'>
        <strong>⚠️ ADVERTENCIA:</strong> Este socio NO tiene stripe_subscription_id asignado.<br>
        Esto significa que el webhook no actualizó correctamente el registro.<br><br>
        <strong>Posibles causas:</strong><br>
        1. El email en Stripe no coincide con el email en la BD<br>
        2. El webhook no se ejecutó correctamente<br>
        3. El socio ya tenía un stripe_subscription_id (el UPDATE solo afecta registros con NULL)
    </div>";
}

echo "<h2>Intentando enviar email de bienvenida...</h2>";

// Intentar enviar email
try {
    $resultado = enviarEmailBienvenidaSocio($socio);

    if ($resultado) {
        echo "<div class='success'>
            <strong>✅ Email enviado correctamente</strong><br>
            Destinatario: " . htmlspecialchars($socio['email']) . "<br>
            Verifica tu bandeja de entrada (y spam)
        </div>";
    } else {
        echo "<div class='error'>
            <strong>❌ Error al enviar email</strong><br>
            La función mail() devolvió false.<br>
            Posibles causas:<br>
            - El servidor no permite envío de emails<br>
            - El email está mal configurado<br>
            - El destinatario está bloqueado
        </div>";
    }

} catch (Exception $e) {
    echo "<div class='error'>
        <strong>❌ Excepción al enviar email:</strong><br>
        " . htmlspecialchars($e->getMessage()) . "
    </div>";
}

echo "
        <h2>Información del servidor</h2>
        <pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "mail() disponible: " . (function_exists('mail') ? 'SÍ' : 'NO') . "\n";
echo "EMAIL_METHOD configurado: " . (defined('EMAIL_METHOD') ? EMAIL_METHOD : 'NO DEFINIDO') . "\n";
echo "</pre>

        <h2>Verificar webhook de Stripe</h2>
        <div class='info'>
            <p>Para ver si el webhook está procesando suscripciones correctamente:</p>
            <ol>
                <li>Ve a <a href='https://dashboard.stripe.com/test/webhooks' target='_blank'>Stripe Dashboard - Webhooks</a></li>
                <li>Click en tu endpoint de webhook</li>
                <li>Busca eventos recientes de tipo <code>checkout.session.completed</code> con <code>mode: subscription</code></li>
                <li>Verifica si hay errores en los logs</li>
            </ol>
        </div>

        <hr>
        <p><a href='test-email-socio.php'>🔄 Reintentar</a> | <a href='../admin/socios.php'>Ver Socios</a></p>

        <div class='info' style='margin-top: 20px;'>
            <strong>⚠️ IMPORTANTE:</strong> Elimina este archivo después de las pruebas por seguridad.
        </div>
    </div>
</body>
</html>";
?>
