<?php
/**
 * Testimonios - Gestión de Testimonios y Casos de Éxito
 * Coordicanarias CMS
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/core/auth.php';
require_once __DIR__ . '/../php/core/security.php';
require_once __DIR__ . '/../php/models/Testimonio.php';

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
    $testimonio_id = (int)$_GET['editar'];
    $testimonio = Testimonio::getById($testimonio_id);

    if (!$testimonio) {
        header('Location: ' . url('admin/testimonios.php?error=not_found'));
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
        if ($accion === 'toggle_activo' && isset($_POST['testimonio_id'])) {
            $testimonio_id = (int)$_POST['testimonio_id'];
            $nuevo_estado = (int)$_POST['nuevo_estado'];

            if (Testimonio::toggleActivo($testimonio_id, $nuevo_estado)) {
                registrarActividad('update', 'testimonios', $testimonio_id, 'Cambió estado a ' . ($nuevo_estado ? 'activo' : 'inactivo'));
                $mensaje = 'Estado actualizado correctamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al actualizar el estado';
                $tipo_mensaje = 'danger';
            }
        }

        // ACCIÓN: Toggle Destacado
        elseif ($accion === 'toggle_destacado' && isset($_POST['testimonio_id'])) {
            $testimonio_id = (int)$_POST['testimonio_id'];
            $nuevo_estado = (int)$_POST['nuevo_estado'];

            if (Testimonio::toggleDestacado($testimonio_id, $nuevo_estado)) {
                registrarActividad('update', 'testimonios', $testimonio_id, 'Cambió destacado a ' . ($nuevo_estado ? 'sí' : 'no'));
                $mensaje = 'Estado destacado actualizado correctamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al actualizar el estado destacado';
                $tipo_mensaje = 'danger';
            }
        }

        // ACCIÓN: Eliminar
        elseif ($accion === 'eliminar' && isset($_POST['testimonio_id'])) {
            $testimonio_id = (int)$_POST['testimonio_id'];

            if (Testimonio::delete($testimonio_id)) {
                registrarActividad('delete', 'testimonios', $testimonio_id, 'Eliminó testimonio (soft delete)');
                $mensaje = 'Testimonio eliminado correctamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al eliminar el testimonio';
                $tipo_mensaje = 'danger';
            }
        }

        // ACCIÓN: Crear
        elseif ($accion === 'crear') {
            // Manejar subida de foto
            $ruta_foto = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
                $resultado = Testimonio::subirFoto($_FILES['foto']);
                if ($resultado['success']) {
                    $ruta_foto = $resultado['ruta'];
                } else {
                    $mensaje = $resultado['mensaje'];
                    $tipo_mensaje = 'danger';
                }
            }

            // Solo continuar si no hubo error con la foto
            if (empty($mensaje)) {
                // Preparar datos
                $datos = [
                    'nombre' => trim($_POST['nombre'] ?? ''),
                    'profesion' => trim($_POST['profesion'] ?? ''),
                    'texto' => trim($_POST['texto'] ?? ''),
                    'foto' => $ruta_foto,
                    'rating' => (int)($_POST['rating'] ?? 5),
                    'orden' => (int)($_POST['orden'] ?? Testimonio::getSiguienteOrden()),
                    'destacado' => isset($_POST['destacado']) ? 1 : 0,
                    'activo' => isset($_POST['activo']) ? 1 : 0
                ];

                // Validar datos
                $errores = Testimonio::validar($datos);

                if (empty($errores)) {
                    $nuevo_id = Testimonio::create($datos);

                    if ($nuevo_id) {
                        registrarActividad('create', 'testimonios', $nuevo_id, 'Creó testimonio: ' . $datos['nombre']);
                        header('Location: ' . url('admin/testimonios.php?success=created'));
                        exit;
                    } else {
                        $mensaje = 'Error al crear el testimonio';
                        $tipo_mensaje = 'danger';
                    }
                } else {
                    $mensaje = implode('<br>', $errores);
                    $tipo_mensaje = 'danger';
                }
            }
        }

        // ACCIÓN: Actualizar
        elseif ($accion === 'actualizar' && isset($_POST['testimonio_id'])) {
            $testimonio_id = (int)$_POST['testimonio_id'];
            $testimonio_actual = Testimonio::getById($testimonio_id);

            if (!$testimonio_actual) {
                $mensaje = 'Testimonio no encontrado';
                $tipo_mensaje = 'danger';
            } else {
                // Manejar subida de nueva foto
                $ruta_foto = $testimonio_actual['foto'];
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $resultado = Testimonio::subirFoto($_FILES['foto'], $testimonio_id);
                    if ($resultado['success']) {
                        // Eliminar foto anterior si existe
                        if (!empty($testimonio_actual['foto'])) {
                            Testimonio::eliminarFoto($testimonio_actual['foto']);
                        }
                        $ruta_foto = $resultado['ruta'];
                    } else {
                        $mensaje = $resultado['mensaje'];
                        $tipo_mensaje = 'danger';
                    }
                }

                // Solo continuar si no hubo error con la foto
                if (empty($mensaje)) {
                    // Preparar datos
                    $datos = [
                        'nombre' => trim($_POST['nombre'] ?? ''),
                        'profesion' => trim($_POST['profesion'] ?? ''),
                        'texto' => trim($_POST['texto'] ?? ''),
                        'foto' => $ruta_foto,
                        'rating' => (int)($_POST['rating'] ?? 5),
                        'orden' => (int)($_POST['orden'] ?? 0),
                        'destacado' => isset($_POST['destacado']) ? 1 : 0,
                        'activo' => isset($_POST['activo']) ? 1 : 0
                    ];

                    // Validar datos
                    $errores = Testimonio::validar($datos, $testimonio_id);

                    if (empty($errores)) {
                        if (Testimonio::update($testimonio_id, $datos)) {
                            registrarActividad('update', 'testimonios', $testimonio_id, 'Actualizó testimonio: ' . $datos['nombre']);
                            header('Location: ' . url('admin/testimonios.php?success=updated'));
                            exit;
                        } else {
                            $mensaje = 'Error al actualizar el testimonio';
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
}

// Obtener datos para vista de listado
if ($modo === 'listado') {
    $testimonios = Testimonio::getAll(false);
    $total_testimonios = count($testimonios);
}

// Mostrar mensajes de URL
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'created':
            $mensaje = 'Testimonio creado correctamente';
            $tipo_mensaje = 'success';
            break;
        case 'updated':
            $mensaje = 'Testimonio actualizado correctamente';
            $tipo_mensaje = 'success';
            break;
    }
}

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'not_found':
            $mensaje = 'Testimonio no encontrado';
            $tipo_mensaje = 'danger';
            break;
    }
}

// Generar token CSRF
$csrf_token = generateCSRFToken();

// Incluir header
$page_title = 'Gestión de Testimonios';
include __DIR__ . '/includes/header.php';
?>

<!-- Contenido principal -->
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-comment-dots me-2"></i>
                Testimonios
            </h1>
            <p class="text-muted mb-0">Gestión de testimonios y casos de éxito</p>
        </div>
        <?php if ($modo === 'listado'): ?>
            <a href="<?= url('admin/testimonios.php?crear') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Nuevo Testimonio
            </a>
        <?php else: ?>
            <a href="<?= url('admin/testimonios.php') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver al Listado
            </a>
        <?php endif; ?>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-<?= e($tipo_mensaje) ?> alert-dismissible fade show" role="alert">
            <?= $mensaje ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($modo === 'listado'): ?>
        <!-- Vista de Listado -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2 text-primary"></i>
                        Lista de Testimonios
                    </h5>
                    <span class="badge bg-secondary"><?= $total_testimonios ?> testimonios</span>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($testimonios)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-comment-dots fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No hay testimonios registrados</p>
                        <a href="<?= url('admin/testimonios.php?crear') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Crear Primer Testimonio
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="80">Foto</th>
                                    <th>Nombre</th>
                                    <th>Profesión</th>
                                    <th width="100">Rating</th>
                                    <th width="80">Orden</th>
                                    <th width="100">Destacado</th>
                                    <th width="100">Estado</th>
                                    <th width="150" class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($testimonios as $t): ?>
                                    <tr>
                                        <td>
                                            <?php if ($t['foto']): ?>
                                                <img src="<?= url($t['foto']) ?>" alt="<?= attr($t['nombre']) ?>" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle">
                                            <strong><?= e($t['nombre']) ?></strong>
                                        </td>
                                        <td class="align-middle"><?= e($t['profesion']) ?></td>
                                        <td class="align-middle">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?= $i <= $t['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                                            <?php endfor; ?>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge bg-light text-dark"><?= e($t['orden']) ?></span>
                                        </td>
                                        <td class="align-middle">
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                <input type="hidden" name="accion" value="toggle_destacado">
                                                <input type="hidden" name="testimonio_id" value="<?= $t['id'] ?>">
                                                <input type="hidden" name="nuevo_estado" value="<?= $t['destacado'] ? 0 : 1 ?>">
                                                <button type="submit" class="btn btn-sm <?= $t['destacado'] ? 'btn-warning' : 'btn-outline-secondary' ?>">
                                                    <i class="fas fa-star"></i>
                                                    <?= $t['destacado'] ? 'Destacado' : 'Normal' ?>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="align-middle">
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                <input type="hidden" name="accion" value="toggle_activo">
                                                <input type="hidden" name="testimonio_id" value="<?= $t['id'] ?>">
                                                <input type="hidden" name="nuevo_estado" value="<?= $t['activo'] ? 0 : 1 ?>">
                                                <button type="submit" class="btn btn-sm <?= $t['activo'] ? 'btn-success' : 'btn-secondary' ?>">
                                                    <?= $t['activo'] ? 'Activo' : 'Inactivo' ?>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="align-middle text-end">
                                            <div class="btn-group" role="group">
                                                <a href="<?= url('admin/testimonios.php?editar=' . $t['id']) ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmarEliminar(<?= $t['id'] ?>, '<?= addslashes($t['nombre']) ?>')" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    <?php elseif ($modo === 'crear' || $modo === 'editar'): ?>
        <!-- Vista de Formulario -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-<?= $modo === 'crear' ? 'plus' : 'edit' ?> me-2 text-primary"></i>
                            <?= $modo === 'crear' ? 'Nuevo Testimonio' : 'Editar Testimonio' ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <input type="hidden" name="accion" value="<?= $modo === 'crear' ? 'crear' : 'actualizar' ?>">
                            <?php if ($modo === 'editar'): ?>
                                <input type="hidden" name="testimonio_id" value="<?= $testimonio['id'] ?>">
                            <?php endif; ?>

                            <!-- Nombre -->
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre" name="nombre"
                                       value="<?= $modo === 'editar' ? attr($testimonio['nombre']) : '' ?>"
                                       required maxlength="200">
                            </div>

                            <!-- Profesión -->
                            <div class="mb-3">
                                <label for="profesion" class="form-label">Profesión / Cargo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="profesion" name="profesion"
                                       value="<?= $modo === 'editar' ? attr($testimonio['profesion']) : '' ?>"
                                       required maxlength="200">
                            </div>

                            <!-- Texto del Testimonio -->
                            <div class="mb-3">
                                <label for="texto" class="form-label">Testimonio <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="texto" name="texto" rows="5" required><?= $modo === 'editar' ? e($testimonio['texto']) : '' ?></textarea>
                                <div class="form-text">Mínimo 20 caracteres</div>
                            </div>

                            <!-- Foto -->
                            <div class="mb-3">
                                <label for="foto" class="form-label">Foto</label>
                                <?php if ($modo === 'editar' && $testimonio['foto']): ?>
                                    <div class="mb-2">
                                        <img src="<?= url($testimonio['foto']) ?>" alt="Foto actual" class="rounded" style="max-width: 150px;">
                                        <div class="form-text">Foto actual - Sube una nueva para reemplazarla</div>
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                <div class="form-text">Formatos: JPG, PNG, GIF, WEBP (máximo 5MB)</div>
                            </div>

                            <!-- Rating -->
                            <div class="mb-3">
                                <label class="form-label">Valoración <span class="text-danger">*</span></label>
                                <div class="rating-selector">
                                    <?php
                                    $rating_actual = $modo === 'editar' ? $testimonio['rating'] : 5;
                                    for ($i = 1; $i <= 5; $i++):
                                    ?>
                                        <label class="rating-star">
                                            <input type="radio" name="rating" value="<?= $i ?>" <?= $i === $rating_actual ? 'checked' : '' ?> required>
                                            <i class="fas fa-star"></i>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                                <div class="form-text">Selecciona entre 1 y 5 estrellas</div>
                            </div>

                            <!-- Orden -->
                            <div class="mb-3">
                                <label for="orden" class="form-label">Orden</label>
                                <input type="number" class="form-control" id="orden" name="orden"
                                       value="<?= $modo === 'editar' ? $testimonio['orden'] : Testimonio::getSiguienteOrden() ?>"
                                       min="0">
                                <div class="form-text">Número para ordenar los testimonios</div>
                            </div>

                            <!-- Destacado -->
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="destacado" name="destacado"
                                           <?= ($modo === 'editar' && $testimonio['destacado']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="destacado">
                                        <i class="fas fa-star text-warning me-1"></i>
                                        Destacar (se mostrará en index.html)
                                    </label>
                                </div>
                            </div>

                            <!-- Activo -->
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="activo" name="activo"
                                           <?= ($modo === 'crear' || ($modo === 'editar' && $testimonio['activo'])) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="activo">
                                        Activo
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="<?= url('admin/testimonios.php') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>
                                    <?= $modo === 'crear' ? 'Crear Testimonio' : 'Actualizar Testimonio' ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar el testimonio de <strong id="nombreTestimonio"></strong>?</p>
                <p class="text-muted mb-0">Esta acción se puede revertir activándolo nuevamente.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" id="formEliminar">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="testimonio_id" id="testimonioIdEliminar">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.rating-selector {
    display: flex;
    gap: 5px;
}

.rating-star {
    cursor: pointer;
    margin: 0;
}

.rating-star input[type="radio"] {
    display: none;
}

.rating-star i {
    font-size: 1.5rem;
    color: #ddd;
    transition: color 0.2s;
}

.rating-star input[type="radio"]:checked ~ i,
.rating-star:hover i {
    color: #ffc107;
}

.rating-star input[type="radio"]:checked + i {
    color: #ffc107;
}

/* Efecto hover en toda la fila */
.rating-selector:hover .rating-star i {
    color: #ffc107;
}

