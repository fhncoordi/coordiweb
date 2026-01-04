<?php
/**
 * Configuración - Gestión de configuración general del sitio
 * Coordicanarias CMS
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/core/auth.php';
require_once __DIR__ . '/../php/core/security.php';
require_once __DIR__ . '/../php/models/Configuracion.php';

// Requerir autenticación
requireLogin();

// Establecer headers de seguridad
setSecurityHeaders();

// Solo admin puede acceder
if (!puedeGestionarConfiguracion()) {
    header('Location: ' . url('admin/index.php'));
    exit;
}

// Obtener usuario actual
$usuario = getCurrentUser();

// Variables
$mensaje = '';
$tipo_mensaje = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $mensaje = 'Token de seguridad inválido';
        $tipo_mensaje = 'danger';
    } else {
        // Preparar configuraciones
        $configuraciones = [
            // General
            'nombre_sitio' => sanitizarTexto($_POST['nombre_sitio'] ?? ''),
            'descripcion_sitio' => sanitizarTexto($_POST['descripcion_sitio'] ?? ''),
            'slogan' => sanitizarTexto($_POST['slogan'] ?? ''),

            // Contacto
            'contacto_telefono' => sanitizarTexto($_POST['contacto_telefono'] ?? ''),
            'contacto_email' => sanitizarTexto($_POST['contacto_email'] ?? ''),
            'contacto_direccion' => sanitizarTexto($_POST['contacto_direccion'] ?? ''),
            'contacto_horario' => sanitizarTexto($_POST['contacto_horario'] ?? ''),

            // Redes sociales
            'redes_facebook' => sanitizarTexto($_POST['redes_facebook'] ?? ''),
            'redes_twitter' => sanitizarTexto($_POST['redes_twitter'] ?? ''),
            'redes_instagram' => sanitizarTexto($_POST['redes_instagram'] ?? ''),
            'redes_linkedin' => sanitizarTexto($_POST['redes_linkedin'] ?? ''),
            'redes_youtube' => sanitizarTexto($_POST['redes_youtube'] ?? ''),
        ];

        // Validar datos
        $errores = [];

        if (empty($configuraciones['nombre_sitio'])) {
            $errores[] = 'El nombre del sitio es requerido';
        }

        if (!empty($configuraciones['contacto_email']) && !Configuracion::validarEmail($configuraciones['contacto_email'])) {
            $errores[] = 'El email de contacto no es válido';
        }

        if (!empty($configuraciones['contacto_telefono']) && !Configuracion::validarTelefono($configuraciones['contacto_telefono'])) {
            $errores[] = 'El teléfono de contacto no es válido';
        }

        // Validar URLs de redes sociales
        $redes = ['redes_facebook', 'redes_twitter', 'redes_instagram', 'redes_linkedin', 'redes_youtube'];
        foreach ($redes as $red) {
            if (!empty($configuraciones[$red]) && !Configuracion::validarURL($configuraciones[$red])) {
                $errores[] = 'La URL de ' . str_replace('redes_', '', $red) . ' no es válida';
            }
        }

        if (empty($errores)) {
            // Guardar configuraciones
            if (Configuracion::updateMultiple($configuraciones)) {
                // Registrar actividad
                registrarActividad(
                    $usuario['id'],
                    'actualizar',
                    'configuracion',
                    0,
                    'Actualizó la configuración del sitio'
                );

                $mensaje = 'Configuración guardada correctamente';
                $tipo_mensaje = 'success';
            } else {
                $mensaje = 'Error al guardar la configuración';
                $tipo_mensaje = 'danger';
            }
        } else {
            $mensaje = implode('<br>', $errores);
            $tipo_mensaje = 'danger';
        }
    }
}

// Obtener configuración actual
$config = Configuracion::getAll();

// Variables para el header
$page_title = 'Configuración';

// Incluir header y sidebar
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<!-- Contenido Principal -->
<main class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-cog me-2"></i>Configuración del Sitio</h1>
        <div class="page-breadcrumb">
            <a href="<?= url('admin/index.php') ?>"><i class="fas fa-home me-1"></i>Inicio</a>
            <span class="mx-2">/</span>
            <span>Configuración</span>
        </div>
    </div>

    <!-- Mensajes -->
    <?php if ($mensaje): ?>
    <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show" role="alert">
        <?= $mensaje ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <?php endif; ?>

    <!-- Formulario de Configuración -->
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

        <div class="row g-4">
            <!-- Información General -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Información General
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Nombre del Sitio -->
                        <div class="mb-3">
                            <label for="nombre_sitio" class="form-label">
                                Nombre del Sitio <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nombre_sitio" name="nombre_sitio"
                                   value="<?= e($config['nombre_sitio'] ?? 'Coordicanarias') ?>" required maxlength="100">
                            <small class="form-text text-muted">Aparece en el header y título de la página</small>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="descripcion_sitio" class="form-label">Descripción</label>
                            <input type="text" class="form-control" id="descripcion_sitio" name="descripcion_sitio"
                                   value="<?= e($config['descripcion_sitio'] ?? '') ?>" maxlength="200">
                            <small class="form-text text-muted">Breve descripción de la organización</small>
                        </div>

                        <!-- Slogan -->
                        <div class="mb-3">
                            <label for="slogan" class="form-label">Slogan</label>
                            <input type="text" class="form-control" id="slogan" name="slogan"
                                   value="<?= e($config['slogan'] ?? '') ?>" maxlength="200">
                            <small class="form-text text-muted">Frase descriptiva del sitio</small>
                        </div>
                    </div>
                </div>

                <!-- Redes Sociales -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-share-alt me-2"></i>Redes Sociales
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Facebook -->
                        <div class="mb-3">
                            <label for="redes_facebook" class="form-label">
                                <i class="fab fa-facebook text-primary me-2"></i>Facebook
                            </label>
                            <input type="url" class="form-control" id="redes_facebook" name="redes_facebook"
                                   value="<?= e($config['redes_facebook'] ?? '') ?>"
                                   placeholder="https://facebook.com/coordicanarias">
                        </div>

                        <!-- Twitter -->
                        <div class="mb-3">
                            <label for="redes_twitter" class="form-label">
                                <i class="fab fa-twitter text-info me-2"></i>Twitter
                            </label>
                            <input type="url" class="form-control" id="redes_twitter" name="redes_twitter"
                                   value="<?= e($config['redes_twitter'] ?? '') ?>"
                                   placeholder="https://twitter.com/coordicanarias">
                        </div>

                        <!-- Instagram -->
                        <div class="mb-3">
                            <label for="redes_instagram" class="form-label">
                                <i class="fab fa-instagram text-danger me-2"></i>Instagram
                            </label>
                            <input type="url" class="form-control" id="redes_instagram" name="redes_instagram"
                                   value="<?= e($config['redes_instagram'] ?? '') ?>"
                                   placeholder="https://instagram.com/coordicanarias">
                        </div>

                        <!-- LinkedIn -->
                        <div class="mb-3">
                            <label for="redes_linkedin" class="form-label">
                                <i class="fab fa-linkedin text-primary me-2"></i>LinkedIn
                            </label>
                            <input type="url" class="form-control" id="redes_linkedin" name="redes_linkedin"
                                   value="<?= e($config['redes_linkedin'] ?? '') ?>"
                                   placeholder="https://linkedin.com/company/coordicanarias">
                        </div>

                        <!-- YouTube -->
                        <div class="mb-3">
                            <label for="redes_youtube" class="form-label">
                                <i class="fab fa-youtube text-danger me-2"></i>YouTube
                            </label>
                            <input type="url" class="form-control" id="redes_youtube" name="redes_youtube"
                                   value="<?= e($config['redes_youtube'] ?? '') ?>"
                                   placeholder="https://youtube.com/@coordicanarias">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de Contacto -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-address-book me-2"></i>Información de Contacto
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Teléfono -->
                        <div class="mb-3">
                            <label for="contacto_telefono" class="form-label">
                                <i class="fas fa-phone me-2"></i>Teléfono
                            </label>
                            <input type="tel" class="form-control" id="contacto_telefono" name="contacto_telefono"
                                   value="<?= e($config['contacto_telefono'] ?? '') ?>"
                                   placeholder="928 123 456">
                            <small class="form-text text-muted">Teléfono de contacto general</small>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="contacto_email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email
                            </label>
                            <input type="email" class="form-control" id="contacto_email" name="contacto_email"
                                   value="<?= e($config['contacto_email'] ?? '') ?>"
                                   placeholder="info@coordicanarias.com">
                            <small class="form-text text-muted">Email de contacto general</small>
                        </div>

                        <!-- Dirección -->
                        <div class="mb-3">
                            <label for="contacto_direccion" class="form-label">
                                <i class="fas fa-map-marker-alt me-2"></i>Dirección
                            </label>
                            <textarea class="form-control" id="contacto_direccion" name="contacto_direccion"
                                      rows="3" placeholder="Calle Ejemplo, 123, Las Palmas de Gran Canaria"><?= e($config['contacto_direccion'] ?? '') ?></textarea>
                            <small class="form-text text-muted">Dirección física de la sede</small>
                        </div>

                        <!-- Horario -->
                        <div class="mb-3">
                            <label for="contacto_horario" class="form-label">
                                <i class="fas fa-clock me-2"></i>Horario de Atención
                            </label>
                            <textarea class="form-control" id="contacto_horario" name="contacto_horario"
                                      rows="2" placeholder="Lunes a Viernes: 8:00 - 15:00"><?= e($config['contacto_horario'] ?? '') ?></textarea>
                            <small class="form-text text-muted">Horarios de atención al público</small>
                        </div>
                    </div>
                </div>

                <!-- Vista Previa -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-eye me-2"></i>Vista Previa
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-0">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>Información
                            </h6>
                            <p class="mb-0">
                                Esta configuración se mostrará en el footer del sitio público y en la página de contacto.
                                Asegúrate de que toda la información sea correcta antes de guardar.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="mt-4">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save me-2"></i>Guardar Configuración
            </button>
            <a href="<?= url('admin/index.php') ?>" class="btn btn-secondary btn-lg">
                <i class="fas fa-times me-2"></i>Cancelar
            </a>
        </div>
    </form>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
