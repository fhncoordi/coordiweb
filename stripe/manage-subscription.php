<?php
/**
 * Portal de Gestión de Suscripción
 * Coordicanarias - Permitir a los socios gestionar su suscripción
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/stripe-php/init.php';
require_once __DIR__ . '/../php/db/connection.php';

// Configurar Stripe
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$email = isset($_GET['email']) ? trim($_GET['email']) : '';
$error = null;
$socio = null;

if ($email) {
    // Buscar socio en BD
    $stmt = getDB()->prepare("
        SELECT * FROM socios
        WHERE email = ?
        ORDER BY fecha_creacion DESC
        LIMIT 1
    ");
    $stmt->execute([$email]);
    $socio = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Suscripción - Coordicanarias</title>
    <link rel="stylesheet" href="<?= url('css/bootstrap.min.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 50px 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .portal-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            padding: 50px 40px;
            max-width: 700px;
            margin: 0 auto;
        }
        h1 {
            color: #333;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            text-align: center;
        }
        .subtitle {
            color: #666;
            font-size: 16px;
            text-align: center;
            margin-bottom: 40px;
        }
        .info-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin: 20px 0;
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
        }
        .badge-estado {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        .badge-active {
            background: #d4edda;
            color: #155724;
        }
        .badge-past_due {
            background: #fff3cd;
            color: #856404;
        }
        .badge-canceled {
            background: #f8d7da;
            color: #721c24;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .btn-danger-custom {
            background: #dc3545;
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            transition: transform 0.2s;
            cursor: pointer;
        }
        .btn-danger-custom:hover {
            transform: translateY(-2px);
            background: #c82333;
            color: white;
        }
        .search-box {
            text-align: center;
            padding: 40px 20px;
        }
        .search-box input {
            padding: 15px 20px;
            border-radius: 50px;
            border: 2px solid #667eea;
            font-size: 16px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .error-box {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #dc3545;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="portal-container">
        <?php if (!$email): ?>
            <!-- Formulario de búsqueda por email -->
            <h1>Gestionar Mi Suscripción</h1>
            <p class="subtitle">Ingresa tu email para acceder a tu portal de gestión</p>

            <div class="search-box">
                <form method="GET" action="">
                    <input type="email" name="email" placeholder="tu@email.com" required autofocus>
                    <br>
                    <button type="submit" class="btn-primary-custom" style="margin-top: 20px;">
                        Acceder
                    </button>
                </form>
            </div>

            <div style="text-align: center; margin-top: 40px;">
                <a href="<?= url('index.php') ?>" style="color: #667eea; text-decoration: none;">
                    ← Volver al inicio
                </a>
            </div>

        <?php elseif (!$socio): ?>
            <!-- Socio no encontrado -->
            <h1>Suscripción No Encontrada</h1>
            <div class="error-box">
                <strong>No se encontró ninguna suscripción</strong> asociada al email:
                <br><strong><?= htmlspecialchars($email) ?></strong>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="<?= url('stripe/manage-subscription.php') ?>" class="btn-primary-custom">
                    Intentar con otro email
                </a>
                <br>
                <a href="<?= url('index.php#colabora') ?>" class="btn-primary-custom" style="background: #28a745;">
                    Hacerme socio
                </a>
            </div>

        <?php else: ?>
            <!-- Panel de gestión del socio -->
            <h1>Mi Suscripción</h1>
            <p class="subtitle">Hola, <strong><?= htmlspecialchars($socio['nombre']) ?></strong></p>

            <div class="info-box">
                <div class="info-item">
                    <span class="info-label">Estado:</span>
                    <span class="info-value">
                        <?php
                        $estados = [
                            'active' => '<span class="badge-estado badge-active">✓ Activa</span>',
                            'trialing' => '<span class="badge-estado badge-active">🎁 En período de prueba</span>',
                            'past_due' => '<span class="badge-estado badge-past_due">⚠️ Pago pendiente</span>',
                            'canceled' => '<span class="badge-estado badge-canceled">✗ Cancelada</span>',
                            'incomplete' => '<span class="badge-estado badge-past_due">⏳ Procesando...</span>',
                            'unpaid' => '<span class="badge-estado badge-canceled">✗ Impagada</span>',
                        ];
                        echo $estados[$socio['estado']] ?? $socio['estado'];
                        ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Importe mensual:</span>
                    <span class="info-value">5,00 €</span>
                </div>
                <?php if ($socio['fecha_inicio']): ?>
                <div class="info-item">
                    <span class="info-label">Socio desde:</span>
                    <span class="info-value"><?= date('d/m/Y', strtotime($socio['fecha_inicio'])) ?></span>
                </div>
                <?php endif; ?>
                <?php if ($socio['fecha_proximo_cobro'] && $socio['estado'] === 'active'): ?>
                <div class="info-item">
                    <span class="info-label">Próximo cobro:</span>
                    <span class="info-value"><?= date('d/m/Y', strtotime($socio['fecha_proximo_cobro'])) ?></span>
                </div>
                <?php endif; ?>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?= htmlspecialchars($socio['email']) ?></span>
                </div>
            </div>

            <?php if ($socio['estado'] === 'active' || $socio['estado'] === 'trialing' || $socio['estado'] === 'past_due'): ?>
                <!-- Suscripción activa - Mostrar opciones de gestión -->
                <div style="background: #e7f3ff; border-radius: 10px; padding: 20px; margin: 20px 0;">
                    <h5 style="color: #014361; margin-bottom: 10px;">Gestionar Suscripción</h5>
                    <p style="margin: 0; color: #014361; font-size: 15px;">
                        Puedes actualizar tu método de pago, ver facturas o cancelar tu suscripción
                        usando el portal de Stripe.
                    </p>
                </div>

                <div style="text-align: center; margin-top: 30px;">
                    <form method="POST" action="<?= url('stripe/create-portal-session.php') ?>">
                        <input type="hidden" name="customer_id" value="<?= htmlspecialchars($socio['stripe_customer_id']) ?>">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($socio['email']) ?>">
                        <button type="submit" class="btn-primary-custom">
                            Acceder al Portal de Gestión
                        </button>
                    </form>
                </div>

            <?php elseif ($socio['estado'] === 'canceled'): ?>
                <!-- Suscripción cancelada -->
                <div style="background: #fff3cd; border-radius: 10px; padding: 20px; margin: 20px 0; border-left: 4px solid #ffc107;">
                    <h5 style="color: #856404; margin-bottom: 10px;">Suscripción Cancelada</h5>
                    <p style="margin: 0; color: #856404; font-size: 15px;">
                        Tu suscripción fue cancelada el <?= date('d/m/Y', strtotime($socio['fecha_cancelacion'])) ?>.
                        <br>
                        ¿Te gustaría volver a ser socio?
                    </p>
                </div>

                <div style="text-align: center; margin-top: 30px;">
                    <a href="<?= url('index.php#colabora') ?>" class="btn-primary-custom" style="background: #28a745;">
                        Volver a suscribirme
                    </a>
                </div>
            <?php endif; ?>

            <div style="text-align: center; margin-top: 40px;">
                <a href="<?= url('index.php') ?>" style="color: #667eea; text-decoration: none;">
                    ← Volver al inicio
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="<?= url('js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