.rating-selector .rating-star:hover ~ .rating-star i {
    color: #ddd;
}
</style>

<script>
// Función para confirmar eliminación
function confirmarEliminar(id, nombre) {
    document.getElementById('testimonioIdEliminar').value = id;
    document.getElementById('nombreTestimonio').textContent = nombre;

    const modal = new bootstrap.Modal(document.getElementById('modalEliminar'));
    modal.show();
}

// Mejorar selector de rating
document.addEventListener('DOMContentLoaded', function() {
    const ratingStars = document.querySelectorAll('.rating-star');

    ratingStars.forEach((star, index) => {
        star.addEventListener('mouseover', function() {
            ratingStars.forEach((s, i) => {
                const icon = s.querySelector('i');
                if (i <= index) {
                    icon.style.color = '#ffc107';
                } else {
                    icon.style.color = '#ddd';
                }
            });
        });

        star.addEventListener('click', function() {
            const input = this.querySelector('input');
            input.checked = true;
        });
    });

    const ratingContainer = document.querySelector('.rating-selector');
    if (ratingContainer) {
        ratingContainer.addEventListener('mouseleave', function() {
            const checked = document.querySelector('.rating-star input:checked');
            const checkedValue = checked ? parseInt(checked.value) : 0;

            ratingStars.forEach((s, i) => {
                const icon = s.querySelector('i');
                if (i < checkedValue) {
                    icon.style.color = '#ffc107';
                } else {
                    icon.style.color = '#ddd';
                }
            });
        });
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
