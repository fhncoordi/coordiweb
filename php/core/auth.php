<?php
/**
 * Sistema de Autenticación
 * Coordicanarias CMS
 *
 * Gestión de login, logout, sesiones seguras y CSRF
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db/connection.php';
require_once __DIR__ . '/security.php';

// ============================================
// CONFIGURACIÓN DE SESIONES SEGURAS
// ============================================

// Configurar sesiones antes de iniciarlas
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');

// Solo habilitar si se usa HTTPS
// ini_set('session.cookie_secure', 1);

session_name('COORDI_SESSION');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// FUNCIONES DE AUTENTICACIÓN
// ============================================

/**
 * Intentar login de usuario
 *
 * @param string $username Nombre de usuario
 * @param string $password Contraseña
 * @return bool True si login exitoso, false si falla
 */
function login($username, $password) {
    // Sanitizar input
    $username = sanitizarTexto($username);

    // Buscar usuario
    $sql = "SELECT * FROM usuarios WHERE username = ? AND activo = 1 LIMIT 1";
    $usuario = fetchOne($sql, [$username]);

    if (!$usuario) {
        registrarIntentoFallido($username, 'Usuario no existe o inactivo');
        return false;
    }

    // Verificar contraseña
    if (!password_verify($password, $usuario['password_hash'])) {
        registrarIntentoFallido($username, 'Contraseña incorrecta');
        return false;
    }

    // Regenerar ID de sesión (protección contra session fixation)
    session_regenerate_id(true);

    // Guardar datos en sesión
    $_SESSION['user_id'] = $usuario['id'];
    $_SESSION['username'] = $usuario['username'];
    $_SESSION['rol'] = $usuario['rol'];
    $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    $_SESSION['login_time'] = time();

    // Actualizar último acceso
    $sql = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?";
    execute($sql, [$usuario['id']]);

    // Registrar login exitoso
    registrarActividad($usuario['id'], 'login', 'usuarios', $usuario['id'], 'Login exitoso');

    return true;
}

/**
 * Cerrar sesión
 */
function logout() {
    if (isset($_SESSION['user_id'])) {
        registrarActividad($_SESSION['user_id'], 'logout', 'usuarios', $_SESSION['user_id'], 'Logout');
    }

    // Limpiar sesión
    $_SESSION = array();

    // Destruir cookie de sesión
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/');
    }

    // Destruir sesión
    session_destroy();
}

/**
 * Verificar si usuario está autenticado
 *
 * @return bool True si está logueado
 */
function isLoggedIn() {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    // Validar IP (protección contra session hijacking)
    if (!isset($_SESSION['ip_address']) || $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
        logout();
        return false;
    }

    // Validar User Agent (protección contra session hijacking)
    if (!isset($_SESSION['user_agent']) || $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        logout();
        return false;
    }

    // Timeout de sesión (4 horas)
    $timeout = 4 * 60 * 60; // 4 horas en segundos
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $timeout) {
        logout();
        return false;
    }

    return true;
}

/**
 * Requerir autenticación (redirigir si no logueado)
 *
 * @param string $redirect_url URL a redirigir si no autenticado (opcional)
 */
function requireLogin($redirect_url = null) {
    if (!isLoggedIn()) {
        // Si no se proporciona URL, usar la función url() para generar la ruta correcta
        $redirect_url = $redirect_url ?? url('admin/login.php');
        header('Location: ' . $redirect_url);
        exit;
    }
}

/**
 * Verificar rol de usuario
 *
 * @param string $rol Rol requerido ('admin' o 'editor')
 * @return bool True si el usuario tiene el rol
 */
function hasRole($rol) {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === $rol;
}

/**
 * Requerir rol específico
 *
 * @param string $rol Rol requerido
 */
function requireRole($rol) {
    requireLogin();

    if (!hasRole($rol)) {
        http_response_code(403);
        die('Acceso denegado. No tienes permisos suficientes.');
    }
}

/**
 * Verificar si el usuario es admin
 *
 * @return bool True si es admin
 */
function isAdmin() {
    return hasRole('admin');
}

