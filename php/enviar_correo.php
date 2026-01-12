<?php
/**
 * Script de envío de correos para formularios de contacto
 * Coordicanarias - 2025
 *
 * Usa PHPMailer con SMTP de Google Workspace
 * INCLUYE PROTECCIÓN ANTI-BOT MULTICAPA
 */

// Incluir PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// Cargar configuración de SMTP desde archivo externo
// IMPORTANTE: El archivo config.php contiene credenciales sensibles y NO está en git
require_once 'config.php';

// Cargar sistema de seguridad anti-bot
require_once 'security_antibot.php';

// ============================================
// FUNCIONES DE VALIDACIÓN Y SANITIZACIÓN
// ============================================

/**
 * Sanitiza y valida una dirección de email
 */
function validar_email($email) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $email;
    }
    return false;
}

/**
 * Sanitiza texto para prevenir inyección de headers en emails
 */
function sanitizar_texto($texto) {
    $texto = trim($texto);
    $texto = stripslashes($texto);
    $texto = htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
    // Eliminar saltos de línea para prevenir inyección de headers
    $texto = str_replace(array("\r", "\n", "%0a", "%0d"), '', $texto);
    return $texto;
}

/**
 * Verifica que la petición viene del mismo dominio
 */
function verificar_origen($dominios_permitidos) {
    if (!isset($_SERVER['HTTP_REFERER'])) {
        return false;
    }

    $referer = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);

    foreach ($dominios_permitidos as $dominio) {
        if (stripos($referer, $dominio) !== false) {
            return true;
        }
    }

    return false;
}

// ============================================
// PROCESAMIENTO DEL FORMULARIO
// ============================================

// Verificar que es una petición POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.html?error=metodo_invalido");
    exit;
}

// Verificar origen de la petición (protección anti-spam)
if (!verificar_origen($dominios_permitidos)) {
    header("Location: ../index.html?error=origen_invalido");
    exit;
}

// ============================================
// VALIDACIONES ANTI-BOT
// ============================================

// Preparar datos del formulario para validación anti-bot
$datos_antibot = [
    'nombre' => $_POST['txtName'] ?? '',
    'email' => $_POST['txtEmail'] ?? '',
    'mensaje' => $_POST['txtMsg'] ?? '',
    'website' => $_POST['website'] ?? '',  // Honeypot
    'timestamp' => $_POST['form_timestamp'] ?? '',  // Tiempo de carga
    'csrf_token' => $_POST['csrf_token'] ?? '',  // Token CSRF
    'recaptcha_token' => $_POST['recaptcha_token'] ?? ''  // reCAPTCHA v3
];

// Ejecutar todas las validaciones anti-bot
$resultado_antibot = validar_antibot($datos_antibot);

// Si las validaciones anti-bot fallan, bloquear y registrar
if (!$resultado_antibot['valido']) {
    $errores_encoded = urlencode('Mensaje bloqueado por seguridad. Si crees que es un error, contacta por teléfono.');

    // Determinar la página de origen
    $pagina_origen = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.html';
    $pagina_origen = basename(parse_url($pagina_origen, PHP_URL_PATH));

    // Ajustar ruta si viene de areas/
    if (strpos($_SERVER['HTTP_REFERER'], '/areas/') !== false) {
        $pagina_origen = '../areas/' . $pagina_origen;
    } else {
        $pagina_origen = '../' . $pagina_origen;
    }

    // Log detallado del intento bloqueado (para debugging)
    error_log("Formulario bloqueado por anti-bot: " . json_encode($resultado_antibot));

    header("Location: $pagina_origen?error=" . $errores_encoded . "#contact");
    exit;
}

// Detectar el área desde el formulario
$area = isset($_POST['area']) ? sanitizar_texto($_POST['area']) : 'default';

// Obtener el email destino según el área
$email_destino = isset($emails_por_area[$area]) ? $emails_por_area[$area] : $emails_por_area['default'];

