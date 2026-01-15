<?php
/**
 * Sistema de Emails para Donaciones y Suscripciones
 * Coordicanarias
 */

require_once __DIR__ . '/config.php';

// Color corporativo naranja de Coordicanarias
define('EMAIL_COLOR_PRIMARY', '#E5A649');
define('EMAIL_COLOR_SECONDARY', '#F5F5F5');

/**
 * Template base de email con banda naranja y logo blanco
 */
function getEmailTemplate($titulo, $contenido) {
    return '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($titulo) . '</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
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
                background-color: ' . EMAIL_COLOR_PRIMARY . ';
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
            .content {
                padding: 30px 20px;
                background-color: white;
            }
            .content h2 {
                color: ' . EMAIL_COLOR_PRIMARY . ';
                margin-top: 0;
            }
            .info-box {
                background-color: ' . EMAIL_COLOR_SECONDARY . ';
                border-left: 4px solid ' . EMAIL_COLOR_PRIMARY . ';
                padding: 15px;
                margin: 20px 0;
            }
            .info-row {
                margin: 10px 0;
                padding: 8px 0;
                border-bottom: 1px solid #e0e0e0;
            }
            .info-row:last-child {
                border-bottom: none;
            }
            .info-label {
                font-weight: 600;
                color: #555;
                display: inline-block;
                min-width: 120px;
            }
            .info-value {
                color: #333;
            }
            .button {
                display: inline-block;
                padding: 12px 30px;
                background-color: ' . EMAIL_COLOR_PRIMARY . ';
                color: white !important;
                text-decoration: none;
                border-radius: 5px;
                margin: 20px 0;
                font-weight: 600;
            }
            .footer {
                background-color: #f9f9f9;
                padding: 20px;
                text-align: center;
                font-size: 13px;
                color: #777;
                border-top: 1px solid #e0e0e0;
            }
            .footer a {
                color: ' . EMAIL_COLOR_PRIMARY . ';
                text-decoration: none;
            }
            @media only screen and (max-width: 600px) {
                .email-wrapper {
                    margin: 0;
                }
                .content {
                    padding: 20px 15px;
                }
            }
        </style>
    </head>
    <body>
        <div class="email-wrapper">
            <div class="header">
                <img src="https://coordicanarias.com/images/brand-coordi-white.png" alt="Coordicanarias" />
            </div>
            <div class="content">
                ' . $contenido . '
            </div>
            <div class="footer">
                <p><strong>Coordicanarias</strong><br>
                Coordinadora de Personas con Discapacidad Física de Canarias</p>
                <p>
                    <a href="https://coordicanarias.com">coordicanarias.com</a> |
                    <a href="mailto:fhn@coordicanarias.com">fhn@coordicanarias.com</a>
                </p>
                <p style="font-size: 11px; color: #999;">
                    Este es un email automático. Por favor, no respondas a este mensaje.
                </p>
            </div>
        </div>
    </body>
    </html>
    ';
}

/**
 * Enviar email de confirmación de donación
 */
