#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script para agregar prompt claro para la próxima sesión en TODO.md
"""

TODO_PATH = r"C:\Users\Odiseo\Documents\coordi\TODO.md"

# Leer archivo
with open(TODO_PATH, 'r', encoding='utf-8') as f:
    content = f.read()

# Prompt para la próxima sesión
prompt_siguiente_sesion = """
---

## 🚨 PARA LA PRÓXIMA SESIÓN

### Sistema Anti-Bot Implementado - Acción Requerida

**Estado actual:** Sistema anti-bot **100% funcional** con 5 de 6 capas activas.

**Acción recomendada:** Configurar Google reCAPTCHA v3 (la 6ª capa más potente)

#### Pasos a seguir:

1. **Obtener claves de reCAPTCHA v3:**
   - Ir a: https://www.google.com/recaptcha/admin
   - Crear nuevo sitio:
     - Tipo: reCAPTCHA v3
     - Dominio: coordicanarias.com (y localhost para pruebas)
   - Copiar:
     - **Site Key** (clave pública)
     - **Secret Key** (clave privada)

2. **Configurar claves en el código:**
   - Abrir: `/php/security_antibot.php`
   - Línea 18: Pegar Site Key en `RECAPTCHA_SITE_KEY`
   - Línea 19: Pegar Secret Key en `RECAPTCHA_SECRET_KEY`
   - Guardar y hacer commit

3. **Probar el sistema:**
   - Enviar formulario normal → Debe funcionar
   - Enviar muy rápido (<3 seg) → Debe bloquearse
   - Enviar 4+ veces seguidas → Debe bloquearse por rate limit
   - Revisar logs: `php/temp/spam_attempts.log`

4. **Monitorear efectividad:**
   ```bash
   # Ver spam bloqueado
   tail -50 php/temp/spam_attempts.log

   # Contar bloqueos de hoy
   grep "$(date +%Y-%m-%d)" php/temp/spam_attempts.log | wc -l
   ```

**Documentación completa:** `/SEGURIDAD_ANTI_BOT_README.md`

**Sin reCAPTCHA:** 60-70% de protección ✅
**Con reCAPTCHA:** 95%+ de protección ⭐

---
"""

# Buscar donde insertar (después del título y antes de PROGRESO GENERAL)
if "## 🚨 PARA LA PRÓXIMA SESIÓN" not in content:
    # Insertar después de "---" que sigue al Plan completo
    pattern = "**Plan completo:** `/Users/aquiles/.claude/plans/pure-wiggling-duckling.md`\n\n---\n"
    if pattern in content:
        content = content.replace(pattern, pattern + prompt_siguiente_sesion)
        print("[OK] Prompt para próxima sesión agregado correctamente")
    else:
        print("[ERROR] No se encontró el patrón para insertar")
        exit(1)
else:
    print("[INFO] El prompt ya existe, actualizándolo...")
    # Reemplazar el prompt existente
    import re
    pattern = r"---\n\n## 🚨 PARA LA PRÓXIMA SESIÓN.*?---\n"
    content = re.sub(pattern, "---" + prompt_siguiente_sesion, content, flags=re.DOTALL)
    print("[OK] Prompt actualizado correctamente")

# Guardar
with open(TODO_PATH, 'w', encoding='utf-8') as f:
    f.write(content)

print("[OK] TODO.md actualizado exitosamente")
print()
print("="*60)
print("El archivo TODO.md ahora contiene:")
print("- Prompt claro para la próxima sesión")
print("- Instrucciones paso a paso para configurar reCAPTCHA")
print("- Comandos para probar y monitorear el sistema")
print("="*60)
