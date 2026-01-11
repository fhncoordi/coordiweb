<?php
/**
 * Script de Sincronización - Actualizar datos de socios desde Stripe
 * Ejecutar UNA VEZ para corregir fechas faltantes
 *
 * URL: https://coordicanarias.com/admin/sync-socios-stripe.php
 *
 * IMPORTANTE: Eliminar después de ejecutarlo
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/core/auth.php';
require_once __DIR__ . '/../php/stripe-php/init.php';

// Requerir autenticación de admin
requireLogin();

// Configurar Stripe
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$actualizados = 0;
$errores = 0;

try {
    // Obtener socios con suscripción activa pero sin fecha de próximo cobro
    $stmt = getDB()->prepare("
        SELECT id, stripe_subscription_id, nombre, email
        FROM socios
        WHERE stripe_subscription_id IS NOT NULL
        AND stripe_subscription_id != ''
        AND (fecha_proximo_cobro IS NULL OR fecha_proximo_cobro = '0000-00-00' OR fecha_proximo_cobro = '')
        ORDER BY fecha_creacion DESC
    ");
    $stmt->execute();
    $socios = $stmt->fetchAll();

    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <title>Sincronizar Socios con Stripe</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
            .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            h1 { color: #667eea; }
            .success { color: #28a745; background: #d4edda; padding: 10px; margin: 10px 0; border-radius: 5px; }
            .error { color: #dc3545; background: #f8d7da; padding: 10px; margin: 10px 0; border-radius: 5px; }
            .info { color: #004085; background: #cce5ff; padding: 10px; margin: 10px 0; border-radius: 5px; }
            .socio { background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #667eea; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>🔄 Sincronización de Socios con Stripe</h1>
            <p>Actualizando datos de socios desde Stripe...</p>
            <hr>";

    if (count($socios) === 0) {
        echo "<div class='info'><strong>ℹ️ No hay socios que necesiten sincronización</strong><br>Todos los socios tienen sus fechas actualizadas correctamente.</div>";
    } else {
        echo "<div class='info'><strong>Encontrados " . count($socios) . " socios para sincronizar</strong></div>";

        foreach ($socios as $socio) {
            echo "<div class='socio'>";
            echo "<strong>Socio:</strong> " . htmlspecialchars($socio['nombre']) . " (" . htmlspecialchars($socio['email']) . ")<br>";
            echo "<strong>Subscription ID:</strong> " . htmlspecialchars($socio['stripe_subscription_id']) . "<br>";

            try {
                // Obtener datos de la suscripción desde Stripe
                $subscription = \Stripe\Subscription::retrieve($socio['stripe_subscription_id']);

                if ($subscription) {
                    $estado = $subscription->status;
                    $fecha_proximo_cobro = date('Y-m-d', $subscription->current_period_end);
                    $metodo_pago = 'card'; // Por defecto

                    // Actualizar en base de datos
                    $stmt_update = getDB()->prepare("
                        UPDATE socios
                        SET estado = ?,
                            fecha_proximo_cobro = ?,
                            fecha_inicio = COALESCE(fecha_inicio, FROM_UNIXTIME(?)),
                            metodo_pago = ?
                        WHERE id = ?
                    ");

                    $stmt_update->execute([
                        $estado,
                        $fecha_proximo_cobro,
                        $subscription->start_date,
                        $metodo_pago,
                        $socio['id']
                    ]);

                    echo "<div class='success'>✓ Actualizado correctamente<br>";
                    echo "- Estado: <strong>" . $estado . "</strong><br>";
                    echo "- Próximo cobro: <strong>" . date('d/m/Y', $subscription->current_period_end) . "</strong><br>";
                    echo "- Fecha inicio: <strong>" . date('d/m/Y', $subscription->start_date) . "</strong></div>";

                    $actualizados++;
                } else {
                    echo "<div class='error'>✗ No se encontró la suscripción en Stripe</div>";
                    $errores++;
                }
            } catch (Exception $e) {
                echo "<div class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
                $errores++;
            }

            echo "</div>";
        }
    }

    echo "<hr>
        <h2>📊 Resumen</h2>
        <ul>
            <li>Socios actualizados: <strong style='color: #28a745;'>$actualizados</strong></li>
            <li>Errores: <strong style='color: #dc3545;'>$errores</strong></li>
        </ul>";

    if ($actualizados > 0) {
        echo "<div class='success'>
            <strong>✓ Sincronización completada exitosamente</strong><br>
            Puedes volver al panel de socios para verificar los cambios.
        </div>";
    }

    echo "
        <div style='margin-top: 30px;'>
            <a href='socios.php' style='display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px;'>
                ← Volver al Panel de Socios
            </a>
        </div>

        <div style='margin-top: 30px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107;'>
            <strong>⚠️ IMPORTANTE:</strong> Elimina este archivo (sync-socios-stripe.php) después de ejecutarlo por seguridad.
        </div>
    </div>
    </body>
    </html>";

} catch (Exception $e) {
    echo "<div class='error'><strong>Error crítico:</strong><br>" . htmlspecialchars($e->getMessage()) . "</div>";
    echo "</div></body></html>";
}
?>
