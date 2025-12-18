<?php
/**
 * Test simple de la función mail() de PHP
 * Coordicanarias - 2025
 */

// Configuración
$destino = 'fhn@coordicanarias.com';
$asunto = 'Test de mail() - ' . date('H:i:s');
$mensaje = 'Este es un correo de prueba enviado con mail() nativa de PHP.';

$headers = array();
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/plain; charset=UTF-8';
$headers[] = 'From: Coordicanarias Test <noreply@coordicanarias.com>';
$headers[] = 'X-Mailer: PHP/' . phpversion();

echo "<h1>Test de función mail()</h1>";
echo "<hr>";
echo "<strong>Enviando a:</strong> $destino<br>";
echo "<strong>Asunto:</strong> $asunto<br>";
echo "<strong>Hora:</strong> " . date('Y-m-d H:i:s') . "<br>";
echo "<hr>";

// Verificar si mail() está habilitada
if (!function_exists('mail')) {
    echo "<p style='color: red;'>❌ La función mail() NO está disponible en este servidor.</p>";
    exit;
}

echo "<p style='color: green;'>✓ La función mail() está disponible</p>";

// Intentar enviar (con parámetro -f para usar noreply como remitente)
$parametros_adicionales = '-fnoreply@coordicanarias.com';
$resultado = mail(
    $destino,
    $asunto,
    $mensaje,
    implode("\r\n", $headers),
    $parametros_adicionales
);

if ($resultado) {
    echo "<p style='color: green;'><strong>✓ mail() retornó TRUE</strong></p>";
    echo "<p>Esto significa que PHP entregó el correo al servidor de correo local.</p>";
    echo "<p><strong>Verifica:</strong></p>";
    echo "<ul>";
    echo "<li>Bandeja de entrada de $destino</li>";
    echo "<li>Carpeta de SPAM</li>";
    echo "<li>Espera 2-5 minutos (puede tardar)</li>";
    echo "</ul>";
} else {
    echo "<p style='color: red;'><strong>❌ mail() retornó FALSE</strong></p>";
    echo "<p>Esto significa que PHP no pudo entregar el correo al servidor de correo.</p>";
    echo "<p><strong>Posibles causas:</strong></p>";
    echo "<ul>";
    echo "<li>Sendmail no está configurado correctamente</li>";
    echo "<li>El hosting tiene bloqueada la función mail()</li>";
    echo "<li>Problemas de permisos</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<h3>Información del servidor</h3>";
echo "<strong>PHP Version:</strong> " . phpversion() . "<br>";
echo "<strong>Sistema:</strong> " . PHP_OS . "<br>";

// Verificar configuración de mail en php.ini
echo "<br><strong>Configuración de mail en PHP:</strong><br>";
echo "sendmail_path: " . ini_get('sendmail_path') . "<br>";
echo "SMTP: " . ini_get('SMTP') . "<br>";
echo "smtp_port: " . ini_get('smtp_port') . "<br>";

echo "<hr>";
echo "<p><a href='enviar_correo.php'>← Volver</a></p>";
?>
