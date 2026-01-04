<?php
/**
 * Sidebar - Menú Lateral de Navegación
 * Coordicanarias CMS
 */

// Determinar página activa
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-content">
        <!-- Navegación Principal -->
        <nav class="sidebar-nav">
            <!-- Dashboard -->
            <a href="<?= url('admin/index.php') ?>" class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>

            <!-- Sección: Contenido -->
            <div class="nav-section">
                <div class="nav-section-title">Contenido</div>

                <?php if (puedeGestionarAreas()): ?>
                <a href="<?= url('admin/areas.php') ?>" class="nav-link <?= $current_page === 'areas.php' ? 'active' : '' ?>">
                    <i class="fas fa-th-large"></i>
                    <span>Áreas</span>
                </a>
                <?php elseif (getCurrentUser()['rol'] === 'coordinador'): ?>
                <a href="<?= url('admin/areas.php') ?>" class="nav-link <?= $current_page === 'areas.php' ? 'active' : '' ?>">
                    <i class="fas fa-th-large"></i>
                    <span>Mi Área</span>
                </a>
                <?php endif; ?>

                <a href="<?= url('admin/proyectos.php') ?>" class="nav-link <?= $current_page === 'proyectos.php' ? 'active' : '' ?>">
                    <i class="fas fa-folder-open"></i>
                    <span>Proyectos</span>
                </a>

                <a href="<?= url('admin/servicios.php') ?>" class="nav-link <?= $current_page === 'servicios.php' ? 'active' : '' ?>">
                    <i class="fas fa-concierge-bell"></i>
                    <span>Servicios</span>
                </a>

                <a href="<?= url('admin/beneficios.php') ?>" class="nav-link <?= $current_page === 'beneficios.php' ? 'active' : '' ?>">
                    <i class="fas fa-star"></i>
                    <span>Beneficios</span>
                </a>

                <a href="<?= url('admin/noticias.php') ?>" class="nav-link <?= $current_page === 'noticias.php' ? 'active' : '' ?>">
                    <i class="fas fa-newspaper"></i>
                    <span>Noticias</span>
                </a>

                <?php if (getCurrentUser()['rol'] !== 'coordinador'): ?>
                <a href="<?= url('admin/testimonios.php') ?>" class="nav-link <?= $current_page === 'testimonios.php' ? 'active' : '' ?>">
                    <i class="fas fa-quote-left"></i>
                    <span>Testimonios</span>
                </a>
                <?php endif; ?>
            </div>

            <!-- Sección: Sistema (solo admin) -->
            <?php if (puedeGestionarUsuarios() || puedeGestionarConfiguracion() || puedeVerRegistroActividad()): ?>
            <div class="nav-section">
                <div class="nav-section-title">Sistema</div>

                <?php if (puedeGestionarUsuarios()): ?>
                <a href="<?= url('admin/usuarios.php') ?>" class="nav-link <?= $current_page === 'usuarios.php' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i>
                    <span>Usuarios</span>
                </a>
                <?php endif; ?>

                <?php if (puedeGestionarConfiguracion()): ?>
                <a href="<?= url('admin/configuracion.php') ?>" class="nav-link <?= $current_page === 'configuracion.php' ? 'active' : '' ?>">
                    <i class="fas fa-cog"></i>
                    <span>Configuración</span>
                </a>
                <?php endif; ?>

                <?php if (puedeVerRegistroActividad()): ?>
                <a href="<?= url('admin/actividad.php') ?>" class="nav-link <?= $current_page === 'actividad.php' ? 'active' : '' ?>">
                    <i class="fas fa-history"></i>
                    <span>Registro de Actividad</span>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Separador -->
            <hr class="sidebar-divider">

            <!-- Ver sitio público -->
            <a href="<?= url('index.php') ?>" target="_blank" class="nav-link">
                <i class="fas fa-external-link-alt"></i>
                <span>Ver Sitio Público</span>
            </a>
        </nav>

        <!-- Footer del Sidebar -->
        <div class="sidebar-footer">
            <div class="text-muted small">
                <i class="fas fa-code"></i> Coordicanarias CMS v1.0
            </div>
        </div>
    </div>
</aside>
