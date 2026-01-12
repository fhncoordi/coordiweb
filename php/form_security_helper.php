<?php
/**
 * Helper para insertar campos de seguridad en formularios
 * Coordicanarias - 2025
 *
 * Genera los campos ocultos necesarios para las validaciones anti-bot
 */

require_once __DIR__ . '/security_antibot.php';

/**
 * Genera todos los campos de seguridad ocultos para el formulario
 * Incluye: honeypot, timestamp, CSRF token
 *
 * @return string HTML con los campos ocultos
 */
function generar_campos_seguridad() {
    // Generar token CSRF
    $csrf_token = generar_token_csrf();

    // Timestamp actual (se usará para validar tiempo de envío)
    $timestamp = time();

    $html = '';

    // Campo honeypot (invisible para humanos, visible para bots)
    $html .= '<div class="form-field-hp" style="position:absolute;left:-9999px;" aria-hidden="true">';
    $html .= '<label for="website">Sitio web (no llenar)</label>';
    $html .= '<input type="text" id="website" name="website" value="" tabindex="-1" autocomplete="off" />';
    $html .= '</div>';
    $html .= "\n";

    // Campo timestamp (oculto)
    $html .= '<input type="hidden" name="form_timestamp" value="' . htmlspecialchars($timestamp, ENT_QUOTES, 'UTF-8') . '" />';
    $html .= "\n";

    // Campo CSRF token (oculto)
    $html .= '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') . '" />';
    $html .= "\n";

    // Campo para token de reCAPTCHA v3 (se llenará con JavaScript)
    $html .= '<input type="hidden" name="recaptcha_token" id="recaptchaToken" value="" />';
    $html .= "\n";

    return $html;
}

/**
 * Genera el script de reCAPTCHA v3 para incluir en el head
 *
 * @return string HTML con el script de reCAPTCHA
 */
function generar_script_recaptcha() {
    // Si no está configurada la clave, retornar vacío
    if (empty(RECAPTCHA_SITE_KEY) || RECAPTCHA_SITE_KEY === '') {
        return '<!-- reCAPTCHA no configurado -->';
    }

    $site_key = RECAPTCHA_SITE_KEY;

    return '<script src="https://www.google.com/recaptcha/api.js?render=' . htmlspecialchars($site_key, ENT_QUOTES, 'UTF-8') . '"></script>';
}

/**
 * Obtiene la clave pública de reCAPTCHA
 * Útil para pasarla al JavaScript
 *
 * @return string Clave pública de reCAPTCHA
 */
function obtener_recaptcha_site_key() {
    return RECAPTCHA_SITE_KEY ?? '';
}
?>
