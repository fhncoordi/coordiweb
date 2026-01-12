#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script para integrar validaciones anti-bot en enviar_correo.php
"""

import re

FILEPATH = r"C:\Users\Odiseo\Documents\coordi\php\enviar_correo.php"

print("="*60)
print("Integrando Validaciones Anti-Bot en enviar_correo.php")
print("="*60)
print()

# Leer el archivo
with open(FILEPATH, 'r', encoding='utf-8') as f:
    content = f.read()

original_content = content
changes_made = []

# 1. Agregar require del sistema de seguridad
if "security_antibot.php" not in content:
    pattern = r"(require_once 'config\.php';)"
    replacement = r"\1\n\n// Cargar sistema de seguridad anti-bot\nrequire_once 'security_antibot.php';"
    content = re.sub(pattern, replacement, content)
    changes_made.append("1. Agregado require de security_antibot.php")
    print("[OK] Agregado require de security_antibot.php")

# 2. Agregar bloque de validaciones anti-bot
if "validar_antibot" not in content:
    validation_block = """
// ============================================
// VALIDACIONES ANTI-BOT
// ============================================

// Preparar datos del formulario para validación anti-bot
$datos_antibot = [
    'nombre' => $_POST['txtName'] ?? '',
    'email' => $_POST['txtEmail'] ?? '',
    'mensaje' => $_POST['txtMsg'] ?? '',
    'website' => $_POST['website'] ?? '',  // Honeypot
    'timestamp' => $_POST['form_timestamp'] ?? '',  // Tiempo de carga
    'csrf_token' => $_POST['csrf_token'] ?? '',  // Token CSRF
    'recaptcha_token' => $_POST['recaptcha_token'] ?? ''  // reCAPTCHA v3
];

// Ejecutar todas las validaciones anti-bot
$resultado_antibot = validar_antibot($datos_antibot);

// Si las validaciones anti-bot fallan, bloquear y registrar
if (!$resultado_antibot['valido']) {
    $errores_encoded = urlencode('Mensaje bloqueado por seguridad. Si crees que es un error, contacta por teléfono.');

    // Determinar la página de origen
    $pagina_origen = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.html';
    $pagina_origen = basename(parse_url($pagina_origen, PHP_URL_PATH));

    // Ajustar ruta si viene de areas/
    if (strpos($_SERVER['HTTP_REFERER'], '/areas/') !== false) {
        $pagina_origen = '../areas/' . $pagina_origen;
    } else {
        $pagina_origen = '../' . $pagina_origen;
    }

    // Log detallado del intento bloqueado (para debugging)
    error_log("Formulario bloqueado por anti-bot: " . json_encode($resultado_antibot));

    header("Location: $pagina_origen?error=" . $errores_encoded . "#contact");
    exit;
}

"""
    # Buscar donde insertar (después de verificar_origen y antes de detectar área)
    pattern = r"(if \(!verificar_origen\(\$dominios_permitidos\)\) \{\s+header\(\"Location: \.\./index\.html\?error=origen_invalido\"\);\s+exit;\s+\})\s+(// Detectar el área desde el formulario)"
    replacement = r"\1\n" + validation_block + r"\2"
    content = re.sub(pattern, replacement, content, flags=re.DOTALL)
    changes_made.append("2. Agregado bloque de validaciones anti-bot")
    print("[OK] Agregado bloque de validaciones anti-bot")

# 3. Agregar estilo CSS para badge de seguridad
if "security-badge" not in content:
    css_addition = """        .security-badge {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 11px;
            margin-left: 10px;
        }
"""
    # Buscar después de .area-badge
    pattern = r"(\.area-badge \{[^\}]+\})"
    replacement = r"\1\n" + css_addition
    content = re.sub(pattern, replacement, content, flags=re.DOTALL)
    changes_made.append("3. Agregado CSS para security-badge")
    print("[OK] Agregado CSS para security-badge")

# 4. Agregar badge de seguridad en el email (después del area-badge)
if "security-badge" in content and "resultado_antibot['scores']['recaptcha']" not in content:
    badge_code = """
