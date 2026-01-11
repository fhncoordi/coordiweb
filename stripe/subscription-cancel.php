<?php
/**
 * Página de Cancelación - Suscripción
 * Coordicanarias
 */

require_once __DIR__ . '/../php/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripción Cancelada - Coordicanarias</title>
    <link rel="stylesheet" href="<?= url('css/bootstrap.min.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .cancel-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            padding: 50px 40px;
            max-width: 550px;
            text-align: center;
        }
        .cancel-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 50px;
        }
        h1 {
            color: #333;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .subtitle {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .info-box {
            background: #fff3cd;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
            border-left: 4px solid #ffc107;
        }
        .info-box p {
            margin: 0;
            color: #856404;
            font-size: 15px;
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
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .btn-secondary-custom {
            background: transparent;
            border: 2px solid #667eea;
            color: #667eea;
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
            background: #667eea;
            color: white;
        }
    </style>
</head>
<body>
    <div class="cancel-container">
        <div class="cancel-icon">⏸️</div>

        <h1>Suscripción No Completada</h1>
        <p class="subtitle">
            Has cancelado el proceso de suscripción. No se ha realizado ningún cargo a tu tarjeta.
        </p>

        <div class="info-box">
            <p>
                <strong>¿Tuviste algún problema?</strong><br>
                Si necesitas ayuda o tienes alguna pregunta, no dudes en contactarnos.
                Estamos aquí para ayudarte.
            </p>
        </div>

        <div style="margin-top: 30px;">
            <a href="<?= url('index.php#contact') ?>" class="btn-primary-custom">
                Contactar con nosotros
            </a>
            <br>
            <a href="<?= url('index.php#colabora') ?>" class="btn-secondary-custom">
                Intentar de nuevo
            </a>
            <br>
            <a href="<?= url('index.php') ?>" class="btn-secondary-custom" style="border: none; color: #999;">
                Volver al inicio
            </a>
        </div>
    </div>

    <script src="<?= url('js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
