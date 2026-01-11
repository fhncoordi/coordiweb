<?php
/**
 * Donaciones - Gestión de Donaciones
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
    $where[] = "DATE(fecha_creacion) >= ?";
    $params[] = $fecha_desde;
}

if ($fecha_hasta) {
    $where[] = "DATE(fecha_creacion) <= ?";
    $params[] = $fecha_hasta;
}

$whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Obtener donaciones
$donaciones = fetchAll("
    SELECT *
    FROM donaciones
    $whereClause
    ORDER BY fecha_creacion DESC
", $params);

// Calcular estadísticas
$stats = fetchOne("
    SELECT
        COUNT(*) as total_donaciones,
        SUM(CASE WHEN estado = 'completed' THEN importe ELSE 0 END) as total_recaudado,
        SUM(CASE WHEN estado = 'completed' THEN 1 ELSE 0 END) as completadas,
        SUM(CASE WHEN estado = 'pending' THEN 1 ELSE 0 END) as pendientes,
        SUM(CASE WHEN estado = 'failed' THEN 1 ELSE 0 END) as fallidas
    FROM donaciones
    $whereClause
", $params);

// Variables para el header
$page_title = 'Donaciones';
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
            <i class="fas fa-hand-holding-heart me-2"></i>Donaciones
        </h1>
        <div class="page-breadcrumb">
            <i class="fas fa-home me-1"></i>
            <a href="index.php">Inicio</a>
            <i class="fas fa-chevron-right mx-2"></i>
            Donaciones
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card primary">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-card-title">Total Donaciones</div>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fas fa-list"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= $stats['total_donaciones'] ?></div>
                <div class="stat-card-label">Donaciones recibidas</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card success">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-card-title">Total Recaudado</div>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fas fa-euro-sign"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= number_format($stats['total_recaudado'], 2) ?> €</div>
                <div class="stat-card-label">Importe total</div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="stat-card info">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-card-title">Completadas</div>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= $stats['completadas'] ?></div>
                <div class="stat-card-label">Donaciones pagadas</div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="stat-card warning">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-card-title">Pendientes</div>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= $stats['pendientes'] ?></div>
                <div class="stat-card-label">Por procesar</div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="stat-card danger">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-card-title">Fallidas</div>
                    </div>
                    <div class="stat-card-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= $stats['fallidas'] ?></div>
                <div class="stat-card-label">Con error</div>
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
                        <option value="completed" <?= $estado === 'completed' ? 'selected' : '' ?>>Completadas</option>
                        <option value="pending" <?= $estado === 'pending' ? 'selected' : '' ?>>Pendientes</option>
                        <option value="failed" <?= $estado === 'failed' ? 'selected' : '' ?>>Fallidas</option>
                        <option value="refunded" <?= $estado === 'refunded' ? 'selected' : '' ?>>Reembolsadas</option>
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
                    <a href="donaciones.php" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de donaciones -->
    <div class="admin-table-wrapper">
        <div class="table-header">
            <h3 class="table-title">
                <i class="fas fa-list me-2"></i>Listado de Donaciones
            </h3>
            <a href="socios.php" class="btn btn-sm btn-primary">
                <i class="fas fa-users me-1"></i>Ver Socios
            </a>
        </div>

        <?php if (empty($donaciones)): ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-hand-holding-heart fa-3x mb-3 opacity-25"></i>
                <p class="mb-0">No hay donaciones que mostrar</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Donante</th>
                        <th>Email</th>
                        <th>Importe</th>
                        <th>Método</th>
                        <th>Estado</th>
                        <th>Mensaje</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donaciones as $donacion): ?>
                    <tr>
                        <td><?= $donacion['id'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($donacion['fecha_creacion'])) ?></td>
                        <td>
                            <?php if ($donacion['es_anonimo']): ?>
                                <em class="text-muted">Anónimo</em>
                            <?php else: ?>
                                <?= e($donacion['nombre']) ?>
                            <?php endif; ?>
                        </td>
                        <td><?= e($donacion['email']) ?></td>
                        <td><strong><?= number_format($donacion['importe'], 2) ?> €</strong></td>
                        <td>
                            <?php if ($donacion['metodo_pago']): ?>
                                <span class="badge bg-info"><?= strtoupper($donacion['metodo_pago']) ?></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $badgeClass = [
                                'completed' => 'bg-success',
                                'pending' => 'bg-warning',
                                'failed' => 'bg-danger',
                                'refunded' => 'bg-secondary'
                            ][$donacion['estado']] ?? 'bg-secondary';

                            $estadoTexto = [
                                'completed' => 'Completada',
                                'pending' => 'Pendiente',
                                'failed' => 'Fallida',
                                'refunded' => 'Reembolsada'
                            ][$donacion['estado']] ?? $donacion['estado'];
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= $estadoTexto ?></span>
                        </td>
                        <td>
                            <?php if ($donacion['mensaje']): ?>
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#mensajeModal<?= $donacion['id'] ?>">
                                    <i class="fas fa-envelope me-1"></i>Ver mensaje
                                </button>

                                <!-- Modal de mensaje -->
                                <div class="modal fade" id="mensajeModal<?= $donacion['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Mensaje de <?= e($donacion['nombre']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><?= nl2br(e($donacion['mensaje'])) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($donacion['stripe_payment_intent_id']): ?>
                                <a href="https://dashboard.stripe.com/test/payments/<?= $donacion['stripe_payment_intent_id'] ?>"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-external-link-alt me-1"></i>Ver en Stripe
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
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
