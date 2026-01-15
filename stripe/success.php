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
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
        }
        .success-card {
            background: white;
            border-radius: 20px;
            padding: 50px;
            max-width: 700px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .success-icon {
            font-size: 80px;
            color: #E5A649;
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
            background: linear-gradient(135deg, #E5A649 0%, #d89a3a 100%);
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            font-weight: bold;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(229, 166, 73, 0.4);
            color: white;
        }
        .btn-print {
            display: inline-block;
            padding: 10px 25px;
            background: white;
            color: #E5A649;
            border: 2px solid #E5A649;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.2s;
            cursor: pointer;
            margin: 10px;
        }
        .btn-print:hover {
            background: #E5A649;
            color: white;
        }
        .header-recibo {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #E5A649;
        }
        .header-recibo img {
            max-width: 200px;
            margin-bottom: 15px;
        }
        .header-recibo h1 {
            color: #E5A649;
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 5px 0;
        }
        .header-recibo p {
            color: #666;
            font-size: 13px;
            margin: 0;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .success-container {
                background: white;
                padding: 0;
            }
            .success-card {
                box-shadow: none;
                padding: 20px;
            }
            .btn-print, .btn-home {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-card">
            <?php if ($donacion && $donacion['estado'] === 'completed'): ?>
                <div class="header-recibo">
                    <img src="<?= url('images/logo-coordi.png') ?>" alt="Coordicanarias" onerror="this.style.display='none'">
                    <h1>Recibo de Donación</h1>
                    <p>Coordinadora de Personas con Discapacidad Física de Canarias</p>
                </div>

                <div class="success-icon">✓</div>
                <h2 class="success-title" style="font-size: 24px;">¡Pago Completado!</h2>
                <p class="success-message">
                    Gracias <strong><?= htmlspecialchars($donacion['nombre']) ?></strong> por tu generosa donación.<br>
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
                        <span style="font-size: 12px; word-break: break-all; display: block; margin-top: 5px;"><?= htmlspecialchars($donacion['stripe_session_id']) ?></span>
                    </div>
                </div>

                <p style="font-size: 14px; color: #999;">
                    Recibirás un email de confirmación en breve.
                </p>

                <div style="text-align: center; margin-top: 20px;">
                    <button onclick="window.print()" class="btn-print">
                        🖨️ Imprimir Recibo
                    </button>
                </div>

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
