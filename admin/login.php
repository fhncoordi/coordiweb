<?php
/**
 * Página de Login - Panel de Administración
 * Coordicanarias CMS
 */

require_once __DIR__ . '/../php/core/auth.php';
require_once __DIR__ . '/../php/core/security.php';

// Establecer headers de seguridad
setSecurityHeaders();

// Si ya está logueado, redirigir al dashboard
if (isLoggedIn()) {
    header('Location: /admin/index.php');
    exit;
}

$error = '';
$success = '';

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Por favor, complete todos los campos';
    } else {
        if (login($username, $password)) {
            header('Location: /admin/index.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Iniciar Sesión - Panel de Administración | Coordicanarias</title>
    <link rel="icon" href="../favicon.ico" type="image/x-icon">

    <!-- Stylesheets (reutilizar del sitio principal) -->
    <link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../css/style.css" rel="stylesheet" type="text/css">

    <style>
        /* Estilos específicos para la página de login */
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Open Sans', sans-serif;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .login-header {
            background: #243659;
            color: #ffffff;
            padding: 30px 40px;
            text-align: center;
        }

        .login-header img {
            max-width: 200px;
            height: auto;
            margin-bottom: 15px;
            filter: brightness(0) invert(1);
        }

        .login-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }

        .login-body {
            padding: 40px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            background: #667eea;
            border: none;
            border-radius: 8px;
            color: #ffffff;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #ffffff;
            text-decoration: none;
            font-size: 14px;
            transition: opacity 0.3s ease;
        }

        .back-link a:hover {
            opacity: 0.8;
            text-decoration: underline;
        }

        /* Accesibilidad */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <!-- Header -->
        <div class="login-header">
            <img src="../images/brand-coordi-black.svg" alt="Coordicanarias" onerror="this.style.display='none'">
            <h1>Panel de Administración</h1>
        </div>

        <!-- Body -->
        <div class="login-body">
            <!-- Mensajes de error -->
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert" aria-live="assertive">
                    <strong>Error:</strong> <?= e($error) ?>
                </div>
            <?php endif; ?>

            <!-- Formulario de login -->
            <form method="POST" action="" novalidate>
                <div class="mb-3">
                    <label for="username" class="form-label">
                        Usuario
                        <span class="sr-only">(requerido)</span>
                    </label>
                    <input type="text"
                           class="form-control"
                           id="username"
                           name="username"
                           required
                           autofocus
                           autocomplete="username"
                           aria-required="true"
                           placeholder="Ingrese su usuario">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">
                        Contraseña
                        <span class="sr-only">(requerida)</span>
                    </label>
                    <input type="password"
                           class="form-control"
                           id="password"
                           name="password"
                           required
                           autocomplete="current-password"
                           aria-required="true"
                           placeholder="Ingrese su contraseña">
                </div>

                <button type="submit" class="btn btn-login">
                    Iniciar Sesión
                </button>
            </form>
        </div>
    </div>

    <!-- Volver al sitio -->
    <div class="back-link">
        <a href="../index.html">← Volver al sitio web</a>
    </div>
</div>

</body>
</html>
