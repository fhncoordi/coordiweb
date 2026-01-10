<?php
/**
 * Gestión de Noticias
 * Coordicanarias CMS
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/core/auth.php';
require_once __DIR__ . '/../php/core/security.php';
require_once __DIR__ . '/../php/models/Noticia.php';

// Requerir autenticación
requireLogin();

// Establecer headers de seguridad
setSecurityHeaders();

// Obtener usuario actual
$usuario = getCurrentUser();

// Verificar permisos por rol
$es_coordinador = ($usuario['rol'] === 'coordinador');
$area_permitida = $es_coordinador ? $usuario['area_id'] : null;

// Variables
$mensaje = '';
$tipo_mensaje = '';
$modo = 'listado'; // listado, crear, editar
$noticia_editar = null;
$datos_formulario = []; // Para preservar datos cuando hay errores

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $mensaje = 'Token de seguridad inválido';
        $tipo_mensaje = 'danger';
    } else {
        $accion = $_POST['accion'] ?? '';

        // ACCIÓN: Toggle Activo/Inactivo
        if ($accion === 'toggle_activo' && isset($_POST['noticia_id'])) {
            $noticia_id = (int)$_POST['noticia_id'];
            $nuevo_estado = (int)$_POST['nuevo_estado'];

            if (Noticia::toggleActivo($noticia_id, $nuevo_estado)) {
                $noticia = Noticia::getById($noticia_id);
                $estado_texto = $nuevo_estado ? 'activada' : 'desactivada';

                registrarActividad(
                    $usuario['id'],
                    $nuevo_estado ? 'activar' : 'desactivar',
                    'noticias',
                    $noticia_id,
                    "Noticia '{$noticia['titulo']}' {$estado_texto}"
                );

                $mensaje = "Noticia {$estado_texto} correctamente";
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al cambiar el estado';
                $tipo_mensaje = 'danger';
            }
        }

        // ACCIÓN: Toggle Destacada
        elseif ($accion === 'toggle_destacada' && isset($_POST['noticia_id'])) {
            $noticia_id = (int)$_POST['noticia_id'];
            $nuevo_estado = (int)$_POST['nuevo_estado'];

            if (Noticia::toggleDestacada($noticia_id, $nuevo_estado)) {
                $noticia = Noticia::getById($noticia_id);
                $estado_texto = $nuevo_estado ? 'marcada como destacada' : 'desmarcada como destacada';

                registrarActividad(
                    $usuario['id'],
                    $nuevo_estado ? 'destacar' : 'desdestacar',
                    'noticias',
                    $noticia_id,
                    "Noticia '{$noticia['titulo']}' {$estado_texto}"
                );

                $mensaje = "Noticia {$estado_texto}";
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al cambiar el estado';
                $tipo_mensaje = 'danger';
            }
        }

        // ACCIÓN: Eliminar (soft delete)
        elseif ($accion === 'eliminar' && isset($_POST['noticia_id'])) {
            $noticia_id = (int)$_POST['noticia_id'];
            $noticia = Noticia::getById($noticia_id);

            if (Noticia::delete($noticia_id)) {
                registrarActividad(
                    $usuario['id'],
                    'eliminar',
                    'noticias',
                    $noticia_id,
                    "Noticia '{$noticia['titulo']}' eliminada"
                );

                $mensaje = 'Noticia eliminada correctamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al eliminar la noticia';
                $tipo_mensaje = 'danger';
            }
        }

        // ACCIÓN: Crear noticia
        elseif ($accion === 'crear') {
            // Preparar datos
            $datos = [
                'area_id' => $es_coordinador ? $area_permitida : (int)($_POST['area_id'] ?? 0),
                'titulo' => sanitizarTexto($_POST['titulo'] ?? ''),
                'slug' => sanitizarTexto($_POST['slug'] ?? ''),
                'resumen' => sanitizarTexto($_POST['resumen'] ?? ''),
                'contenido' => sanitizarTexto($_POST['contenido'] ?? ''),
                'imagen_destacada' => '',
                'fecha_publicacion' => $_POST['fecha_publicacion'] ?? date('Y-m-d'),
                'autor' => sanitizarTexto($_POST['autor'] ?? $usuario['nombre_completo']),
                'categoria' => sanitizarTexto($_POST['categoria'] ?? ''),
                'destacada' => isset($_POST['destacada']) ? 1 : 0,
                'activo' => isset($_POST['activo']) ? 1 : 0
            ];

            // SIEMPRE generar un slug único desde el título (ignora el slug del formulario)
            // Esto evita problemas de duplicados y valida correctamente
            if (!empty($datos['titulo'])) {
                $datos['slug'] = Noticia::generarSlugUnico($datos['titulo']);
            }

            // Validar datos (sin validar slug único ya que lo acabamos de generar)
            $errores = Noticia::validar($datos, null, true);

            if (empty($errores)) {
                // Crear noticia
                $noticia_id = Noticia::create($datos);

                if ($noticia_id) {
                    // Procesar imagen después de crear
                    if (isset($_FILES['imagen_destacada']) && $_FILES['imagen_destacada']['error'] === UPLOAD_ERR_OK) {
                        $validacion_imagen = validarImagen($_FILES['imagen_destacada']);

                        if ($validacion_imagen['success']) {
                            $upload_dir = __DIR__ . '/../uploads/noticias/';
                            if (!file_exists($upload_dir)) {
                                mkdir($upload_dir, 0755, true);
                            }

                            $extension = $validacion_imagen['extension'];
                            $nombre_archivo = 'noticia_' . $noticia_id . '_' . time() . '.' . $extension;
                            $ruta_destino = $upload_dir . $nombre_archivo;

                            if (move_uploaded_file($_FILES['imagen_destacada']['tmp_name'], $ruta_destino)) {
                                $datos['imagen_destacada'] = 'uploads/noticias/' . $nombre_archivo;
                                Noticia::update($noticia_id, $datos);
                            }
                        }
                    }

                    registrarActividad(
                        $usuario['id'],
                        'crear',
                        'noticias',
                        $noticia_id,
                        "Noticia '{$datos['titulo']}' creada"
                    );

                    $mensaje = 'Noticia creada correctamente';
                    $tipo_mensaje = 'success';
                    $modo = 'listado';
                } else {
                    $mensaje = 'Error al crear la noticia';
                    $tipo_mensaje = 'danger';
                }
            } else {
                $mensaje = implode('<br>', $errores);
                $tipo_mensaje = 'danger';
                $modo = 'crear';
                $datos_formulario = $datos; // Preservar datos para repoblar formulario
            }
        }

        // ACCIÓN: Actualizar noticia
        elseif ($accion === 'actualizar' && isset($_POST['noticia_id'])) {
            $noticia_id = (int)$_POST['noticia_id'];

            // Verificar permisos: coordinador solo puede editar noticias de su área
            $noticia_actual = Noticia::getById($noticia_id);
            if ($es_coordinador && $noticia_actual['area_id'] != $area_permitida) {
                $mensaje = 'No tienes permisos para editar esta noticia';
                $tipo_mensaje = 'danger';
            } else {
                // Preparar datos
                $datos = [
                    'area_id' => $es_coordinador ? $area_permitida : (int)($_POST['area_id'] ?? 0),
                    'titulo' => sanitizarTexto($_POST['titulo'] ?? ''),
                    'slug' => sanitizarTexto($_POST['slug'] ?? ''),
                    'resumen' => sanitizarTexto($_POST['resumen'] ?? ''),
                    'contenido' => sanitizarTexto($_POST['contenido'] ?? ''),
                    'imagen_destacada' => $_POST['imagen_destacada_actual'] ?? '',
                    'fecha_publicacion' => $_POST['fecha_publicacion'] ?? date('Y-m-d'),
                    'autor' => sanitizarTexto($_POST['autor'] ?? ''),
                    'categoria' => sanitizarTexto($_POST['categoria'] ?? ''),
                    'destacada' => isset($_POST['destacada']) ? 1 : 0,
                    'activo' => isset($_POST['activo']) ? 1 : 0
                ];

            // Verificar si se solicita eliminar la imagen actual
            if (isset($_POST['eliminar_imagen']) && $_POST['eliminar_imagen'] == '1') {
                // Eliminar archivo físico
                if (!empty($datos['imagen_destacada']) && file_exists(__DIR__ . '/../' . $datos['imagen_destacada'])) {
                    @unlink(__DIR__ . '/../' . $datos['imagen_destacada']);
                }
                // Limpiar campo en la base de datos
                $datos['imagen_destacada'] = '';
            }

            // Procesar subida de imagen nueva
            if (isset($_FILES['imagen_destacada']) && $_FILES['imagen_destacada']['error'] === UPLOAD_ERR_OK) {
                $validacion_imagen = validarImagen($_FILES['imagen_destacada']);

                if ($validacion_imagen['success']) {
                    $upload_dir = __DIR__ . '/../uploads/noticias/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    $extension = $validacion_imagen['extension'];
                    $nombre_archivo = 'noticia_' . $noticia_id . '_' . time() . '.' . $extension;
                    $ruta_destino = $upload_dir . $nombre_archivo;

                    if (move_uploaded_file($_FILES['imagen_destacada']['tmp_name'], $ruta_destino)) {
                        // Eliminar imagen anterior si existe
                        if (!empty($datos['imagen_destacada']) && file_exists(__DIR__ . '/../' . $datos['imagen_destacada'])) {
                            @unlink(__DIR__ . '/../' . $datos['imagen_destacada']);
                        }

                        $datos['imagen_destacada'] = 'uploads/noticias/' . $nombre_archivo;
                    }
                }
            }

                // SIEMPRE generar un slug único desde el título (ignora el slug del formulario)
                // Esto evita problemas de duplicados y valida correctamente
                if (!empty($datos['titulo'])) {
                    $datos['slug'] = Noticia::generarSlugUnico($datos['titulo'], $noticia_id);
                }

                // Validar datos (sin validar slug único ya que lo acabamos de generar)
                $errores = Noticia::validar($datos, $noticia_id, true);

                if (empty($errores)) {
                    if (Noticia::update($noticia_id, $datos)) {
                        registrarActividad(
                            $usuario['id'],
                            'actualizar',
                            'noticias',
                            $noticia_id,
                            "Noticia '{$datos['titulo']}' actualizada"
                        );

                        $mensaje = 'Noticia actualizada correctamente';
                        $tipo_mensaje = 'success';
                        $modo = 'listado';
                    } else {
                        $mensaje = 'Error al actualizar la noticia';
                        $tipo_mensaje = 'danger';
                    }
                } else {
                    $mensaje = implode('<br>', $errores);
                    $tipo_mensaje = 'danger';
                    $modo = 'editar';
                    $datos_formulario = $datos; // Preservar datos para repoblar formulario
                    $datos_formulario['id'] = $noticia_id; // Mantener ID de la noticia
                    $noticia_editar = array_merge($noticia_actual, $datos_formulario); // Mezclar datos actuales con nuevos
                }
            }
        }
    }
}

// Determinar modo de visualización
if (isset($_GET['crear'])) {
    $modo = 'crear';
} elseif (isset($_GET['editar'])) {
    $modo = 'editar';
    $noticia_editar = Noticia::getById((int)$_GET['editar']);

    if (!$noticia_editar) {
        $mensaje = 'Noticia no encontrada';
        $tipo_mensaje = 'danger';
        $modo = 'listado';
    } elseif ($es_coordinador && $noticia_editar['area_id'] != $area_permitida) {
        $mensaje = 'No tienes permisos para editar esta noticia';
        $tipo_mensaje = 'danger';
        $modo = 'listado';
    }
}

// Obtener todas las noticias agrupadas por área (filtradas por área si es coordinador)
if ($es_coordinador) {
    $noticias_agrupadas = Noticia::getAllAgrupados(false, $area_permitida);
    $noticias_todas = Noticia::getAll(false, 0, $area_permitida);
} else {
    $noticias_agrupadas = Noticia::getAllAgrupados(false);
    $noticias_todas = Noticia::getAll();
}
$total_noticias = count($noticias_todas);

// Obtener áreas para el selector
$areas = Noticia::getAreas();

// Obtener nombre del área del coordinador si aplica
$nombre_area_coordinador = '';
if ($es_coordinador && $area_permitida) {
    $area_coord = fetchOne("SELECT nombre FROM areas WHERE id = ?", [$area_permitida]);
    $nombre_area_coordinador = $area_coord['nombre'] ?? '';
}

// Obtener categorías para el filtro
$categorias = Noticia::getCategorias();

// Variables para el header
$page_title = $modo === 'crear' ? 'Nueva Noticia' : ($modo === 'editar' ? 'Editar Noticia' : 'Noticias');

// Incluir header y sidebar
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<!-- Contenido Principal -->
<main class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <i class="fas fa-newspaper me-2"></i>
            <?= $modo === 'crear' ? 'Nueva Noticia' : ($modo === 'editar' ? 'Editar Noticia' : 'Noticias') ?>
        </h1>
        <div class="page-breadcrumb">
            <a href="<?= url('admin/index.php') ?>"><i class="fas fa-home me-1"></i>Inicio</a>
            <span class="mx-2">/</span>
            <span>Noticias</span>
        </div>
    </div>

    <!-- Mensajes -->
    <?php if ($mensaje): ?>
    <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
        <?= $mensaje ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <?php endif; ?>

    <?php if ($modo === 'listado'): ?>
        <!-- LISTADO DE NOTICIAS -->
        <div class="admin-table-wrapper">
            <div class="table-header">
                <h3 class="table-title">
                    <i class="fas fa-list me-2"></i>Listado de Noticias
                    <span class="badge bg-primary ms-2"><?= $total_noticias ?></span>
                </h3>
                <a href="<?= url('admin/noticias.php?crear=1') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Nueva Noticia
                </a>
            </div>

            <?php if ($total_noticias > 0): ?>
            <!-- Noticias agrupadas por área -->
            <?php foreach ($noticias_agrupadas as $area_nombre => $noticias): ?>
            <div class="mb-4">
                <h4 class="mb-3">
                    <i class="fas fa-th-large me-2 text-primary"></i>
                    <?= e($area_nombre) ?>
                    <span class="badge bg-secondary ms-2"><?= count($noticias) ?> noticia<?= count($noticias) !== 1 ? 's' : '' ?></span>
                </h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">Imagen</th>
                                <th>Noticia</th>
                                <th style="width: 120px;">Fecha</th>
                                <th style="width: 100px;">Categoría</th>
                                <th style="width: 100px;">Destacada</th>
                                <th style="width: 100px;">Estado</th>
                                <th style="width: 180px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($noticias as $noticia): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($noticia['imagen_destacada'])): ?>
                                    <img src="<?= url($noticia['imagen_destacada']) ?>" alt=""
                                         class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                    <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                         style="width: 60px; height: 60px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= e($noticia['titulo']) ?></strong>
                                    <?php if (!empty($noticia['resumen'])): ?>
                                    <br><small class="text-muted"><?= e(substr($noticia['resumen'], 0, 80)) ?><?= strlen($noticia['resumen']) > 80 ? '...' : '' ?></small>
                                    <?php endif; ?>
                                    <br><small class="text-muted">Por: <?= e($noticia['autor'] ?? 'Sin autor') ?></small>
                                </td>
                            <td>
                                <small><?= date('d/m/Y', strtotime($noticia['fecha_publicacion'])) ?></small>
                            </td>
                            <td>
                                <?php if (!empty($noticia['categoria'])): ?>
                                <span class="badge bg-info"><?= e($noticia['categoria']) ?></span>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                    <input type="hidden" name="accion" value="toggle_destacada">
                                    <input type="hidden" name="noticia_id" value="<?= $noticia['id'] ?>">
                                    <input type="hidden" name="nuevo_estado" value="<?= $noticia['destacada'] ? 0 : 1 ?>">
                                    <button type="submit" class="btn btn-sm <?= $noticia['destacada'] ? 'btn-warning' : 'btn-outline-secondary' ?>"
                                            title="<?= $noticia['destacada'] ? 'Destacada' : 'No destacada' ?>">
                                        <i class="fas fa-star"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                    <input type="hidden" name="accion" value="toggle_activo">
                                    <input type="hidden" name="noticia_id" value="<?= $noticia['id'] ?>">
                                    <input type="hidden" name="nuevo_estado" value="<?= $noticia['activo'] ? 0 : 1 ?>">
                                    <button type="submit" class="btn btn-sm <?= $noticia['activo'] ? 'btn-success' : 'btn-secondary' ?>">
                                        <i class="fas fa-<?= $noticia['activo'] ? 'check-circle' : 'times-circle' ?>"></i>
                                        <?= $noticia['activo'] ? 'Activo' : 'Inactivo' ?>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?= url('admin/noticias.php?editar=' . $noticia['id']) ?>"
                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            title="Eliminar" onclick="confirmarEliminar(<?= $noticia['id'] ?>, '<?= addslashes($noticia['titulo']) ?>')">
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
                <i class="fas fa-newspaper fa-4x mb-3 opacity-25"></i>
                <p>No hay noticias creadas aún</p>
                <a href="<?= url('admin/noticias.php?crear=1') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Crear primera noticia
                </a>
            </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <!-- FORMULARIO (CREAR / EDITAR) -->
        <div class="row">
            <div class="col-lg-8">
                <div class="admin-table-wrapper">
                    <div class="table-header">
                        <h3 class="table-title">
                            <i class="fas fa-<?= $modo === 'crear' ? 'plus' : 'edit' ?> me-2"></i>
                            <?= $modo === 'crear' ? 'Nueva Noticia' : 'Editar: ' . e($noticia_editar['titulo']) ?>
                        </h3>
                        <a href="<?= url('admin/noticias.php') ?>" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>

                    <form method="POST" enctype="multipart/form-data" class="p-4">
                        <?php
                        // Determinar qué datos usar en el formulario
                        // Si hay datos_formulario (por errores), usarlos; de lo contrario, usar noticia_editar o vacío
                        $form_data = !empty($datos_formulario) ? $datos_formulario : ($modo === 'editar' ? $noticia_editar : []);
                        ?>

                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        <input type="hidden" name="accion" value="<?= $modo === 'crear' ? 'crear' : 'actualizar' ?>">
                        <?php if ($modo === 'editar'): ?>
                        <input type="hidden" name="noticia_id" value="<?= $noticia_editar['id'] ?>">
                        <input type="hidden" name="imagen_destacada_actual" value="<?= e($noticia_editar['imagen_destacada'] ?? '') ?>">
                        <?php endif; ?>

                        <!-- Título -->
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="titulo" name="titulo"
                                   value="<?= e($form_data['titulo'] ?? '') ?>"
                                   required maxlength="200">
                        </div>

                        <!-- Slug -->
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="slug" name="slug"
                                   value="<?= e($form_data['slug'] ?? '') ?>"
                                   required maxlength="200" pattern="[a-z0-9-]+">
                            <small class="form-text text-muted">URL amigable. Se genera automáticamente desde el título.</small>
                        </div>

                        <!-- Resumen -->
                        <div class="mb-3">
                            <label for="resumen" class="form-label">Resumen</label>
                            <textarea class="form-control" id="resumen" name="resumen" rows="3"><?= e($form_data['resumen'] ?? '') ?></textarea>
                            <small class="form-text text-muted">Breve descripción para listados (opcional)</small>
                        </div>

                        <!-- Contenido -->
                        <div class="mb-3">
                            <label for="contenido" class="form-label">Contenido</label>
                            <textarea class="form-control" id="contenido" name="contenido" rows="10"><?= e($form_data['contenido'] ?? '') ?></textarea>
                        </div>

                        <!-- Imagen Destacada -->
                        <div class="mb-3">
                            <label for="imagen_destacada" class="form-label">Imagen Destacada</label>

                            <?php if ($modo === 'editar' && !empty($noticia_editar['imagen_destacada'])): ?>
                            <div class="mb-2">
                                <img src="<?= url($noticia_editar['imagen_destacada']) ?>" alt="Imagen actual"
                                     class="img-thumbnail" style="max-width: 300px; max-height: 200px;">
                                <div class="form-text">Imagen actual</div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="eliminar_imagen" name="eliminar_imagen" value="1">
                                    <label class="form-check-label text-danger" for="eliminar_imagen">
                                        <i class="fas fa-trash me-1"></i>Eliminar imagen actual
                                    </label>
                                </div>
                            </div>
                            <?php endif; ?>

                            <input type="file" class="form-control" id="imagen_destacada" name="imagen_destacada"
                                   accept=".jpg,.jpeg,.png,.gif,.webp">
                            <small class="form-text text-muted">JPG, PNG, GIF, WEBP. Máx 5MB.</small>
                        </div>

                        <div class="row">
                            <!-- Fecha de Publicación -->
                            <div class="col-md-6 mb-3">
                                <label for="fecha_publicacion" class="form-label">Fecha de Publicación <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="fecha_publicacion" name="fecha_publicacion"
                                       value="<?= e($form_data['fecha_publicacion'] ?? date('Y-m-d')) ?>" required>
                            </div>

                            <!-- Autor -->
                            <div class="col-md-6 mb-3">
                                <label for="autor" class="form-label">Autor</label>
                                <input type="text" class="form-control" id="autor" name="autor"
                                       value="<?= e($form_data['autor'] ?? $usuario['nombre_completo']) ?>"
                                       maxlength="100">
                            </div>
                        </div>

                        <!-- Área -->
                        <?php if (!$es_coordinador): ?>
                        <div class="mb-3">
                            <label for="area_id" class="form-label">Área</label>
                            <select class="form-select" id="area_id" name="area_id">
                                <option value="0">Sin área específica</option>
                                <?php
                                $selected_area_id = $form_data['area_id'] ?? 0;
                                foreach ($areas as $area):
                                ?>
                                <option value="<?= $area['id'] ?>"
                                        <?= ($selected_area_id == $area['id']) ? 'selected' : '' ?>>
                                    <?= e($area['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Área temática a la que pertenece esta noticia (opcional)</small>
                        </div>
                        <?php else: ?>
                        <input type="hidden" name="area_id" value="<?= $area_permitida ?>">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>Esta noticia se publicará en el área: <strong><?= e($nombre_area_coordinador) ?></strong>
                        </div>
                        <?php endif; ?>

                        <!-- Categoría -->
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoría</label>
                            <input type="text" class="form-control" id="categoria" name="categoria"
                                   value="<?= e($form_data['categoria'] ?? '') ?>"
                                   maxlength="100" list="categorias-list">
                            <datalist id="categorias-list">
                                <?php foreach ($categorias as $cat): ?>
                                <option value="<?= e($cat) ?>">
                                <?php endforeach; ?>
                            </datalist>
                            <small class="form-text text-muted">Ej: Eventos, Convenios, Comunicados</small>
                        </div>

                        <div class="row">
                            <!-- Destacada -->
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="destacada" name="destacada"
                                           <?= (!empty($form_data['destacada'])) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="destacada">
                                        <i class="fas fa-star text-warning"></i> Marcar como destacada
                                    </label>
                                </div>
                            </div>

                            <!-- Activo -->
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="activo" name="activo"
                                           <?= ($modo === 'crear' || !empty($form_data['activo'])) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="activo">
                                        Noticia activa (visible)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i><?= $modo === 'crear' ? 'Crear Noticia' : 'Guardar Cambios' ?>
                            </button>
                            <a href="<?= url('admin/noticias.php') ?>" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Panel lateral -->
            <div class="col-lg-4">
                <div class="admin-table-wrapper">
                    <h5 class="mb-3"><i class="fas fa-lightbulb me-2"></i>Consejos</h5>
                    <ul class="small text-muted mb-0">
                        <li>El slug se genera automáticamente desde el título</li>
                        <li>Las noticias destacadas aparecerán en la homepage</li>
                        <li>El resumen se usa en listados y previews</li>
                        <li>La fecha de publicación determina el orden</li>
                    </ul>
                </div>

                <?php if ($modo === 'editar'): ?>
                <div class="admin-table-wrapper mt-3">
                    <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Información</h6>
                    <div class="small text-muted">
                        <p><strong>ID:</strong> <?= $noticia_editar['id'] ?></p>
                        <p><strong>Creada:</strong> <?= date('d/m/Y H:i', strtotime($noticia_editar['fecha_creacion'])) ?></p>
                        <p><strong>Modificada:</strong> <?= date('d/m/Y H:i', strtotime($noticia_editar['fecha_modificacion'])) ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</main>

<script>
// Auto-generar slug desde el título
document.getElementById('titulo')?.addEventListener('input', function() {
    const slugInput = document.getElementById('slug');
    if (slugInput && (slugInput.value === '' || <?= $modo === 'crear' ? 'true' : 'false' ?>)) {
        let slug = this.value.toLowerCase();
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

// Confirmar eliminación de noticia
function confirmarEliminar(id, titulo) {
    if (confirm(`¿Estás seguro de que deseas eliminar la noticia "${titulo}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="accion" value="eliminar">
            <input type="hidden" name="noticia_id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
