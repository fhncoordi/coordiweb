<?php
/**
 * Test de conexión SMTP directa
 * Verifica si el servidor puede conectarse a Gmail SMTP
 * ELIMINAR después de usar
 */

echo "<h1>Test de Conexión SMTP Directa</h1>";
echo "<pre>";

echo "=== TEST 1: Conexión al puerto 465 (SSL) ===\n";
$errno = 0;
$errstr = '';
$timeout = 10;

$socket = @fsockopen('ssl://smtp.gmail.com', 465, $errno, $errstr, $timeout);

if ($socket) {
    echo "✓ Puerto 465 ABIERTO - Conexión exitosa\n";
    echo "  Respuesta del servidor: ";
    echo fgets($socket, 1024);
    fclose($socket);
} else {
    echo "✗ Puerto 465 BLOQUEADO\n";
    echo "  Error: $errstr (código: $errno)\n";
}

echo "\n=== TEST 2: Conexión al puerto 587 (STARTTLS) ===\n";
$socket = @fsockopen('smtp.gmail.com', 587, $errno, $errstr, $timeout);

if ($socket) {
    echo "✓ Puerto 587 ABIERTO - Conexión exitosa\n";
    echo "  Respuesta del servidor: ";
    echo fgets($socket, 1024);
    fclose($socket);
} else {
    echo "✗ Puerto 587 BLOQUEADO\n";
    echo "  Error: $errstr (código: $errno)\n";
}

echo "\n=== TEST 3: Verificar mail() con headers completos ===\n";

$to = 'fhn@coordicanarias.com';
$subject = 'Test con headers completos - ' . date('H:i:s');
$message = 'Este es un test de email con todos los headers configurados correctamente.';

// Headers más completos
$headers = array(
    'From: Coordicanarias - Formulario Web <noreply@coordicanarias.com>',
    'Reply-To: noreply@coordicanarias.com',
    'X-Mailer: PHP/' . phpversion(),
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: 8bit'
);

$result = mail($to, $subject, $message, implode("\r\n", $headers));

if ($result) {
    echo "✓ mail() devolvió TRUE\n";
    echo "  Email enviado a: $to\n";
    echo "  NOTA: Aunque devuelva TRUE, puede no llegar\n";
    echo "  Revisa spam en 5-10 minutos\n";
} else {
    echo "✗ mail() devolvió FALSE\n";
    echo "  La función mail() está bloqueada o mal configurada\n";
}

echo "\n=== TEST 4: Información de configuración ===\n";
echo "sendmail_path: " . ini_get('sendmail_path') . "\n";
echo "sendmail_from: " . ini_get('sendmail_from') . "\n";
echo "SMTP (Windows): " . ini_get('SMTP') . "\n";
echo "smtp_port (Windows): " . ini_get('smtp_port') . "\n";

echo "\n=== CONCLUSIÓN ===\n";
if (!$socket && $errno != 0) {
    echo "⚠️  PROBLEMA CONFIRMADO:\n";
    echo "   Los puertos SMTP están BLOQUEADOS por el firewall del servidor.\n";
    echo "   Solución: Contactar a Alojared para que habiliten los puertos.\n\n";
    echo "   Opciones:\n";
    echo "   1. Pedir que abran puerto 465 (SSL) o 587 (STARTTLS)\n";
    echo "   2. Usar un servicio alternativo (SendGrid, Mailgun, etc.)\n";
    echo "   3. Configurar correctamente el servidor de correo local\n";
}

echo "</pre>";

echo "<p style='color: red; font-weight: bold;'>⚠️ ELIMINA este archivo después de usarlo.</p>";
?>