function enviarEmailConfirmacionDonacion($donacion) {
    $nombre = htmlspecialchars($donacion['nombre']);
    $importe = number_format($donacion['importe'], 2);
    $fecha = date('d/m/Y H:i', strtotime($donacion['fecha_creacion']));
    $metodo = strtoupper($donacion['metodo_pago'] ?? 'card');
    $metodoPago = $metodo === 'CUSTOMER_BALANCE' ? 'Bizum' : 'Tarjeta';

    $contenido = '
        <h2>¡Gracias por tu generosa donación!</h2>

        <p>Hola <strong>' . $nombre . '</strong>,</p>

        <p>Hemos recibido correctamente tu donación de <strong>' . $importe . '€</strong>.
        Tu apoyo es fundamental para continuar nuestra labor en favor de las personas con
        discapacidad en Canarias.</p>

        <div class="info-box">
            <h3 style="margin-top: 0; color: ' . EMAIL_COLOR_PRIMARY . ';">Detalles de tu donación</h3>
            <div class="info-row">
                <span class="info-label">Importe:</span>
                <span class="info-value"><strong>' . $importe . ' €</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha:</span>
                <span class="info-value">' . $fecha . '</span>
            </div>
            <div class="info-row">
                <span class="info-label">Método de pago:</span>
                <span class="info-value">' . $metodoPago . '</span>
            </div>
            <div class="info-row">
                <span class="info-label">ID Transacción:</span>
                <span class="info-value" style="font-size: 11px; color: #666;">' . htmlspecialchars($donacion['stripe_payment_intent_id']) . '</span>
            </div>
        </div>

        ' . ($donacion['stripe_payment_intent_id'] ? '
        <p style="text-align: center;">
            <a href="https://coordicanarias.com/stripe/recibo-donacion.php?pi=' . urlencode($donacion['stripe_payment_intent_id']) . '" class="button">
                Ver Recibo Completo
            </a>
        </p>
        ' : '') . '

        <p>Tu contribución nos permite seguir trabajando en proyectos de:</p>
        <ul>
            <li>Empleo con Apoyo</li>
            <li>Formación e Innovación</li>
            <li>Atención Integral</li>
            <li>Igualdad y Promoción de la Mujer</li>
            <li>Ocio y Tiempo Libre</li>
            <li>Participación Ciudadana</li>
        </ul>

        <p><strong>¡Muchas gracias por tu solidaridad!</strong></p>

        <p>Un cordial saludo,<br>
        <strong>El equipo de Coordicanarias</strong></p>
    ';

    $html = getEmailTemplate('Confirmación de Donación - Coordicanarias', $contenido);

    // Enviar email
    return enviarEmailHTML(
        $donacion['email'],
        'Gracias por tu donación a Coordicanarias',
        $html,
        $nombre
    );
}

/**
 * Enviar email de bienvenida a nuevo socio
 */
function enviarEmailBienvenidaSocio($socio) {
    $nombre = htmlspecialchars($socio['nombre']);
    $fecha = date('d/m/Y', strtotime($socio['fecha_inicio'] ?? 'now'));
    $proximoCobro = date('d/m/Y', strtotime($socio['fecha_proximo_cobro']));

    $contenido = '
        <h2>¡Bienvenido/a a Coordicanarias!</h2>

        <p>Hola <strong>' . $nombre . '</strong>,</p>

        <p>¡Gracias por hacerte socio/a de Coordicanarias! Tu apoyo mensual de <strong>5€</strong>
        es esencial para mantener y ampliar nuestros programas en favor de las personas con
        discapacidad en Canarias.</p>

        <div class="info-box">
            <h3 style="margin-top: 0; color: ' . EMAIL_COLOR_PRIMARY . ';">RECIBO y detalle de tu suscripción</h3>
            <div class="info-row">
                <span class="info-label">Cuota mensual:</span>
                <span class="info-value"><strong>5,00 €</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha de alta:</span>
                <span class="info-value">' . $fecha . '</span>
            </div>
            <div class="info-row">
                <span class="info-label">Próximo cobro:</span>
                <span class="info-value">' . $proximoCobro . '</span>
            </div>
            <div class="info-row">
                <span class="info-label">Estado:</span>
                <span class="info-value"><span style="color: #28a745; font-weight: 600;">✓ Activa</span></span>
            </div>
        </div>

        <h3 style="color: ' . EMAIL_COLOR_PRIMARY . ';">Como socio/a disfrutas de:</h3>
        <ul>
            <li><strong>Contribución directa</strong> a nuestros 6 áreas de actuación</li>
            <li><strong>Invitaciones</strong> a eventos y actividades especiales</li>
            <li><strong>Transparencia total</strong> sobre el uso de los fondos</li>
        </ul>

        <p style="text-align: center;">
            <a href="https://coordicanarias.com/stripe/manage-subscription.php" class="button">
                Gestionar mi Suscripción
            </a>
        </p>

        <p><em>Desde ese portal podrás actualizar tu método de pago, ver tu historial de pagos
        o cancelar la suscripción cuando lo desees.</em></p>

        <p><strong>¡Gracias por tu compromiso con la discapacidad!</strong></p>

        <p>Un cordial saludo,<br>
        <strong>El equipo de Coordicanarias</strong></p>
    ';

    $html = getEmailTemplate('Bienvenido/a a Coordicanarias', $contenido);

    return enviarEmailHTML(
        $socio['email'],
        '¡Bienvenido/a a Coordicanarias! - Confirmación de suscripción',
        $html,
        $nombre
    );
}

