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
require_once __DIR__ . '/../php/emails_donaciones.php';

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

/**
 * Helper: Obtener current_period_end de una suscripción
 * En versiones recientes de Stripe API, este campo está en items.data[0]
 */
function getSubscriptionPeriodEnd($subscription) {
    // Método nuevo: buscar en subscription items
    if (isset($subscription->items->data[0]->current_period_end)) {
        return $subscription->items->data[0]->current_period_end;
    }

    // Método antiguo: buscar en subscription directamente (fallback)
    if (isset($subscription->current_period_end)) {
        return $subscription->current_period_end;
    }

    return null;
}

/**
 * Helper: Obtener current_period_start de una suscripción
 */
function getSubscriptionPeriodStart($subscription) {
    // Método nuevo: buscar en subscription items
    if (isset($subscription->items->data[0]->current_period_start)) {
        return $subscription->items->data[0]->current_period_start;
    }

    // Método antiguo: buscar en subscription directamente (fallback)
    if (isset($subscription->current_period_start)) {
        return $subscription->current_period_start;
    }

    return null;
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

                    // Obtener fechas usando helper (busca en items.data[0] o fallback a subscription)
                    $periodStart = getSubscriptionPeriodStart($subscription);
                    $periodEnd = getSubscriptionPeriodEnd($subscription);

                    // Debug: registrar valores
                    error_log("DEBUG Subscription - current_period_end: " . ($periodEnd ?? 'NULL'));
                    error_log("DEBUG Subscription - current_period_start: " . ($periodStart ?? 'NULL'));
                    error_log("DEBUG Subscription - status: " . $subscription->status);

                    // Calcular fecha del próximo cobro
                    $fechaProximoCobro = $periodEnd ? date('Y-m-d', $periodEnd) : null;

                    error_log("DEBUG fecha_proximo_cobro calculada: " . ($fechaProximoCobro ?? 'NULL'));

                    // Actualizar el registro en la BD
                    $stmt = getDB()->prepare("
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
                    ");
                    $stmt->execute([
                        $session->customer,
                        $session->subscription,
                        $subscription->status,
                        $fechaProximoCobro,
                        $customer->email
                    ]);

                    $filasActualizadas = $stmt->rowCount();
                    error_log("UPDATE socios: {$filasActualizadas} filas actualizadas para {$customer->email}");

                    // Buscar el socio actualizado
                    $socioNuevo = fetchOne("SELECT * FROM socios WHERE stripe_subscription_id = ?", [$session->subscription]);

                    if ($socioNuevo) {
                        error_log("Socio encontrado: ID {$socioNuevo['id']}, intentando enviar email...");
                        $resultadoEmail = enviarEmailBienvenidaSocio($socioNuevo);

                        if ($resultadoEmail) {
                            error_log("✅ Email de bienvenida enviado correctamente a {$customer->email}");
                        } else {
                            error_log("❌ Error: enviarEmailBienvenidaSocio() devolvió false para {$customer->email}");
                        }
                    } else {
                        error_log("❌ Error: No se encontró el socio con subscription_id {$session->subscription}");
                    }

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

                    // Enviar email de confirmación al donante
                    $donacionActualizada = fetchOne("SELECT * FROM donaciones WHERE stripe_session_id = ?", [$session->id]);
                    if ($donacionActualizada) {
                        enviarEmailConfirmacionDonacion($donacionActualizada);
                    }
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

    case 'customer.updated':
        $customer = $event->data->object;

        // Actualizar datos del cliente (email, nombre, teléfono)
        try {
            // Extraer datos del customer
            $email = $customer->email;
            $nombre = $customer->name ?? null;
            $telefono = $customer->phone ?? null;

            // Si no hay nombre en el customer, intentar sacarlo de metadata
            if (!$nombre && isset($customer->metadata->member_name)) {
                $nombre = $customer->metadata->member_name;
            }

            // Si no hay teléfono en el customer, intentar sacarlo de metadata
            if (!$telefono && isset($customer->metadata->member_phone)) {
                $telefono = $customer->metadata->member_phone;
            }

            // Construir el UPDATE dinámicamente según qué campos tenemos
            $campos = [];
            $valores = [];

            if ($email) {
                $campos[] = "email = ?";
                $valores[] = $email;
            }

            if ($nombre) {
                $campos[] = "nombre = ?";
                $valores[] = $nombre;
            }

            if ($telefono) {
                $campos[] = "telefono = ?";
                $valores[] = $telefono;
            }

            if (count($campos) > 0) {
                $valores[] = $customer->id; // WHERE stripe_customer_id = ?

                $sql = "UPDATE socios SET " . implode(", ", $campos) . " WHERE stripe_customer_id = ?";

                execute($sql, $valores);

                error_log("Customer actualizado: {$customer->id} - Email: " . ($email ?? 'N/A') . ", Nombre: " . ($nombre ?? 'N/A'));
            } else {
                error_log("Customer actualizado pero sin campos relevantes: {$customer->id}");
            }

        } catch (Exception $e) {
            error_log("Error actualizando customer: " . $e->getMessage());
        }

        break;

    case 'customer.subscription.updated':
        $subscription = $event->data->object;

        // Actualizar estado de la suscripción
        try {
            // Obtener fecha usando helper
            $periodEnd = getSubscriptionPeriodEnd($subscription);

            // Calcular fecha del próximo cobro
            $fechaProximoCobro = $periodEnd ? date('Y-m-d', $periodEnd) : null;

            // Verificar si está programada para cancelarse
            // Stripe puede usar dos campos diferentes:
            // 1. cancel_at_period_end (boolean) - Método antiguo
            // 2. cancel_at (timestamp) - Método moderno
            $cancelarAlFinal = 0;

            if (isset($subscription->cancel_at_period_end) && $subscription->cancel_at_period_end) {
                $cancelarAlFinal = 1;
                error_log("DEBUG subscription.updated - Cancelación detectada vía cancel_at_period_end");
            } elseif (isset($subscription->cancel_at) && $subscription->cancel_at !== null) {
                $cancelarAlFinal = 1;
                error_log("DEBUG subscription.updated - Cancelación detectada vía cancel_at: " . date('Y-m-d H:i:s', $subscription->cancel_at));
            }

            error_log("DEBUG subscription.updated - current_period_end: " . ($periodEnd ?? 'NULL'));
            error_log("DEBUG subscription.updated - fecha calculada: " . ($fechaProximoCobro ?? 'NULL'));
            error_log("DEBUG subscription.updated - cancelar_al_final_periodo: " . ($cancelarAlFinal ? '1' : '0'));

            execute("
                UPDATE socios
                SET estado = ?,
                    fecha_proximo_cobro = ?,
                    cancelar_al_final_periodo = ?
                WHERE stripe_subscription_id = ?
            ", [
                $subscription->status,
                $fechaProximoCobro,
                $cancelarAlFinal,
                $subscription->id
            ]);

            error_log("Suscripción actualizada: {$subscription->id} - Estado: {$subscription->status} - Cancelar al final: " . ($cancelarAlFinal ? 'Sí' : 'No'));
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
                    motivo_cancelacion = 'usuario',
                    cancelar_al_final_periodo = 0
                WHERE stripe_subscription_id = ?
            ", [$subscription->id]);

            error_log("Suscripción cancelada: {$subscription->id}");

            // Enviar email de despedida
            $socio = fetchOne("SELECT * FROM socios WHERE stripe_subscription_id = ?", [$subscription->id]);
            if ($socio) {
                enviarEmailCancelacionSocio($socio);
            }
        } catch (Exception $e) {
            error_log("Error marcando suscripción como cancelada: " . $e->getMessage());
        }

        break;

    case 'invoice.payment_succeeded':
        $invoice = $event->data->object;

        // Actualizar última factura pagada (cobros mensuales recurrentes)
        try {
            if ($invoice->subscription) {
                // Obtener la suscripción para tener la fecha del próximo cobro
                $subscription = \Stripe\Subscription::retrieve($invoice->subscription);

                // Obtener fecha usando helper
                $periodEnd = getSubscriptionPeriodEnd($subscription);
                $fechaProximoCobro = $periodEnd ? date('Y-m-d', $periodEnd) : null;

                error_log("DEBUG invoice.payment_succeeded - Actualizando próximo cobro: " . ($fechaProximoCobro ?? 'NULL'));

                execute("
                    UPDATE socios
                    SET ultima_factura_pagada = NOW(),
                        estado = 'active',
                        fecha_proximo_cobro = ?
                    WHERE stripe_subscription_id = ?
                ", [$fechaProximoCobro, $invoice->subscription]);

                error_log("Pago mensual exitoso para suscripción: {$invoice->subscription}");

                // Enviar recibo mensual al socio
                $socio = fetchOne("SELECT * FROM socios WHERE stripe_subscription_id = ?", [$invoice->subscription]);
                if ($socio) {
                    enviarEmailReciboMensual($socio);
                }
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

                // Notificar al socio del fallo de pago
                $socio = fetchOne("SELECT * FROM socios WHERE stripe_subscription_id = ?", [$invoice->subscription]);
                if ($socio) {
                    enviarEmailPagoFallido($socio);
                }
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
