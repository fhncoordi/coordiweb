<?php
/**
 * Dashboard - Panel de Administración
 * Coordicanarias CMS
 *
 * TEMPORAL: Solo para probar el sistema de login
 * TODO: Crear dashboard completo con estadísticas
 */

require_once __DIR__ . '/../php/core/auth.php';
require_once __DIR__ . '/../php/core/security.php';

// Requerir autenticación
requireLogin();

// Establecer headers de seguridad
setSecurityHeaders();

// Obtener usuario actual
$usuario = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Panel de Administración | Coordicanarias</title>
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">

    <style>
        body {
            background: #f5f5f5;
            font-family: 'Open Sans', sans-serif;
        }

        .navbar-admin {
            background: #243659;
            padding: 15px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-admin .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            color: #ffffff;
            font-size: 20px;
            font-weight: 600;
            text-decoration: none;
        }

        .user-info {
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-logout {
            background: #dc3545;
            color: #ffffff;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background: #c82333;
            color: #ffffff;
        }

        .welcome-section {
            background: #ffffff;
            border-radius: 12px;
            padding: 40px;
            margin: 40px auto;
            max-width: 800px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .welcome-section h1 {
            color: #243659;
            margin-bottom: 20px;
        }

        .badge-role {
            background: #667eea;
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .info-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .info-card strong {
            display: block;
            color: #666;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .info-card span {
            color: #333;
            font-size: 16px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar-admin">
    <div class="container">
        <a href="/admin/index.php" class="navbar-brand">
            Coordicanarias - Panel Admin
        </a>
        <div class="user-info">
            <span>
                <?= e($usuario['nombre_completo']) ?>
                <span class="badge-role"><?= e($usuario['rol']) ?></span>
            </span>
            <a href="/admin/logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </div>
</nav>

<!-- Contenido Principal -->
<div class="container">
    <div class="welcome-section">
        <h1>¡Bienvenido al Panel de Administración! 🎉</h1>

        <p>Has iniciado sesión correctamente en el sistema de gestión de contenido de Coordicanarias.</p>

        <div class="info-grid">
            <div class="info-card">
                <strong>Usuario</strong>
                <span><?= e($usuario['username']) ?></span>
            </div>

            <div class="info-card">
                <strong>Rol</strong>
                <span><?= e($usuario['rol']) ?></span>
            </div>

            <div class="info-card">
                <strong>Email</strong>
                <span><?= e($usuario['email']) ?></span>
            </div>

            <div class="info-card">
                <strong>Último acceso</strong>
                <span><?= $usuario['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_acceso'])) : 'Primera vez' ?></span>
            </div>
        </div>

        <div style="margin-top: 40px; padding: 20px; background: #e7f3ff; border-radius: 8px; border-left: 4px solid #007bff;">
            <strong>✅ Sistema de autenticación funcionando correctamente</strong>
            <p style="margin: 10px 0 0 0; color: #666;">
                El sistema de login, sesiones y protección CSRF está operativo.
                Próximo paso: Crear el dashboard completo y los módulos CRUD.
            </p>
        </div>
    </div>
</div>

</body>
</html>
