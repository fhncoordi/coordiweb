<?php
/**
 * Página de Logout - Panel de Administración
 * Coordicanarias CMS
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/core/auth.php';

// Cerrar sesión
logout();

// Redirigir al login
header('Location: ' . url('admin/login.php?logout=success'));
exit;
