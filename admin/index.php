<?php
/**
 * Dashboard - Panel de Administración
 * Coordicanarias CMS
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/core/auth.php';
require_once __DIR__ . '/../php/core/security.php';

// Requerir autenticación
requireLogin();

// Establecer headers de seguridad
setSecurityHeaders();

// Obtener usuario actual
$usuario = getCurrentUser();

// Obtener estadísticas
try {
    // Total de proyectos
    $total_proyectos = fetchOne("SELECT COUNT(*) as total FROM proyectos")['total'];
    $proyectos_activos = fetchOne("SELECT COUNT(*) as total FROM proyectos WHERE activo = 1")['total'];

    // Total de áreas
    $total_areas = fetchOne("SELECT COUNT(*) as total FROM areas WHERE activo = 1")['total'];

    // Total de servicios
    $total_servicios = fetchOne("SELECT COUNT(*) as total FROM servicios WHERE activo = 1")['total'];

    // Total de beneficios
    $total_beneficios = fetchOne("SELECT COUNT(*) as total FROM beneficios WHERE activo = 1")['total'];

    // Total de testimonios
    $total_testimonios = fetchOne("SELECT COUNT(*) as total FROM testimonios WHERE activo = 1")['total'];

    // Total de usuarios
    $total_usuarios = fetchOne("SELECT COUNT(*) as total FROM usuarios WHERE activo = 1")['total'];

    // Últimos proyectos creados
    $ultimos_proyectos = fetchAll("
        SELECT p.titulo, p.fecha_creacion, a.nombre as area_nombre
        FROM proyectos p
        LEFT JOIN areas a ON p.area_id = a.id
        ORDER BY p.fecha_creacion DESC
        LIMIT 5
    ");

    // Últimas actividades (si el usuario es admin)
    if ($usuario['rol'] === 'admin') {
        $ultimas_actividades = fetchAll("
            SELECT ra.accion, ra.tabla_afectada, ra.fecha_hora, u.nombre_completo
            FROM registro_actividad ra
            LEFT JOIN usuarios u ON ra.usuario_id = u.id
            ORDER BY ra.fecha_hora DESC
            LIMIT 10
        ");
    }

} catch (Exception $e) {
    error_log("Error al obtener estadísticas: " . $e->getMessage());
    $total_proyectos = $proyectos_activos = $total_areas = $total_servicios = 0;
    $total_beneficios = $total_testimonios = $total_usuarios = 0;
    $ultimos_proyectos = [];
    $ultimas_actividades = [];
}

// Variables para el header
$page_title = 'Dashboard';

// Incluir header y sidebar
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<!-- Contenido Principal -->
<main class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-chart-line me-2"></i>Dashboard</h1>
        <div class="page-breadcrumb">
            <i class="fas fa-home me-1"></i> Inicio
        </div>
    </div>

    <!-- Bienvenida -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <strong>¡Bienvenido, <?= e($usuario['nombre_completo']) ?>!</strong>
        Este es tu panel de administración de contenido.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="stats-grid">
        <!-- Proyectos -->
        <div class="stat-card primary">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-title">Proyectos</div>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
            </div>
            <div class="stat-card-value"><?= $proyectos_activos ?></div>
            <div class="stat-card-label">
                <?= $proyectos_activos ?> activos de <?= $total_proyectos ?> total
            </div>
        </div>

        <!-- Áreas -->
        <div class="stat-card success">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-title">Áreas Temáticas</div>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-th-large"></i>
                </div>
            </div>
            <div class="stat-card-value"><?= $total_areas ?></div>
            <div class="stat-card-label">Áreas activas</div>
        </div>

        <!-- Servicios -->
        <div class="stat-card warning">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-title">Servicios</div>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-concierge-bell"></i>
                </div>
            </div>
            <div class="stat-card-value"><?= $total_servicios ?></div>
            <div class="stat-card-label">Servicios activos</div>
        </div>

        <!-- Testimonios -->
        <div class="stat-card info">
            <div class="stat-card-header">
                <div>
                    <div class="stat-card-title">Testimonios</div>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-quote-left"></i>
                </div>
            </div>
            <div class="stat-card-value"><?= $total_testimonios ?></div>
            <div class="stat-card-label">Testimonios publicados</div>
        </div>
    </div>

    <!-- Fila con dos columnas -->
    <div class="row g-4">
        <!-- Últimos Proyectos -->
        <div class="col-lg-6">
            <div class="admin-table-wrapper">
                <div class="table-header">
                    <h3 class="table-title">
                        <i class="fas fa-folder-open me-2"></i>Últimos Proyectos
                    </h3>
                    <a href="<?= url('admin/proyectos.php') ?>" class="btn btn-sm btn-primary">
                        Ver todos <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

                <?php if (count($ultimos_proyectos) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Proyecto</th>
                                <th>Área</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimos_proyectos as $proyecto): ?>
                            <tr>
                                <td>
                                    <strong><?= e($proyecto['titulo']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= $proyecto['area_nombre'] ? e($proyecto['area_nombre']) : 'Sin área' ?>
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    <?= date('d/m/Y', strtotime($proyecto['fecha_creacion'])) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                    <p>No hay proyectos creados aún</p>
                    <a href="<?= url('admin/proyectos.php') ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Crear primer proyecto
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Registro de Actividad (solo admin) -->
        <div class="col-lg-6">
            <div class="admin-table-wrapper">
                <div class="table-header">
                    <h3 class="table-title">
                        <i class="fas fa-history me-2"></i>Actividad Reciente
                    </h3>
                    <?php if ($usuario['rol'] === 'admin'): ?>
                    <a href="<?= url('admin/actividad.php') ?>" class="btn btn-sm btn-secondary">
                        Ver todo <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                    <?php endif; ?>
                </div>

                <?php if ($usuario['rol'] === 'admin' && count($ultimas_actividades) > 0): ?>
                <div class="list-group list-group-flush">
                    <?php foreach (array_slice($ultimas_actividades, 0, 5) as $actividad): ?>
                    <div class="list-group-item px-0">
                        <div class="d-flex w-100 justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <i class="fas fa-user-circle me-2 text-primary"></i>
                                    <?= e($actividad['nombre_completo'] ?? 'Sistema') ?>
                                </h6>
                                <p class="mb-1 small">
                                    <strong><?= ucfirst(e($actividad['accion'])) ?></strong>
                                    <?php if ($actividad['tabla_afectada']): ?>
                                    en <code><?= e($actividad['tabla_afectada']) ?></code>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <small class="text-muted">
                                <?= date('d/m H:i', strtotime($actividad['fecha_hora'])) ?>
                            </small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-history fa-3x mb-3 opacity-25"></i>
                    <p>No hay actividad reciente</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="mt-4">
        <div class="admin-table-wrapper">
            <div class="table-header mb-3">
                <h3 class="table-title">
                    <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                </h3>
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <a href="<?= url('admin/areas.php') ?>" class="btn btn-outline-primary w-100 py-3">
                        <i class="fas fa-th-large fa-2x mb-2 d-block"></i>
                        Gestionar Áreas
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= url('admin/proyectos.php') ?>" class="btn btn-outline-success w-100 py-3">
                        <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                        Nuevo Proyecto
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= url('admin/noticias.php?crear=1') ?>" class="btn btn-outline-warning w-100 py-3">
                        <i class="fas fa-newspaper fa-2x mb-2 d-block"></i>
                        Nueva Noticia
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="<?= url('admin/configuracion.php') ?>" class="btn btn-outline-info w-100 py-3">
                        <i class="fas fa-cog fa-2x mb-2 d-block"></i>
                        Configuración
                    </a>
                </div>
            </div>
        </div>
    </div>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
