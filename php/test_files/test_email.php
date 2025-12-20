<?php
/**
 * Script de prueba simple para diagnosticar envío de emails
 * ELIMINAR después de usar
 */

// Cargar configuración
require_once '../config.php';

echo "<h1>Test de envío de email</h1>";
echo "<pre>";

// Mostrar configuración (sin contraseña)
echo "=== CONFIGURACIÓN ===\n";
echo "EMAIL_METHOD: " . EMAIL_METHOD . "\n";
echo "SMTP_HOST: " . SMTP_HOST . "\n";
echo "SMTP_PORT: " . SMTP_PORT . "\n";
echo "SMTP_SECURE: " . SMTP_SECURE . "\n";
echo "SMTP_USER: " . SMTP_USER . "\n";
echo "SMTP_PASS: " . (strlen(SMTP_PASS) > 0 ? "[configurada - " . strlen(SMTP_PASS) . " caracteres]" : "[NO CONFIGURADA]") . "\n\n";

// Test de mail() nativa
echo "=== TEST DE mail() NATIVA ===\n";

$to = 'fhn@coordicanarias.com';
$subject = 'Test de email desde ' . $_SERVER['HTTP_HOST'];
$message = 'Este es un email de prueba enviado desde el servidor usando mail() nativa de PHP.';
$headers = "From: " . SMTP_FROM_NAME . " <" . SMTP_USER . ">\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$result = mail($to, $subject, $message, $headers);

if ($result) {
    echo "✓ mail() ejecutado correctamente\n";
    echo "  Email enviado a: $to\n";
    echo "  Verifica tu bandeja de entrada (puede tardar unos minutos)\n";
} else {
    echo "✗ mail() falló\n";
    echo "  Posible causa: La función mail() está deshabilitada en el servidor\n";
}

echo "\n=== INFORMACIÓN DEL SERVIDOR ===\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Servidor: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "mail() disponible: " . (function_exists('mail') ? 'SÍ' : 'NO') . "\n";
echo "sendmail_path: " . ini_get('sendmail_path') . "\n";

echo "\n=== EXTENSIONES PHP ===\n";
echo "openssl: " . (extension_loaded('openssl') ? 'SÍ' : 'NO') . "\n";
echo "sockets: " . (extension_loaded('sockets') ? 'SÍ' : 'NO') . "\n";
echo "mbstring: " . (extension_loaded('mbstring') ? 'SÍ' : 'NO') . "\n";

echo "</pre>";

echo "<p style='color: red; font-weight: bold;'>⚠️ IMPORTANTE: Elimina este archivo (test_email.php) después de usarlo por seguridad.</p>";
?>
