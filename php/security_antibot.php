<?php
/**
 * Sistema Anti-Bot para Formularios de Contacto
 * Coordicanarias - 2025
 *
 * Incluye:
 * - Google reCAPTCHA v3
 * - Honeypot (campo trampa)
 * - Rate Limiting por IP
 * - Validación de tiempo de envío
 * - Detección de spam por contenido
 * - Token CSRF
 */

// ============================================
// CONFIGURACIÓN
// ============================================

// Importar configuración de reCAPTCHA desde config.php
// Las claves están en php/config.php (archivo NO versionado en git)
if (!defined('RECAPTCHA_SITE_KEY')) {
    require_once __DIR__ . '/config.php';
}

// Si aún no están definidas (por retrocompatibilidad), usar valores vacíos
if (!defined('RECAPTCHA_SITE_KEY')) {
    define('RECAPTCHA_SITE_KEY', ''); // Configurar en php/config.php
}
if (!defined('RECAPTCHA_SECRET_KEY')) {
    define('RECAPTCHA_SECRET_KEY', ''); // Configurar en php/config.php
}
if (!defined('RECAPTCHA_MIN_SCORE')) {
    define('RECAPTCHA_MIN_SCORE', 0.5); // Puntuación mínima por defecto
}

// Rate Limiting
define('RATE_LIMIT_MAX_ATTEMPTS', 3); // Máximo de envíos permitidos
define('RATE_LIMIT_WINDOW', 3600); // Ventana de tiempo en segundos (1 hora)

// Validación de tiempo
define('MIN_SUBMIT_TIME', 3); // Tiempo mínimo en segundos para enviar el formulario

// Archivo temporal para rate limiting
define('RATE_LIMIT_FILE', __DIR__ . '/temp/rate_limit.json');

// ============================================
// 1. GOOGLE reCAPTCHA v3
// ============================================

/**
 * Verifica el token de reCAPTCHA v3 con Google
 */
function verificar_recaptcha($token) {
    // Si no está configurado reCAPTCHA, omitir validación
    if (empty(RECAPTCHA_SECRET_KEY) || RECAPTCHA_SECRET_KEY === '') {
        return ['success' => true, 'score' => 1.0, 'message' => 'reCAPTCHA no configurado'];
    }

    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => RECAPTCHA_SECRET_KEY,
        'response' => $token,
        'remoteip' => obtener_ip_cliente()
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        return ['success' => false, 'score' => 0, 'message' => 'Error al verificar reCAPTCHA'];
    }

    $response = json_decode($result, true);

    if (!$response['success']) {
        return [
            'success' => false,
            'score' => 0,
            'message' => 'reCAPTCHA inválido',
            'error_codes' => $response['error-codes'] ?? []
        ];
    }

    $score = $response['score'] ?? 0;

    if ($score < RECAPTCHA_MIN_SCORE) {
        return [
            'success' => false,
            'score' => $score,
            'message' => "Puntuación de reCAPTCHA muy baja: $score"
        ];
    }

    return [
        'success' => true,
        'score' => $score,
        'message' => 'reCAPTCHA verificado correctamente'
    ];
}

// ============================================
// 2. HONEYPOT (Campo Trampa)
// ============================================

/**
 * Verifica que el campo honeypot esté vacío
 * Los bots suelen llenar todos los campos automáticamente
 */
function verificar_honeypot($valor) {
    // El campo debe estar vacío
    if (!empty($valor)) {
        return [
            'success' => false,
            'message' => 'Honeypot activado - Posible bot detectado'
        ];
    }

    return ['success' => true, 'message' => 'Honeypot pasado'];
}

// ============================================
// 3. RATE LIMITING POR IP
// ============================================

/**
 * Verifica límite de intentos por IP
 */
