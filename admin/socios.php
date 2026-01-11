<?php
/**
 * Socios - Gestión de Suscripciones Mensuales
 * Coordicanarias CMS
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/core/auth.php';
require_once __DIR__ . '/../php/core/security.php';

// Requerir autenticación
requireLogin();

// Establecer headers de seguridad
setSecurityHeaders();

// Obtener filtros
$estado = $_GET['estado'] ?? 'all';
$fecha_desde = $_GET['fecha_desde'] ?? '';
$fecha_hasta = $_GET['fecha_hasta'] ?? '';

// Construir query
$where = [];
$params = [];

if ($estado !== 'all') {
    $where[] = "estado = ?";
    $params[] = $estado;
}

if ($fecha_desde) {
    $where[] = "DATE(fecha_inicio) >= ?";
    $params[] = $fecha_desde;
}

if ($fecha_hasta) {
    $where[] = "DATE(fecha_inicio) <= ?";
    $params[] = $fecha_hasta;
}

$whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Obtener socios
$socios = fetchAll("
    SELECT *
    FROM socios
    $whereClause
    ORDER BY fecha_creacion DESC
", $params);

// Calcular estadísticas
$stats = fetchOne("
    SELECT
        COUNT(*) as total_socios,
        SUM(CASE WHEN estado = 'active' THEN 1 ELSE 0 END) as activos,
        SUM(CASE WHEN estado IN ('active', 'trialing') THEN 1 ELSE 0 END) * 5 as ingresos_mensuales,
        SUM(CASE WHEN estado = 'canceled' THEN 1 ELSE 0 END) as cancelados,
        SUM(CASE WHEN estado = 'past_due' THEN 1 ELSE 0 END) as con_problemas
    FROM socios
    $whereClause
", $params);

// Variables para el header
$page_title = 'Socios';
$usuario = getCurrentUser(); // Obtener datos del usuario actual

// Incluir header y sidebar
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<!-- Contenido Principal -->
<main class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <i class="fas fa-users me-2"></i>Socios
        </h1>
        <div class="page-breadcrumb">
            <i class="fas fa-home me-1"></i>
            <a href="index.php">Inicio</a>
            <i class="fas fa-chevron-right mx-2"></i>
            Socios
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card primary">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-card-title">Total Socios</div>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= $stats['total_socios'] ?></div>
                <div class="stat-card-label">Socios registrados</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card success">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-card-title">Socios Activos</div>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= $stats['activos'] ?></div>
                <div class="stat-card-label">Con suscripción activa</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card info">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-card-title">Ingresos Mensuales</div>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fas fa-euro-sign"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= number_format($stats['ingresos_mensuales'], 2) ?> €</div>
                <div class="stat-card-label">Recurrentes (5€/mes)</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card warning">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-card-title">Con Problemas</div>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= $stats['con_problemas'] ?></div>
                <div class="stat-card-label">Pagos fallidos</div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="admin-table-wrapper mb-4">
        <div class="table-header">
            <h3 class="table-title">
                <i class="fas fa-filter me-2"></i>Filtros
            </h3>
        </div>
        <div class="p-3">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="estado" class="form-label">Estado:</label>
                    <select id="estado" name="estado" class="form-select">
                        <option value="all" <?= $estado === 'all' ? 'selected' : '' ?>>Todos</option>
                        <option value="active" <?= $estado === 'active' ? 'selected' : '' ?>>Activos</option>
                        <option value="trialing" <?= $estado === 'trialing' ? 'selected' : '' ?>>En Prueba</option>
                        <option value="past_due" <?= $estado === 'past_due' ? 'selected' : '' ?>>Pago Vencido</option>
                        <option value="canceled" <?= $estado === 'canceled' ? 'selected' : '' ?>>Cancelados</option>
                        <option value="incomplete" <?= $estado === 'incomplete' ? 'selected' : '' ?>>Incompletos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="fecha_desde" class="form-label">Desde:</label>
                    <input type="date" id="fecha_desde" name="fecha_desde" class="form-control" value="<?= e($fecha_desde) ?>">
                </div>
                <div class="col-md-3">
                    <label for="fecha_hasta" class="form-label">Hasta:</label>
                    <input type="date" id="fecha_hasta" name="fecha_hasta" class="form-control" value="<?= e($fecha_hasta) ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>Filtrar
                    </button>
                    <a href="socios.php" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de socios -->
    <div class="admin-table-wrapper">
        <div class="table-header">
            <h3 class="table-title">
                <i class="fas fa-list me-2"></i>Listado de Socios
            </h3>
            <a href="donaciones.php" class="btn btn-sm btn-primary">
                <i class="fas fa-hand-holding-heart me-1"></i>Ver Donaciones
            </a>
        </div>

        <?php if (empty($socios)): ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-users fa-3x mb-3 opacity-25"></i>
                <p class="mb-0">No hay socios que mostrar</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha Alta</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Próximo Cobro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($socios as $socio): ?>
                    <tr>
                        <td><?= $socio['id'] ?></td>
                        <td>
                            <?php if ($socio['fecha_inicio']): ?>
                                <?= date('d/m/Y', strtotime($socio['fecha_inicio'])) ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e($socio['nombre']) ?></td>
                        <td><?= e($socio['email']) ?></td>
                        <td>
                            <?php if ($socio['telefono']): ?>
                                <?= e($socio['telefono']) ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $badgeClass = [
                                'active' => 'bg-success',
                                'trialing' => 'bg-info',
                                'past_due' => 'bg-warning',
                                'canceled' => 'bg-danger',
                                'incomplete' => 'bg-secondary',
                                'unpaid' => 'bg-secondary'
                            ][$socio['estado']] ?? 'bg-secondary';

                            $estadoTexto = [
                                'active' => 'Activo',
                                'trialing' => 'En Prueba',
                                'past_due' => 'Pago Vencido',
                                'canceled' => 'Cancelado',
                                'incomplete' => 'Incompleto',
                                'unpaid' => 'Impagado'
                            ][$socio['estado']] ?? $socio['estado'];
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= $estadoTexto ?></span>
                        </td>
                        <td>
                            <?php
                            // Validar que la fecha sea válida y no sea '0000-00-00'
                            if ($socio['fecha_proximo_cobro']
                                && $socio['fecha_proximo_cobro'] !== '0000-00-00'
                                && in_array($socio['estado'], ['active', 'trialing'])) {
                                $timestamp = strtotime($socio['fecha_proximo_cobro']);
                                // Verificar que el timestamp sea válido (después del 1 de enero de 2020)
                                if ($timestamp && $timestamp > strtotime('2020-01-01')) {
                                    echo date('d/m/Y', $timestamp);
                                } else {
                                    echo '<span class="text-muted">Consultar Stripe</span>';
                                }
                            } else {
                                echo '<span class="text-muted">-</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if ($socio['stripe_subscription_id']): ?>
                                <a href="https://dashboard.stripe.com/test/subscriptions/<?= e($socio['stripe_subscription_id']) ?>"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-external-link-alt me-1"></i>Ver en Stripe
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>

                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#notasModal<?= $socio['id'] ?>">
                                <i class="fas fa-sticky-note me-1"></i>Notas
                            </button>

                            <!-- Modal de notas -->
                            <div class="modal fade" id="notasModal<?= $socio['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Notas de <?= e($socio['nombre']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="guardar-notas-socio.php">
                                                <input type="hidden" name="socio_id" value="<?= $socio['id'] ?>">
                                                <textarea class="form-control" name="notas" rows="5" placeholder="Notas internas sobre este socio..."><?= e($socio['notas_admin'] ?? '') ?></textarea>
                                                <button type="submit" class="btn btn-primary mt-3">
                                                    <i class="fas fa-save me-1"></i>Guardar Notas
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
