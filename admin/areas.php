<?php
/**
 * Gestión de Áreas Temáticas
 * Coordicanarias CMS
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/core/auth.php';
require_once __DIR__ . '/../php/core/security.php';
require_once __DIR__ . '/../php/models/Area.php';

// Requerir autenticación
requireLogin();

// Establecer headers de seguridad
setSecurityHeaders();

// Obtener usuario actual
$usuario = getCurrentUser();

// Si es coordinador, redirigir automáticamente a editar SU área
if ($usuario['rol'] === 'coordinador' && !isset($_GET['editar']) && !isset($_POST['accion'])) {
    header('Location: ' . url('admin/areas.php?editar=' . $usuario['area_id']));
    exit;
}

// Variables
$mensaje = '';
$tipo_mensaje = '';
$modo_edicion = false;
$area_editar = null;

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $mensaje = 'Token de seguridad inválido';
        $tipo_mensaje = 'danger';
    } else {
        $accion = $_POST['accion'] ?? '';

        // ACCIÓN: Toggle Activo/Inactivo
        if ($accion === 'toggle_activo' && isset($_POST['area_id'])) {
            $area_id = (int)$_POST['area_id'];
            $nuevo_estado = (int)$_POST['nuevo_estado'];

            if (Area::toggleActivo($area_id, $nuevo_estado)) {
                $area = Area::getById($area_id);
                $estado_texto = $nuevo_estado ? 'activada' : 'desactivada';

                // Registrar actividad
                registrarActividad(
                    $usuario['id'],
                    $nuevo_estado ? 'activar' : 'desactivar',
                    'areas',
                    $area_id,
                    "Área '{$area['nombre']}' {$estado_texto}"
                );

                $mensaje = "Área {$estado_texto} exitosamente";
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al cambiar el estado del área';
                $tipo_mensaje = 'danger';
            }
        }

        // ACCIÓN: Actualizar Área
        elseif ($accion === 'actualizar' && isset($_POST['area_id'])) {
            $area_id = (int)$_POST['area_id'];

            // Verificar permisos
            if (!puedeGestionarArea($area_id)) {
                $mensaje = 'No tienes permisos para editar esta área';
                $tipo_mensaje = 'danger';
            } else {
                // Preparar datos
                $datos = [
                'nombre' => sanitizarTexto($_POST['nombre'] ?? ''),
                'slug' => sanitizarTexto($_POST['slug'] ?? ''),
                'descripcion' => sanitizarTexto($_POST['descripcion'] ?? ''),
                'imagen_banner' => $_POST['imagen_banner_actual'] ?? '',
                'color_tema' => sanitizarTexto($_POST['color_tema'] ?? '#243659'),
                'orden' => (int)($_POST['orden'] ?? 0),
                'activo' => isset($_POST['activo']) ? 1 : 0
            ];

            // Procesar subida de imagen
            if (isset($_FILES['imagen_banner']) && $_FILES['imagen_banner']['error'] === UPLOAD_ERR_OK) {
                $validacion_imagen = validarImagen($_FILES['imagen_banner']);

                if ($validacion_imagen['success']) {
                    // Crear directorio si no existe
                    $upload_dir = __DIR__ . '/../uploads/areas/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    // Generar nombre único
                    $extension = $validacion_imagen['extension'];
                    $nombre_archivo = 'area_' . $area_id . '_' . time() . '.' . $extension;
                    $ruta_destino = $upload_dir . $nombre_archivo;

                    // Mover archivo
                    if (move_uploaded_file($_FILES['imagen_banner']['tmp_name'], $ruta_destino)) {
                        // Eliminar imagen anterior si existe y no es la default
                        if (!empty($datos['imagen_banner']) && file_exists(__DIR__ . '/../' . $datos['imagen_banner'])) {
                            @unlink(__DIR__ . '/../' . $datos['imagen_banner']);
                        }

                        $datos['imagen_banner'] = 'uploads/areas/' . $nombre_archivo;
                    } else {
                        $mensaje = 'Error al subir la imagen';
                        $tipo_mensaje = 'warning';
                    }
                } else {
                    $mensaje = 'Error en la imagen: ' . $validacion_imagen['error'];
                    $tipo_mensaje = 'warning';
                }
            }

                // Validar datos
                $errores = Area::validar($datos, $area_id);

                if (empty($errores)) {
                    // Actualizar área
                    if (Area::update($area_id, $datos)) {
                        // Registrar actividad
                        registrarActividad(
                            $usuario['id'],
                            'actualizar',
                            'areas',
                            $area_id,
                            "Área '{$datos['nombre']}' actualizada"
                        );

                        $mensaje = 'Área actualizada exitosamente';
                        $tipo_mensaje = 'success';
                    } else {
                        $mensaje = 'Error al actualizar el área';
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

// Modo edición
if (isset($_GET['editar'])) {
    $area_id_editar = (int)$_GET['editar'];

    // Verificar permisos
    if (!puedeGestionarArea($area_id_editar)) {
        $mensaje = 'No tienes permisos para editar esta área';
        $tipo_mensaje = 'danger';
        $modo_edicion = false;
    } else {
        $modo_edicion = true;
        $area_editar = Area::getById($area_id_editar);

        if (!$area_editar) {
            $mensaje = 'Área no encontrada';
            $tipo_mensaje = 'danger';
            $modo_edicion = false;
        }
    }
}

// Obtener todas las áreas
$areas = Area::getAll();

// Variables para el header
$page_title = $modo_edicion ? 'Editar Área' : 'Áreas Temáticas';

// Incluir header y sidebar
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<!-- Contenido Principal -->
<main class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-th-large me-2"></i><?= $modo_edicion ? 'Editar Área' : 'Áreas Temáticas' ?></h1>
        <div class="page-breadcrumb">
            <a href="<?= url('admin/index.php') ?>"><i class="fas fa-home me-1"></i>Inicio</a>
            <span class="mx-2">/</span>
            <span>Áreas</span>
        </div>
    </div>

    <!-- Mensajes -->
    <?php if ($mensaje): ?>
    <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
        <?= $mensaje ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <?php endif; ?>

    <?php if ($modo_edicion): ?>
        <!-- FORMULARIO DE EDICIÓN -->
        <div class="row">
            <div class="col-lg-8">
                <div class="admin-table-wrapper">
                    <div class="table-header">
                        <h3 class="table-title">
                            <i class="fas fa-edit me-2"></i><?= $usuario['rol'] === 'coordinador' ? 'Mi Área' : 'Editar Área' ?>: <?= e($area_editar['nombre']) ?>
                        </h3>
                        <?php if ($usuario['rol'] !== 'coordinador'): ?>
                        <a href="<?= url('admin/areas.php') ?>" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Volver al listado
                        </a>
                        <?php endif; ?>
                    </div>

                    <form method="POST" enctype="multipart/form-data" class="p-4">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        <input type="hidden" name="accion" value="actualizar">
                        <input type="hidden" name="area_id" value="<?= $area_editar['id'] ?>">
                        <input type="hidden" name="imagen_banner_actual" value="<?= e($area_editar['imagen_banner']) ?>">

                        <!-- Nombre -->
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Área <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                   value="<?= e($area_editar['nombre']) ?>" required maxlength="100">
                            <small class="form-text text-muted">Nombre visible en el sitio web</small>
                        </div>

                        <!-- Slug -->
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="slug" name="slug"
                                   value="<?= e($area_editar['slug']) ?>" required maxlength="100"
                                   pattern="[a-z0-9-]+" title="Solo minúsculas, números y guiones">
                            <small class="form-text text-muted">URL amigable (ej: empleo, forminno). Solo minúsculas, números y guiones.</small>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4"><?= e($area_editar['descripcion']) ?></textarea>
                            <small class="form-text text-muted">Breve descripción del área (se muestra en el frontend)</small>
                        </div>

                        <!-- Imagen Banner -->
                        <div class="mb-3">
                            <label for="imagen_banner" class="form-label">Imagen Banner</label>

                            <?php if (!empty($area_editar['imagen_banner'])): ?>
                            <div class="mb-2">
                                <img src="<?= url($area_editar['imagen_banner']) ?>" alt="Banner actual"
                                     class="img-thumbnail" style="max-width: 300px; max-height: 200px;">
                                <div class="form-text">Imagen actual</div>
                            </div>
                            <?php endif; ?>

                            <input type="file" class="form-control" id="imagen_banner" name="imagen_banner"
                                   accept=".jpg,.jpeg,.png,.gif,.webp">
                            <small class="form-text text-muted">
                                Formatos: JPG, PNG, GIF, WEBP. Tamaño máximo: 5MB. Dejar vacío para mantener la imagen actual.
                            </small>
                        </div>

                        <!-- Color Tema -->
                        <div class="mb-3">
                            <label for="color_tema" class="form-label">Color Tema</label>
                            <input type="color" class="form-control form-control-color" id="color_tema" name="color_tema"
                                   value="<?= e($area_editar['color_tema']) ?>" title="Elige el color del tema">
                            <small class="form-text text-muted">Color para identificar el área en el frontend</small>
                        </div>

                        <div class="row">
                            <!-- Orden -->
                            <div class="col-md-6 mb-3">
                                <label for="orden" class="form-label">Orden <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="orden" name="orden"
                                       value="<?= $area_editar['orden'] ?>" required min="0" max="999">
                                <small class="form-text text-muted">Orden de visualización (menor = primero)</small>
                            </div>

                            <!-- Activo -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block">Estado</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="activo" name="activo"
                                           <?= $area_editar['activo'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="activo">
                                        Área activa (visible en el sitio)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                            <a href="<?= url('admin/areas.php') ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Panel lateral con información -->
            <div class="col-lg-4">
                <div class="admin-table-wrapper">
                    <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Información</h5>
                    <div class="small text-muted">
                        <p><strong>ID:</strong> <?= $area_editar['id'] ?></p>
                        <p><strong>Creada:</strong> <?= date('d/m/Y H:i', strtotime($area_editar['fecha_creacion'])) ?></p>
                        <p><strong>Última modificación:</strong> <?= date('d/m/Y H:i', strtotime($area_editar['fecha_modificacion'])) ?></p>
                    </div>

                    <hr>

                    <h6 class="mb-2"><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Importante</h6>
                    <ul class="small text-muted mb-0">
                        <li>Las áreas son fijas, no se pueden crear ni eliminar</li>
                        <li>El slug debe coincidir con el archivo PHP de la página del área</li>
                        <li>Desactivar un área la ocultará del sitio público</li>
                    </ul>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- LISTADO DE ÁREAS -->
        <div class="admin-table-wrapper">
            <div class="table-header">
                <h3 class="table-title">
                    <i class="fas fa-list me-2"></i>Listado de Áreas (<?= count($areas) ?>)
                </h3>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Áreas temáticas de la organización.</strong>
                Las áreas son fijas y no se pueden crear ni eliminar, solo editar su información.
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 50px;">Orden</th>
                            <th>Área</th>
                            <th>Slug</th>
                            <th style="width: 120px;">Estado</th>
                            <th style="width: 150px;" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($areas as $area): ?>
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-secondary"><?= $area['orden'] ?></span>
                            </td>
                            <td>
                                <strong><?= e($area['nombre']) ?></strong>
                                <?php if (!empty($area['descripcion'])): ?>
                                <br><small class="text-muted"><?= e(substr($area['descripcion'], 0, 80)) ?><?= strlen($area['descripcion']) > 80 ? '...' : '' ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <code><?= e($area['slug']) ?></code>
                            </td>
                            <td>
                                <form method="POST" class="d-inline" id="form-toggle-<?= $area['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                    <input type="hidden" name="accion" value="toggle_activo">
                                    <input type="hidden" name="area_id" value="<?= $area['id'] ?>">
                                    <input type="hidden" name="nuevo_estado" value="<?= $area['activo'] ? 0 : 1 ?>">

                                    <button type="submit" class="btn btn-sm <?= $area['activo'] ? 'btn-success' : 'btn-secondary' ?>"
                                            onclick="return confirm('¿Cambiar estado del área?')">
                                        <i class="fas fa-<?= $area['activo'] ? 'check-circle' : 'times-circle' ?> me-1"></i>
                                        <?= $area['activo'] ? 'Activa' : 'Inactiva' ?>
                                    </button>
                                </form>
                            </td>
                            <td class="text-end">
                                <a href="<?= url('admin/areas.php?editar=' . $area['id']) ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

</main>

<script>
// Auto-generar slug desde el nombre (solo si el campo slug está vacío)
document.getElementById('nombre')?.addEventListener('input', function() {
    const slugInput = document.getElementById('slug');
    if (slugInput && slugInput.value === '') {
        let slug = this.value.toLowerCase();
        // Reemplazar caracteres especiales
        slug = slug.replace(/[áàäâ]/g, 'a')
                   .replace(/[éèëê]/g, 'e')
                   .replace(/[íìïî]/g, 'i')
                   .replace(/[óòöô]/g, 'o')
                   .replace(/[úùüû]/g, 'u')
                   .replace(/ñ/g, 'n')
                   .replace(/[^a-z0-9]+/g, '-')
                   .replace(/^-+|-+$/g, '');
        slugInput.value = slug;
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
