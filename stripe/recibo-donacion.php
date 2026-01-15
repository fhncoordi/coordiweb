<?php
/**
 * Recibo Público de Donación
 * Coordicanarias - Mostrar recibo de donación completada
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/db/connection.php';

$payment_intent_id = isset($_GET['pi']) ? trim($_GET['pi']) : null;
$error = null;
$donacion = null;

if ($payment_intent_id) {
    // Buscar donación por payment_intent_id
    $stmt = getDB()->prepare("
        SELECT * FROM donaciones
        WHERE stripe_payment_intent_id = ?
        AND estado = 'completed'
        LIMIT 1
    ");
    $stmt->execute([$payment_intent_id]);
    $donacion = $stmt->fetch();

    if (!$donacion) {
        $error = 'No se encontró el recibo solicitado o la donación aún no se ha completado.';
    }
} else {
    $error = 'No se especificó ningún recibo para mostrar.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Donación - Coordicanarias</title>
    <link rel="stylesheet" href="<?= url('css/bootstrap.min.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 50px 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .recibo-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            padding: 50px 40px;
            max-width: 700px;
            margin: 0 auto;
        }
        .header-recibo {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #E5A649;
        }
        .header-recibo img {
            max-width: 250px;
            margin-bottom: 20px;
        }
        .header-recibo h1 {
            color: #E5A649;
            font-size: 28px;
            font-weight: 700;
            margin: 0;
        }
        .header-recibo p {
            color: #666;
            font-size: 14px;
            margin: 5px 0 0 0;
        }
        .recibo-id {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
            font-size: 12px;
            color: #666;
        }
        .recibo-id strong {
            color: #333;
        }
        .importe-destacado {
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #E5A649 0%, #d89a3a 100%);
            border-radius: 15px;
            margin: 30px 0;
            color: white;
        }
        .importe-destacado .label {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 10px;
        }
        .importe-destacado .cantidad {
            font-size: 48px;
            font-weight: 700;
            line-height: 1;
        }
        .info-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin: 20px 0;
        }
        .info-box h3 {
            color: #E5A649;
            font-size: 18px;
            margin-top: 0;
            margin-bottom: 20px;
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
            text-align: right;
        }
        .mensaje-donante {
            background: #fff9f0;
            border-left: 4px solid #E5A649;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .mensaje-donante h4 {
            color: #E5A649;
            font-size: 16px;
            margin-top: 0;
            margin-bottom: 10px;
        }
        .mensaje-donante p {
            margin: 0;
            color: #555;
            font-style: italic;
        }
        .agradecimiento {
            text-align: center;
            padding: 30px 20px;
            color: #666;
        }
        .agradecimiento h2 {
            color: #E5A649;
            font-size: 24px;
            margin-bottom: 15px;
        }
        .btn-home {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #E5A649 0%, #d89a3a 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            margin-top: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(229, 166, 73, 0.4);
            color: white;
        }
        .error-box {
            background: #f8d7da;
            color: #721c24;
            padding: 30px;
            border-radius: 10px;
            border-left: 4px solid #dc3545;
            text-align: center;
        }
        .print-button {
            text-align: center;
            margin: 20px 0;
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
        }
        .btn-print:hover {
            background: #E5A649;
            color: white;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .recibo-container {
                box-shadow: none;
                padding: 20px;
            }
            .print-button, .btn-home {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="recibo-container">
        <?php if ($error): ?>
            <!-- Error: recibo no encontrado -->
            <div class="error-box">
                <h2>❌ Recibo No Encontrado</h2>
                <p><?= htmlspecialchars($error) ?></p>
                <a href="<?= url('index.php') ?>" class="btn-home" style="background: #dc3545;">
                    Volver al inicio
                </a>
            </div>

        <?php elseif ($donacion): ?>
            <!-- Recibo válido -->
            <div class="header-recibo">
                <img src="<?= url('images/logo-coordi.png') ?>" alt="Coordicanarias" onerror="this.style.display='none'">
                <h1>Recibo de Donación</h1>
                <p>Coordinadora de Personas con Discapacidad Física de Canarias</p>
            </div>

            <div class="recibo-id">
                <strong>Nº de Recibo:</strong> <?= strtoupper(substr($donacion['stripe_payment_intent_id'], -12)) ?>
            </div>

            <div class="importe-destacado">
                <div class="label">Total Donado</div>
                <div class="cantidad"><?= number_format($donacion['importe'], 2) ?> €</div>
            </div>

            <div class="info-box">
                <h3>Información del Donante</h3>
                <div class="info-item">
                    <span class="info-label">Nombre:</span>
                    <span class="info-value">
                        <?php if ($donacion['es_anonimo']): ?>
                            Donante Anónimo
                        <?php else: ?>
                            <?= htmlspecialchars($donacion['nombre']) ?>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?= htmlspecialchars($donacion['email']) ?></span>
                </div>
            </div>

            <div class="info-box">
                <h3>Detalles de la Transacción</h3>
                <div class="info-item">
                    <span class="info-label">Fecha de donación:</span>
                    <span class="info-value"><?= date('d/m/Y', strtotime($donacion['fecha_completado'])) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Hora:</span>
                    <span class="info-value"><?= date('H:i', strtotime($donacion['fecha_completado'])) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Método de pago:</span>
                    <span class="info-value">
                        <?php
                        $metodo = strtolower($donacion['metodo_pago'] ?? 'card');
                        $metodosNombre = [
                            'card' => 'Tarjeta de Crédito/Débito',
                            'customer_balance' => 'Bizum',
                            'bizum' => 'Bizum',
                        ];
                        echo $metodosNombre[$metodo] ?? 'Tarjeta';
                        ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Estado:</span>
                    <span class="info-value" style="color: #28a745;">✓ Completado</span>
                </div>
                <div class="info-item">
                    <span class="info-label">ID de transacción:</span>
                    <span class="info-value" style="font-size: 11px; word-break: break-all;">
                        <?= htmlspecialchars($donacion['stripe_payment_intent_id']) ?>
                    </span>
                </div>
            </div>

            <?php if (!empty($donacion['mensaje']) && !$donacion['es_anonimo']): ?>
            <div class="mensaje-donante">
                <h4>💬 Mensaje del Donante</h4>
                <p>"<?= nl2br(htmlspecialchars($donacion['mensaje'])) ?>"</p>
            </div>
            <?php endif; ?>

            <div class="agradecimiento">
                <h2>¡Muchas Gracias por tu Generosidad!</h2>
                <p>Tu donación nos ayuda a continuar con nuestra labor en favor de las personas con discapacidad física en Canarias.</p>
                <p style="margin-top: 20px; font-size: 14px; color: #999;">
                    Este recibo es válido como justificante de tu donación.
                </p>
            </div>

            <div class="print-button">
                <button onclick="window.print()" class="btn-print">
                    🖨️ Imprimir Recibo
                </button>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <a href="<?= url('index.php') ?>" class="btn-home">Volver al inicio</a>
            </div>

        <?php endif; ?>
    </div>

    <script src="<?= url('js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