// Mapeo de áreas a nombres legibles
$nombres_areas = [
    'inicio' => 'Inicio',
    'empleo' => 'Empleo con Apoyo',
    'aintegral' => 'Atención Integral',
    'ocio' => 'Ocio y Tiempo Libre',
    'igualdad' => 'Igualdad y Promoción de la Mujer con Discapacidad',
    'igualdadpm' => 'Igualdad y Promoción de la Mujer con Discapacidad', // Alias
    'formacion' => 'Formación e Innovación',
    'forminno' => 'Formación e Innovación', // Alias
    'accesibilidad' => 'Cultura Accesible',
    'participacion' => 'Participación Ciudadana',
    'participaca' => 'Participación Ciudadana', // Alias
    'transparencia' => 'Transparencia',
    'alegal' => 'Asesoramiento Legal',
    'politica-cookies' => 'Política de Cookies',
    'politica-privacidad' => 'Política de Privacidad',
    'default' => 'Contacto'
];

// Obtener nombre legible del área
$nombre_area = isset($nombres_areas[$area]) ? $nombres_areas[$area] : ucfirst(str_replace(array('-', '_'), ' ', $area));

// Personalizar el asunto según el área
$asunto = "Mensaje desde el formulario de " . $nombre_area . " - Coordicanarias";

// Recoger y sanitizar datos del formulario
$nombre = isset($_POST['txtName']) ? sanitizar_texto($_POST['txtName']) : '';
$email = isset($_POST['txtEmail']) ? $_POST['txtEmail'] : '';
$mensaje = isset($_POST['txtMsg']) ? $_POST['txtMsg'] : ''; // No sanitizar aquí para preservar saltos de línea

// Validar campos obligatorios
$errores = array();

if (empty($nombre)) {
    $errores[] = "El nombre es obligatorio";
}

if (empty($email)) {
    $errores[] = "El email es obligatorio";
} else {
    $email_validado = validar_email($email);
    if (!$email_validado) {
        $errores[] = "El email no es válido";
    } else {
        $email = $email_validado;
    }
}

if (empty($mensaje)) {
    $errores[] = "El mensaje es obligatorio";
}

// Si hay errores, redirigir de vuelta con mensaje de error
if (!empty($errores)) {
    $errores_encoded = urlencode(implode(", ", $errores));

    // Determinar la página de origen
    $pagina_origen = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.html';
    $pagina_origen = basename(parse_url($pagina_origen, PHP_URL_PATH));

    // Ajustar ruta si viene de areas/
    if (strpos($_SERVER['HTTP_REFERER'], '/areas/') !== false) {
        $pagina_origen = '../areas/' . $pagina_origen;
    } else {
        $pagina_origen = '../' . $pagina_origen;
    }

    header("Location: $pagina_origen?error=" . $errores_encoded);
    exit;
}

// ============================================
// PREPARAR Y ENVIAR EL EMAIL CON PHPMAILER
// ============================================

