<?php
/**
 * Generador de Hash de Contraseñas
 * Coordicanarias CMS
 *
 * Ejecutar: php generate_password_hash.php
 * O desde navegador: http://tudominio.com/database/generate_password_hash.php
 *
 * IMPORTANTE: Eliminar este archivo después de generar el hash
 */

// Establecer charset UTF-8
header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Generador de Hash - Coordicanarias</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #243659;
            margin-top: 0;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        input[type='password'],
        input[type='text'] {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        button {
            background: #243659;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background: #1a2640;
        }
        .result {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 20px;
            border-radius: 4px;
            margin-top: 20px;
            word-break: break-all;
        }
        .result strong {
            display: block;
            margin-bottom: 10px;
            color: #155724;
        }
        .hash {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
        }
        .copy-btn {
            background: #28a745;
            margin-top: 10px;
            padding: 8px 20px;
            width: auto;
        }
        .copy-btn:hover {
            background: #218838;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔐 Generador de Hash de Contraseñas</h1>

        <div class='warning'>
            ⚠️ <strong>IMPORTANTE:</strong><br>
            - Elimina este archivo después de generar el hash<br>
            - No uses contraseñas débiles (mínimo 8 caracteres)<br>
            - Incluye mayúsculas, minúsculas y números
        </div>

        <form method='POST'>
            <div class='form-group'>
                <label for='password'>Contraseña:</label>
                <input type='password' id='password' name='password' required
                       placeholder='Mínimo 8 caracteres' minlength='8'>
            </div>

            <button type='submit'>Generar Hash</button>
        </form>";

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['password'])) {
    $password = $_POST['password'];

    // Validar contraseña
    $errors = [];

    if (strlen($password) < 8) {
        $errors[] = 'La contraseña debe tener al menos 8 caracteres';
    }

    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Debe contener al menos una letra mayúscula';
    }

    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Debe contener al menos una letra minúscula';
    }

    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Debe contener al menos un número';
    }

    if (!empty($errors)) {
        echo "<div class='error'>";
        echo "<strong>Error en la contraseña:</strong><ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul></div>";
    } else {
        // Generar hash
        $hash = password_hash($password, PASSWORD_DEFAULT);

        echo "
        <div class='result'>
            <strong>✅ Hash generado exitosamente:</strong>
            <div class='hash' id='hash'>$hash</div>
            <button class='copy-btn' onclick='copyHash()'>Copiar al portapapeles</button>

            <div style='margin-top: 20px; padding-top: 20px; border-top: 1px solid #c3e6cb;'>
                <strong>SQL para insertar usuario admin:</strong>
                <div class='hash' style='white-space: pre-wrap;'>INSERT INTO usuarios (username, email, password_hash, nombre_completo, rol, activo)
VALUES (
    'admin',
    'admin@coordicanarias.com',
    '$hash',
    'Administrador',
    'admin',
    1
);</div>
            </div>
        </div>

        <script>
        function copyHash() {
            const hash = document.getElementById('hash').textContent;
            navigator.clipboard.writeText(hash).then(() => {
                alert('Hash copiado al portapapeles');
            });
        }
        </script>
        ";
    }
}

echo "
    </div>
</body>
</html>";