/**
 * Obtener usuario actual
 *
 * @return array|null Datos del usuario o null
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    $sql = "SELECT * FROM usuarios WHERE id = ? LIMIT 1";
    return fetchOne($sql, [$_SESSION['user_id']]);
}

/**
 * Obtener ID del usuario actual
 *
 * @return int|null ID del usuario o null
 */
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

// ============================================
// PROTECCIÓN CSRF
// ============================================

/**
 * Generar token CSRF
 *
 * @return string Token CSRF
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verificar token CSRF
 *
 * @param string $token Token a verificar
 * @return bool True si es válido
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Campo hidden de CSRF para formularios
 *
 * @return string HTML del campo hidden
 */
function csrfField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . attr($token) . '">';
}

// ============================================
// REGISTRO DE ACTIVIDAD
// ============================================

/**
 * Registrar intento fallido de login
 *
 * @param string $username Nombre de usuario
 * @param string $razon Razón del fallo
 */
function registrarIntentoFallido($username, $razon = '') {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

    error_log("Intento de login fallido: $username ($razon) desde $ip");

    // Aquí se podría implementar bloqueo por intentos (rate limiting)
    // Por ahora solo registramos en log
}

/**
 * Registrar actividad de usuario en BD
 *
 * @param int $usuario_id ID del usuario
 * @param string $accion Acción realizada
 * @param string|null $tabla Tabla afectada
 * @param int|null $registro_id ID del registro afectado
 * @param string|null $detalles Detalles adicionales
 */