/**
 * Enviar recibo mensual de suscripción
 */
function enviarEmailReciboMensual($socio) {
    $nombre = htmlspecialchars($socio['nombre']);
    $fecha = date('d/m/Y');
    $mes = strftime('%B de %Y', time());

    // Obtener fecha del próximo cobro desde BD (más preciso que +1 month)
    $proximoCobro = !empty($socio['fecha_proximo_cobro'])
        ? date('d/m/Y', strtotime($socio['fecha_proximo_cobro']))
        : date('d/m/Y', strtotime('+1 month')); // Fallback si no hay fecha en BD

    $contenido = '
        <h2>Recibo de tu aportación mensual</h2>

        <p>Hola <strong>' . $nombre . '</strong>,</p>

        <p>Te confirmamos que hemos recibido correctamente tu aportación mensual de <strong>5€</strong>
        correspondiente al mes de <strong>' . $mes . '</strong>.</p>

        <div class="info-box">
            <h3 style="margin-top: 0; color: ' . EMAIL_COLOR_PRIMARY . ';">Detalles del pago</h3>
            <div class="info-row">
                <span class="info-label">Importe:</span>
                <span class="info-value"><strong>5,00 €</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha de cobro:</span>
                <span class="info-value">' . $fecha . '</span>
            </div>
            <div class="info-row">
                <span class="info-label">Próximo cobro:</span>
                <span class="info-value">' . $proximoCobro . '</span>
            </div>
        </div>

        <p><strong>Gracias por tu continuo apoyo.</strong> Tu aportación este mes nos está permitiendo:</p>
        <ul>
            <li>Apoyar a 15 personas en búsqueda activa de empleo</li>
            <li>Ofrecer 8 talleres de formación en habilidades digitales</li>
            <li>Mantener nuestro servicio de asesoramiento legal gratuito</li>
            <li>Organizar actividades de ocio inclusivo para 30+ personas</li>
        </ul>

        <p style="text-align: center;">
            <a href="https://coordicanarias.com/stripe/manage-subscription.php" class="button">
                Ver Detalles Completos
            </a>
        </p>

        <p>Un cordial saludo,<br>
        <strong>El equipo de Coordicanarias</strong></p>
    ';

    $html = getEmailTemplate('Recibo Mensual - Coordicanarias', $contenido);

    return enviarEmailHTML(
        $socio['email'],
        'Recibo de tu aportación mensual - Coordicanarias',
        $html,
        $nombre
    );
}

/**
 * Enviar notificación de pago fallido
 */
