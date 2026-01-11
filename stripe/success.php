<?php
/**
 * Stripe - Página de Éxito
 * Mostrada después de completar el pago
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/stripe-php/init.php';
require_once __DIR__ . '/../php/db/connection.php';

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$session_id = $_GET['session_id'] ?? null;
$error = null;
$donacion = null;

if ($session_id) {
    try {
        // Recuperar sesión de Stripe
        $session = \Stripe\Checkout\Session::retrieve($session_id);

        // Buscar donación en BD
        $donacion = fetchOne("SELECT * FROM donaciones WHERE stripe_session_id = ?", [$session_id]);

        if ($donacion && $donacion['estado'] === 'pending') {
            // Actualizar estado a completado
            execute("
                UPDATE donaciones
                SET estado = 'completed',
                    fecha_completado = NOW(),
                    stripe_payment_intent_id = ?,
                    metodo_pago = ?
                WHERE stripe_session_id = ?
            ", [
                $session->payment_intent,
                $session->payment_method_types[0] ?? 'unknown',
                $session_id
            ]);

            $donacion['estado'] = 'completed';
        }

    } catch (Exception $e) {
        $error = 'Error al verificar el pago';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Gracias por tu donación! - Coordicanarias</title>
    <link href="<?= url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= url('css/style.css') ?>" rel="stylesheet">
    <style>
        .success-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .success-card {
            background: white;
            border-radius: 20px;
            padding: 50px;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .success-title {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }
        .success-message {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
        }
        .donation-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
            text-align: left;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .btn-home {
            background: #667eea;
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            font-weight: bold;
        }
        .btn-home:hover {
            background: #764ba2;
            color: white;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-card">
            <?php if ($donacion && $donacion['estado'] === 'completed'): ?>
                <div class="success-icon">✓</div>
                <h1 class="success-title">¡Pago Completado!</h1>
                <p class="success-message">
                    Gracias <?= htmlspecialchars($donacion['nombre']) ?> por tu generosa donación.<br>
                    Tu apoyo nos ayuda a continuar nuestra labor.
                </p>

                <div class="donation-details">
                    <div class="detail-row">
                        <strong>Importe:</strong>
                        <span><?= number_format($donacion['importe'], 2) ?> €</span>
                    </div>
                    <div class="detail-row">
                        <strong>Email:</strong>
                        <span><?= htmlspecialchars($donacion['email']) ?></span>
                    </div>
                    <div class="detail-row">
                        <strong>Fecha:</strong>
                        <span><?= date('d/m/Y H:i', strtotime($donacion['fecha_completado'])) ?></span>
                    </div>
                    <div class="detail-row">
                        <strong>ID de transacción:</strong>
                        <span style="font-size: 12px;"><?= htmlspecialchars($donacion['stripe_session_id']) ?></span>
                    </div>
                </div>

                <p style="font-size: 14px; color: #999;">
                    Recibirás un email de confirmación en breve.
                </p>

            <?php else: ?>
                <div class="success-icon" style="color: #ffc107;">⚠</div>
                <h1 class="success-title">Procesando pago...</h1>
                <p class="success-message">
                    Tu pago está siendo procesado. Recibirás un email de confirmación cuando se complete.
                </p>
            <?php endif; ?>

            <a href="<?= url('index.php') ?>" class="btn-home">Volver al inicio</a>
        </div>
    </div>
</body>
</html>
