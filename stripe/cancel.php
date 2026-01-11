<?php
/**
 * Stripe - Página de Cancelación
 * Mostrada cuando el usuario cancela el pago
 */

require_once __DIR__ . '/../php/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Cancelado - Coordicanarias</title>
    <link href="<?= url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= url('css/style.css') ?>" rel="stylesheet">
    <style>
        .cancel-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .cancel-card {
            background: white;
            border-radius: 20px;
            padding: 50px;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }
        .cancel-icon {
            font-size: 80px;
            color: #6c757d;
            margin-bottom: 20px;
        }
        .cancel-title {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }
        .cancel-message {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .btn-retry {
            background: #667eea;
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            font-weight: bold;
            transition: background 0.3s;
        }
        .btn-retry:hover {
            background: #764ba2;
            color: white;
        }
        .btn-home {
            background: #6c757d;
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            font-weight: bold;
            transition: background 0.3s;
        }
        .btn-home:hover {
            background: #5a6268;
            color: white;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 30px 0;
            text-align: left;
            border-radius: 5px;
        }
        .info-box p {
            margin: 10px 0;
            color: #555;
        }
        .info-box strong {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="cancel-container">
        <div class="cancel-card">
            <div class="cancel-icon">⊘</div>
            <h1 class="cancel-title">Pago Cancelado</h1>
            <p class="cancel-message">
                Has cancelado el proceso de donación.<br>
                No se ha realizado ningún cargo.
            </p>

            <div class="info-box">
                <p><strong>¿Tuviste algún problema?</strong></p>
                <p>Si encontraste alguna dificultad durante el proceso de pago,
                puedes intentarlo nuevamente o contactarnos para ayudarte.</p>
                <p style="margin-top: 15px;">
                    <strong>Contacto:</strong><br>
                    📧 Email: info@coordicanarias.com<br>
                    📞 Teléfono: 928 36 16 47
                </p>
            </div>

            <p style="font-size: 16px; color: #666; margin-bottom: 20px;">
                Tu apoyo es muy importante para nosotros.<br>
                Puedes intentar hacer la donación nuevamente cuando lo desees.
            </p>

            <div>
                <a href="<?= url('index.php#donaciones') ?>" class="btn-retry">Intentar de nuevo</a>
                <a href="<?= url('index.php') ?>" class="btn-home">Volver al inicio</a>
            </div>
        </div>
    </div>
</body>
</html>
