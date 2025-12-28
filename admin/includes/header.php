<?php
/**
 * Header Común - Panel de Administración
 * Coordicanarias CMS
 */

// Asegurar que el usuario está autenticado
if (!isset($usuario) || !isLoggedIn()) {
    header('Location: ' . url('admin/login.php'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= isset($page_title) ? e($page_title) . ' - ' : '' ?>Panel Admin | Coordicanarias</title>
    <link rel="icon" href="<?= url('favicon.ico') ?>" type="image/x-icon">

    <!-- Bootstrap 5 -->
    <link href="<?= url('css/bootstrap.min.css') ?>" rel="stylesheet">

    <!-- Font Awesome 6 (para iconos) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Estilos del sitio principal -->
    <link href="<?= url('css/style.css') ?>" rel="stylesheet">

    <!-- Estilos del panel admin -->
    <link href="<?= url('admin/assets/css/admin.css') ?>" rel="stylesheet">
</head>
<body class="admin-body">

<!-- Navbar Superior -->
<nav class="admin-navbar">
    <div class="container-fluid">
        <div class="navbar-content">
            <!-- Logo y título -->
            <div class="navbar-brand-section">
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="Alternar menú lateral">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="<?= url('admin/index.php') ?>" class="admin-brand">
                    <i class="fas fa-shield-alt"></i>
                    <span>Coordicanarias CMS</span>
                </a>
            </div>

            <!-- Usuario y acciones -->
            <div class="navbar-user-section">
                <!-- Notificaciones (futuro) -->
                <div class="nav-item dropdown" style="display: none;">
                    <button class="nav-link" data-bs-toggle="dropdown" aria-label="Notificaciones">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </button>
                </div>

                <!-- Usuario actual -->
                <div class="nav-item dropdown">
                    <button class="nav-link user-menu" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menú de usuario">
                        <div class="user-avatar">
                            <?= strtoupper(substr($usuario['nombre_completo'], 0, 1)) ?>
                        </div>
                        <div class="user-info">
                            <span class="user-name"><?= e($usuario['nombre_completo']) ?></span>
                            <span class="user-role"><?= e($usuario['rol']) ?></span>
                        </div>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= url('admin/perfil.php') ?>"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="<?= url('admin/configuracion.php') ?>"><i class="fas fa-cog me-2"></i>Configuración</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= url('admin/logout.php') ?>"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Layout Principal: Sidebar + Contenido -->
<div class="admin-layout">
