<?php
/**
 * Test de conexión a Google Workspace
 * Verifica si el problema es de Google o del servidor
 * ELIMINAR después de usar
 */

echo "<h1>Verificación de Google Workspace</h1>";
echo "<pre>";

// Cargar configuración
require_once '../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

echo "=== CONFIGURACIÓN ACTUAL ===\n";
echo "SMTP_HOST: " . SMTP_HOST . "\n";
echo "SMTP_PORT: " . SMTP_PORT . "\n";
echo "SMTP_USER: " . SMTP_USER . "\n";
echo "SMTP_PASS: " . (strlen(SMTP_PASS) > 0 ? "[" . strlen(SMTP_PASS) . " caracteres]" : "[VACÍA]") . "\n";
echo "SMTP_SECURE: " . SMTP_SECURE . "\n\n";

echo "=== TEST 1: Verificar conexión básica ===\n";
$mail = new PHPMailer(true);
$mail->SMTPDebug = SMTP::DEBUG_SERVER; // Modo verbose
$mail->isSMTP();
$mail->Host = SMTP_HOST;
$mail->Port = SMTP_PORT;
$mail->SMTPSecure = SMTP_SECURE;

echo "\nIntentando conectar a " . SMTP_HOST . ":" . SMTP_PORT . "...\n";
echo "Esto mostrará exactamente qué responde el servidor:\n\n";

try {
    // Intentar envío de prueba con debug completo
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASS;

    $mail->setFrom(SMTP_USER, 'Test Coordicanarias');
    $mail->addAddress('fhn@coordicanarias.com');
    $mail->Subject = 'Test de Google Workspace - ' . date('H:i:s');
    $mail->Body = 'Si recibes este email, Google Workspace está funcionando correctamente.';

    $mail->send();

    echo "\n✓ EMAIL ENVIADO CORRECTAMENTE\n";
    echo "  Google Workspace está funcionando bien\n";

} catch (Exception $e) {
    echo "\n✗ ERROR AL ENVIAR\n";
    echo "  Mensaje: {$mail->ErrorInfo}\n\n";

    // Analizar el tipo de error
    $error = $mail->ErrorInfo;

    if (strpos($error, 'Connection refused') !== false || strpos($error, 'Connection timed out') !== false) {
        echo "DIAGNÓSTICO:\n";
        echo "  🔴 PROBLEMA DEL SERVIDOR (Firewall bloqueando)\n";
        echo "  ✓ Google Workspace probablemente está bien configurado\n";
        echo "  ✗ El servidor no permite conexiones salientes SMTP\n";
        echo "  Solución: Contactar a Alojared\n";

    } elseif (strpos($error, 'Authentication') !== false || strpos($error, 'Username and Password') !== false) {
        echo "DIAGNÓSTICO:\n";
        echo "  🔴 PROBLEMA DE GOOGLE WORKSPACE (Credenciales incorrectas)\n";
        echo "  ✓ El servidor permite la conexión\n";
        echo "  ✗ Google rechaza las credenciales\n";
        echo "  Solución: Verificar configuración de Google Workspace\n";

    } else {
        echo "DIAGNÓSTICO:\n";
        echo "  ⚠️ Error desconocido\n";
        echo "  Revisar la salida de debug arriba\n";
    }
}

echo "\n=== COSAS A VERIFICAR EN GOOGLE WORKSPACE ===\n";
echo "1. Ve a: https://mail.google.com/mail/u/0/#settings/fwdandpop\n";
echo "   (inicia sesión con noreply@coordicanarias.com)\n";
echo "   Verifica que esté habilitado: 'Activar IMAP'\n\n";

echo "2. Ve a: https://myaccount.google.com/security\n";
echo "   Verifica que esté habilitado: 'Verificación en dos pasos'\n\n";

echo "3. Ve a: https://myaccount.google.com/apppasswords\n";
echo "   Verifica que la contraseña de aplicación esté activa\n\n";

echo "4. Si es cuenta de Google Workspace administrada:\n";
echo "   Pide al administrador verificar que SMTP esté habilitado\n";
echo "   para toda la organización\n";

echo "</pre>";
echo "<p style='color: red; font-weight: bold;'>⚠️ ELIMINA este archivo después de usarlo.</p>";
?>
