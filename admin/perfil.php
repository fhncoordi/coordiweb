<?php
/**
 * Mi Perfil - Gestión de datos personales y contraseña
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

// Variables
$mensaje = '';
$tipo_mensaje = '';

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $mensaje = 'Token de seguridad inválido';
        $tipo_mensaje = 'danger';
    } else {
        $accion = $_POST['accion'] ?? '';

        // ACCIÓN: Actualizar datos personales
        if ($accion === 'actualizar_datos') {
            $nombre_completo = sanitizarTexto($_POST['nombre_completo'] ?? '');
            $email = sanitizarTexto($_POST['email'] ?? '');

            // Validar datos
            $errores = [];

            if (empty($nombre_completo)) {
                $errores[] = 'El nombre completo es requerido';
            } elseif (strlen($nombre_completo) > 100) {
                $errores[] = 'El nombre completo no puede tener más de 100 caracteres';
            }

            if (empty($email)) {
                $errores[] = 'El email es requerido';
            } elseif (!validarEmail($email)) {
                $errores[] = 'El email no es válido';
            } else {
                // Verificar que el email no esté en uso por otro usuario
                $usuario_existente = fetchOne("SELECT id FROM usuarios WHERE email = ? AND id != ?", [$email, $usuario['id']]);
                if ($usuario_existente) {
                    $errores[] = 'Este email ya está en uso por otro usuario';
                }
            }

            if (empty($errores)) {
                // Actualizar datos
                $sql = "UPDATE usuarios SET nombre_completo = ?, email = ? WHERE id = ?";
                if (execute($sql, [$nombre_completo, $email, $usuario['id']])) {
                    // Actualizar sesión
                    $_SESSION['nombre_completo'] = $nombre_completo;

                    // Registrar actividad
                    registrarActividad($usuario['id'], 'actualizar', 'usuarios', $usuario['id'], 'Actualizó sus datos personales');

                    $mensaje = 'Datos personales actualizados correctamente';
                    $tipo_mensaje = 'success';

                    // Recargar datos del usuario
                    $usuario = getCurrentUser();
                } else {
                    $mensaje = 'Error al actualizar los datos';
                    $tipo_mensaje = 'danger';
                }
            } else {
                $mensaje = implode('<br>', $errores);
                $tipo_mensaje = 'danger';
            }
        }

        // ACCIÓN: Cambiar contraseña
        elseif ($accion === 'cambiar_password') {
            $password_actual = $_POST['password_actual'] ?? '';
            $password_nueva = $_POST['password_nueva'] ?? '';
            $password_confirmar = $_POST['password_confirmar'] ?? '';

            // Validar datos
            $errores = [];

            if (empty($password_actual)) {
                $errores[] = 'Debes ingresar tu contraseña actual';
            } else {
                // Verificar contraseña actual
                if (!password_verify($password_actual, $usuario['password_hash'])) {
                    $errores[] = 'La contraseña actual es incorrecta';
                }
            }

            if (empty($password_nueva)) {
                $errores[] = 'Debes ingresar una nueva contraseña';
            } elseif (strlen($password_nueva) < 8) {
                $errores[] = 'La nueva contraseña debe tener al menos 8 caracteres';
            }

            if ($password_nueva !== $password_confirmar) {
                $errores[] = 'Las contraseñas nuevas no coinciden';
            }

            if (empty($errores)) {
                // Hash de la nueva contraseña
                $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);

                // Actualizar contraseña
                $sql = "UPDATE usuarios SET password_hash = ? WHERE id = ?";
                if (execute($sql, [$password_hash, $usuario['id']])) {
                    // Registrar actividad
                    registrarActividad($usuario['id'], 'actualizar', 'usuarios', $usuario['id'], 'Cambió su contraseña');

                    $mensaje = 'Contraseña cambiada correctamente';
                    $tipo_mensaje = 'success';

                    // Recargar datos del usuario
                    $usuario = getCurrentUser();
                } else {
                    $mensaje = 'Error al cambiar la contraseña';
                    $tipo_mensaje = 'danger';
                }
            } else {
                $mensaje = implode('<br>', $errores);
                $tipo_mensaje = 'danger';
            }
        }
    }
}

// Obtener información del área si es coordinador
$area_info = null;
if ($usuario['rol'] === 'coordinador' && $usuario['area_id']) {
    $area_info = fetchOne("SELECT nombre FROM areas WHERE id = ?", [$usuario['area_id']]);
}

// Variables para el header
$page_title = 'Mi Perfil';

// Incluir header y sidebar
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<!-- Contenido Principal -->
<main class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-user-circle me-2"></i>Mi Perfil</h1>
        <div class="page-breadcrumb">
            <a href="<?= url('admin/index.php') ?>"><i class="fas fa-home me-1"></i>Inicio</a>
            <span class="mx-2">/</span>
            <span>Mi Perfil</span>
        </div>
    </div>

    <!-- Mensajes -->
    <?php if ($mensaje): ?>
    <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
        <?= $mensaje ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Información del Usuario -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-id-card me-2"></i>Información del Usuario
                    </h5>
                </div>
                <div class="card-body text-center">
                    <!-- Avatar -->
                    <div class="user-avatar-large mb-3">
                        <?= strtoupper(substr($usuario['nombre_completo'], 0, 2)) ?>
                    </div>

                    <h4><?= e($usuario['nombre_completo']) ?></h4>
                    <p class="text-muted">@<?= e($usuario['username']) ?></p>

                    <!-- Rol -->
                    <div class="mb-3">
                        <span class="badge bg-<?= $usuario['rol'] === 'admin' ? 'danger' : ($usuario['rol'] === 'editor' ? 'primary' : 'success') ?> fs-6">
                            <?= ucfirst(e($usuario['rol'])) ?>
                        </span>
                    </div>

                    <!-- Área (solo coordinadores) -->
                    <?php if ($area_info): ?>
                    <div class="mb-3">
                        <small class="text-muted d-block">Área asignada:</small>
                        <strong><?= e($area_info['nombre']) ?></strong>
                    </div>
                    <?php endif; ?>

                    <!-- Información adicional -->
                    <hr>
                    <div class="text-start">
                        <p class="mb-2">
                            <i class="fas fa-envelope me-2 text-muted"></i>
                            <small><?= e($usuario['email']) ?></small>
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-calendar me-2 text-muted"></i>
                            <small>Miembro desde: <?= date('d/m/Y', strtotime($usuario['fecha_creacion'])) ?></small>
                        </p>
                        <?php if ($usuario['ultimo_acceso']): ?>
                        <p class="mb-0">
                            <i class="fas fa-clock me-2 text-muted"></i>
                            <small>Último acceso: <?= date('d/m/Y H:i', strtotime($usuario['ultimo_acceso'])) ?></small>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formularios de Edición -->
        <div class="col-lg-8">
            <!-- Datos Personales -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>Datos Personales
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        <input type="hidden" name="accion" value="actualizar_datos">

                        <!-- Usuario (solo lectura) -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="username" value="<?= e($usuario['username']) ?>" disabled>
                            <small class="form-text text-muted">El nombre de usuario no se puede cambiar</small>
                        </div>

                        <!-- Nombre Completo -->
                        <div class="mb-3">
                            <label for="nombre_completo" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre_completo" name="nombre_completo"
                                   value="<?= e($usuario['nombre_completo']) ?>" required maxlength="100">
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= e($usuario['email']) ?>" required maxlength="100">
                        </div>

                        <!-- Rol (solo lectura) -->
                        <div class="mb-3">
                            <label for="rol" class="form-label">Rol</label>
                            <input type="text" class="form-control" id="rol" value="<?= ucfirst(e($usuario['rol'])) ?>" disabled>
                            <small class="form-text text-muted">El rol es asignado por un administrador</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
                    </form>
                </div>
            </div>

            <!-- Cambiar Contraseña -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-key me-2"></i>Cambiar Contraseña
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" id="formPassword">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                        <input type="hidden" name="accion" value="cambiar_password">

                        <!-- Contraseña Actual -->
                        <div class="mb-3">
                            <label for="password_actual" class="form-label">Contraseña Actual <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_actual" name="password_actual" required>
                        </div>

                        <!-- Nueva Contraseña -->
                        <div class="mb-3">
                            <label for="password_nueva" class="form-label">Nueva Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_nueva" name="password_nueva" required minlength="8">
                            <small class="form-text text-muted">Mínimo 8 caracteres</small>
                        </div>

                        <!-- Confirmar Contraseña -->
                        <div class="mb-3">
                            <label for="password_confirmar" class="form-label">Confirmar Nueva Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmar" name="password_confirmar" required minlength="8">
                        </div>

                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-lock me-2"></i>Cambiar Contraseña
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Validar que las contraseñas coincidan
document.getElementById('formPassword')?.addEventListener('submit', function(e) {
    const nueva = document.getElementById('password_nueva').value;
    const confirmar = document.getElementById('password_confirmar').value;

    if (nueva !== confirmar) {
        e.preventDefault();
        alert('Las contraseñas no coinciden');
        return false;
    }

    if (nueva.length < 8) {
        e.preventDefault();
        alert('La contraseña debe tener al menos 8 caracteres');
        return false;
    }
});
</script>

<style>
.user-avatar-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: bold;
    margin: 0 auto;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
