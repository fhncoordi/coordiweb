<?php
/**
 * Gestión de Usuarios
 * Coordicanarias CMS
 *
 * Solo accesible para administradores
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/db/connection.php';
require_once __DIR__ . '/../php/core/auth.php';
require_once __DIR__ . '/../php/core/security.php';
require_once __DIR__ . '/../php/models/Usuario.php';

// Requerir autenticación y rol admin
requireLogin();
if (!puedeGestionarUsuarios()) {
    http_response_code(403);
    die('Acceso denegado. Solo administradores pueden gestionar usuarios.');
}

$titulo_pagina = 'Gestión de Usuarios';
$mensaje = '';
$error = '';

// ============================================
// PROCESAR ACCIONES
// ============================================

// Crear o editar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    // Verificar CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Token de seguridad inválido. Por favor, intenta de nuevo.';
    } else {
        $accion = $_POST['accion'];

        if ($accion === 'crear' || $accion === 'editar') {
            // Recoger datos del formulario
            $datos = [
                'username' => sanitizarTexto($_POST['username'] ?? ''),
                'email' => sanitizarTexto($_POST['email'] ?? ''),
                'nombre_completo' => sanitizarTexto($_POST['nombre_completo'] ?? ''),
                'rol' => sanitizarTexto($_POST['rol'] ?? ''),
                'area_id' => !empty($_POST['area_id']) ? intval($_POST['area_id']) : null,
                'activo' => isset($_POST['activo']) ? 1 : 0
            ];

            // Agregar contraseña si se proporciona
            if (!empty($_POST['password'])) {
                $datos['password'] = $_POST['password'];
            }

            // Validar datos
            $id_usuario = ($accion === 'editar') ? intval($_POST['id']) : null;
            $errores = Usuario::validar($datos, $id_usuario);

            if (empty($errores)) {
                if ($accion === 'crear') {
                    // Crear usuario
                    $nuevo_id = Usuario::create($datos);
                    if ($nuevo_id) {
                        registrarActividad(
                            getCurrentUserId(),
                            'crear',
                            'usuarios',
                            $nuevo_id,
                            "Usuario creado: {$datos['username']}"
                        );
                        $mensaje = 'Usuario creado exitosamente.';
                    } else {
                        $error = 'Error al crear el usuario. Por favor, intenta de nuevo.';
                    }
                } else {
                    // Editar usuario
                    if (Usuario::update($id_usuario, $datos)) {
                        registrarActividad(
                            getCurrentUserId(),
                            'actualizar',
                            'usuarios',
                            $id_usuario,
                            "Usuario actualizado: {$datos['username']}"
                        );
                        $mensaje = 'Usuario actualizado exitosamente.';
                    } else {
                        $error = 'Error al actualizar el usuario. Por favor, intenta de nuevo.';
                    }
                }
            } else {
                $error = implode('<br>', $errores);
            }
        } elseif ($accion === 'eliminar') {
            // Eliminar usuario (soft delete)
            $id = intval($_POST['id']);

            // No permitir eliminar al usuario actual
            if ($id === getCurrentUserId()) {
                $error = 'No puedes eliminar tu propia cuenta.';
            } else {
                $usuario = Usuario::getById($id);
                if (Usuario::delete($id)) {
                    registrarActividad(
                        getCurrentUserId(),
                        'eliminar',
                        'usuarios',
                        $id,
                        "Usuario desactivado: {$usuario['username']}"
                    );
                    $mensaje = 'Usuario desactivado exitosamente.';
                } else {
                    $error = 'Error al desactivar el usuario.';
                }
            }
        } elseif ($accion === 'toggle_activo') {
            // Cambiar estado activo/inactivo
            $id = intval($_POST['id']);
            $activo = intval($_POST['activo']);

            // No permitir desactivar al usuario actual
            if ($id === getCurrentUserId() && $activo === 0) {
                $error = 'No puedes desactivar tu propia cuenta.';
            } else {
                if (Usuario::toggleActivo($id, $activo)) {
                    $estado = $activo ? 'activado' : 'desactivado';
                    registrarActividad(
                        getCurrentUserId(),
                        'actualizar',
                        'usuarios',
                        $id,
                        "Usuario {$estado}"
                    );
                    $mensaje = "Usuario {$estado} exitosamente.";
                } else {
                    $error = 'Error al cambiar el estado del usuario.';
                }
            }
        }
    }
}

// Obtener todos los usuarios
$usuarios = Usuario::getAll();

// Obtener áreas para el selector
$areas = Usuario::getAreas();

// Si se está editando, obtener datos del usuario
$usuario_editando = null;
if (isset($_GET['editar'])) {
    $usuario_editando = Usuario::getById(intval($_GET['editar']));
}

include __DIR__ . '/includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><?= e($titulo_pagina) ?></h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario" onclick="resetForm()">
                        <i class="fas fa-plus"></i> Nuevo Usuario
                    </button>
                </div>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= e($mensaje) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>

            <!-- Tabla de usuarios -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Nombre Completo</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Área Asignada</th>
                            <th>Último Acceso</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?= e($usuario['id']) ?></td>
                                <td><strong><?= e($usuario['username']) ?></strong></td>
                                <td><?= e($usuario['nombre_completo']) ?></td>
                                <td><?= e($usuario['email']) ?></td>
                                <td><?= Usuario::getBadgeRol($usuario['rol']) ?></td>
                                <td><?= $usuario['area_nombre'] ? e($usuario['area_nombre']) : '<em class="text-muted">N/A</em>' ?></td>
                                <td>
                                    <?php if ($usuario['ultimo_acceso']): ?>
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($usuario['ultimo_acceso'])) ?>
                                        </small>
                                    <?php else: ?>
                                        <em class="text-muted">Nunca</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($usuario['activo']): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary" onclick="editarUsuario(<?= attr(json_encode($usuario)) ?>)" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <?php if ($usuario['id'] !== getCurrentUserId()): ?>
                                            <button type="button" class="btn btn-outline-<?= $usuario['activo'] ? 'warning' : 'success' ?>" onclick="toggleActivo(<?= $usuario['id'] ?>, <?= $usuario['activo'] ? 0 : 1 ?>)" title="<?= $usuario['activo'] ? 'Desactivar' : 'Activar' ?>">
                                                <i class="fas fa-<?= $usuario['activo'] ? 'ban' : 'check' ?>"></i>
                                            </button>

                                            <button type="button" class="btn btn-outline-danger" onclick="eliminarUsuario(<?= $usuario['id'] ?>, '<?= attr($usuario['username']) ?>')" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-outline-secondary" disabled title="No puedes modificar tu propia cuenta">
                                                <i class="fas fa-user-shield"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (empty($usuarios)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay usuarios registrados.
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<!-- Modal para crear/editar usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="formUsuario">
                <?= csrfField() ?>
                <input type="hidden" name="accion" id="accion" value="crear">
                <input type="hidden" name="id" id="usuario_id" value="">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalUsuarioLabel">Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Nombre de Usuario <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" required maxlength="100" pattern="[a-zA-Z0-9_]+" title="Solo letras, números y guiones bajos">
                            <div class="form-text">Solo letras, números y guiones bajos (mín. 3 caracteres)</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required maxlength="200">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nombre_completo" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" required maxlength="200">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Contraseña <span id="password_required" class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" minlength="8">
                            <div class="form-text">Mínimo 8 caracteres. Dejar en blanco para mantener la actual (solo al editar)</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="rol" class="form-label">Rol <span class="text-danger">*</span></label>
                            <select class="form-select" id="rol" name="rol" required onchange="toggleAreaField()">
                                <option value="">Seleccionar rol...</option>
                                <option value="admin">Administrador</option>
                                <option value="editor">Editor</option>
                                <option value="coordinador">Coordinador de Área</option>
                            </select>
                            <div class="form-text">
                                <strong>Admin:</strong> Acceso total<br>
                                <strong>Editor:</strong> Puede editar todo el contenido<br>
                                <strong>Coordinador:</strong> Solo su área asignada
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="area_field" style="display: none;">
                        <label for="area_id" class="form-label">Área Asignada <span class="text-danger">*</span></label>
                        <select class="form-select" id="area_id" name="area_id">
                            <option value="">Seleccionar área...</option>
                            <?php foreach ($areas as $area): ?>
                                <option value="<?= e($area['id']) ?>"><?= e($area['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Requerido solo para coordinadores</div>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" checked>
                        <label class="form-check-label" for="activo">
                            Usuario activo
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Formulario oculto para acciones (eliminar, toggle) -->
<form method="POST" id="formAccion" style="display: none;">
    <?= csrfField() ?>
    <input type="hidden" name="accion" id="accion_tipo" value="">
    <input type="hidden" name="id" id="accion_id" value="">
    <input type="hidden" name="activo" id="accion_activo" value="">
</form>

<script>
// Resetear formulario para crear nuevo usuario
function resetForm() {
    document.getElementById('formUsuario').reset();
    document.getElementById('accion').value = 'crear';
    document.getElementById('usuario_id').value = '';
    document.getElementById('modalUsuarioLabel').textContent = 'Nuevo Usuario';
    document.getElementById('password').required = true;
    document.getElementById('password_required').style.display = 'inline';
    toggleAreaField();
}

// Editar usuario existente
function editarUsuario(usuario) {
    document.getElementById('accion').value = 'editar';
    document.getElementById('usuario_id').value = usuario.id;
    document.getElementById('username').value = usuario.username;
    document.getElementById('email').value = usuario.email;
    document.getElementById('nombre_completo').value = usuario.nombre_completo;
    document.getElementById('password').value = '';
    document.getElementById('password').required = false;
    document.getElementById('password_required').style.display = 'none';
    document.getElementById('rol').value = usuario.rol;
    document.getElementById('area_id').value = usuario.area_id || '';
    document.getElementById('activo').checked = usuario.activo == 1;
    document.getElementById('modalUsuarioLabel').textContent = 'Editar Usuario';

    toggleAreaField();

    // Abrir modal
    new bootstrap.Modal(document.getElementById('modalUsuario')).show();
}

// Mostrar/ocultar campo de área según el rol
function toggleAreaField() {
    const rol = document.getElementById('rol').value;
    const areaField = document.getElementById('area_field');
    const areaSelect = document.getElementById('area_id');

    if (rol === 'coordinador') {
        areaField.style.display = 'block';
        areaSelect.required = true;
    } else {
        areaField.style.display = 'none';
        areaSelect.required = false;
        areaSelect.value = '';
    }
}

// Toggle activo/inactivo
function toggleActivo(id, activo) {
    const estado = activo ? 'activar' : 'desactivar';
    if (confirm(`¿Estás seguro de que deseas ${estado} este usuario?`)) {
        document.getElementById('accion_tipo').value = 'toggle_activo';
        document.getElementById('accion_id').value = id;
        document.getElementById('accion_activo').value = activo;
        document.getElementById('formAccion').submit();
    }
}

// Eliminar usuario
function eliminarUsuario(id, username) {
    if (confirm(`¿Estás seguro de que deseas desactivar al usuario "${username}"?\n\nEsta acción se puede revertir activándolo nuevamente.`)) {
        document.getElementById('accion_tipo').value = 'eliminar';
        document.getElementById('accion_id').value = id;
        document.getElementById('formAccion').submit();
    }
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    toggleAreaField();

    <?php if ($usuario_editando): ?>
        editarUsuario(<?= json_encode($usuario_editando) ?>);
    <?php endif; ?>
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
