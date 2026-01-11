<?php
/**
 * Test de envío de email de donación
 * TEMPORAL: Eliminar después de probar
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/db/connection.php';
require_once __DIR__ . '/../php/emails_donaciones.php';

// Obtener la última donación de la BD
$donacion = fetchOne("SELECT * FROM donaciones ORDER BY id DESC LIMIT 1");

if (!$donacion) {
    echo "<h1>❌ No hay donaciones en la base de datos</h1>";
    echo "<p>Por favor, realiza una donación de prueba primero.</p>";
    exit;
}

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Test Email Donación</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        pre { background: #f9f9f9; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🧪 Test de Email de Donación</h1>

        <div class='info'>
            <strong>Última donación encontrada:</strong><br>
            ID: {$donacion['id']}<br>
            Nombre: " . htmlspecialchars($donacion['nombre']) . "<br>
            Email: " . htmlspecialchars($donacion['email']) . "<br>
            Importe: {$donacion['importe']} €<br>
            Estado: {$donacion['estado']}<br>
            Fecha: {$donacion['fecha_creacion']}
        </div>

        <h2>Intentando enviar email...</h2>";

// Intentar enviar email
try {
    $resultado = enviarEmailConfirmacionDonacion($donacion);

    if ($resultado) {
        echo "<div class='success'>
            <strong>✅ Email enviado correctamente</strong><br>
            Destinatario: " . htmlspecialchars($donacion['email']) . "<br>
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

        <hr>
        <p><a href='test-email-donacion.php'>🔄 Reintentar</a> | <a href='../admin/donaciones.php'>Ver Donaciones</a></p>

        <div class='info' style='margin-top: 20px;'>
            <strong>⚠️ IMPORTANTE:</strong> Elimina este archivo después de las pruebas por seguridad.
        </div>
    </div>
</body>
</html>";
?>
