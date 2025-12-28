<?php
/**
 * Página de Logout - Panel de Administración
 * Coordicanarias CMS
 */

require_once __DIR__ . '/../php/core/auth.php';

// Cerrar sesión
logout();

// Redirigir al login
header('Location: /admin/login.php?logout=success');
exit;
