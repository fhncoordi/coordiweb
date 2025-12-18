<?php
/**
 * Verificar registros MX del dominio
 * Coordicanarias - 2025
 */

header('Content-Type: text/html; charset=UTF-8');

$dominio = 'coordicanarias.com';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificación de registros MX</title>
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
        .info { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .success { background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #dc3545; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Verificación de registros MX</h1>

        <div class="info">
            <strong>Dominio:</strong> <?php echo $dominio; ?>
        </div>

        <h2>Registros MX encontrados:</h2>

        <?php
        // Obtener registros MX
        $mx_records = [];
        if (getmxrr($dominio, $mx_hosts, $mx_weights)) {
            // Combinar hosts con sus prioridades
            foreach ($mx_hosts as $key => $host) {
                $mx_records[] = [
                    'host' => $host,
                    'priority' => $mx_weights[$key]
                ];
            }

            // Ordenar por prioridad (menor número = mayor prioridad)
            usort($mx_records, function($a, $b) {
                return $a['priority'] - $b['priority'];
            });

            echo "<table>";
            echo "<tr><th>Prioridad</th><th>Servidor de correo</th></tr>";

            $usa_google = false;
            foreach ($mx_records as $record) {
                echo "<tr>";
                echo "<td>{$record['priority']}</td>";
                echo "<td>{$record['host']}</td>";
                echo "</tr>";

                // Verificar si usa Google
                if (stripos($record['host'], 'google') !== false ||
                    stripos($record['host'], 'googlemail') !== false) {
                    $usa_google = true;
                }
            }
            echo "</table>";

            // Verificar si usa Google Workspace
            if ($usa_google) {
                echo "<div class='success'>";
                echo "✅ <strong>Correcto:</strong> El dominio usa servidores de Google Workspace/Gmail.";
                echo "<br>Los correos deberían llegar a Google Workspace.";
                echo "</div>";
            } else {
                echo "<div class='error'>";
                echo "⚠️ <strong>Problema:</strong> Los registros MX NO apuntan a Google Workspace.";
                echo "<br>Los correos pueden no estar llegando a Google.";
                echo "</div>";
            }

        } else {
            echo "<div class='error'>";
            echo "❌ <strong>Error:</strong> No se encontraron registros MX para el dominio.";
            echo "</div>";
        }
        ?>

        <h2>¿Qué significa esto?</h2>
        <div class="info">
            <p><strong>Registros MX</strong> indican qué servidores de correo manejan los emails de tu dominio.</p>

            <p><strong>Si usas Google Workspace</strong>, los registros MX deben apuntar a servidores de Google como:</p>
            <ul>
                <li>aspmx.l.google.com</li>
                <li>alt1.aspmx.l.google.com</li>
                <li>alt2.aspmx.l.google.com</li>
                <li>etc.</li>
            </ul>

            <p><strong>Si NO apuntan a Google</strong>, los correos no llegarán a Google Workspace aunque la cuenta exista ahí.</p>
        </div>

        <h2>Test de resolución DNS</h2>
        <?php
        // Test adicional: verificar resolución DNS del dominio
        $dns_a = dns_get_record($dominio, DNS_A);
        if ($dns_a && count($dns_a) > 0) {
            echo "<div class='success'>✅ El dominio resuelve correctamente a: " . $dns_a[0]['ip'] . "</div>";
        } else {
            echo "<div class='error'>❌ Problema al resolver el dominio</div>";
        }
        ?>
    </div>
</body>
</html>
