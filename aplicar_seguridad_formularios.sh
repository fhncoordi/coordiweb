#!/bin/bash
# Script para actualizar todos los formularios con seguridad anti-bot
# Coordicanarias - 2025

echo "=== Aplicando Seguridad Anti-Bot a Formularios ==="
echo ""

# Directorio base
BASE_DIR="C:/Users/Odiseo/Documents/coordi"

# Archivos a actualizar
FILES=(
    "$BASE_DIR/index.php"
    "$BASE_DIR/areas/accesibilidad.php"
    "$BASE_DIR/areas/aintegral.php"
    "$BASE_DIR/areas/alegal.php"
    "$BASE_DIR/areas/empleo.php"
    "$BASE_DIR/areas/forminno.php"
    "$BASE_DIR/areas/igualdadpm.php"
    "$BASE_DIR/areas/ocio.php"
    "$BASE_DIR/areas/participaca.php"
    "$BASE_DIR/areas/politica-cookies.php"
    "$BASE_DIR/areas/politica-privacidad.php"
)

# Contador de archivos procesados
COUNT=0

# Procesar cada archivo
for FILE in "${FILES[@]}"; do
    if [ ! -f "$FILE" ]; then
        echo "⚠️  Archivo no encontrado: $FILE"
        continue
    fi

    echo "📝 Procesando: $(basename "$FILE")"

    # 1. Agregar require del helper si no existe
    if ! grep -q "form_security_helper.php" "$FILE"; then
        # Detectar si es un archivo en areas/ o raíz
        if [[ "$FILE" == *"/areas/"* ]]; then
            # Para archivos en areas/, usar ../
            sed -i '/require_once.*Noticia\.php/a require_once __DIR__ . '\''/../php/form_security_helper.php'\'';' "$FILE" 2>/dev/null || \
            sed -i '/require_once.*config\.php/a require_once __DIR__ . '\''/../php/form_security_helper.php'\'';' "$FILE" 2>/dev/null || \
            echo "   ⚠️  No se pudo agregar el require (esto es normal si el archivo tiene estructura diferente)"
        else
            # Para index.php en raíz, usar ./
            sed -i '/require_once.*Noticia\.php/a require_once __DIR__ . '\''/php/form_security_helper.php'\'';' "$FILE" 2>/dev/null || \
            sed -i '/require_once.*config\.php/a require_once __DIR__ . '\''/php/form_security_helper.php'\'';' "$FILE" 2>/dev/null || \
            echo "   ⚠️  No se pudo agregar el require (esto es normal si el archivo tiene estructura diferente)"
        fi
    fi

    # 2. Agregar scripts de reCAPTCHA en el <head> si no existen
    if ! grep -q "generar_script_recaptcha" "$FILE"; then
        sed -i '/<\/head>/i\    <!-- reCAPTCHA v3 -->\n    <?php echo generar_script_recaptcha(); ?>\n\n    <!-- Configuración para JavaScript -->\n    <script>\n        window.RECAPTCHA_SITE_KEY = '\''<?php echo obtener_recaptcha_site_key(); ?>'\'';\n    </script>' "$FILE" 2>/dev/null
    fi

    # 3. Agregar campos de seguridad en el formulario si no existen
    if ! grep -q "generar_campos_seguridad" "$FILE"; then
        # Buscar la línea con input hidden name="area" y agregar después
        sed -i '/<input type="hidden" name="area"/a\                                    <!-- CAMPOS DE SEGURIDAD ANTI-BOT -->\n                                    <?php echo generar_campos_seguridad(); ?>\n                                    <!-- FIN CAMPOS DE SEGURIDAD -->' "$FILE" 2>/dev/null
    fi

    # 4. Agregar script de seguridad antes del </body> si no existe
    if ! grep -q "form-security.js" "$FILE"; then
        # Detectar si es un archivo en areas/ o raíz
        if [[ "$FILE" == *"/areas/"* ]]; then
            sed -i '/<\/body>/i\    <!-- Script de seguridad de formularios -->\n    <script src="<?= url('\''js/form-security.js'\'') ?>"></script>' "$FILE" 2>/dev/null || \
            sed -i '/<\/body>/i\    <!-- Script de seguridad de formularios -->\n    <script src="../js/form-security.js"></script>' "$FILE" 2>/dev/null
        else
            sed -i '/<\/body>/i\    <!-- Script de seguridad de formularios -->\n    <script src="<?= url('\''js/form-security.js'\'') ?>"></script>' "$FILE" 2>/dev/null || \
            sed -i '/<\/body>/i\    <!-- Script de seguridad de formularios -->\n    <script src="js/form-security.js"></script>' "$FILE" 2>/dev/null
        fi
    fi

    COUNT=$((COUNT + 1))
    echo "   ✅ Completado"
    echo ""
done

echo "=== Resumen ==="
echo "✅ $COUNT archivos procesados"
echo ""
echo "Nota: Revisa manualmente los archivos para verificar que los cambios"
echo "se aplicaron correctamente. Algunos archivos pueden requerir ajustes"
echo "manuales si tienen una estructura diferente."