function enviarEmailPagoFallido($socio) {
    $nombre = htmlspecialchars($socio['nombre']);

    $contenido = '
        <h2>⚠️ Problema con tu suscripción</h2>

        <p>Hola <strong>' . $nombre . '</strong>,</p>

        <p>Te escribimos para informarte que <strong>no hemos podido procesar tu pago mensual</strong>
        de 5€ correspondiente a este mes.</p>

        <div class="info-box" style="border-left-color: #dc3545; background-color: #fff3cd;">
            <h3 style="margin-top: 0; color: #dc3545;">Acción necesaria</h3>
            <p style="margin: 0;">Por favor, actualiza tu método de pago para continuar apoyando
            nuestros programas. Si no actualizas tu información de pago, tu suscripción será
            cancelada automáticamente.</p>
        </div>

        <p><strong>Posibles causas:</strong></p>
        <ul>
            <li>Tarjeta caducada</li>
            <li>Fondos insuficientes</li>
            <li>Datos bancarios modificados</li>
            <li>Límite de la tarjeta alcanzado</li>
        </ul>

        <p style="text-align: center;">
            <a href="https://coordicanarias.com/stripe/manage-subscription.php" class="button">
                Actualizar Método de Pago
            </a>
        </p>

        <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactarnos respondiendo
        a este email.</p>

        <p>Gracias por tu comprensión,<br>
        <strong>El equipo de Coordicanarias</strong></p>
    ';

    $html = getEmailTemplate('Acción requerida: Pago fallido', $contenido);

    return enviarEmailHTML(
        $socio['email'],
        '⚠️ Problema con tu suscripción - Acción requerida',
        $html,
        $nombre
    );
}

/**
 * Enviar email de cancelación de suscripción
 */
function enviarEmailCancelacionSocio($socio) {
    $nombre = htmlspecialchars($socio['nombre']);
    $fecha = date('d/m/Y');

    $contenido = '
        <h2>Confirmación de cancelación</h2>

        <p>Hola <strong>' . $nombre . '</strong>,</p>

        <p>Hemos procesado tu solicitud de cancelación de la suscripción mensual a Coordicanarias.</p>

        <div class="info-box">
            <h3 style="margin-top: 0; color: ' . EMAIL_COLOR_PRIMARY . ';">Detalles</h3>
            <div class="info-row">
                <span class="info-label">Fecha de cancelación:</span>
                <span class="info-value">' . $fecha . '</span>
            </div>
            <div class="info-row">
                <span class="info-label">Estado:</span>
                <span class="info-value">Suscripción cancelada</span>
            </div>
        </div>

        <p><strong>Lamentamos verte partir.</strong> Tu apoyo ha sido muy importante para nosotros
        y ha contribuido directamente a mejorar la vida de muchas personas con discapacidad en Canarias.</p>

        <p>Si en algún momento deseas volver a apoyarnos, estaremos encantados de recibirte de nuevo:</p>

        <p style="text-align: center;">
            <a href="https://coordicanarias.com/#colabora" class="button">
                Volver a Colaborar
            </a>
        </p>

        <p>Si tu cancelación se debe a algún problema o insatisfacción, por favor háznoslo saber.
        Tu opinión es muy valiosa para mejorar nuestros servicios.</p>

        <p>¡Muchas gracias por todo el tiempo que has estado con nosotros!</p>

        <p>Un cordial saludo,<br>
        <strong>El equipo de Coordicanarias</strong></p>
    ';

    $html = getEmailTemplate('Cancelación de suscripción', $contenido);

    return enviarEmailHTML(
        $socio['email'],
        'Confirmación de cancelación - Coordicanarias',
        $html,
        $nombre
    );
}

/**
 * Función auxiliar para enviar emails HTML usando PHPMailer
 */
function enviarEmailHTML($to, $subject, $htmlContent, $recipientName = '') {
    // Usar el método configurado en config.php
    if (EMAIL_METHOD === 'smtp') {
        // TODO: Implementar con PHPMailer si se habilita SMTP
        error_log("SMTP no implementado todavía, usando mail()");
    }

    // Preparar headers para HTML
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Coordicanarias <noreply@coordicanarias.com>" . "\r\n";
    $headers .= "Reply-To: fhn@coordicanarias.com" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Enviar con mail()
    $result = mail($to, $subject, $htmlContent, $headers);

    if ($result) {
        error_log("Email enviado a: $to - Asunto: $subject");
    } else {
        error_log("Error enviando email a: $to - Asunto: $subject");
    }

    return $result;
}