function verificar_rate_limit() {
    $ip = obtener_ip_cliente();

    // Crear directorio temporal si no existe
    $temp_dir = dirname(RATE_LIMIT_FILE);
    if (!file_exists($temp_dir)) {
        mkdir($temp_dir, 0755, true);
    }

    // Cargar datos existentes
    $data = [];
    if (file_exists(RATE_LIMIT_FILE)) {
        $json = file_get_contents(RATE_LIMIT_FILE);
        $data = json_decode($json, true) ?? [];
    }

    // Limpiar entradas antiguas
    $now = time();
    foreach ($data as $stored_ip => $info) {
        if ($now - $info['first_attempt'] > RATE_LIMIT_WINDOW) {
            unset($data[$stored_ip]);
        }
    }

    // Verificar IP actual
    if (isset($data[$ip])) {
        $attempts = $data[$ip]['attempts'];
        $first_attempt = $data[$ip]['first_attempt'];

        if ($attempts >= RATE_LIMIT_MAX_ATTEMPTS) {
            $tiempo_restante = RATE_LIMIT_WINDOW - ($now - $first_attempt);
            $minutos = ceil($tiempo_restante / 60);

            return [
                'success' => false,
                'message' => "Demasiados intentos. Intenta de nuevo en $minutos minutos.",
                'attempts' => $attempts,
                'time_remaining' => $tiempo_restante
            ];
        }

        // Incrementar contador
        $data[$ip]['attempts']++;
        $data[$ip]['last_attempt'] = $now;
    } else {
        // Primera vez que esta IP intenta
        $data[$ip] = [
            'attempts' => 1,
            'first_attempt' => $now,
            'last_attempt' => $now
        ];
    }

    // Guardar datos actualizados
    file_put_contents(RATE_LIMIT_FILE, json_encode($data, JSON_PRETTY_PRINT));

    return [
        'success' => true,
        'message' => 'Rate limit OK',
        'attempts' => $data[$ip]['attempts']
    ];
}

/**
 * Limpia un intento exitoso del rate limiter
 */
function limpiar_rate_limit_exitoso() {
    $ip = obtener_ip_cliente();

    if (file_exists(RATE_LIMIT_FILE)) {
        $json = file_get_contents(RATE_LIMIT_FILE);
        $data = json_decode($json, true) ?? [];

        // Resetear el contador para esta IP tras envío exitoso
        if (isset($data[$ip])) {
            $data[$ip]['attempts'] = 0;
            file_put_contents(RATE_LIMIT_FILE, json_encode($data, JSON_PRETTY_PRINT));
        }
    }
}

// ============================================
// 4. VALIDACIÓN DE TIEMPO DE ENVÍO
// ============================================

/**
 * Verifica que haya pasado un tiempo mínimo desde que se cargó el formulario
 * Los bots suelen enviar formularios instantáneamente
 */
function verificar_tiempo_envio($timestamp) {
    if (empty($timestamp) || !is_numeric($timestamp)) {
        return [
            'success' => false,
            'message' => 'Timestamp inválido o ausente'
        ];
    }

    $tiempo_transcurrido = time() - intval($timestamp);

    if ($tiempo_transcurrido < MIN_SUBMIT_TIME) {
        return [
            'success' => false,
            'message' => "Formulario enviado demasiado rápido ($tiempo_transcurrido segundos)"
        ];
    }

    // También rechazar si el timestamp es demasiado antiguo (más de 2 horas)
    if ($tiempo_transcurrido > 7200) {
        return [
            'success' => false,
            'message' => 'Formulario expirado (más de 2 horas)'
        ];
    }

    return [
        'success' => true,
        'message' => "Tiempo de envío válido ($tiempo_transcurrido segundos)"
    ];
}

// ============================================
// 5. DETECCIÓN DE SPAM POR CONTENIDO
// ============================================

/**
 * Detecta patrones de spam en el contenido del mensaje
 */
