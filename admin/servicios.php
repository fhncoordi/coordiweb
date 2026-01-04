<?php
/**
 * Servicios - Gestión de Servicios por Área
 * Coordicanarias CMS
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/core/auth.php';
require_once __DIR__ . '/../php/core/security.php';
require_once __DIR__ . '/../php/models/Servicio.php';

// Requerir autenticación
requireLogin();

// Establecer headers de seguridad
setSecurityHeaders();

// Obtener usuario actual
$usuario = getCurrentUser();

// Verificar permisos por rol
$es_coordinador = ($usuario['rol'] === 'coordinador');
$area_permitida = $es_coordinador ? $usuario['area_id'] : null;

// Variables para mensajes
$mensaje = '';
$tipo_mensaje = '';

// Determinar modo de vista
$modo = 'listado';
if (isset($_GET['crear'])) {
    $modo = 'crear';
} elseif (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
    $modo = 'editar';
    $servicio_id = (int)$_GET['editar'];
    $servicio = Servicio::getById($servicio_id);

    if (!$servicio) {
        header('Location: ' . url('admin/servicios.php?error=not_found'));
        exit;
    }

    // Verificar permisos: coordinador solo puede editar servicios de su área
    if (!puedeGestionarServicio($servicio_id)) {
        header('Location: ' . url('admin/servicios.php?error=permission_denied'));
        exit;
    }
}

// Procesar formularios POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar token CSRF
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $mensaje = 'Token de seguridad inválido';
        $tipo_mensaje = 'danger';
    } else {
        $accion = $_POST['accion'] ?? '';

        // ACCIÓN: Toggle Activo
        if ($accion === 'toggle_activo' && isset($_POST['servicio_id'])) {
            $servicio_id = (int)$_POST['servicio_id'];
            $nuevo_estado = (int)$_POST['nuevo_estado'];

            // Verificar permisos
            if (!puedeGestionarServicio($servicio_id)) {
                $mensaje = 'No tienes permisos para modificar este servicio';
                $tipo_mensaje = 'danger';
            } elseif (Servicio::toggleActivo($servicio_id, $nuevo_estado)) {
                registrarActividad(getCurrentUserId(), 'actualizar', 'servicios', $servicio_id, 'Cambió estado a ' . ($nuevo_estado ? 'activo' : 'inactivo'));
                $mensaje = 'Estado actualizado correctamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al actualizar el estado';
                $tipo_mensaje = 'danger';
            }
        }

        // ACCIÓN: Eliminar
        elseif ($accion === 'eliminar' && isset($_POST['servicio_id'])) {
            $servicio_id = (int)$_POST['servicio_id'];

            // Verificar permisos
            if (!puedeGestionarServicio($servicio_id)) {
                $mensaje = 'No tienes permisos para eliminar este servicio';
                $tipo_mensaje = 'danger';
            } elseif (Servicio::delete($servicio_id)) {
                registrarActividad(getCurrentUserId(), 'eliminar', 'servicios', $servicio_id, 'Eliminó servicio (soft delete)');
                $mensaje = 'Servicio eliminado correctamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al eliminar el servicio';
                $tipo_mensaje = 'danger';
            }
        }

        // ACCIÓN: Crear
        elseif ($accion === 'crear') {
            // Preparar datos
            $datos = [
                'area_id' => $es_coordinador ? $area_permitida : (int)($_POST['area_id'] ?? 0),
                'titulo' => trim($_POST['titulo'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'icono' => trim($_POST['icono'] ?? ''),
                'orden' => (int)($_POST['orden'] ?? 0),
                'activo' => isset($_POST['activo']) ? 1 : 0
            ];

            // Validar datos
            $errores = Servicio::validar($datos);

            if (empty($errores)) {
                $nuevo_id = Servicio::create($datos);

                if ($nuevo_id) {
                    registrarActividad('create', 'servicios', $nuevo_id, 'Creó servicio: ' . $datos['titulo']);
                    header('Location: ' . url('admin/servicios.php?success=created'));
                    exit;
                } else {
                    $mensaje = 'Error al crear el servicio';
                    $tipo_mensaje = 'danger';
                }
            } else {
                $mensaje = implode('<br>', $errores);
                $tipo_mensaje = 'danger';
            }
        }

        // ACCIÓN: Editar
        elseif ($accion === 'editar' && isset($_POST['servicio_id'])) {
            $servicio_id = (int)$_POST['servicio_id'];
            $servicio_actual = Servicio::getById($servicio_id);

            if (!$servicio_actual) {
                $mensaje = 'Servicio no encontrado';
                $tipo_mensaje = 'danger';
            } elseif (!puedeGestionarServicio($servicio_id)) {
                $mensaje = 'No tienes permisos para editar este servicio';
                $tipo_mensaje = 'danger';
            } else {
                // Preparar datos
                $datos = [
                    'area_id' => $es_coordinador ? $area_permitida : (int)($_POST['area_id'] ?? 0),
                    'titulo' => trim($_POST['titulo'] ?? ''),
                    'descripcion' => trim($_POST['descripcion'] ?? ''),
                    'icono' => trim($_POST['icono'] ?? ''),
                    'orden' => (int)($_POST['orden'] ?? $servicio_actual['orden']),
                    'activo' => isset($_POST['activo']) ? 1 : 0
                ];

                // Validar datos
                $errores = Servicio::validar($datos, $servicio_id);

                if (empty($errores)) {
                    if (Servicio::update($servicio_id, $datos)) {
                        registrarActividad('update', 'servicios', $servicio_id, 'Actualizó servicio: ' . $datos['titulo']);
                        header('Location: ' . url('admin/servicios.php?success=updated'));
                        exit;
                    } else {
                        $mensaje = 'Error al actualizar el servicio';
                        $tipo_mensaje = 'danger';
                    }
                } else {
                    $mensaje = implode('<br>', $errores);
                    $tipo_mensaje = 'danger';
                }
            }
        }
    }
}

// Mostrar mensajes de URL
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'created') {
        $mensaje = 'Servicio creado correctamente';
        $tipo_mensaje = 'success';
    } elseif ($_GET['success'] === 'updated') {
        $mensaje = 'Servicio actualizado correctamente';
        $tipo_mensaje = 'success';
    }
}

if (isset($_GET['error'])) {
    if ($_GET['error'] === 'not_found') {
        $mensaje = 'Servicio no encontrado';
        $tipo_mensaje = 'danger';
    } elseif ($_GET['error'] === 'permission_denied') {
        $mensaje = 'No tienes permisos para acceder a este servicio';
        $tipo_mensaje = 'danger';
    }
}

// Obtener datos para vista de listado
if ($modo === 'listado') {
    // Filtrar por área si es coordinador
    if ($es_coordinador) {
        $servicios_agrupados = Servicio::getAllAgrupados(false, $area_permitida);
        $total_servicios = count(Servicio::getAll(false, $area_permitida));
        $contador_areas = Servicio::contarPorArea(false, $area_permitida);
    } else {
        $servicios_agrupados = Servicio::getAllAgrupados(false);
        $total_servicios = count(Servicio::getAll(false));
        $contador_areas = Servicio::contarPorArea(false);
    }
}

// Obtener áreas para el selector
$areas = Servicio::getAreas();

// Obtener iconos sugeridos
$iconos_sugeridos = Servicio::getIconosSugeridos();

// Variables para el header
$page_title = $modo === 'crear' ? 'Crear Servicio' : ($modo === 'editar' ? 'Editar Servicio' : 'Servicios');

// Incluir header y sidebar
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<!-- Contenido Principal -->
<main class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <i class="fas fa-concierge-bell me-2"></i>
            <?= $modo === 'crear' ? 'Crear Servicio' : ($modo === 'editar' ? 'Editar Servicio' : 'Servicios') ?>
        </h1>
        <div class="page-breadcrumb">
            <i class="fas fa-home me-1"></i>
            <a href="<?= url('admin/index.php') ?>">Inicio</a>
            <i class="fas fa-chevron-right mx-2"></i>
            <?php if ($modo === 'listado'): ?>
                Servicios
            <?php else: ?>
                <a href="<?= url('admin/servicios.php') ?>">Servicios</a>
                <i class="fas fa-chevron-right mx-2"></i>
                <?= $modo === 'crear' ? 'Crear' : 'Editar' ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mensajes -->
    <?php if ($mensaje): ?>
    <div class="alert alert-<?= e($tipo_mensaje) ?> alert-dismissible fade show" role="alert">
        <?= $mensaje ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <?php endif; ?>

    <?php if ($modo === 'listado'): ?>
    <!-- MODO: LISTADO -->
    <div class="admin-table-wrapper">
        <div class="table-header">
            <h3 class="table-title">
                <i class="fas fa-list me-2"></i>Listado de Servicios
                <span class="badge bg-primary ms-2"><?= $total_servicios ?></span>
            </h3>
            <a href="<?= url('admin/servicios.php?crear=1') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Crear Servicio
            </a>
        </div>

        <?php if ($total_servicios > 0): ?>
            <!-- Servicios agrupados por área -->
            <?php foreach ($servicios_agrupados as $area_nombre => $servicios): ?>
            <div class="mb-4">
                <h4 class="mb-3">
                    <i class="fas fa-th-large me-2 text-primary"></i>
                    <?= e($area_nombre) ?>
                    <span class="badge bg-secondary ms-2"><?= count($servicios) ?> servicios</span>
                </h4>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">ID</th>
                                <th style="width: 80px;">Icono</th>
                                <th>Título</th>
                                <th>Descripción</th>
                                <th style="width: 80px;">Orden</th>
                                <th style="width: 100px;">Estado</th>
                                <th style="width: 180px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($servicios as $serv): ?>
                            <tr>
                                <td><strong>#<?= e($serv['id']) ?></strong></td>
                                <td class="text-center">
                                    <?php if ($serv['icono']): ?>
                                        <i class="<?= e($serv['icono']) ?> fa-2x text-primary"></i>
                                    <?php else: ?>
                                        <i class="fas fa-question-circle fa-2x text-muted"></i>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= e($serv['titulo']) ?></strong></td>
                                <td>
                                    <small class="text-muted">
                                        <?= e(mb_substr($serv['descripcion'], 0, 80)) ?>
                                        <?= mb_strlen($serv['descripcion']) > 80 ? '...' : '' ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info"><?= e($serv['orden']) ?></span>
                                </td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                        <input type="hidden" name="accion" value="toggle_activo">
                                        <input type="hidden" name="servicio_id" value="<?= $serv['id'] ?>">
                                        <input type="hidden" name="nuevo_estado" value="<?= $serv['activo'] ? 0 : 1 ?>">
                                        <button type="submit" class="btn btn-sm <?= $serv['activo'] ? 'btn-success' : 'btn-secondary' ?>">
                                            <i class="fas fa-<?= $serv['activo'] ? 'check-circle' : 'times-circle' ?>"></i>
                                            <?= $serv['activo'] ? 'Activo' : 'Inactivo' ?>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= url('admin/servicios.php?editar=' . $serv['id']) ?>"
                                           class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                title="Eliminar" onclick="confirmarEliminar(<?= $serv['id'] ?>, '<?= addslashes($serv['titulo']) ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
        <div class="text-center text-muted py-5">
            <i class="fas fa-concierge-bell fa-3x mb-3 opacity-25"></i>
            <p>No hay servicios creados aún</p>
            <a href="<?= url('admin/servicios.php?crear=1') ?>" class="btn btn-primary mt-2">
                <i class="fas fa-plus me-2"></i>Crear primer servicio
            </a>
        </div>
        <?php endif; ?>
    </div>

    <?php elseif ($modo === 'crear' || $modo === 'editar'): ?>
    <!-- MODO: CREAR / EDITAR -->
    <div class="admin-table-wrapper">
        <form method="POST" id="formServicio">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="accion" value="<?= $modo ?>">
            <?php if ($modo === 'editar'): ?>
            <input type="hidden" name="servicio_id" value="<?= $servicio['id'] ?>">
            <?php endif; ?>

            <div class="row g-4">
                <!-- Columna Izquierda: Datos Principales -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Información del Servicio
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Área -->
                            <div class="mb-3">
                                <label for="area_id" class="form-label">
                                    Área Temática <span class="text-danger">*</span>
                                </label>
                                <?php if ($es_coordinador): ?>
                                    <!-- Coordinador: área fija -->
                                    <?php
                                    $area_coordinador = null;
                                    foreach ($areas as $area) {
                                        if ($area['id'] == $area_permitida) {
                                            $area_coordinador = $area;
                                            break;
                                        }
                                    }
                                    ?>
                                    <input type="text" class="form-control" value="<?= e($area_coordinador['nombre'] ?? 'N/A') ?>" disabled>
                                    <input type="hidden" name="area_id" value="<?= $area_permitida ?>">
                                    <div class="form-text">Solo puedes gestionar servicios de tu área asignada</div>
                                <?php else: ?>
                                    <!-- Admin/Editor: selector normal -->
                                    <select class="form-select" id="area_id" name="area_id" required onchange="actualizarOrden()">
                                        <option value="">Selecciona un área...</option>
                                        <?php foreach ($areas as $area): ?>
                                        <option value="<?= $area['id'] ?>"
                                                data-siguiente-orden="<?= Servicio::getSiguienteOrden($area['id']) ?>"
                                                <?= ($modo === 'editar' && $servicio['area_id'] == $area['id']) ? 'selected' : '' ?>>
                                            <?= e($area['nombre']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                            </div>

                            <!-- Título -->
                            <div class="mb-3">
                                <label for="titulo" class="form-label">
                                    Título <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="titulo" name="titulo"
                                       value="<?= $modo === 'editar' ? e($servicio['titulo']) : '' ?>"
                                       required maxlength="200">
                                <div class="form-text">Máximo 200 caracteres</div>
                            </div>

                            <!-- Descripción -->
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="4"><?= $modo === 'editar' ? e($servicio['descripcion']) : '' ?></textarea>
                                <div class="form-text">Descripción detallada del servicio</div>
                            </div>

                            <!-- Icono -->
                            <div class="mb-3">
                                <label for="icono" class="form-label">Icono Font Awesome</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i id="preview-icono" class="<?= $modo === 'editar' && $servicio['icono'] ? e($servicio['icono']) : 'fas fa-question-circle' ?> fa-lg"></i>
                                    </span>
                                    <input type="text" class="form-control" id="icono" name="icono"
                                           value="<?= $modo === 'editar' ? e($servicio['icono']) : '' ?>"
                                           list="iconos-list" placeholder="fas fa-briefcase"
                                           oninput="actualizarPreviewIcono()">
                                </div>
                                <datalist id="iconos-list">
                                    <?php foreach ($iconos_sugeridos as $clase => $nombre): ?>
                                    <option value="<?= e($clase) ?>"><?= e($nombre) ?></option>
                                    <?php endforeach; ?>
                                </datalist>
                                <div class="form-text">
                                    Ejemplos: <code>fas fa-briefcase</code>, <code>fas fa-heart</code>, <code>fas fa-users</code>
                                    <br><a href="https://fontawesome.com/icons" target="_blank">Ver todos los iconos <i class="fas fa-external-link-alt"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Opciones -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-cog me-2"></i>Opciones
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Orden -->
                            <div class="mb-3">
                                <label for="orden" class="form-label">Orden de visualización</label>
                                <input type="number" class="form-control" id="orden" name="orden"
                                       value="<?= $modo === 'editar' ? e($servicio['orden']) : '1' ?>"
                                       min="1">
                                <div class="form-text">Orden dentro del área</div>
                            </div>

                            <!-- Activo -->
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="activo" name="activo"
                                       <?= ($modo === 'editar' ? ($servicio['activo'] ? 'checked' : '') : 'checked') ?>>
                                <label class="form-check-label" for="activo">
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    Servicio Activo
                                </label>
                                <div class="form-text">Visible en el sitio público</div>
                            </div>
                        </div>
                    </div>

                    <!-- Iconos Sugeridos -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-icons me-2"></i>Iconos Sugeridos
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2">
                                <?php
                                $iconos_destacados = array_slice($iconos_sugeridos, 0, 12, true);
                                foreach ($iconos_destacados as $clase => $nombre):
                                ?>
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                        onclick="seleccionarIcono('<?= e($clase) ?>')"
                                        title="<?= e($nombre) ?>">
                                    <i class="<?= e($clase) ?>"></i>
                                </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>
                    <?= $modo === 'crear' ? 'Crear Servicio' : 'Guardar Cambios' ?>
                </button>
                <a href="<?= url('admin/servicios.php') ?>" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
    <?php endif; ?>

</main>

<!-- Form de eliminación oculto -->
<form method="POST" id="formEliminar" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
    <input type="hidden" name="accion" value="eliminar">
    <input type="hidden" name="servicio_id" id="eliminar_servicio_id">
</form>

<script>
// Confirmar eliminación
function confirmarEliminar(id, titulo) {
    if (confirm('¿Estás seguro de eliminar el servicio "' + titulo + '"?\n\nEsta acción se puede revertir activándolo de nuevo.')) {
        document.getElementById('eliminar_servicio_id').value = id;
        document.getElementById('formEliminar').submit();
    }
}

// Actualizar preview del icono
function actualizarPreviewIcono() {
    const iconoInput = document.getElementById('icono');
    const preview = document.getElementById('preview-icono');
    const valor = iconoInput.value.trim();

    if (valor) {
        preview.className = valor + ' fa-lg';
    } else {
        preview.className = 'fas fa-question-circle fa-lg';
    }
}

// Seleccionar icono de los sugeridos
function seleccionarIcono(clase) {
    document.getElementById('icono').value = clase;
    actualizarPreviewIcono();
}

// Actualizar orden sugerido al cambiar área
function actualizarOrden() {
    const areaSelect = document.getElementById('area_id');
    const ordenInput = document.getElementById('orden');
    const selectedOption = areaSelect.options[areaSelect.selectedIndex];

    if (selectedOption && selectedOption.dataset.siguienteOrden) {
        <?php if ($modo === 'crear'): ?>
        ordenInput.value = selectedOption.dataset.siguienteOrden;
        <?php endif; ?>
    }
}

// Validación del formulario
document.getElementById('formServicio')?.addEventListener('submit', function(e) {
    const titulo = document.getElementById('titulo').value.trim();
    const area_id = document.getElementById('area_id').value;

    if (!titulo) {
        e.preventDefault();
        alert('El título es requerido');
        return false;
    }

    if (!area_id) {
        e.preventDefault();
        alert('Debe seleccionar un área');
        return false;
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
