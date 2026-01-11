-- =====================================================
-- Tabla: socios
-- Descripción: Almacena información de socios con suscripción mensual recurrente (5€/mes)
-- Fecha: 2026-01-10
-- =====================================================
USE coordica_crc;


CREATE TABLE IF NOT EXISTS `socios` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `stripe_customer_id` VARCHAR(255) UNIQUE COMMENT 'ID del cliente en Stripe (cus_xxxxx)',
    `stripe_subscription_id` VARCHAR(255) UNIQUE COMMENT 'ID de la suscripción en Stripe (sub_xxxxx)',
    `nombre` VARCHAR(255) NOT NULL COMMENT 'Nombre completo del socio',
    `email` VARCHAR(255) NOT NULL COMMENT 'Email del socio',
    `telefono` VARCHAR(50) DEFAULT NULL COMMENT 'Teléfono del socio (opcional)',
    `estado` ENUM('active', 'past_due', 'canceled', 'incomplete', 'trialing', 'unpaid') DEFAULT 'incomplete' COMMENT 'Estado de la suscripción según Stripe',
    `fecha_inicio` DATETIME NULL COMMENT 'Fecha de inicio de la suscripción (cuando se activa)',
    `fecha_proximo_cobro` DATE NULL COMMENT 'Fecha del próximo cobro mensual',
    `metodo_pago` VARCHAR(50) DEFAULT NULL COMMENT 'Método de pago último usado (card, sepa_debit, etc)',
    `ultima_factura_pagada` DATETIME DEFAULT NULL COMMENT 'Fecha de la última factura pagada exitosamente',
    `ip_address` VARCHAR(45) DEFAULT NULL COMMENT 'IP desde donde se registró',
    `user_agent` VARCHAR(500) DEFAULT NULL COMMENT 'Navegador/dispositivo usado',
    `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de registro en el sistema',
    `fecha_cancelacion` DATETIME DEFAULT NULL COMMENT 'Fecha de cancelación de la suscripción',
    `motivo_cancelacion` VARCHAR(255) DEFAULT NULL COMMENT 'Razón de cancelación (usuario, admin, stripe)',
    `notas_admin` TEXT DEFAULT NULL COMMENT 'Notas internas del administrador',

    -- Índices para mejorar rendimiento
    INDEX `idx_email` (`email`),
    INDEX `idx_estado` (`estado`),
    INDEX `idx_stripe_customer` (`stripe_customer_id`),
    INDEX `idx_stripe_subscription` (`stripe_subscription_id`),
    INDEX `idx_fecha_inicio` (`fecha_inicio`),
    INDEX `idx_fecha_proximo_cobro` (`fecha_proximo_cobro`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Socios con suscripción mensual recurrente de 5€/mes';

-- =====================================================
-- Notas de implementación:
-- =====================================================
-- Estados de suscripción (según Stripe):
--   - incomplete: Suscripción creada pero primer pago no completado
--   - trialing: En período de prueba (si se configura)
--   - active: Suscripción activa y al día con los pagos
--   - past_due: Pago fallido, esperando reintento
--   - unpaid: Todos los reintentos fallaron
--   - canceled: Suscripción cancelada
--
-- El estado se actualiza automáticamente vía webhooks de Stripe
-- =====================================================