function detectar_spam_contenido($nombre, $email, $mensaje) {
    $razones = [];

    // Lista de palabras/frases sospechosas (expandir según necesidad)
    $palabras_spam = [
        'cialis', 'viagra', 'casino', 'poker', 'forex', 'bitcoin wallet',
        'click here', 'buy now', 'limited time', 'act now', 'prize',
        'congratulations', 'you have won', 'claim now', 'earn money',
        'work from home', 'seo service', 'cheap', 'discount', 'free money',
        'loan', 'credit card', 'insurance', 'pharmacy', 'pills'
    ];

    $mensaje_lower = mb_strtolower($mensaje, 'UTF-8');
    $nombre_lower = mb_strtolower($nombre, 'UTF-8');

    foreach ($palabras_spam as $palabra) {
        if (stripos($mensaje_lower, $palabra) !== false || stripos($nombre_lower, $palabra) !== false) {
            $razones[] = "Contiene palabra sospechosa: '$palabra'";
        }
    }

    // Detectar múltiples URLs (común en spam)
    $num_urls = preg_match_all('/https?:\/\/[^\s]+/i', $mensaje, $matches);
    if ($num_urls > 2) {
        $razones[] = "Contiene muchas URLs ($num_urls enlaces)";
    }

    // Detectar URLs acortadas (bit.ly, tinyurl, etc)
    $url_shorteners = ['bit.ly', 'tinyurl.com', 'goo.gl', 'ow.ly', 't.co', 'short.link'];
    foreach ($url_shorteners as $shortener) {
        if (stripos($mensaje, $shortener) !== false) {
            $razones[] = "Contiene URL acortada ($shortener)";
        }
    }

    // Detectar demasiadas mayúsculas (SPAM EN MAYÚSCULAS)
    $mayusculas = preg_match_all('/[A-Z]/', $mensaje);
    $total_letras = preg_match_all('/[a-zA-Z]/', $mensaje);
    if ($total_letras > 20 && ($mayusculas / $total_letras) > 0.5) {
        $razones[] = "Demasiadas MAYÚSCULAS (posible spam)";
    }

    // Detectar caracteres repetidos (aaaaaaa, !!!!!!!)
    if (preg_match('/(.)\1{7,}/', $mensaje)) {
        $razones[] = "Caracteres repetidos excesivamente";
    }

    // Detectar email diferente en el cuerpo del mensaje
    if (preg_match('/[\w\.-]+@[\w\.-]+\.\w+/', $mensaje, $email_matches)) {
        $email_en_mensaje = $email_matches[0];
        if (strtolower($email_en_mensaje) !== strtolower($email)) {
            $razones[] = "Email diferente en el mensaje ($email_en_mensaje)";
        }
    }

    if (!empty($razones)) {
        return [
            'success' => false,
            'message' => 'Contenido sospechoso detectado',
            'reasons' => $razones
        ];
    }

    return ['success' => true, 'message' => 'Contenido válido'];
}

// ============================================
// 6. TOKEN CSRF
// ============================================

/**
 * Genera un token CSRF único
 */
function generar_token_csrf() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_token_time'] = time();

    return $token;
}

/**
 * Verifica el token CSRF
 */
function verificar_token_csrf($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['csrf_token'])) {
        return [
            'success' => false,
            'message' => 'Token CSRF no encontrado en sesión'
        ];
    }

    if (!isset($_SESSION['csrf_token_time'])) {
        return [
            'success' => false,
            'message' => 'Token CSRF sin timestamp'
        ];
    }

    // Verificar que el token no haya expirado (30 minutos)
    if (time() - $_SESSION['csrf_token_time'] > 1800) {
        return [
            'success' => false,
            'message' => 'Token CSRF expirado'
        ];
    }

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        return [
            'success' => false,
            'message' => 'Token CSRF inválido'
        ];
    }

    // Token válido - regenerar para el próximo envío
    unset($_SESSION['csrf_token']);
    unset($_SESSION['csrf_token_time']);

    return ['success' => true, 'message' => 'Token CSRF válido'];
}

// ============================================
// FUNCIONES AUXILIARES
// ============================================

/**
 * Obtiene la IP real del cliente
 */
function obtener_ip_cliente() {
    $ip = '';

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    }

    // Si hay múltiples IPs (proxy), tomar la primera
    if (strpos($ip, ',') !== false) {
        $ip = trim(explode(',', $ip)[0]);
    }

    return $ip;
}

