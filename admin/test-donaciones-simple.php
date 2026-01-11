<?php
/**
 * Test simplificado de donaciones.php SIN includes
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/core/auth.php';
require_once __DIR__ . '/../php/core/security.php';

// Requerir autenticación
requireLogin();

// Establecer headers de seguridad
setSecurityHeaders();

// Obtener donaciones
$donaciones = fetchAll("
    SELECT *
    FROM donaciones
    ORDER BY fecha_creacion DESC
    LIMIT 10
");

// Calcular estadísticas
$stats = fetchOne("
    SELECT
        COUNT(*) as total_donaciones,
        SUM(CASE WHEN estado = 'completed' THEN importe ELSE 0 END) as total_recaudado,
        SUM(CASE WHEN estado = 'completed' THEN 1 ELSE 0 END) as completadas
    FROM donaciones
");

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Test Donaciones Simple</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
    </style>
</head>
<body>
    <h1>✅ Test Donaciones Simple - SIN Header/Sidebar</h1>
    <p class="success">Si ves esto, el problema está en header.php o sidebar.php</p>

    <h2>Estadísticas</h2>
    <ul>
        <li>Total Donaciones: <strong><?= $stats['total_donaciones'] ?></strong></li>
        <li>Total Recaudado: <strong><?= number_format($stats['total_recaudado'], 2) ?> €</strong></li>
        <li>Completadas: <strong><?= $stats['completadas'] ?></strong></li>
    </ul>

    <h2>Últimas Donaciones</h2>
    <?php if (empty($donaciones)): ?>
        <p>No hay donaciones</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Nombre</th>
                    <th>Importe</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donaciones as $d): ?>
                <tr>
                    <td><?= $d['id'] ?></td>
                    <td><?= date('d/m/Y', strtotime($d['fecha_creacion'])) ?></td>
                    <td><?= htmlspecialchars($d['nombre']) ?></td>
                    <td><?= number_format($d['importe'], 2) ?> €</td>
                    <td><?= $d['estado'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <hr>
    <p><a href="donaciones.php">← Ir a donaciones.php completo</a></p>
    <p><a href="index.php">← Volver al Dashboard</a></p>
</body>
</html>
