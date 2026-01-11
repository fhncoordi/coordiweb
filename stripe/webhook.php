<?php
/**
 * Stripe Webhook Handler
 * Procesa eventos de Stripe (donaciones y suscripciones)
 *
 * CONFIGURACIÓN REQUERIDA:
 * 1. Ve a https://dashboard.stripe.com/test/webhooks
 * 2. Crea un nuevo endpoint apuntando a: https://tudominio.com/stripe/webhook.php
 * 3. Selecciona estos eventos:
 *    DONACIONES:
 *    - checkout.session.completed
 *    - payment_intent.succeeded
 *    - payment_intent.payment_failed
 *    - charge.refunded
 *
 *    SUSCRIPCIONES:
 *    - customer.subscription.created
 *    - customer.subscription.updated
 *    - customer.subscription.deleted
 *    - invoice.payment_succeeded
 *    - invoice.payment_failed
 *
 * 4. Copia el Signing Secret (empieza con whsec_)
 * 5. Pégalo en php/config.php como STRIPE_WEBHOOK_SECRET
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/stripe-php/init.php';
require_once __DIR__ . '/../php/db/connection.php';

// Establecer API key
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

// Obtener el Signing Secret (debe estar en config.php)
// define('STRIPE_WEBHOOK_SECRET', 'whsec_tu_webhook_secret_aqui');
$endpoint_secret = defined('STRIPE_WEBHOOK_SECRET') ? STRIPE_WEBHOOK_SECRET : '';

// Obtener el payload y la firma
$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

$event = null;

try {
    // Verificar la firma del webhook (solo si hay secret configurado)
    if ($endpoint_secret) {
        $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
    } else {
        // Modo de desarrollo: aceptar sin verificar (NO USAR EN PRODUCCIÓN)
        $event = json_decode($payload, false);
        error_log('ADVERTENCIA: Webhook procesado sin verificar firma. Configura STRIPE_WEBHOOK_SECRET');
    }
} catch(\UnexpectedValueException $e) {
    // Payload inválido
    http_response_code(400);
    error_log('Webhook error: Payload inválido');
    exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
    // Firma inválida
    http_response_code(400);
    error_log('Webhook error: Firma inválida - ' . $e->getMessage());
    exit();
}

// Procesar el evento
switch ($event->type) {
    case 'checkout.session.completed':
        $session = $event->data->object;

        // Determinar si es una donación o suscripción
        if ($session->mode === 'subscription') {
            // Es una suscripción - actualizar tabla socios
            try {
                if ($session->customer && $session->subscription) {
                    $customer = \Stripe\Customer::retrieve($session->customer);
                    $subscription = \Stripe\Subscription::retrieve($session->subscription);

                    execute("
                        UPDATE socios
                        SET stripe_customer_id = ?,
                            stripe_subscription_id = ?,
                            estado = ?,
                            fecha_inicio = NOW(),
                            fecha_proximo_cobro = ?
                        WHERE email = ?
                        AND stripe_subscription_id IS NULL
                        ORDER BY fecha_creacion DESC
                        LIMIT 1
                    ", [
                        $session->customer,
                        $session->subscription,
                        $subscription->status,
                        date('Y-m-d', $subscription->current_period_end),
                        $customer->email
                    ]);

                    error_log("Suscripción completada vía webhook: {$session->subscription} para {$customer->email}");
                }
            } catch (Exception $e) {
                error_log("Error actualizando suscripción en webhook: " . $e->getMessage());
            }
        } else {
            // Es una donación - actualizar tabla donaciones
            try {
                $donacion = fetchOne("SELECT * FROM donaciones WHERE stripe_session_id = ?", [$session->id]);

                if ($donacion && $donacion['estado'] === 'pending') {
                    execute("
                        UPDATE donaciones
                        SET estado = 'completed',
                            fecha_completado = NOW(),
                            stripe_payment_intent_id = ?,
                            metodo_pago = ?
                        WHERE stripe_session_id = ?
                    ", [
                        $session->payment_intent,
                        $session->payment_method_types[0] ?? 'unknown',
                        $session->id
                    ]);

                    error_log("Donación completada vía webhook: {$session->id}");

                    // TODO: Aquí puedes enviar email de confirmación
                    // require_once __DIR__ . '/../php/enviar_correo.php';
                    // enviar_email_confirmacion_donacion($donacion);
                }
            } catch (Exception $e) {
                error_log("Error actualizando donación en webhook: " . $e->getMessage());
            }
        }

        break;

    case 'payment_intent.succeeded':
        $paymentIntent = $event->data->object;

        // Log exitoso
        error_log("Payment Intent exitoso: {$paymentIntent->id}");

        // Ya se procesó en checkout.session.completed, pero podemos hacer acciones adicionales
        break;

    case 'payment_intent.payment_failed':
        $paymentIntent = $event->data->object;

        // Marcar donación como fallida
        try {
            execute("
                UPDATE donaciones
                SET estado = 'failed'
                WHERE stripe_payment_intent_id = ?
            ", [$paymentIntent->id]);

            error_log("Pago fallido: {$paymentIntent->id}");

            // TODO: Notificar al donante del fallo
        } catch (Exception $e) {
            error_log("Error marcando pago como fallido: " . $e->getMessage());
        }

        break;

    case 'charge.refunded':
        $charge = $event->data->object;

        // Marcar donación como reembolsada
        try {
            execute("
                UPDATE donaciones
                SET estado = 'refunded'
                WHERE stripe_payment_intent_id = ?
            ", [$charge->payment_intent]);

            error_log("Reembolso procesado: {$charge->id}");

            // TODO: Notificar al donante del reembolso
        } catch (Exception $e) {
            error_log("Error marcando reembolso: " . $e->getMessage());
        }

        break;

    // ============================================
    // EVENTOS DE SUSCRIPCIONES
    // ============================================

    case 'customer.subscription.created':
        $subscription = $event->data->object;

        // Log de creación de suscripción
        error_log("Suscripción creada: {$subscription->id} - Estado: {$subscription->status}");

        break;

    case 'customer.subscription.updated':
        $subscription = $event->data->object;

        // Actualizar estado de la suscripción
        try {
            execute("
                UPDATE socios
                SET estado = ?,
                    fecha_proximo_cobro = ?
                WHERE stripe_subscription_id = ?
            ", [
                $subscription->status,
                date('Y-m-d', $subscription->current_period_end),
                $subscription->id
            ]);

            error_log("Suscripción actualizada: {$subscription->id} - Nuevo estado: {$subscription->status}");
        } catch (Exception $e) {
            error_log("Error actualizando suscripción: " . $e->getMessage());
        }

        break;

    case 'customer.subscription.deleted':
        $subscription = $event->data->object;

        // Marcar suscripción como cancelada
        try {
            execute("
                UPDATE socios
                SET estado = 'canceled',
                    fecha_cancelacion = NOW(),
                    motivo_cancelacion = 'usuario'
                WHERE stripe_subscription_id = ?
            ", [$subscription->id]);

            error_log("Suscripción cancelada: {$subscription->id}");

            // TODO: Enviar email de despedida
        } catch (Exception $e) {
            error_log("Error marcando suscripción como cancelada: " . $e->getMessage());
        }

        break;

    case 'invoice.payment_succeeded':
        $invoice = $event->data->object;

        // Actualizar última factura pagada (cobros mensuales recurrentes)
        try {
            if ($invoice->subscription) {
                execute("
                    UPDATE socios
                    SET ultima_factura_pagada = NOW(),
                        estado = 'active'
                    WHERE stripe_subscription_id = ?
                ", [$invoice->subscription]);

                error_log("Pago mensual exitoso para suscripción: {$invoice->subscription}");

                // TODO: Enviar recibo por email
            }
        } catch (Exception $e) {
            error_log("Error registrando pago mensual: " . $e->getMessage());
        }

        break;

    case 'invoice.payment_failed':
        $invoice = $event->data->object;

        // Marcar suscripción con pago fallido
        try {
            if ($invoice->subscription) {
                execute("
                    UPDATE socios
                    SET estado = 'past_due'
                    WHERE stripe_subscription_id = ?
                ", [$invoice->subscription]);

                error_log("Pago mensual fallido para suscripción: {$invoice->subscription}");

                // TODO: Notificar al socio del fallo de pago
            }
        } catch (Exception $e) {
            error_log("Error marcando pago fallido: " . $e->getMessage());
        }

        break;

    default:
        // Evento no manejado
        error_log("Evento Stripe no manejado: {$event->type}");
}

// Responder a Stripe
http_response_code(200);
echo json_encode(['success' => true]);
