<?php
/**
 * Script de diagnóstico para suscripciones
 * TEMPORAL: Eliminar después de diagnosticar
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/stripe-php/init.php';
require_once __DIR__ . '/../php/db/connection.php';

header('Content-Type: text/html; charset=UTF-8');

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

// Obtener el último socio de la BD
$socio = fetchOne("SELECT * FROM socios ORDER BY id DESC LIMIT 1");

if (!$socio) {
    die("<h1>❌ No hay socios en la base de datos</h1>");
}

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Debug Suscripción</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
        .container { max-width: 900px; margin: 0 auto; }
        h1 { color: #4ec9b0; }
        h2 { color: #dcdcaa; margin-top: 30px; }
        .box { background: #252526; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #007acc; }
        .success { border-left-color: #4ec9b0; }
        .error { border-left-color: #f48771; }
        .warning { border-left-color: #ce9178; }
        .info { border-left-color: #569cd6; }
        pre { background: #1e1e1e; padding: 10px; border-radius: 3px; overflow-x: auto; }
        .key { color: #9cdcfe; }
        .value { color: #ce9178; }
        .null { color: #569cd6; font-style: italic; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        td { padding: 8px; border-bottom: 1px solid #3e3e42; }
        td:first-child { color: #9cdcfe; width: 250px; }
        td:nth-child(2) { color: #ce9178; }
        a { color: #569cd6; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Diagnóstico de Suscripción</h1>

        <h2>📊 Datos en Base de Datos</h2>
        <div class='box info'>
            <table>
                <tr>
                    <td>ID:</td>
                    <td>{$socio['id']}</td>
                </tr>
                <tr>
                    <td>Nombre:</td>
                    <td>" . htmlspecialchars($socio['nombre']) . "</td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td>" . htmlspecialchars($socio['email']) . "</td>
                </tr>
                <tr>
                    <td>Estado:</td>
                    <td>{$socio['estado']}</td>
                </tr>
                <tr>
                    <td>Stripe Customer ID:</td>
                    <td>" . ($socio['stripe_customer_id'] ?? '<span class="null">NULL</span>') . "</td>
                </tr>
                <tr>
                    <td>Stripe Subscription ID:</td>
                    <td>" . ($socio['stripe_subscription_id'] ?? '<span class="null">NULL</span>') . "</td>
                </tr>
                <tr>
                    <td>Fecha creación (BD):</td>
                    <td>{$socio['fecha_creacion']}</td>
                </tr>
                <tr>
                    <td>Fecha inicio (BD):</td>
                    <td>" . ($socio['fecha_inicio'] ?? '<span class="null">NULL</span>') . "</td>
                </tr>
                <tr>
                    <td><strong>Fecha próximo cobro (BD):</strong></td>
                    <td><strong>" . ($socio['fecha_proximo_cobro'] ?? '<span class="null">NULL</span>') . "</strong></td>
                </tr>
            </table>
        </div>";

// Si tiene subscription_id, obtener datos de Stripe
if (!empty($socio['stripe_subscription_id'])) {
    echo "<h2>☁️ Datos en Stripe</h2>";

    try {
        $subscription = \Stripe\Subscription::retrieve($socio['stripe_subscription_id']);

        // Buscar current_period_end en items.data[0] (nuevo) o en subscription directamente (antiguo)
        $current_period_start = $subscription->items->data[0]->current_period_start ?? $subscription->current_period_start ?? null;
        $current_period_end = $subscription->items->data[0]->current_period_end ?? $subscription->current_period_end ?? null;

        $fecha_inicio_stripe = $current_period_start ? date('Y-m-d H:i:s', $current_period_start) : 'NULL';
        $fecha_fin_stripe = $current_period_end ? date('Y-m-d H:i:s', $current_period_end) : 'NULL';

        echo "<div class='box success'>
            <table>
                <tr>
                    <td>Subscription ID:</td>
                    <td>{$subscription->id}</td>
                </tr>
                <tr>
                    <td>Customer ID:</td>
                    <td>{$subscription->customer}</td>
                </tr>
                <tr>
                    <td>Status:</td>
                    <td>{$subscription->status}</td>
                </tr>
                <tr>
                    <td>Created (timestamp):</td>
                    <td>{$subscription->created} (" . date('Y-m-d H:i:s', $subscription->created) . ")</td>
                </tr>
                <tr>
                    <td><strong>current_period_start (timestamp):</strong></td>
                    <td><strong>" . ($current_period_start ?? '<span class="null">NULL</span>') . "</strong></td>
                </tr>
                <tr>
                    <td><strong>current_period_start (fecha):</strong></td>
                    <td><strong>{$fecha_inicio_stripe}</strong></td>
                </tr>
                <tr>
                    <td><strong>current_period_end (timestamp):</strong></td>
                    <td><strong>" . ($current_period_end ?? '<span class="null">NULL</span>') . "</strong></td>
                </tr>
                <tr>
                    <td><strong>current_period_end (fecha):</strong></td>
                    <td><strong style='color: " . ($current_period_end ? '#4ec9b0' : '#f48771') . ";'>{$fecha_fin_stripe}</strong></td>
                </tr>
                <tr>
                    <td>billing_cycle_anchor:</td>
                    <td>" . ($subscription->billing_cycle_anchor ? date('Y-m-d H:i:s', $subscription->billing_cycle_anchor) : 'NULL') . "</td>
                </tr>
                <tr>
                    <td>cancel_at_period_end:</td>
                    <td>" . ($subscription->cancel_at_period_end ? 'true' : 'false') . "</td>
                </tr>
            </table>
        </div>";

        // Comparación
        echo "<h2>⚖️ Comparación BD vs Stripe</h2>";

        $fecha_bd = $socio['fecha_proximo_cobro'];
        $fecha_stripe = $current_period_end ? date('Y-m-d', $current_period_end) : null;

        $coincide = ($fecha_bd === $fecha_stripe);

        echo "<div class='box " . ($coincide ? 'success' : 'error') . "'>
            <table>
                <tr>
                    <td>Fecha en BD:</td>
                    <td>" . ($fecha_bd ?? '<span class="null">NULL</span>') . "</td>
                </tr>
                <tr>
                    <td>Fecha en Stripe (calculada):</td>
                    <td>" . ($fecha_stripe ?? '<span class="null">NULL</span>') . "</td>
                </tr>
                <tr>
                    <td><strong>¿Coinciden?</strong></td>
                    <td><strong>" . ($coincide ? '✅ SÍ' : '❌ NO') . "</strong></td>
                </tr>
            </table>
        </div>";

        if (!$coincide) {
            echo "<div class='box error'>
                <h3>❌ Problema detectado</h3>
                <p>La fecha en la base de datos NO coincide con la fecha en Stripe.</p>
                <p><strong>Posibles causas:</strong></p>
                <ul>
                    <li>El webhook no se ejecutó correctamente</li>
                    <li>Hubo un error al guardar en la BD</li>
                    <li>El valor de current_period_end era NULL/0 cuando se procesó</li>
                </ul>
                <p><strong>Solución:</strong></p>
                <form method='POST'>
                    <button type='submit' name='corregir' style='background: #4ec9b0; color: #1e1e1e; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 14px;'>
                        🔧 Corregir fecha desde Stripe
                    </button>
                </form>
            </div>";
        }

        // JSON completo
        echo "<h2>📄 Objeto Subscription completo (JSON)</h2>";
        echo "<div class='box'>
            <pre>" . json_encode($subscription->jsonSerialize(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>
        </div>";

    } catch (Exception $e) {
        echo "<div class='box error'>
            <strong>❌ Error al obtener datos de Stripe:</strong><br>
            " . htmlspecialchars($e->getMessage()) . "
        </div>";
    }
} else {
    echo "<div class='box warning'>
        <strong>⚠️ Este socio no tiene stripe_subscription_id asignado</strong><br>
        El webhook probablemente no se ejecutó o falló.
    </div>";
}

// Procesar corrección
if (isset($_POST['corregir']) && !empty($socio['stripe_subscription_id'])) {
    try {
        $subscription = \Stripe\Subscription::retrieve($socio['stripe_subscription_id']);

        // Buscar en items.data[0] (nuevo) o subscription directamente (antiguo)
        $periodEnd = $subscription->items->data[0]->current_period_end ?? $subscription->current_period_end ?? null;
        $fechaCorrecta = $periodEnd ? date('Y-m-d', $periodEnd) : null;

        if ($fechaCorrecta) {
            execute("
                UPDATE socios
                SET fecha_proximo_cobro = ?
                WHERE id = ?
            ", [$fechaCorrecta, $socio['id']]);

            echo "<div class='box success'>
                <h3>✅ Fecha corregida</h3>
                <p>La fecha se actualizó correctamente a: <strong>{$fechaCorrecta}</strong></p>
                <p><a href='debug-subscription.php'>🔄 Recargar página</a></p>
            </div>";
        } else {
            echo "<div class='box error'>
                <h3>❌ No se pudo corregir</h3>
                <p>El campo current_period_end sigue siendo NULL en Stripe.</p>
            </div>";
        }
    } catch (Exception $e) {
        echo "<div class='box error'>
            <strong>❌ Error al corregir:</strong><br>
            " . htmlspecialchars($e->getMessage()) . "
        </div>";
    }
}

echo "
        <hr style='border: 1px solid #3e3e42; margin: 30px 0;'>
        <p>
            <a href='debug-subscription.php'>🔄 Recargar</a> |
            <a href='../admin/socios.php'>Ver Socios</a> |
            <a href='https://dashboard.stripe.com/test/subscriptions' target='_blank'>Stripe Dashboard</a>
        </p>

        <div class='box warning' style='margin-top: 20px;'>
            <strong>⚠️ IMPORTANTE:</strong> Elimina este archivo después de diagnosticar por seguridad.
        </div>
    </div>
</body>
</html>";
?>
