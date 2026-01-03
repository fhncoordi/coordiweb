<?php
/**
 * Proyectos - Gestión de Proyectos Destacados
 * Coordicanarias CMS
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/core/auth.php';
require_once __DIR__ . '/../php/core/security.php';
require_once __DIR__ . '/../php/models/Proyecto.php';

// Requerir autenticación
requireLogin();

// Establecer headers de seguridad
setSecurityHeaders();

// Obtener usuario actual
$usuario = getCurrentUser();

// Variables para mensajes
$mensaje = '';
$tipo_mensaje = '';

// Determinar modo de vista
$modo = 'listado';
if (isset($_GET['crear'])) {
    $modo = 'crear';
} elseif (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
    $modo = 'editar';
    $proyecto_id = (int)$_GET['editar'];
    $proyecto = Proyecto::getById($proyecto_id);

    if (!$proyecto) {
        header('Location: ' . url('admin/proyectos.php?error=not_found'));
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
        if ($accion === 'toggle_activo' && isset($_POST['proyecto_id'])) {
            $proyecto_id = (int)$_POST['proyecto_id'];
            $nuevo_estado = (int)$_POST['nuevo_estado'];

            if (Proyecto::toggleActivo($proyecto_id, $nuevo_estado)) {
                registrarActividad('update', 'proyectos', $proyecto_id, 'Cambió estado a ' . ($nuevo_estado ? 'activo' : 'inactivo'));
                $mensaje = 'Estado actualizado correctamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al actualizar el estado';
                $tipo_mensaje = 'danger';
            }
        }

        // ACCIÓN: Toggle Destacado
        elseif ($accion === 'toggle_destacado' && isset($_POST['proyecto_id'])) {
            $proyecto_id = (int)$_POST['proyecto_id'];
            $nuevo_estado = (int)$_POST['nuevo_estado'];

            if (Proyecto::toggleDestacado($proyecto_id, $nuevo_estado)) {
                registrarActividad('update', 'proyectos', $proyecto_id, 'Cambió destacado a ' . ($nuevo_estado ? 'sí' : 'no'));
                $mensaje = 'Proyecto ' . ($nuevo_estado ? 'marcado como destacado' : 'desmarcado como destacado');
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al actualizar el estado destacado';
                $tipo_mensaje = 'danger';
            }
        }

        // ACCIÓN: Eliminar
        elseif ($accion === 'eliminar' && isset($_POST['proyecto_id'])) {
            $proyecto_id = (int)$_POST['proyecto_id'];

            if (Proyecto::delete($proyecto_id)) {
                registrarActividad('delete', 'proyectos', $proyecto_id, 'Eliminó proyecto (soft delete)');
                $mensaje = 'Proyecto eliminado correctamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al eliminar el proyecto';
                $tipo_mensaje = 'danger';
            }
        }

        // ACCIÓN: Crear
        elseif ($accion === 'crear') {
            // Preparar datos
            $datos = [
                'titulo' => trim($_POST['titulo'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'imagen' => '', // Se actualizará después de subir
                'area_id' => (int)($_POST['area_id'] ?? 0),
                'categorias' => trim($_POST['categorias'] ?? ''),
                'destacado' => isset($_POST['destacado']) ? 1 : 0,
                'orden' => (int)($_POST['orden'] ?? Proyecto::getSiguienteOrden()),
                'activo' => isset($_POST['activo']) ? 1 : 0
            ];

            // Validar datos
            $errores = Proyecto::validar($datos);

            if (empty($errores)) {
                // Subir imagen
                $resultado_imagen = Proyecto::subirImagen($_FILES['imagen'] ?? null);

                if ($resultado_imagen['success'] || empty($_FILES['imagen']['name'])) {
                    $datos['imagen'] = $resultado_imagen['filename'] ?? '';

                    // Crear proyecto
                    $nuevo_id = Proyecto::create($datos);

                    if ($nuevo_id) {
                        registrarActividad('create', 'proyectos', $nuevo_id, 'Creó proyecto: ' . $datos['titulo']);
                        header('Location: ' . url('admin/proyectos.php?success=created'));
                        exit;
                    } else {
                        $mensaje = 'Error al crear el proyecto';
                        $tipo_mensaje = 'danger';
                    }
                } else {
                    $mensaje = 'Error al subir la imagen: ' . $resultado_imagen['message'];
                    $tipo_mensaje = 'danger';
                }
            } else {
                $mensaje = implode('<br>', $errores);
                $tipo_mensaje = 'danger';
            }
        }

        // ACCIÓN: Editar
        elseif ($accion === 'editar' && isset($_POST['proyecto_id'])) {
            $proyecto_id = (int)$_POST['proyecto_id'];
            $proyecto_actual = Proyecto::getById($proyecto_id);

            if (!$proyecto_actual) {
                $mensaje = 'Proyecto no encontrado';
                $tipo_mensaje = 'danger';
            } else {
                // Preparar datos
                $datos = [
                    'titulo' => trim($_POST['titulo'] ?? ''),
                    'descripcion' => trim($_POST['descripcion'] ?? ''),
                    'imagen' => $proyecto_actual['imagen'], // Mantener imagen actual por defecto
                    'area_id' => (int)($_POST['area_id'] ?? 0),
                    'categorias' => trim($_POST['categorias'] ?? ''),
                    'destacado' => isset($_POST['destacado']) ? 1 : 0,
                    'orden' => (int)($_POST['orden'] ?? $proyecto_actual['orden']),
                    'activo' => isset($_POST['activo']) ? 1 : 0
                ];

                // Validar datos
                $errores = Proyecto::validar($datos, $proyecto_id);

                if (empty($errores)) {
                    // Subir nueva imagen si se seleccionó
                    if (!empty($_FILES['imagen']['name'])) {
                        $resultado_imagen = Proyecto::subirImagen($_FILES['imagen'], $proyecto_actual['imagen']);

                        if ($resultado_imagen['success']) {
                            $datos['imagen'] = $resultado_imagen['filename'];
                        } else {
                            $mensaje = 'Error al subir la imagen: ' . $resultado_imagen['message'];
                            $tipo_mensaje = 'danger';
                        }
                    }

                    // Actualizar proyecto solo si no hubo error de imagen
                    if (empty($mensaje)) {
                        if (Proyecto::update($proyecto_id, $datos)) {
                            registrarActividad('update', 'proyectos', $proyecto_id, 'Actualizó proyecto: ' . $datos['titulo']);
                            header('Location: ' . url('admin/proyectos.php?success=updated'));
                            exit;
                        } else {
                            $mensaje = 'Error al actualizar el proyecto';
                            $tipo_mensaje = 'danger';
                        }
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
        $mensaje = 'Proyecto creado correctamente';
        $tipo_mensaje = 'success';
    } elseif ($_GET['success'] === 'updated') {
        $mensaje = 'Proyecto actualizado correctamente';
        $tipo_mensaje = 'success';
    }
}

if (isset($_GET['error'])) {
    if ($_GET['error'] === 'not_found') {
        $mensaje = 'Proyecto no encontrado';
        $tipo_mensaje = 'danger';
    }
}

// Obtener datos para vista de listado
if ($modo === 'listado') {
    $proyectos = Proyecto::getAll(false); // Mostrar todos (activos e inactivos)
}

// Obtener áreas para el selector
$areas = Proyecto::getAreas();

// Obtener categorías existentes para datalist
$categorias_existentes = Proyecto::getCategorias();

// Variables para el header
$page_title = $modo === 'crear' ? 'Crear Proyecto' : ($modo === 'editar' ? 'Editar Proyecto' : 'Proyectos');

// Incluir header y sidebar
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<!-- Contenido Principal -->
<main class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <i class="fas fa-folder-open me-2"></i>
            <?= $modo === 'crear' ? 'Crear Proyecto' : ($modo === 'editar' ? 'Editar Proyecto' : 'Proyectos') ?>
        </h1>
        <div class="page-breadcrumb">
            <i class="fas fa-home me-1"></i>
            <a href="<?= url('admin/index.php') ?>">Inicio</a>
            <i class="fas fa-chevron-right mx-2"></i>
            <?php if ($modo === 'listado'): ?>
                Proyectos
            <?php else: ?>
                <a href="<?= url('admin/proyectos.php') ?>">Proyectos</a>
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
                <i class="fas fa-list me-2"></i>Listado de Proyectos
                <span class="badge bg-primary ms-2"><?= count($proyectos) ?></span>
            </h3>
            <a href="<?= url('admin/proyectos.php?crear=1') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Crear Proyecto
            </a>
        </div>

        <?php if (count($proyectos) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th style="width: 80px;">Imagen</th>
                        <th>Título</th>
                        <th>Área</th>
                        <th style="width: 100px;">Orden</th>
                        <th style="width: 100px;">Destacado</th>
                        <th style="width: 100px;">Estado</th>
                        <th style="width: 200px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proyectos as $proy): ?>
                    <tr>
                        <td><strong>#<?= e($proy['id']) ?></strong></td>
                        <td>
                            <?php if ($proy['imagen']): ?>
                            <img src="<?= url($proy['imagen']) ?>" alt="<?= attr($proy['titulo']) ?>"
                                 class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                            <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center"
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-image text-muted"></i>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= e($proy['titulo']) ?></strong>
                            <?php if ($proy['categorias']): ?>
                            <br><small class="text-muted"><?= e($proy['categorias']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                <?= e($proy['area_nombre'] ?? 'Sin área') ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info"><?= e($proy['orden']) ?></span>
                        </td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                <input type="hidden" name="accion" value="toggle_destacado">
                                <input type="hidden" name="proyecto_id" value="<?= $proy['id'] ?>">
                                <input type="hidden" name="nuevo_estado" value="<?= $proy['destacado'] ? 0 : 1 ?>">
                                <button type="submit" class="btn btn-sm <?= $proy['destacado'] ? 'btn-warning' : 'btn-outline-secondary' ?>"
                                        title="<?= $proy['destacado'] ? 'Destacado' : 'No destacado' ?>">
                                    <i class="fas fa-star"></i>
                                </button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                <input type="hidden" name="accion" value="toggle_activo">
                                <input type="hidden" name="proyecto_id" value="<?= $proy['id'] ?>">
                                <input type="hidden" name="nuevo_estado" value="<?= $proy['activo'] ? 0 : 1 ?>">
                                <button type="submit" class="btn btn-sm <?= $proy['activo'] ? 'btn-success' : 'btn-secondary' ?>">
                                    <i class="fas fa-<?= $proy['activo'] ? 'check-circle' : 'times-circle' ?>"></i>
                                    <?= $proy['activo'] ? 'Activo' : 'Inactivo' ?>
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="<?= url('admin/proyectos.php?editar=' . $proy['id']) ?>"
                                   class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        title="Eliminar" onclick="confirmarEliminar(<?= $proy['id'] ?>, '<?= addslashes($proy['titulo']) ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center text-muted py-5">
            <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
            <p>No hay proyectos creados aún</p>
            <a href="<?= url('admin/proyectos.php?crear=1') ?>" class="btn btn-primary mt-2">
                <i class="fas fa-plus me-2"></i>Crear primer proyecto
            </a>
        </div>
        <?php endif; ?>
    </div>

    <?php elseif ($modo === 'crear' || $modo === 'editar'): ?>
    <!-- MODO: CREAR / EDITAR -->
    <div class="admin-table-wrapper">
        <form method="POST" enctype="multipart/form-data" id="formProyecto">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="accion" value="<?= $modo ?>">
            <?php if ($modo === 'editar'): ?>
            <input type="hidden" name="proyecto_id" value="<?= $proyecto['id'] ?>">
            <?php endif; ?>

            <div class="row g-4">
                <!-- Columna Izquierda: Datos Principales -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Información del Proyecto
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Título -->
                            <div class="mb-3">
                                <label for="titulo" class="form-label">
                                    Título <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="titulo" name="titulo"
                                       value="<?= $modo === 'editar' ? e($proyecto['titulo']) : '' ?>"
                                       required maxlength="200">
                                <div class="form-text">Máximo 200 caracteres</div>
                            </div>

                            <!-- Descripción -->
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="6"><?= $modo === 'editar' ? e($proyecto['descripcion']) : '' ?></textarea>
                                <div class="form-text">Descripción detallada del proyecto</div>
                            </div>

                            <!-- Área -->
                            <div class="mb-3">
                                <label for="area_id" class="form-label">
                                    Área Temática <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="area_id" name="area_id" required>
                                    <option value="">Selecciona un área...</option>
                                    <?php foreach ($areas as $area): ?>
                                    <option value="<?= $area['id'] ?>"
                                            <?= ($modo === 'editar' && $proyecto['area_id'] == $area['id']) ? 'selected' : '' ?>>
                                        <?= e($area['nombre']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Categorías -->
                            <div class="mb-3">
                                <label for="categorias" class="form-label">Categorías</label>
                                <input type="text" class="form-control" id="categorias" name="categorias"
                                       value="<?= $modo === 'editar' ? e($proyecto['categorias']) : '' ?>"
                                       list="categorias-list" placeholder="empleo,formacion,integral">
                                <datalist id="categorias-list">
                                    <?php foreach ($categorias_existentes as $cat): ?>
                                    <option value="<?= e($cat) ?>">
                                    <?php endforeach; ?>
                                </datalist>
                                <div class="form-text">
                                    Separadas por comas. Se usan para filtros en el portfolio.
                                    <br>Ejemplos: <code>empleo,integral</code>, <code>ocio,participacion</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Imagen y Opciones -->
                <div class="col-lg-4">
                    <!-- Imagen -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-image me-2"></i>Imagen del Proyecto
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if ($modo === 'editar' && $proyecto['imagen']): ?>
                            <div class="mb-3">
                                <label class="form-label">Imagen actual:</label>
                                <img src="<?= url($proyecto['imagen']) ?>"
                                     alt="<?= attr($proyecto['titulo']) ?>"
                                     class="img-fluid rounded mb-2"
                                     id="preview-actual">
                            </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="imagen" class="form-label">
                                    <?= $modo === 'editar' ? 'Cambiar imagen' : 'Subir imagen' ?>
                                </label>
                                <input type="file" class="form-control" id="imagen" name="imagen"
                                       accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                                       onchange="previewImagen(this)">
                                <div class="form-text">
                                    JPG, PNG, GIF, WEBP. Máximo 5MB
                                </div>
                            </div>

                            <div id="preview-container" style="display: none;">
                                <label class="form-label">Vista previa:</label>
                                <img id="preview-nueva" src="" alt="Vista previa" class="img-fluid rounded">
                            </div>
                        </div>
                    </div>

                    <!-- Opciones -->
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
                                       value="<?= $modo === 'editar' ? e($proyecto['orden']) : Proyecto::getSiguienteOrden() ?>"
                                       min="1">
                                <div class="form-text">Número de orden para el listado</div>
                            </div>

                            <!-- Destacado -->
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="destacado" name="destacado"
                                       <?= ($modo === 'editar' && $proyecto['destacado']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="destacado">
                                    <i class="fas fa-star text-warning me-1"></i>
                                    Proyecto Destacado
                                </label>
                                <div class="form-text">Se mostrará en la página del área</div>
                            </div>

                            <!-- Activo -->
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="activo" name="activo"
                                       <?= ($modo === 'editar' ? ($proyecto['activo'] ? 'checked' : '') : 'checked') ?>>
                                <label class="form-check-label" for="activo">
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    Proyecto Activo
                                </label>
                                <div class="form-text">Visible en el sitio público</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>
                    <?= $modo === 'crear' ? 'Crear Proyecto' : 'Guardar Cambios' ?>
                </button>
                <a href="<?= url('admin/proyectos.php') ?>" class="btn btn-secondary">
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
    <input type="hidden" name="proyecto_id" id="eliminar_proyecto_id">
</form>

<script>
// Confirmar eliminación
function confirmarEliminar(id, titulo) {
    if (confirm('¿Estás seguro de eliminar el proyecto "' + titulo + '"?\n\nEsta acción se puede revertir activándolo de nuevo.')) {
        document.getElementById('eliminar_proyecto_id').value = id;
        document.getElementById('formEliminar').submit();
    }
}

// Preview de imagen
function previewImagen(input) {
    const previewContainer = document.getElementById('preview-container');
    const previewNueva = document.getElementById('preview-nueva');
    const previewActual = document.getElementById('preview-actual');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            previewNueva.src = e.target.result;
            previewContainer.style.display = 'block';

            // Ocultar imagen actual si existe
            if (previewActual) {
                previewActual.style.opacity = '0.3';
            }
        };

        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.style.display = 'none';
        if (previewActual) {
            previewActual.style.opacity = '1';
        }
    }
}

// Validación del formulario
document.getElementById('formProyecto')?.addEventListener('submit', function(e) {
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
