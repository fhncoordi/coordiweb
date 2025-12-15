<?php
/**
 * Script de verificación de requisitos del servidor
 * Coordicanarias - Diagnóstico para PHPMailer con SMTP
 *
 * IMPORTANTE: Elimina este archivo después de usarlo por seguridad
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación del Servidor - PHPMailer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }
        .check-item {
            background: white;
            margin: 10px 0;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #ddd;
        }
        .check-item.success {
            border-left-color: #28a745;
        }
        .check-item.warning {
            border-left-color: #ffc107;
            background-color: #fff3cd;
        }
        .check-item.error {
            border-left-color: #dc3545;
            background-color: #f8d7da;
        }
        .status {
            font-weight: bold;
            margin-right: 10px;
        }
        .success .status { color: #28a745; }
        .warning .status { color: #ffc107; }
        .error .status { color: #dc3545; }
        .info-box {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .alert {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            color: #721c24;
        }
    </style>
</head>
<body>
    <h1>🔍 Verificación del Servidor</h1>
    <p>Este script verifica que tu servidor tenga todo lo necesario para que PHPMailer funcione con SMTP de Google.</p>

    <?php
    $all_ok = true;

    // Verificar versión de PHP
    echo '<h2>📋 Información General</h2>';

    $php_version = phpversion();
    $php_ok = version_compare($php_version, '5.5.0', '>=');
    echo '<div class="check-item ' . ($php_ok ? 'success' : 'error') . '">';
    echo '<span class="status">' . ($php_ok ? '✓' : '✗') . '</span>';
    echo '<strong>Versión de PHP:</strong> ' . $php_version;
    if (!$php_ok) {
        echo ' <em>(Se requiere PHP 5.5.0 o superior)</em>';
        $all_ok = false;
    }
    echo '</div>';

    // Verificar extensiones críticas
    echo '<h2>🔌 Extensiones PHP Necesarias</h2>';

    $extensions = [
        'openssl' => 'Necesaria para conexiones seguras (TLS/SSL) con Gmail',
        'sockets' => 'Necesaria para conexiones de red',
        'mbstring' => 'Recomendada para manejo de caracteres especiales',
    ];

    foreach ($extensions as $ext => $description) {
        $loaded = extension_loaded($ext);
        echo '<div class="check-item ' . ($loaded ? 'success' : ($ext === 'mbstring' ? 'warning' : 'error')) . '">';
        echo '<span class="status">' . ($loaded ? '✓' : '✗') . '</span>';
        echo '<strong>' . $ext . ':</strong> ' . ($loaded ? 'Habilitada' : 'NO habilitada');
        echo '<br><small>' . $description . '</small>';
        echo '</div>';

        if (!$loaded && $ext !== 'mbstring') {
            $all_ok = false;
        }
    }

    // Verificar funciones necesarias
    echo '<h2>⚙️ Funciones PHP Requeridas</h2>';

    $functions = [
        'fsockopen' => 'Conexiones de red básicas',
        'stream_socket_client' => 'Conexiones de red seguras',
        'function_exists' => 'Verificación de funciones',
    ];

    $disabled_functions = ini_get('disable_functions');

    foreach ($functions as $func => $description) {
        $available = function_exists($func) && stripos($disabled_functions, $func) === false;
        echo '<div class="check-item ' . ($available ? 'success' : 'error') . '">';
        echo '<span class="status">' . ($available ? '✓' : '✗') . '</span>';
        echo '<strong>' . $func . ':</strong> ' . ($available ? 'Disponible' : 'NO disponible / Deshabilitada');
        echo '<br><small>' . $description . '</small>';
        echo '</div>';

        if (!$available) {
            $all_ok = false;
        }
    }

    // Verificar configuración PHP
    echo '<h2>⚙️ Configuración PHP</h2>';

    $allow_url_fopen = ini_get('allow_url_fopen');
    echo '<div class="check-item ' . ($allow_url_fopen ? 'success' : 'warning') . '">';
    echo '<span class="status">' . ($allow_url_fopen ? '✓' : '⚠') . '</span>';
    echo '<strong>allow_url_fopen:</strong> ' . ($allow_url_fopen ? 'Habilitado' : 'Deshabilitado');
    echo '<br><small>Permite operaciones de red</small>';
    echo '</div>';

    // Verificar permisos de escritura
    echo '<h2>📁 Permisos de Archivos</h2>';

    $upload_dir = dirname(__FILE__);
    $writable = is_writable($upload_dir);
    echo '<div class="check-item ' . ($writable ? 'success' : 'warning') . '">';
    echo '<span class="status">' . ($writable ? '✓' : '⚠') . '</span>';
    echo '<strong>Directorio php/:</strong> ' . ($writable ? 'Escribible' : 'No escribible');
    echo '<br><small>Ruta: ' . $upload_dir . '</small>';
    echo '</div>';

    // Probar conexión a Gmail SMTP
    echo '<h2>🌐 Test de Conexión a Gmail SMTP</h2>';

    $smtp_test = false;
    $smtp_message = '';

    if (function_exists('fsockopen')) {
        $errno = 0;
        $errstr = '';
        $socket = @fsockopen('smtp.gmail.com', 465, $errno, $errstr, 10);

        if ($socket) {
            $smtp_test = true;
            $smtp_message = 'Conexión exitosa al servidor SMTP de Gmail';
            fclose($socket);
        } else {
            $smtp_message = 'No se pudo conectar: ' . $errstr . ' (código: ' . $errno . ')';
        }
    } else {
        $smtp_message = 'La función fsockopen no está disponible';
    }

    echo '<div class="check-item ' . ($smtp_test ? 'success' : 'error') . '">';
    echo '<span class="status">' . ($smtp_test ? '✓' : '✗') . '</span>';
    echo '<strong>Conexión a smtp.gmail.com:465 (SSL):</strong> ' . $smtp_message;
    echo '</div>';

    if (!$smtp_test) {
        $all_ok = false;
    }

    // Verificar archivos de PHPMailer
    echo '<h2>📦 Archivos de PHPMailer</h2>';

    $phpmailer_files = [
        'PHPMailer/PHPMailer.php',
        'PHPMailer/SMTP.php',
        'PHPMailer/Exception.php',
        'enviar_correo.php',
    ];

    foreach ($phpmailer_files as $file) {
        $file_path = dirname(__FILE__) . '/' . $file;
        $exists = file_exists($file_path);
        echo '<div class="check-item ' . ($exists ? 'success' : 'error') . '">';
        echo '<span class="status">' . ($exists ? '✓' : '✗') . '</span>';
        echo '<strong>' . $file . ':</strong> ' . ($exists ? 'Encontrado' : 'NO encontrado');
        echo '</div>';

        if (!$exists) {
            $all_ok = false;
        }
    }

    // Resultado final
    echo '<h2>📊 Resultado Final</h2>';

    if ($all_ok) {
        echo '<div class="info-box">';
        echo '<h3 style="color: #28a745; margin-top: 0;">✓ ¡Todo está correcto!</h3>';
        echo '<p>Tu servidor tiene todos los requisitos necesarios para que PHPMailer funcione correctamente con Gmail SMTP.</p>';
        echo '<p><strong>Próximos pasos:</strong></p>';
        echo '<ul>';
        echo '<li>Prueba enviando un formulario desde tu web</li>';
        echo '<li>Verifica que el correo llegue a fhn@coordicanarias.com</li>';
        echo '<li><strong>IMPORTANTE:</strong> Elimina este archivo (verificar_servidor.php) por seguridad</li>';
        echo '</ul>';
        echo '</div>';
    } else {
        echo '<div class="alert">';
        echo '<h3 style="margin-top: 0;">⚠ Se detectaron problemas</h3>';
        echo '<p>Algunos requisitos no se cumplen. Contacta con el soporte de tu hosting (Alojared) y solicita que:</p>';
        echo '<ul>';
        if (!$php_ok) {
            echo '<li>Actualicen la versión de PHP a 7.4 o superior</li>';
        }
        if (!extension_loaded('openssl')) {
            echo '<li>Habiliten la extensión <strong>openssl</strong></li>';
        }
        if (!extension_loaded('sockets')) {
            echo '<li>Habiliten la extensión <strong>sockets</strong></li>';
        }
        if (!$smtp_test) {
            echo '<li>Permitan conexiones salientes al puerto 465 (SMTP con SSL)</li>';
        }
        echo '</ul>';
        echo '<p>La mayoría de hostings modernos tienen estas opciones habilitadas por defecto.</p>';
        echo '</div>';
    }
    ?>

    <hr style="margin: 40px 0;">
    <p style="text-align: center; color: #666; font-size: 12px;">
        <strong>⚠️ IMPORTANTE:</strong> Por seguridad, elimina este archivo después de verificar la configuración.<br>
        Script de diagnóstico - Coordicanarias 2024
    </p>
</body>
</html>
