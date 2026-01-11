<?php
/**
 * Crear Sesión del Portal de Stripe
 * Redirige al usuario al Billing Portal de Stripe donde puede gestionar su suscripción
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/stripe-php/init.php';

// Configurar Stripe
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

// Verificar que es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . url('index.php'));
    exit;
}

$customer_id = isset($_POST['customer_id']) ? trim($_POST['customer_id']) : '';

if (empty($customer_id)) {
    header("Location: " . url('stripe/manage-subscription.php?error=customer_id_missing'));
    exit;
}

try {
    // URL base
    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
                . '://' . $_SERVER['HTTP_HOST'];

    // Crear sesión del Billing Portal
    $session = \Stripe\BillingPortal\Session::create([
        'customer' => $customer_id,
        'return_url' => $base_url . BASE_PATH . '/stripe/manage-subscription.php?email=' . urlencode($_POST['email'] ?? ''),
    ]);

    // Redirigir al portal
    header("Location: " . $session->url);
    exit;

} catch (Exception $e) {
    error_log("Error creando portal session: " . $e->getMessage());
    header("Location: " . url('stripe/manage-subscription.php?error=' . urlencode($e->getMessage())));
    exit;
}
