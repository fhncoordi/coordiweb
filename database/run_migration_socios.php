<?php
/**
 * Script de migración: Crear tabla socios
 * Ejecutar UNA SOLA VEZ desde: https://coordicanarias.com/database/run_migration_socios.php
 *
 * IMPORTANTE: Eliminar este archivo después de ejecutarlo
 */

require_once __DIR__ . '/../php/config.php';

// Verificar que config.php tiene las constantes de BD
if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASS')) {
    die('ERROR: Constantes de base de datos no definidas en config.php');
}

try {
    // Conectar a la base de datos
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "<h2>Ejecutando migración: Tabla socios</h2>";
    echo "<p>Base de datos: <strong>" . DB_NAME . "</strong></p>";
    echo "<hr>";

    // Leer el archivo SQL
    $sql = file_get_contents(__DIR__ . '/create_table_socios.sql');

    // Eliminar comentarios y líneas vacías para evitar problemas
    $sql = preg_replace('/^--.*$/m', '', $sql); // Comentarios --
    $sql = preg_replace('/^\s*$/m', '', $sql);  // Líneas vacías

    // Ejecutar el SQL
    $pdo->exec($sql);

    echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0;'>";
    echo "<strong style='color: #155724;'>✓ Tabla 'socios' creada exitosamente</strong>";
    echo "</div>";

    // Verificar que la tabla existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'socios'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✓ Verificación: La tabla existe en la base de datos</p>";

        // Mostrar estructura de la tabla
        $stmt = $pdo->query("DESCRIBE socios");
        echo "<h3>Estructura de la tabla:</h3>";
        echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse;'>";
        echo "<tr style='background: #f0f0f0;'><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $stmt->fetch()) {
            echo "<tr>";
            echo "<td><strong>" . htmlspecialchars($row['Field']) . "</strong></td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    echo "<hr>";
    echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;'>";
    echo "<strong>⚠️ IMPORTANTE:</strong> Elimina este archivo (run_migration_socios.php) por seguridad.";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 20px 0;'>";
    echo "<strong style='color: #721c24;'>✗ Error al crear la tabla:</strong><br>";
    echo htmlspecialchars($e->getMessage());
    echo "</div>";
}
?>
