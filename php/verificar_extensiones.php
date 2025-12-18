<?php
/**
 * Script de diagnóstico para verificar extensiones PHP necesarias para SMTP
 * Coordicanarias - 2025
 */

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Diagnóstico de Extensiones PHP - SMTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; }
        .check {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            display: flex;
            align-items: center;
        }
        .check-ok { background: #d4edda; border-left: 4px solid #28a745; }
        .check-error { background: #f8d7da; border-left: 4px solid #dc3545; }
        .check-warning { background: #fff3cd; border-left: 4px solid #ffc107; }
        .icon { font-size: 24px; margin-right: 15px; }
        .info { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 20px 0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnóstico de Extensiones PHP para SMTP</h1>

        <h2>📋 Información General</h2>
        <div class="info">
            <strong>Versión de PHP:</strong> <?php echo phpversion(); ?><br>
            <strong>Sistema Operativo:</strong> <?php echo PHP_OS; ?><br>
            <strong>Servidor:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido'; ?>
        </div>

        <h2>🔌 Extensiones Necesarias para SMTP</h2>

        <?php
        // Lista de extensiones necesarias
        $extensiones_necesarias = [
            'openssl' => [
                'nombre' => 'OpenSSL',
                'descripcion' => 'Necesaria para conexiones SSL/TLS con Gmail',
                'critica' => true
            ],
            'mbstring' => [
                'nombre' => 'Multibyte String',
                'descripcion' => 'Para manejo de caracteres especiales en emails',
                'critica' => false
            ],
            'sockets' => [
                'nombre' => 'Sockets',
                'descripcion' => 'Para conexiones de red',
                'critica' => false
            ]
        ];

        $todas_ok = true;

        foreach ($extensiones_necesarias as $ext => $info) {
            $instalada = extension_loaded($ext);

            if (!$instalada && $info['critica']) {
                $todas_ok = false;
            }

            $clase = $instalada ? 'check-ok' : ($info['critica'] ? 'check-error' : 'check-warning');
            $icono = $instalada ? '✅' : ($info['critica'] ? '❌' : '⚠️');
            $estado = $instalada ? 'INSTALADA' : 'NO INSTALADA';

            echo "<div class='check $clase'>";
            echo "<span class='icon'>$icono</span>";
            echo "<div>";
            echo "<strong>{$info['nombre']}</strong> ($ext): $estado<br>";
            echo "<small>{$info['descripcion']}</small>";
            echo "</div>";
            echo "</div>";
        }
        ?>

        <h2>🔧 Funciones de Red</h2>

        <?php
        // Verificar funciones importantes
        $funciones = [
            'fsockopen' => 'Conexión a sockets (alternativa)',
            'stream_socket_client' => 'Conexión a sockets (moderna)',
            'curl_init' => 'cURL (opcional pero útil)'
        ];

        foreach ($funciones as $funcion => $desc) {
            $existe = function_exists($funcion);
            $clase = $existe ? 'check-ok' : 'check-warning';
            $icono = $existe ? '✅' : '⚠️';
            $estado = $existe ? 'DISPONIBLE' : 'NO DISPONIBLE';

            echo "<div class='check $clase'>";
            echo "<span class='icon'>$icono</span>";
            echo "<div>";
            echo "<strong>$funcion</strong>: $estado<br>";
            echo "<small>$desc</small>";
            echo "</div>";
            echo "</div>";
        }
        ?>

        <h2>🌐 Test de Conectividad a Gmail</h2>

        <?php
        // Intentar conectar a Gmail SMTP
        echo "<div class='info'>";
        echo "<strong>Probando conexión a smtp.gmail.com...</strong><br><br>";

        // Probar puerto 587 (TLS)
        echo "<strong>Puerto 587 (STARTTLS):</strong> ";
        $errno = 0;
        $errstr = '';
        $socket587 = @fsockopen('smtp.gmail.com', 587, $errno, $errstr, 10);
        if ($socket587) {
            echo "✅ <span style='color: green;'>CONECTA CORRECTAMENTE</span><br>";
            fclose($socket587);
        } else {
            echo "❌ <span style='color: red;'>NO PUEDE CONECTAR</span> (Error: $errstr)<br>";
        }

        echo "<br>";

        // Probar puerto 465 (SSL)
        echo "<strong>Puerto 465 (SSL):</strong> ";
        $socket465 = @fsockopen('ssl://smtp.gmail.com', 465, $errno, $errstr, 10);
        if ($socket465) {
            echo "✅ <span style='color: green;'>CONECTA CORRECTAMENTE</span><br>";
            fclose($socket465);
        } else {
            echo "❌ <span style='color: red;'>NO PUEDE CONECTAR</span> (Error: $errstr)<br>";
        }

        echo "</div>";
        ?>

        <h2>📊 Resultado Final</h2>

        <?php
        if ($todas_ok) {
            echo "<div class='check check-ok'>";
            echo "<span class='icon'>✅</span>";
            echo "<div>";
            echo "<strong>Todas las extensiones críticas están instaladas</strong><br>";
            echo "<small>Tu servidor tiene todo lo necesario para enviar emails por SMTP.</small>";
            echo "</div>";
            echo "</div>";
        } else {
            echo "<div class='check check-error'>";
            echo "<span class='icon'>❌</span>";
            echo "<div>";
            echo "<strong>Faltan extensiones críticas</strong><br>";
            echo "<small>Necesitas contactar a tu proveedor de hosting para habilitar las extensiones marcadas en rojo.</small>";
            echo "</div>";
            echo "</div>";
        }
        ?>

        <h2>📝 Extensiones Cargadas en PHP</h2>
        <div class="info">
            <details>
                <summary style="cursor: pointer; font-weight: bold;">Ver todas las extensiones instaladas (<?php echo count(get_loaded_extensions()); ?>)</summary>
                <div style="margin-top: 10px; max-height: 200px; overflow-y: auto;">
                    <?php
                    $extensiones = get_loaded_extensions();
                    sort($extensiones);
                    foreach ($extensiones as $ext) {
                        echo "• $ext<br>";
                    }
                    ?>
                </div>
            </details>
        </div>

        <div class="info" style="margin-top: 30px;">
            <strong>💡 Nota:</strong> Si OpenSSL no está instalado, contacta a tu proveedor de hosting (Alojared)
            y pide que habiliten la extensión <code>php-openssl</code> para tu cuenta.
        </div>
    </div>
</body>
</html>