// Agregar badge de seguridad si reCAPTCHA está activo
if (isset(\$resultado_antibot['scores']['recaptcha'])) {
    \$score = \$resultado_antibot['scores']['recaptcha'];
    \$cuerpo_email .= \"<span class='security-badge'>✓ Verificado (Score: \" . number_format(\$score, 2) . \")</span>\";
}

\$cuerpo_email .= \"
"""
    # Buscar donde agregar (después de area-badge)
    pattern = r"(\$cuerpo_email = \"[^\"]+'area-badge'>📧 \" \. strtoupper\(htmlspecialchars\(\$nombre_area, ENT_QUOTES, 'UTF-8'\)\) \. \"</span>)"
    replacement = r"\1\";\n" + badge_code
    content = re.sub(pattern, replacement, content, flags=re.DOTALL)
    changes_made.append("4. Agregado badge de seguridad en email")
    print("[OK] Agregado badge de seguridad en email")

# 5. Agregar IP en el footer del email
if "obtener_ip_cliente()" not in content:
    # Buscar la línea del footer y agregar IP
    pattern = r"(Email recibido desde formulario de contacto \| \" \. date\('d/m/Y H:i:s'\) \. \")"
    replacement = r"\1<br>\n                IP: \" . obtener_ip_cliente() . \""
    content = re.sub(pattern, replacement, content)
    changes_made.append("5. Agregada IP en footer del email")
    print("[OK] Agregada IP en footer del email")

# 6. Agregar limpieza de rate limit tras envío exitoso
if "limpiar_rate_limit_exitoso" not in content:
    cleanup_code = """
// Si el email se envió exitosamente, limpiar el rate limiter
if (\$email_enviado) {
    limpiar_rate_limit_exitoso();
}

"""
    # Buscar antes de la redirección final
    pattern = r"(// Determinar la página de origen\s+\$pagina_origen = isset\(\$_SERVER\['HTTP_REFERER'\]\))"
    replacement = cleanup_code + r"\1"
    # Buscar la última ocurrencia
    matches = list(re.finditer(pattern, content, flags=re.DOTALL))
    if matches:
        last_match = matches[-1]
        content = content[:last_match.start()] + cleanup_code + content[last_match.start():]
        changes_made.append("6. Agregada limpieza de rate limit")
        print("[OK] Agregada limpieza de rate limit tras envío exitoso")

# 7. Desactivar modo debug de SMTP
if "SMTPDebug = 3" in content or "SMTPDebug = 2" in content:
    pattern = r"// Activar modo debug \(TEMPORAL - quitar después\)\s+\$mail->SMTPDebug = \d+;[^\n]*\n\s+\$mail->Debugoutput = '[^']+';[^\n]*"
    replacement = "// Desactivar modo debug en producción\n        \\$mail->SMTPDebug = 0; // 0=sin debug, 1=cliente, 2=cliente+servidor, 3=detallado"
    content = re.sub(pattern, replacement, content)
    changes_made.append("7. Desactivado modo debug de SMTP")
    print("[OK] Desactivado modo debug de SMTP")

# 8. Actualizar año en el comentario
content = content.replace("Coordicanarias - 2024", "Coordicanarias - 2025")
content = content.replace("* Usa PHPMailer con SMTP de Google Workspace\n */",
                         "* Usa PHPMailer con SMTP de Google Workspace\n * INCLUYE PROTECCIÓN ANTI-BOT MULTICAPA\n */")
changes_made.append("8. Actualizado año y comentario header")
print("[OK] Actualizado comentario header")

# Guardar si hubo cambios
if content != original_content:
    with open(FILEPATH, 'w', encoding='utf-8') as f:
        f.write(content)
    print()
    print("="*60)
    print("RESUMEN")
    print("="*60)
    print(f"[OK] Archivo actualizado exitosamente")
    print(f"[OK] Total de cambios aplicados: {len(changes_made)}")
    print()
    for i, change in enumerate(changes_made, 1):
        print(f"  {i}. {change}")
    print()
else:
    print()
    print("[INFO] No se realizaron cambios (archivo ya actualizado)")
    print()
