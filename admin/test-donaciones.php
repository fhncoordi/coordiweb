<?php
/**
 * Test de diagnóstico para donaciones.php
 */

// Activar errores para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Test de Diagnóstico</h1>";
echo "<hr>";

// Test 1: Verificar archivos base
echo "<h2>1. Verificación de archivos</h2>";

$archivos = [
    '../php/config.php',
    '../php/core/auth.php',
    '../php/core/security.php',
    '../php/db/connection.php'
];

foreach ($archivos as $archivo) {
    $ruta = __DIR__ . '/' . $archivo;
    if (file_exists($ruta)) {
        echo "✅ <strong>$archivo</strong> existe<br>";
    } else {
        echo "❌ <strong>$archivo</strong> NO EXISTE<br>";
    }
}

echo "<hr>";

// Test 2: Cargar config
echo "<h2>2. Cargar configuración</h2>";
try {
    require_once __DIR__ . '/../php/config.php';
    echo "✅ config.php cargado correctamente<br>";
} catch (Exception $e) {
    echo "❌ Error cargando config.php: " . $e->getMessage() . "<br>";
    exit;
}

echo "<hr>";

// Test 3: Cargar auth
echo "<h2>3. Cargar autenticación</h2>";
try {
    require_once __DIR__ . '/../php/core/auth.php';
    echo "✅ auth.php cargado correctamente<br>";

    if (function_exists('isLoggedIn')) {
        echo "✅ Función isLoggedIn() existe<br>";

        if (isLoggedIn()) {
            echo "✅ Usuario AUTENTICADO<br>";
            echo "User ID: " . ($_SESSION['user_id'] ?? 'N/A') . "<br>";
            echo "Username: " . ($_SESSION['username'] ?? 'N/A') . "<br>";
        } else {
            echo "⚠️ Usuario NO autenticado<br>";
        }
    } else {
        echo "❌ Función isLoggedIn() NO existe<br>";
    }
} catch (Exception $e) {
    echo "❌ Error cargando auth.php: " . $e->getMessage() . "<br>";
    exit;
}

echo "<hr>";

// Test 4: Verificar conexión BD
echo "<h2>4. Conexión a base de datos</h2>";
try {
    require_once __DIR__ . '/../php/db/connection.php';
    echo "✅ connection.php cargado correctamente<br>";

    if (function_exists('fetchAll')) {
        echo "✅ Función fetchAll() existe<br>";

        // Test query simple
        $donaciones = fetchAll("SELECT COUNT(*) as total FROM donaciones");
        if ($donaciones) {
            echo "✅ Consulta a tabla donaciones exitosa<br>";
            echo "Total donaciones en BD: " . ($donaciones[0]['total'] ?? 0) . "<br>";
        }
    } else {
        echo "❌ Función fetchAll() NO existe<br>";
    }
} catch (Exception $e) {
    echo "❌ Error con BD: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 5: Verificar includes
echo "<h2>5. Verificar archivos de layout</h2>";

$layouts = [
    'includes/header.php',
    'includes/sidebar.php',
    'includes/footer.php'
];

foreach ($layouts as $archivo) {
    $ruta = __DIR__ . '/' . $archivo;
    if (file_exists($ruta)) {
        echo "✅ <strong>$archivo</strong> existe<br>";
    } else {
        echo "❌ <strong>$archivo</strong> NO EXISTE<br>";
    }
}

echo "<hr>";
echo "<h2>✅ Test completado</h2>";
echo "<p><a href='index.php'>← Volver al Dashboard</a></p>";
?>
