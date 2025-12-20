<?php
/**
 * Script de prueba del formulario completo
 * Simula un envío desde el formulario de contacto
 * ELIMINAR después de usar
 */

echo "<h1>Test del Formulario de Contacto</h1>";
echo "<pre>";

// Simular datos del formulario
$_POST['area'] = 'prueba';
$_POST['txtName'] = 'Usuario de Prueba';
$_POST['txtEmail'] = 'prueba@ejemplo.com';
$_POST['txtMsg'] = 'Este es un mensaje de prueba para verificar que el formulario funciona correctamente.';

// Simular método POST
$_SERVER['REQUEST_METHOD'] = 'POST';

// Simular referer (importante para la validación)
$_SERVER['HTTP_REFERER'] = 'https://coordicanarias.com/new/index.html';

echo "=== DATOS SIMULADOS ===\n";
echo "Método: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "Referer: " . $_SERVER['HTTP_REFERER'] . "\n";
echo "Área: " . $_POST['area'] . "\n";
echo "Nombre: " . $_POST['txtName'] . "\n";
echo "Email: " . $_POST['txtEmail'] . "\n";
echo "Mensaje: " . $_POST['txtMsg'] . "\n\n";

echo "=== EJECUTANDO enviar_correo.php ===\n";
echo "Capturando salida...\n\n";

// Capturar la salida de enviar_correo.php
ob_start();

try {
    // Verificar que el archivo existe
    if (!file_exists('../enviar_correo.php')) {
        echo "✗ ERROR: enviar_correo.php NO EXISTE en el servidor\n";
        echo "  Ruta buscada: " . __DIR__ . "/../enviar_correo.php\n";
    } else {
        echo "✓ Archivo enviar_correo.php encontrado\n";
        echo "  Ejecutando...\n\n";

        // Incluir el archivo (esto ejecutará el código)
        include '../enviar_correo.php';
    }
} catch (Exception $e) {
    echo "✗ ERROR CAPTURADO:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

$output = ob_get_clean();

echo "=== SALIDA DEL SCRIPT ===\n";
echo $output;
echo "\n";

echo "=== RESULTADO ===\n";
if (strpos($output, 'success=1') !== false) {
    echo "✓ El script redirigió con éxito\n";
    echo "  El formulario debería funcionar correctamente\n";
} elseif (strpos($output, 'error=') !== false) {
    echo "✗ El script redirigió con un error\n";
    preg_match('/error=([^&\s]+)/', $output, $matches);
    if (isset($matches[1])) {
        echo "  Código de error: " . urldecode($matches[1]) . "\n";
    }
} else {
    echo "⚠ Respuesta inesperada\n";
    echo "  Revisa la salida del script arriba\n";
}

echo "</pre>";

echo "<p style='color: red; font-weight: bold;'>⚠️ IMPORTANTE: Elimina este archivo (test_formulario.php) después de usarlo.</p>";
?>
