# Base de Datos - Coordicanarias CMS

Instrucciones para configurar la base de datos MySQL.

---

## 📋 Instalación

### 1. Crear la base de datos

En cPanel o phpMyAdmin, crear:
- **Base de datos:** `coordica_crc` (o el nombre que prefieras)
- **Usuario:** Con permisos completos
- **Agregar usuario a la BD** con todos los privilegios

### 2. Ejecutar el schema

Desde phpMyAdmin:
1. Seleccionar la base de datos
2. Ir a pestaña "SQL"
3. Copiar y pegar todo el contenido de `schema.sql`
4. Ejecutar

Esto creará:
- ✅ 8 tablas (usuarios, areas, proyectos, servicios, beneficios, testimonios, configuracion, registro_actividad)
- ✅ 6 áreas temáticas precargadas
- ✅ Configuración inicial del sitio

### 3. Crear usuario administrador

⚠️ **IMPORTANTE:** El schema NO incluye el usuario admin por seguridad.

**Opción A: Desde phpMyAdmin (más fácil)**

1. Ir a tabla `usuarios`
2. Click en "Insertar"
3. Llenar los campos:
   - `username`: `admin`
   - `email`: `admin@coordicanarias.com`
   - `password_hash`: *Generar con la Opción B*
   - `nombre_completo`: `Administrador`
   - `rol`: `admin`
   - `activo`: `1`

**Opción B: Generar hash de password**

Usar el script `generate_password_hash.php`:

```bash
php generate_password_hash.php
```

Esto generará el hash de tu contraseña para copiar en el INSERT.

**Opción C: SQL directo**

```sql
INSERT INTO usuarios (username, email, password_hash, nombre_completo, rol, activo)
VALUES (
    'admin',
    'admin@coordicanarias.com',
    'TU_HASH_AQUI',  -- Generar con Opción B
    'Administrador',
    'admin',
    1
);
```

---

## 🔧 Archivos

- `schema.sql` - Esquema completo de la BD (estructura + datos base)
- `generate_password_hash.php` - Utilidad para generar hash de passwords
- `README.md` - Este archivo

---

## ⚠️ Seguridad

- ❌ **NO** subir a git archivos con passwords o hashes reales
- ✅ Usar contraseñas fuertes (mínimo 8 caracteres, mayúsculas, números)
- ✅ Cambiar password después del primer login
- ✅ Crear usuarios adicionales según necesidad (no usar solo admin)

---

## 📝 Credenciales de BD

Las credenciales están en `/php/config.php` (archivo NO trackeado en git):

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'coordica_crc');
define('DB_USER', 'coordica_crc');
define('DB_PASS', '***********');
```

---

*Última actualización: 2025-12-28*
