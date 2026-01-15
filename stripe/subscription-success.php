<?php
/**
 * Página de Confirmación - Suscripción Completada
 * Coordicanarias - Bienvenida a Nuevo Socio
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/stripe-php/init.php';
require_once __DIR__ . '/../php/db/connection.php';

// Configurar Stripe
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : null;
$error = null;
$session = null;
$subscription = null;
$customer = null;

if ($session_id) {
    try {
        // Recuperar la sesión de Checkout
        $session = \Stripe\Checkout\Session::retrieve($session_id);

        // Obtener la suscripción
        if (isset($session->subscription)) {
            $subscription = \Stripe\Subscription::retrieve($session->subscription);
        }

        // Obtener el customer
        if (isset($session->customer)) {
            $customer = \Stripe\Customer::retrieve($session->customer);
        }

        // Actualizar la base de datos con los IDs de Stripe
        if ($session->customer && $session->subscription) {
            $stmt = getDB()->prepare("
                UPDATE socios
                SET stripe_customer_id = ?,
                    stripe_subscription_id = ?,
                    estado = ?
                WHERE email = ?
                AND stripe_subscription_id IS NULL
                ORDER BY fecha_creacion DESC
                LIMIT 1
            ");

            $estado = $subscription->status; // 'active', 'trialing', 'incomplete', etc.

            $stmt->execute([
                $session->customer,
                $session->subscription,
                $estado,
                $customer->email
            ]);

            // Si el estado es 'active', actualizar fecha_inicio
            if ($estado === 'active' || $estado === 'trialing') {
                $stmt = getDB()->prepare("
                    UPDATE socios
                    SET fecha_inicio = NOW(),
                        fecha_proximo_cobro = ?
                    WHERE stripe_subscription_id = ?
                ");

                $proximo_cobro = date('Y-m-d', $subscription->current_period_end);
                $stmt->execute([$proximo_cobro, $session->subscription]);
            }
        }

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Bienvenido Socio! - Coordicanarias</title>
    <link rel="stylesheet" href="<?= url('css/bootstrap.min.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        .success-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            padding: 50px 40px;
            max-width: 700px;
            text-align: center;
        }
        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #E5A649 0%, #d89a3a 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: scaleIn 0.5s ease-out;
        }
        .success-icon svg {
            width: 60px;
            height: 60px;
            stroke: white;
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
            animation: checkmark 0.8s ease-out 0.3s forwards;
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
        }
        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }
        @keyframes checkmark {
            to { stroke-dashoffset: 0; }
        }
        h1 {
            color: #333;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .subtitle {
            color: #666;
            font-size: 18px;
            margin-bottom: 30px;
        }
        .info-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin: 30px 0;
            text-align: left;
        }
        .info-box h3 {
            color: #E5A649;
            font-size: 18px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #666;
            font-weight: 500;
        }
        .info-value {
            color: #333;
            font-weight: 600;
            word-break: break-word;
            overflow-wrap: break-word;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #E5A649 0%, #d89a3a 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(229, 166, 73, 0.4);
            color: white;
        }
        .btn-secondary-custom {
            background: transparent;
            border: 2px solid #E5A649;
            color: #E5A649;
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            transition: all 0.2s;
        }
        .btn-secondary-custom:hover {
            background: #E5A649;
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
                box-shadow: none;
                padding: 20px;
            }
            .btn-print, .btn-primary-custom, .btn-secondary-custom {
                display: none;
            }
        }
        .error-box {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #dc3545;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <?php if ($error): ?>
            <div class="error-box">
                <strong>Error:</strong> <?= htmlspecialchars($error) ?>
                <p style="margin-top: 15px;">
                    <a href="<?= url('index.php') ?>" class="btn-primary-custom">Volver al inicio</a>
                </p>
            </div>
        <?php elseif ($session && $subscription): ?>
            <div class="header-recibo">
                <img src="<?= url('images/logo-coordi.png') ?>" alt="Coordicanarias" onerror="this.style.display='none'">
                <h1>Alta como Socio</h1>
                <p>Coordinadora de Personas con Discapacidad Física de Canarias</p>
            </div>

            <div class="success-icon">
                <svg viewBox="0 0 52 52">
                    <polyline points="14 27 22 35 38 19" />
                </svg>
            </div>

            <h2 style="color: #333; font-size: 28px; font-weight: 700; margin-bottom: 15px;">¡Bienvenido a la Familia!</h2>
            <p class="subtitle">
                Gracias por convertirte en socio de Coordicanarias.<br>
                Tu apoyo mensual hace una diferencia real en la vida de muchas personas.
            </p>

            <div class="info-box">
                <h3>Detalles de tu Suscripción</h3>
                <div class="info-item">
                    <span class="info-label">Estado:</span>
                    <span class="info-value">
                        <?php
                        $estados = [
                            'active' => '✓ Activa',
                            'trialing' => '🎁 En período de prueba',
                            'incomplete' => '⏳ Procesando pago...',
                        ];
                        echo $estados[$subscription->status] ?? $subscription->status;
                        ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Importe mensual:</span>
                    <span class="info-value">5,00 €</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Próximo cobro:</span>
                    <span class="info-value">
                        <?= date('d/m/Y', $subscription->current_period_end) ?>
                    </span>
                </div>
                <?php if ($customer && $customer->email): ?>
                <div class="info-item">
                    <span class="info-label">Email de contacto:</span>
                    <span class="info-value"><?= htmlspecialchars($customer->email) ?></span>
                </div>
                <?php endif; ?>
            </div>

            <div style="background: #e7f3ff; border-radius: 10px; padding: 20px; margin: 20px 0;">
                <p style="margin: 0; color: #014361; font-size: 15px;">
                    <strong>📧 Te hemos enviado un email</strong> con toda la información de tu suscripción
                    y los datos para acceder a tu portal de gestión.
                </p>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <button onclick="window.print()" class="btn-print">
                    🖨️ Imprimir Confirmación
                </button>
            </div>

            <div style="margin-top: 20px;">
                <a href="<?= url('index.php') ?>" class="btn-primary-custom">Volver al inicio</a>
                <br>
                <a href="<?= url('stripe/manage-subscription.php?email=' . urlencode($customer->email ?? '')) ?>" class="btn-secondary-custom">
                    Gestionar mi suscripción
                </a>
            </div>

        <?php else: ?>
            <div class="error-box">
                <strong>Error:</strong> No se encontró información de la suscripción.
                <p style="margin-top: 15px;">
                    <a href="<?= url('index.php') ?>" class="btn-primary-custom">Volver al inicio</a>
                </p>
            </div>
        <?php endif; ?>
    </div>

    <script src="<?= url('js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
