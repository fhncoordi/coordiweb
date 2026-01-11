<?php
/**
 * Stripe Checkout - Crear Sesión de Suscripción
 * Coordicanarias - Sistema de Socios Mensuales (5€/mes)
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/stripe-php/init.php';
require_once __DIR__ . '/../php/db/connection.php';

header('Content-Type: application/json');

// Configurar Stripe con la API key
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

try {
    // Obtener datos del POST
    $input = json_decode(file_get_contents('php://input'), true);

    $nombre = isset($input['name']) ? trim($input['name']) : '';
    $email = isset($input['email']) ? trim($input['email']) : '';
    $telefono = isset($input['phone']) ? trim($input['phone']) : null;

    // Validaciones
    if (empty($nombre)) {
        throw new Exception('El nombre es obligatorio');
    }

    if (empty($email)) {
        throw new Exception('El email es obligatorio');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email inválido');
    }

    // Verificar si el email ya está registrado como socio activo
    $stmt = getDB()->prepare("
        SELECT id, estado FROM socios
        WHERE email = ?
        AND estado IN ('active', 'trialing', 'past_due')
        LIMIT 1
    ");
    $stmt->execute([$email]);
    $socio_existente = $stmt->fetch();

    if ($socio_existente) {
        throw new Exception('Este email ya tiene una suscripción activa. Si necesitas ayuda, contáctanos.');
    }

    // URL base para success/cancel
    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
                . '://' . $_SERVER['HTTP_HOST'];

    // Precio ID del producto "Socio de Coordicanarias" (5€/mes)
    $price_id = 'price_1SoAfyLhc0iibDcCLkcC0VcG';

    // Crear sesión de Checkout en Stripe (modo suscripción)
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'], // Solo tarjetas en modo TEST
        'line_items' => [[
            'price' => $price_id, // Usar Price ID del producto mensual
            'quantity' => 1,
        ]],
        'mode' => 'subscription', // ← IMPORTANTE: modo suscripción, no 'payment'
        'success_url' => $base_url . BASE_PATH . '/stripe/subscription-success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => $base_url . BASE_PATH . '/stripe/subscription-cancel.php',
        'customer_email' => $email, // Stripe creará el Customer automáticamente
        'metadata' => [
            'member_name' => $nombre,
            'member_phone' => $telefono,
        ],
        'subscription_data' => [
            'metadata' => [
                'member_name' => $nombre,
                'member_phone' => $telefono,
            ],
        ],
        // Configuración de facturación
        'billing_address_collection' => 'auto', // Solicitar dirección de facturación
        // Nota: En modo 'subscription', Stripe crea automáticamente el Customer
    ]);

    // Guardar en base de datos como 'incomplete' hasta que se complete el pago
    $stmt = getDB()->prepare("
        INSERT INTO socios
        (stripe_customer_id, stripe_subscription_id, nombre, email, telefono, estado, ip_address, user_agent)
        VALUES (?, ?, ?, ?, ?, 'incomplete', ?, ?)
    ");

    // Nota: stripe_customer_id y stripe_subscription_id se rellenarán después vía webhook
    // Por ahora guardamos la sesión para poder actualizarla después
    $stmt->execute([
        null, // customer_id se llenará en webhook
        null, // subscription_id se llenará en webhook
        $nombre,
        $email,
        $telefono,
        $_SERVER['REMOTE_ADDR'] ?? null,
        $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);

    $socio_id = getDB()->lastInsertId();

    // Devolver URL de checkout
    echo json_encode([
        'success' => true,
        'sessionId' => $checkout_session->id,
        'url' => $checkout_session->url
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