/**
 * Registra intentos sospechosos en un log
 */
function registrar_intento_sospechoso($razon, $datos = []) {
    $log_file = __DIR__ . '/temp/spam_attempts.log';
    $log_dir = dirname($log_file);

    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }

    $entrada = [
        'timestamp' => date('Y-m-d H:i:s'),
        'ip' => obtener_ip_cliente(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN',
        'razon' => $razon,
        'datos' => $datos
    ];

    file_put_contents(
        $log_file,
        json_encode($entrada, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n",
        FILE_APPEND
    );
}

// ============================================
// FUNCIÓN PRINCIPAL DE VALIDACIÓN
// ============================================

/**
 * Ejecuta todas las validaciones anti-bot
 * Retorna array con resultado y mensajes
 */
function validar_antibot($datos_formulario) {
    $resultados = [
        'valido' => true,
        'errores' => [],
        'warnings' => [],
        'scores' => []
    ];

    // 1. Verificar reCAPTCHA v3
    if (isset($datos_formulario['recaptcha_token'])) {
        $resultado_recaptcha = verificar_recaptcha($datos_formulario['recaptcha_token']);
        $resultados['scores']['recaptcha'] = $resultado_recaptcha['score'];

        if (!$resultado_recaptcha['success']) {
            $resultados['valido'] = false;
            $resultados['errores'][] = $resultado_recaptcha['message'];
            registrar_intento_sospechoso('reCAPTCHA fallido', $resultado_recaptcha);
        }
    } else {
        // Si no hay token de reCAPTCHA, agregar warning pero no bloquear
        // (para permitir compatibilidad durante la transición)
        $resultados['warnings'][] = 'Sin token reCAPTCHA';
    }

    // 2. Verificar Honeypot
    if (isset($datos_formulario['website'])) {
        $resultado_honeypot = verificar_honeypot($datos_formulario['website']);
        if (!$resultado_honeypot['success']) {
            $resultados['valido'] = false;
            $resultados['errores'][] = $resultado_honeypot['message'];
            registrar_intento_sospechoso('Honeypot activado', ['valor' => $datos_formulario['website']]);
        }
    }

    // 3. Verificar Rate Limiting
    $resultado_rate = verificar_rate_limit();
    if (!$resultado_rate['success']) {
        $resultados['valido'] = false;
        $resultados['errores'][] = $resultado_rate['message'];
        registrar_intento_sospechoso('Rate limit excedido', $resultado_rate);
    }

    // 4. Verificar Tiempo de Envío
    if (isset($datos_formulario['timestamp'])) {
        $resultado_tiempo = verificar_tiempo_envio($datos_formulario['timestamp']);
        if (!$resultado_tiempo['success']) {
            $resultados['valido'] = false;
            $resultados['errores'][] = $resultado_tiempo['message'];
            registrar_intento_sospechoso('Tiempo de envío inválido', $resultado_tiempo);
        }
    }

    // 5. Detectar Spam por Contenido
    if (isset($datos_formulario['nombre']) && isset($datos_formulario['email']) && isset($datos_formulario['mensaje'])) {
        $resultado_spam = detectar_spam_contenido(
            $datos_formulario['nombre'],
            $datos_formulario['email'],
            $datos_formulario['mensaje']
        );

        if (!$resultado_spam['success']) {
            $resultados['valido'] = false;
            $resultados['errores'][] = $resultado_spam['message'];
            $resultados['errores'] = array_merge($resultados['errores'], $resultado_spam['reasons']);
            registrar_intento_sospechoso('Spam detectado', $resultado_spam);
        }
    }

    // 6. Verificar Token CSRF
    if (isset($datos_formulario['csrf_token'])) {
        $resultado_csrf = verificar_token_csrf($datos_formulario['csrf_token']);
        if (!$resultado_csrf['success']) {
            $resultados['valido'] = false;
            $resultados['errores'][] = $resultado_csrf['message'];
            registrar_intento_sospechoso('Token CSRF inválido', $resultado_csrf);
        }
    }

    return $resultados;
}
?>