function registrarActividad($usuario_id, $accion, $tabla = null, $registro_id = null, $detalles = null) {
    $sql = "INSERT INTO registro_actividad
            (usuario_id, accion, tabla_afectada, registro_id, detalles, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    execute($sql, [
        $usuario_id,
        $accion,
        $tabla,
        $registro_id,
        $detalles,
        $_SERVER['REMOTE_ADDR'] ?? null,
        $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);
}

// ============================================
// GESTIÓN DE CONTRASEÑAS
// ============================================

/**
 * Hash de contraseña seguro
 *
 * @param string $password Contraseña en texto plano
 * @return string Hash de la contraseña
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verificar si una contraseña necesita rehash
 * (por si se cambia el algoritmo o cost factor)
 *
 * @param string $hash Hash actual
 * @return bool True si necesita rehash
 */
function needsRehash($hash) {
    return password_needs_rehash($hash, PASSWORD_DEFAULT);
}

// ============================================
// SISTEMA DE PERMISOS POR ÁREA
// ============================================

/**
 * Verificar si el usuario puede gestionar un área específica
 *
 * @param int $area_id ID del área a verificar
 * @return bool True si puede gestionar
 */
function puedeGestionarArea($area_id) {
    $usuario = getCurrentUser();
    if (!$usuario) {
        return false;
    }

    // Admin puede gestionar todo
    if ($usuario['rol'] === 'admin') {
        return true;
    }

    // Editor puede gestionar todas las áreas
    if ($usuario['rol'] === 'editor') {
        return true;
    }

    // Coordinador solo puede gestionar su área
    if ($usuario['rol'] === 'coordinador') {
        return $usuario['area_id'] == $area_id;
    }

    return false;
}

/**
 * Verificar si el usuario puede gestionar un proyecto específico
 *
 * @param int $proyecto_id ID del proyecto
 * @return bool True si puede gestionar
 */
function puedeGestionarProyecto($proyecto_id) {
    $usuario = getCurrentUser();
    if (!$usuario) {
        return false;
    }

    // Admin puede gestionar todo
    if ($usuario['rol'] === 'admin') {
        return true;
    }

    // Obtener el área del proyecto
    $proyecto = fetchOne("SELECT area_id FROM proyectos WHERE id = ?", [$proyecto_id]);
    if (!$proyecto) {
        return false;
    }

    return puedeGestionarArea($proyecto['area_id']);
}

/**
 * Verificar si el usuario puede gestionar un servicio específico
 *
 * @param int $servicio_id ID del servicio
 * @return bool True si puede gestionar
 */
function puedeGestionarServicio($servicio_id) {
    $usuario = getCurrentUser();
    if (!$usuario) {
        return false;
    }

    // Admin puede gestionar todo
    if ($usuario['rol'] === 'admin') {
        return true;
    }

    // Obtener el área del servicio
    $servicio = fetchOne("SELECT area_id FROM servicios WHERE id = ?", [$servicio_id]);
    if (!$servicio) {
        return false;
    }

    return puedeGestionarArea($servicio['area_id']);
}

/**
 * Verificar si el usuario puede gestionar un beneficio específico
 *
 * @param int $beneficio_id ID del beneficio
 * @return bool True si puede gestionar
 */
function puedeGestionarBeneficio($beneficio_id) {
    $usuario = getCurrentUser();
    if (!$usuario) {
        return false;
    }

    // Admin puede gestionar todo
    if ($usuario['rol'] === 'admin') {
        return true;
    }

    // Obtener el área del beneficio
    $beneficio = fetchOne("SELECT area_id FROM beneficios WHERE id = ?", [$beneficio_id]);
    if (!$beneficio) {
        return false;
    }

    return puedeGestionarArea($beneficio['area_id']);
}

/**
 * Obtener condición SQL para filtrar por áreas permitidas
 *
 * @param string $alias Alias de tabla (ej: 'p' para proyectos)
 * @return string Condición SQL para WHERE
 */
function getAreaFilterSQL($alias = '') {
    $usuario = getCurrentUser();
    if (!$usuario) {
        return '1=0'; // No mostrar nada si no está autenticado
    }

    // Admin y editor ven todo
    if ($usuario['rol'] === 'admin' || $usuario['rol'] === 'editor') {
        return '1=1';
    }

    // Coordinador solo ve su área
    if ($usuario['rol'] === 'coordinador' && $usuario['area_id']) {
        $column = $alias ? "{$alias}.area_id" : 'area_id';
        return "{$column} = " . intval($usuario['area_id']);
    }

    return '1=0'; // Por defecto no mostrar nada
}

/**
 * Obtener lista de IDs de áreas que el usuario puede gestionar
 *
 * @return array Lista de IDs de áreas
 */
function getAreasGestionables() {
    $usuario = getCurrentUser();
    if (!$usuario) {
        return [];
    }

    // Admin y editor pueden gestionar todas las áreas
    if ($usuario['rol'] === 'admin' || $usuario['rol'] === 'editor') {
        $areas = fetchAll("SELECT id FROM areas WHERE activo = 1");
        return array_column($areas, 'id');
    }

    // Coordinador solo puede gestionar su área
    if ($usuario['rol'] === 'coordinador' && $usuario['area_id']) {
        return [$usuario['area_id']];
    }

    return [];
}

/**
 * Verificar si el usuario puede gestionar usuarios
 * Solo admin puede gestionar usuarios
 *
 * @return bool True si puede gestionar usuarios
 */
function puedeGestionarUsuarios() {
    return isAdmin();
}

/**
 * Verificar si el usuario puede gestionar áreas
 * Solo admin puede gestionar áreas
 *
 * @return bool True si puede gestionar áreas
 */
function puedeGestionarAreas() {
    return isAdmin();
}

/**
 * Verificar si el usuario puede gestionar configuración
 * Solo admin puede gestionar configuración
 *
 * @return bool True si puede gestionar configuración
 */
function puedeGestionarConfiguracion() {
    return isAdmin();
}

/**
 * Verificar si el usuario puede ver el registro de actividad
 * Solo admin puede ver el registro
 *
 * @return bool True si puede ver registro
 */
function puedeVerRegistroActividad() {
    return isAdmin();
}

/**
 * Requerir permiso para gestionar área
 * Redirige o muestra error si no tiene permisos
 *
 * @param int $area_id ID del área
 */
function requirePermissionArea($area_id) {
    if (!puedeGestionarArea($area_id)) {
        http_response_code(403);
        die('Acceso denegado. No tienes permisos para gestionar esta área.');
    }
}
