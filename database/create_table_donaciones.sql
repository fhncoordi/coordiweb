-- ============================================
-- TABLA: donaciones
-- Coordicanarias - Sistema de Donaciones con Stripe
-- ============================================
USE coordica_crc;


CREATE TABLE IF NOT EXISTS `donaciones` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `stripe_session_id` VARCHAR(255) NOT NULL UNIQUE COMMENT 'ID de la sesión de Stripe Checkout',
  `stripe_payment_intent_id` VARCHAR(255) DEFAULT NULL COMMENT 'ID del Payment Intent de Stripe',
  `nombre` VARCHAR(255) NOT NULL COMMENT 'Nombre del donante',
  `email` VARCHAR(255) NOT NULL COMMENT 'Email del donante',
  `telefono` VARCHAR(50) DEFAULT NULL COMMENT 'Teléfono del donante (opcional)',
  `importe` DECIMAL(10,2) NOT NULL COMMENT 'Importe en euros (ej: 25.00)',
  `moneda` VARCHAR(3) DEFAULT 'EUR' COMMENT 'Moneda (EUR por defecto)',
  `metodo_pago` VARCHAR(50) DEFAULT NULL COMMENT 'Método de pago usado (card, bizum, etc)',
  `estado` ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending' COMMENT 'Estado del pago',
  `mensaje` TEXT DEFAULT NULL COMMENT 'Mensaje opcional del donante',
  `es_anonimo` TINYINT(1) DEFAULT 0 COMMENT '1 si el donante quiere ser anónimo',
  `ip_address` VARCHAR(45) DEFAULT NULL COMMENT 'IP del donante',
  `user_agent` VARCHAR(500) DEFAULT NULL COMMENT 'Navegador del donante',
  `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de inicio del proceso',
  `fecha_completado` DATETIME DEFAULT NULL COMMENT 'Fecha en que se completó el pago',
  `metadata` JSON DEFAULT NULL COMMENT 'Datos adicionales en formato JSON',
  `notas_admin` TEXT DEFAULT NULL COMMENT 'Notas internas del administrador',

  INDEX `idx_email` (`email`),
  INDEX `idx_estado` (`estado`),
  INDEX `idx_fecha_creacion` (`fecha_creacion`),
  INDEX `idx_fecha_completado` (`fecha_completado`),
  INDEX `idx_stripe_payment_intent` (`stripe_payment_intent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de donaciones procesadas con Stripe';

-- ============================================
-- INSERTAR DATOS DE PRUEBA (opcional - comentado)
-- ============================================

/*
INSERT INTO `donaciones` (`stripe_session_id`, `stripe_payment_intent_id`, `nombre`, `email`, `importe`, `estado`, `fecha_completado`) VALUES
('cs_test_ejemplo1', 'pi_test_ejemplo1', 'Juan Pérez', 'juan@example.com', 25.00, 'completed', NOW()),
('cs_test_ejemplo2', 'pi_test_ejemplo2', 'María García', 'maria@example.com', 50.00, 'completed', NOW()),
('cs_test_ejemplo3', NULL, 'Pedro López', 'pedro@example.com', 10.00, 'pending', NULL);
*/
