<?php
/**
 * Stripe Checkout - Crear Sesión de Pago
 * Coordicanarias - Sistema de Donaciones
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

    $importe = isset($input['amount']) ? floatval($input['amount']) : 0;
    $nombre = isset($input['name']) ? trim($input['name']) : '';
    $email = isset($input['email']) ? trim($input['email']) : '';
    $mensaje = isset($input['message']) ? trim($input['message']) : null;
    $anonimo = isset($input['anonymous']) ? (bool)$input['anonymous'] : false;

    // Validaciones
    if ($importe < 1) {
        throw new Exception('El importe mínimo es 1€');
    }

    if ($importe > 10000) {
        throw new Exception('El importe máximo es 10,000€');
    }

    if (empty($nombre) || empty($email)) {
        throw new Exception('Nombre y email son obligatorios');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email inválido');
    }

    // Convertir a centavos (Stripe usa centavos)
    $importe_centavos = (int)($importe * 100);

    // URL base para success/cancel
    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
                . '://' . $_SERVER['HTTP_HOST'];

    // Determinar métodos de pago según el modo
    // TEST: Solo tarjetas (Bizum no disponible en test)
    // LIVE: Tarjetas + Bizum (customer_balance)
    $payment_methods = STRIPE_MODE === 'live'
        ? ['card', 'customer_balance'] // LIVE: Tarjeta + Bizum
        : ['card']; // TEST: Solo tarjeta

    // Crear sesión de Checkout en Stripe
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => $payment_methods,
        'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Donación a Coordicanarias',
                    'description' => 'Apoyo a la Coordinadora de Personas con Discapacidad Física de Canarias',
                ],
                'unit_amount' => $importe_centavos,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $base_url . BASE_PATH . '/stripe/success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => $base_url . BASE_PATH . '/stripe/cancel.php',
        'customer_email' => $email,
        'metadata' => [
            'donor_name' => $nombre,
            'donor_message' => $mensaje,
            'is_anonymous' => $anonimo ? 'yes' : 'no',
        ],
    ]);

    // Guardar en base de datos como pending
    $stmt = getDB()->prepare("
        INSERT INTO donaciones
        (stripe_session_id, nombre, email, importe, mensaje, es_anonimo, estado, ip_address, user_agent)
        VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, ?)
    ");

    $stmt->execute([
        $checkout_session->id,
        $nombre,
        $email,
        $importe,
        $mensaje,
        $anonimo ? 1 : 0,
        $_SERVER['REMOTE_ADDR'] ?? null,
        $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);

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
