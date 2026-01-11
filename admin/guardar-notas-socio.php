<?php
/**
 * Guardar Notas de Admin para Socios
 * Coordicanarias
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/core/auth.php';
require_once __DIR__ . '/../php/db/connection.php';

// Requerir autenticación
requireLogin();

// Verificar que es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: socios.php');
    exit;
}

$socio_id = isset($_POST['socio_id']) ? (int)$_POST['socio_id'] : 0;
$notas = isset($_POST['notas']) ? trim($_POST['notas']) : '';

if ($socio_id > 0) {
    try {
        execute("
            UPDATE socios
            SET notas_admin = ?
            WHERE id = ?
        ", [$notas, $socio_id]);

        header('Location: socios.php?success=notas_guardadas');
        exit;
    } catch (Exception $e) {
        error_log("Error guardando notas: " . $e->getMessage());
        header('Location: socios.php?error=error_guardando_notas');
        exit;
    }
} else {
    header('Location: socios.php?error=socio_invalido');
    exit;
}
