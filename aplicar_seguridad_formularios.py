#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script para actualizar todos los formularios con seguridad anti-bot
Coordicanarias - 2025
"""

import os
import re

# Directorio base
BASE_DIR = r"C:\Users\Odiseo\Documents\coordi"

# Archivos a actualizar
FILES = [
    os.path.join(BASE_DIR, "index.php"),
    os.path.join(BASE_DIR, "areas", "accesibilidad.php"),
    os.path.join(BASE_DIR, "areas", "aintegral.php"),
    os.path.join(BASE_DIR, "areas", "alegal.php"),
    os.path.join(BASE_DIR, "areas", "empleo.php"),
    os.path.join(BASE_DIR, "areas", "forminno.php"),
    os.path.join(BASE_DIR, "areas", "igualdadpm.php"),
    os.path.join(BASE_DIR, "areas", "ocio.php"),
    os.path.join(BASE_DIR, "areas", "participaca.php"),
    os.path.join(BASE_DIR, "areas", "politica-cookies.php"),
    os.path.join(BASE_DIR, "areas", "politica-privacidad.php"),
]

def process_file(filepath):
    """Procesa un archivo agregando las medidas de seguridad"""

    if not os.path.exists(filepath):
        print(f"[!] Archivo no encontrado: {filepath}")
        return False

    print(f"[*] Procesando: {os.path.basename(filepath)}")

    # Leer el archivo
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
    except Exception as e:
        print(f"    [ERROR] No se pudo leer el archivo: {e}")
        return False

    original_content = content
    is_in_areas = "\\areas\\" in filepath or "/areas/" in filepath
    changes_made = False

    # 1. Agregar require del helper si no existe
    if "form_security_helper.php" not in content:
        helper_path = "../php/form_security_helper.php" if is_in_areas else "php/form_security_helper.php"

        # Buscar donde agregar el require (después de otros requires)
        if "require_once" in content:
            # Buscar el último require_once en las primeras líneas
            lines = content.split('\n')
            last_require_line = -1
            for i, line in enumerate(lines[:50]):  # Solo primeras 50 líneas
                if 'require_once' in line and '.php' in line:
                    last_require_line = i

            if last_require_line >= 0:
                lines.insert(last_require_line + 1, f"require_once __DIR__ . '/{helper_path}';")
                content = '\n'.join(lines)
                print("    [OK] Agregado require del helper")
                changes_made = True

    # 2. Agregar scripts de reCAPTCHA en el <head> si no existen
    if "generar_script_recaptcha" not in content:
        recaptcha_block = """    <!-- reCAPTCHA v3 -->
    <?php echo generar_script_recaptcha(); ?>

    <!-- Configuracion para JavaScript -->
    <script>
        window.RECAPTCHA_SITE_KEY = '<?php echo obtener_recaptcha_site_key(); ?>';
    </script>
"""
        # Buscar </head> y agregar antes
        if '</head>' in content:
            content = content.replace('</head>', recaptcha_block + '</head>')
            print("    [OK] Agregado script de reCAPTCHA en <head>")
            changes_made = True

    # 3. Agregar campos de seguridad en el formulario si no existen
    if "generar_campos_seguridad" not in content:
        security_fields = """
                                    <!-- CAMPOS DE SEGURIDAD ANTI-BOT -->
                                    <?php echo generar_campos_seguridad(); ?>
                                    <!-- FIN CAMPOS DE SEGURIDAD -->
"""
        # Buscar la línea con input hidden name="area" y agregar después
        pattern = r'(<input type="hidden" name="area"[^>]*>)'
        if re.search(pattern, content):
            content = re.sub(pattern, r'\1' + security_fields, content)
            print("    [OK] Agregados campos de seguridad en formulario")
            changes_made = True

    # 4. Agregar script de seguridad antes del </body> si no existe
    if "form-security.js" not in content:
        js_path = "../js/form-security.js" if is_in_areas else "js/form-security.js"
        security_script = f"""    <!-- Script de seguridad de formularios -->
    <script src="<?= url('{js_path}') ?>"></script>

"""
        # Buscar </body> y agregar antes
        if '</body>' in content:
            content = content.replace('</body>', security_script + '</body>')
            print("    [OK] Agregado script de seguridad antes de </body>")
            changes_made = True

    # Si hubo cambios, guardar el archivo
    if changes_made:
        try:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(content)
            print("    [GUARDADO] Archivo actualizado correctamente")
            return True
        except Exception as e:
            print(f"    [ERROR] No se pudo guardar el archivo: {e}")
            return False
    else:
        print("    [INFO] No se realizaron cambios (ya estaba actualizado)")
        return False

def main():
    """Funcion principal"""
    print("="*60)
    print("Aplicando Seguridad Anti-Bot a Formularios")
    print("="*60)
    print()

    count = 0
    updated = 0

    for filepath in FILES:
        if process_file(filepath):
            updated += 1
        count += 1
        print()

    print("="*60)
    print("RESUMEN")
    print("="*60)
    print(f"[*] {count} archivos procesados")
    print(f"[*] {updated} archivos actualizados")
    print(f"[*] {count - updated} archivos sin cambios")
    print()
    print("Verifica manualmente los archivos para confirmar que los cambios")
    print("se aplicaron correctamente.")
    print()

if __name__ == "__main__":
    main()
