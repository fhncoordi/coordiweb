-- =====================================================
-- Añadir campo para cancelación programada
-- Coordicanarias - Sistema de Socios
-- =====================================================
USE coordica_crc;

-- Añadir campo para saber si la suscripción está programada para cancelarse
ALTER TABLE `socios`
ADD COLUMN `cancelar_al_final_periodo` TINYINT(1) DEFAULT 0 COMMENT '1 si la suscripción se cancelará al final del período actual' AFTER `fecha_proximo_cobro`;

-- Añadir índice para consultas rápidas
ALTER TABLE `socios`
ADD INDEX `idx_cancelar_periodo` (`cancelar_al_final_periodo`);

-- Nota: Ejecutar este script UNA SOLA VEZ
-- Para revertir (si es necesario):
-- ALTER TABLE `socios` DROP COLUMN `cancelar_al_final_periodo`;
