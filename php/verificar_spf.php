<?php
/**
 * Verificar registros SPF del dominio
 * Coordicanarias - 2025
 */

header('Content-Type: text/html; charset=UTF-8');

$dominio = 'coordicanarias.com';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificación de SPF/DKIM</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
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
        .success { background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #dc3545; }
        .warning { background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #ffc107; }
        .info { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 20px 0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .solution { background: #fff9e6; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #ff9800; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Verificación de SPF y configuración de correo</h1>

        <div class="info">
            <strong>Dominio:</strong> <?php echo $dominio; ?><br>
            <strong>Fecha:</strong> <?php echo date('Y-m-d H:i:s'); ?>
        </div>

        <h2>📋 Registros TXT del dominio</h2>

        <?php
        // Obtener registros TXT
        $dns_txt = dns_get_record($dominio, DNS_TXT);

        $tiene_spf = false;
        $spf_record = '';

        if ($dns_txt && count($dns_txt) > 0) {
            echo "<div class='info'>";
            echo "<strong>Registros TXT encontrados:</strong><br><br>";

            foreach ($dns_txt as $record) {
                $txt_value = $record['txt'];
                echo "<code>" . htmlspecialchars($txt_value) . "</code><br><br>";

                // Verificar si es un registro SPF
                if (stripos($txt_value, 'v=spf1') !== false) {
                    $tiene_spf = true;
                    $spf_record = $txt_value;
                }
            }
            echo "</div>";
        } else {
            echo "<div class='warning'>⚠️ No se encontraron registros TXT</div>";
        }
        ?>

        <h2>🔐 Estado del SPF</h2>

        <?php
        if ($tiene_spf) {
            echo "<div class='success'>";
            echo "✅ <strong>Registro SPF encontrado:</strong><br><br>";
            echo "<code>" . htmlspecialchars($spf_record) . "</code>";
            echo "</div>";

            // Analizar el registro SPF
            echo "<div class='info'>";
            echo "<strong>Análisis del SPF:</strong><br><br>";

            // Verificar si incluye Google
            if (stripos($spf_record, 'include:_spf.google.com') !== false) {
                echo "✅ Autoriza servidores de Google Workspace<br>";
            } else {
                echo "⚠️ NO autoriza servidores de Google Workspace<br>";
            }

            // Verificar si incluye el servidor actual
            $server_ip = $_SERVER['SERVER_ADDR'] ?? gethostbyname($_SERVER['SERVER_NAME']);
            if (stripos($spf_record, 'ip4:' . $server_ip) !== false ||
                stripos($spf_record, 'a') !== false ||
                stripos($spf_record, 'mx') !== false) {
                echo "✅ Puede autorizar el servidor actual ($server_ip)<br>";
            } else {
                echo "❌ NO autoriza el servidor actual ($server_ip)<br>";
            }

            echo "</div>";

        } else {
            echo "<div class='error'>";
            echo "❌ <strong>NO se encontró registro SPF</strong><br><br>";
            echo "Esto significa que los correos enviados desde tu servidor pueden ser rechazados por Gmail y otros proveedores.";
            echo "</div>";
        }
        ?>

        <h2>💡 ¿Por qué no llegan los correos?</h2>

        <div class="warning">
            <strong>Problema detectado:</strong><br><br>

            <?php
            echo "El servidor está enviando correos con <code>envelope-from: coordica@coordicanarias.com</code><br>";
            echo "Pero esa cuenta NO EXISTE, por lo que Google rechaza los correos.<br><br>";

            if (!$tiene_spf) {
                echo "Además, NO tienes configurado un registro SPF, lo que hace que Google sea aún más estricto.";
            }
            ?>
        </div>

        <h2>🛠️ Soluciones</h2>

        <div class="solution">
            <h3>Opción 1: Configurar registro SPF (Recomendado)</h3>
            <p>Agrega este registro TXT en tu proveedor de dominio (donde tengas el DNS):</p>
            <pre>v=spf1 include:_spf.google.com a mx ~all</pre>
            <p><strong>Qué hace:</strong></p>
            <ul>
                <li><code>include:_spf.google.com</code> - Autoriza Google Workspace</li>
                <li><code>a</code> - Autoriza el servidor web (Alojared)</li>
                <li><code>mx</code> - Autoriza los servidores de correo del dominio</li>
                <li><code>~all</code> - Marca otros como sospechosos pero no los rechaza</li>
            </ul>
        </div>

        <div class="solution">
            <h3>Opción 2: Pedir a Alojared que corrijan sendmail_path</h3>
            <p>Contacta a Alojared y pídeles que cambien:</p>
            <pre>-f coordica@coordicanarias.com</pre>
            <p>Por:</p>
            <pre>-f noreply@coordicanarias.com</pre>
        </div>

        <div class="solution">
            <h3>Opción 3: Usar servicio SMTP externo (Más rápida)</h3>
            <p>Usa SendGrid, Mailgun o similar. Se configura en 5 minutos y funciona inmediatamente.</p>
            <ul>
                <li>✅ No depende de configuración DNS</li>
                <li>✅ Funciona inmediatamente</li>
                <li>✅ Mejor reputación de entrega</li>
                <li>✅ Gratis hasta 100-5000 emails/mes</li>
            </ul>
        </div>

        <h2>🔗 Información adicional</h2>
        <div class="info">
            <strong>IP del servidor:</strong> <?php echo $_SERVER['SERVER_ADDR'] ?? gethostbyname($_SERVER['SERVER_NAME']); ?><br>
            <strong>Hostname:</strong> <?php echo gethostname(); ?>
        </div>
    </div>
</body>
</html>