// Preparar el cuerpo del email en formato HTML
$cuerpo_email = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 20px auto;
            background-color: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #E5A649;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header img {
            max-width: 280px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        .content {
            background-color: white;
            padding: 30px 20px;
        }
        .field {
            margin-bottom: 15px;
        }
        .field-label {
            font-weight: 600;
            color: #555;
        }
        .field-value {
            margin-top: 5px;
            padding: 10px;
            background-color: #f5f5f5;
            border-left: 4px solid #E5A649;
        }
        .security-badge {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 11px;
            margin-left: 10px;
        }

        .footer {
            background-color: #f9f9f9;
            margin-top: 20px;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #777;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>
<body>
    <div class='email-wrapper'>
        <div class='header'>
            <img src='https://coordicanarias.com/images/brand-coordi-white.png' alt='Coordicanarias' />
        </div>
        <div class='content'>
            <h2 style='color: #E5A649; margin-top: 0;'>Mensaje desde el formulario de " . htmlspecialchars($nombre_area, ENT_QUOTES, 'UTF-8');

// Agregar badge de seguridad si reCAPTCHA está activo
if (isset(\$resultado_antibot['scores']['recaptcha'])) {
    \$score = \$resultado_antibot['scores']['recaptcha'];
    \$cuerpo_email .= " <span class='security-badge'>✓ Verificado (Score: " . number_format(\$score, 2) . ")</span>";
}

\$cuerpo_email .= "</h2>
            <div class='field'>
                <div class='field-label'>Nombre:</div>
                <div class='field-value'>" . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . "</div>
            </div>
            <div class='field'>
                <div class='field-label'>Email:</div>
                <div class='field-value'>" . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "</div>
            </div>
            <div class='field'>
                <div class='field-label'>Mensaje:</div>
                <div class='field-value'>" . nl2br(htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8')) . "</div>
            </div>
        </div>
        <div class='footer'>
            <p><strong>Coordicanarias</strong><br>
            Coordinadora de Personas con Discapacidad Física de Canarias</p>
            <p>
                <a href='https://coordicanarias.com' style='color: #E5A649; text-decoration: none;'>coordicanarias.com</a> |
                <a href='mailto:fhn@coordicanarias.com' style='color: #E5A649; text-decoration: none;'>fhn@coordicanarias.com</a>
            </p>
            <p style='font-size: 11px; color: #999; margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0;'>
                Email recibido desde formulario de contacto | " . date('d/m/Y H:i:s') . "<br>
                IP: \" . obtener_ip_cliente() . \"
            </p>
        </div>
    </div>
</body>
</html>
";

$email_enviado = false;
$metodo_usado = '';

// Intentar envío con SMTP (método preferido)
if (EMAIL_METHOD === 'smtp' || EMAIL_METHOD === 'smtp_with_fallback') {
    try {
        // Crear instancia de PHPMailer
        $mail = new PHPMailer(true);

        // Desactivar modo debug en producción
        \$mail->SMTPDebug = 0; // 0=sin debug, 1=cliente, 2=cliente+servidor, 3=detallado

        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE; // Usa 'ssl' (puerto 465) o 'tls' (puerto 587)
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // Configuración del correo
        $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);
        $mail->addAddress($email_destino);
        $mail->addReplyTo($email, $nombre);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $cuerpo_email;

        // Versión texto plano (fallback)
        $mail->AltBody = "Nuevo mensaje de contacto\n\n" .
                         "Nombre: $nombre\n" .
                         "Email: $email\n" .
                         "Mensaje: $mensaje\n\n" .
                         "Área: $nombre_area\n" .
                         "Fecha: " . date('d/m/Y H:i:s');

        // Enviar el correo
        $mail->send();
        $email_enviado = true;
        $metodo_usado = 'SMTP';

    } catch (Exception $e) {
        // Si falla SMTP y está configurado el fallback, intentar con mail()
        if (EMAIL_METHOD === 'smtp_with_fallback') {
            error_log("SMTP falló, intentando fallback con mail(): " . $e->getMessage());
            // Continuar al método mail() más abajo
        } else {
            $email_enviado = false;
            error_log("Error al enviar correo con SMTP: " . $e->getMessage());
        }
    }
}

// Fallback: Usar función mail() nativa de PHP si SMTP falló o está configurado
if (!$email_enviado && (EMAIL_METHOD === 'mail' || EMAIL_METHOD === 'smtp_with_fallback')) {
    // Preparar headers para mail()
    $headers = array();
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=UTF-8';
    $headers[] = 'From: ' . SMTP_FROM_NAME . ' <' . SMTP_USER . '>';
    $headers[] = 'Reply-To: ' . $nombre . ' <' . $email . '>';
    $headers[] = 'X-Mailer: PHP/' . phpversion();

    // Enviar con mail() - Usar parámetro adicional para especificar remitente
    $parametros_adicionales = '-f' . SMTP_USER; // Usa noreply@coordicanarias.com como remitente
    $email_enviado = mail(
        $email_destino,
        $asunto,
        $cuerpo_email,
        implode("\r\n", $headers),
        $parametros_adicionales
    );

    if ($email_enviado) {
        $metodo_usado = 'mail()';
        error_log("Email enviado correctamente usando mail() nativa de PHP");
    } else {
        error_log("Error: No se pudo enviar el email ni con SMTP ni con mail()");
    }
}

// ============================================
// REDIRECCIÓN SEGÚN RESULTADO
// ============================================


// Si el email se envió exitosamente, limpiar el rate limiter
if (\$email_enviado) {
    limpiar_rate_limit_exitoso();
}

// Determinar la página de origen
$pagina_origen = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.html';
$pagina_origen = basename(parse_url($pagina_origen, PHP_URL_PATH));

// Ajustar ruta si viene de areas/
if (strpos($_SERVER['HTTP_REFERER'], '/areas/') !== false) {
    $pagina_origen = '../areas/' . $pagina_origen;
} else {
    $pagina_origen = '../' . $pagina_origen;
}

if ($email_enviado) {
    // Éxito - redirigir automáticamente con mensaje de éxito
    header("Location: $pagina_origen?success=1#contact");
    exit;
} else {
    // Error al enviar - redirigir con mensaje de error
    header("Location: $pagina_origen?error=error_envio#contact");
    exit;
}
?>
