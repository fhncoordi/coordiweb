<?php
/**
 * Modelo: Testimonio
 * Coordicanarias CMS
 *
 * Gestión de testimonios y casos de éxito
 */

require_once __DIR__ . '/../db/connection.php';

class Testimonio {
    /**
     * Obtener todos los testimonios
     *
     * @param bool $solo_activos Si true, solo retorna testimonios activos
     * @param bool $solo_destacados Si true, solo retorna testimonios destacados
     * @return array Lista de testimonios ordenados
     */
    public static function getAll($solo_activos = false, $solo_destacados = false) {
        $sql = "SELECT * FROM testimonios WHERE 1=1";

        $params = [];

        if ($solo_activos) {
            $sql .= " AND activo = 1";
        }

        if ($solo_destacados) {
            $sql .= " AND destacado = 1";
        }

        $sql .= " ORDER BY orden ASC, fecha_creacion DESC";

        return fetchAll($sql, $params);
    }

    /**
     * Obtener testimonio por ID
     *
     * @param int $id ID del testimonio
     * @return array|null Datos del testimonio o null si no existe
     */
    public static function getById($id) {
        $sql = "SELECT * FROM testimonios WHERE id = ?";
        return fetchOne($sql, [$id]);
    }

    /**
     * Crear nuevo testimonio
     *
     * @param array $datos Datos del testimonio
     * @return int|false ID del testimonio creado o false si falla
     */
    public static function create($datos) {
        $sql = "INSERT INTO testimonios (
                    nombre, profesion, texto, foto, rating, orden, destacado, activo
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $datos['nombre'],
            $datos['profesion'],
            $datos['texto'],
            $datos['foto'] ?? null,
            $datos['rating'],
            $datos['orden'],
            $datos['destacado'],
            $datos['activo']
        ];

        if (execute($sql, $params)) {
            return lastInsertId();
        }

        return false;
    }

    /**
     * Actualizar testimonio existente
     *
     * @param int $id ID del testimonio a actualizar
     * @param array $datos Datos a actualizar
     * @return bool True si se actualizó correctamente
     */
    public static function update($id, $datos) {
        // Validar que el testimonio existe
        $testimonio = self::getById($id);
        if (!$testimonio) {
            return false;
        }

        $sql = "UPDATE testimonios SET
                nombre = ?,
                profesion = ?,
                texto = ?,
                foto = ?,
                rating = ?,
                orden = ?,
                destacado = ?,
                activo = ?
                WHERE id = ?";

        $params = [
            $datos['nombre'],
            $datos['profesion'],
            $datos['texto'],
            $datos['foto'] ?? $testimonio['foto'],
            $datos['rating'],
            $datos['orden'],
            $datos['destacado'],
            $datos['activo'],
            $id
        ];

        return execute($sql, $params);
    }

    /**
     * Eliminar testimonio (soft delete)
     *
     * @param int $id ID del testimonio
     * @return bool True si se eliminó correctamente
     */
    public static function delete($id) {
        return execute("UPDATE testimonios SET activo = 0 WHERE id = ?", [$id]);
    }

    /**
     * Eliminar testimonio permanentemente
     *
     * @param int $id ID del testimonio
     * @return bool True si se eliminó correctamente
     */
    public static function deletePermanente($id) {
        return execute("DELETE FROM testimonios WHERE id = ?", [$id]);
    }

    /**
     * Cambiar estado activo/inactivo
     *
     * @param int $id ID del testimonio
     * @param int $activo 1 para activar, 0 para desactivar
     * @return bool True si se actualizó correctamente
     */
    public static function toggleActivo($id, $activo) {
        return execute("UPDATE testimonios SET activo = ? WHERE id = ?", [$activo, $id]);
    }

    /**
     * Cambiar estado destacado
     *
     * @param int $id ID del testimonio
     * @param int $destacado 1 para destacar, 0 para no destacar
     * @return bool True si se actualizó correctamente
     */
    public static function toggleDestacado($id, $destacado) {
        return execute("UPDATE testimonios SET destacado = ? WHERE id = ?", [$destacado, $id]);
    }

    /**
     * Validar datos de testimonio
     *
     * @param array $datos Datos a validar
     * @param int $id_testimonio_actual ID del testimonio actual (opcional, para edición)
     * @return array Lista de errores (vacío si todo es válido)
     */
    public static function validar($datos, $id_testimonio_actual = null) {
        $errores = [];

        // Validar nombre
        if (empty($datos['nombre'])) {
            $errores[] = 'El nombre es requerido';
        } elseif (strlen($datos['nombre']) > 200) {
            $errores[] = 'El nombre no puede tener más de 200 caracteres';
        }

        // Validar profesión
        if (empty($datos['profesion'])) {
            $errores[] = 'La profesión es requerida';
        } elseif (strlen($datos['profesion']) > 200) {
            $errores[] = 'La profesión no puede tener más de 200 caracteres';
        }

        // Validar texto
        if (empty($datos['texto'])) {
            $errores[] = 'El texto del testimonio es requerido';
        } elseif (strlen($datos['texto']) < 20) {
            $errores[] = 'El texto del testimonio debe tener al menos 20 caracteres';
        }

        // Validar rating (debe ser numérico entre 1 y 5)
        if (empty($datos['rating'])) {
            $errores[] = 'La valoración es requerida';
        } elseif (!is_numeric($datos['rating']) || $datos['rating'] < 1 || $datos['rating'] > 5) {
            $errores[] = 'La valoración debe ser un número entre 1 y 5';
        }

        // Validar orden (debe ser numérico)
        if (isset($datos['orden']) && !is_numeric($datos['orden'])) {
            $errores[] = 'El orden debe ser un número';
        }

        return $errores;
    }

    /**
     * Obtener el siguiente número de orden disponible
     *
     * @return int Siguiente número de orden
     */
    public static function getSiguienteOrden() {
        $result = fetchOne("SELECT MAX(orden) as max_orden FROM testimonios");
        return ($result['max_orden'] ?? 0) + 1;
    }

    /**
     * Subir foto de testimonio
     *
     * @param array $archivo Archivo de $_FILES
     * @param int $testimonio_id ID del testimonio (opcional, para nombrar el archivo)
     * @return array ['success' => bool, 'mensaje' => string, 'ruta' => string|null]
     */
    public static function subirFoto($archivo, $testimonio_id = null) {
        // Validar que se subió archivo
        if (!isset($archivo) || $archivo['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => false, 'mensaje' => 'No se seleccionó ningún archivo', 'ruta' => null];
        }

        // Validar errores de subida
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'mensaje' => 'Error al subir el archivo', 'ruta' => null];
        }

        // Validar tamaño (máximo 5MB)
        $max_size = 5 * 1024 * 1024; // 5MB
        if ($archivo['size'] > $max_size) {
            return ['success' => false, 'mensaje' => 'El archivo es demasiado grande (máximo 5MB)', 'ruta' => null];
        }

        // Validar tipo MIME
        $tipos_permitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime_type, $tipos_permitidos)) {
            return ['success' => false, 'mensaje' => 'Tipo de archivo no permitido. Solo se permiten imágenes (JPG, PNG, GIF, WEBP)', 'ruta' => null];
        }

        // Obtener extensión
        $extension = '';
        switch ($mime_type) {
            case 'image/jpeg':
            case 'image/jpg':
                $extension = 'jpg';
                break;
            case 'image/png':
                $extension = 'png';
                break;
            case 'image/gif':
                $extension = 'gif';
                break;
            case 'image/webp':
                $extension = 'webp';
                break;
        }

        // Crear directorio si no existe
        $directorio = __DIR__ . '/../../uploads/testimonios';
        if (!file_exists($directorio)) {
            mkdir($directorio, 0755, true);
        }

        // Generar nombre único
        $nombre_base = $testimonio_id ? "testimonio_{$testimonio_id}" : "testimonio_" . uniqid();
        $nombre_archivo = $nombre_base . '.' . $extension;
        $ruta_completa = $directorio . '/' . $nombre_archivo;

        // Si ya existe un archivo con ese nombre, agregar timestamp
        if (file_exists($ruta_completa)) {
            $nombre_archivo = $nombre_base . '_' . time() . '.' . $extension;
            $ruta_completa = $directorio . '/' . $nombre_archivo;
        }

        // Mover archivo
        if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
            // Retornar ruta relativa desde la raíz del proyecto
            $ruta_relativa = 'uploads/testimonios/' . $nombre_archivo;
            return ['success' => true, 'mensaje' => 'Foto subida correctamente', 'ruta' => $ruta_relativa];
        } else {
            return ['success' => false, 'mensaje' => 'Error al guardar el archivo', 'ruta' => null];
        }
    }

    /**
     * Eliminar foto de testimonio
     *
     * @param string $ruta Ruta de la foto a eliminar
     * @return bool True si se eliminó correctamente
     */
    public static function eliminarFoto($ruta) {
        if (empty($ruta)) {
            return false;
        }

        $ruta_completa = __DIR__ . '/../../' . $ruta;

        if (file_exists($ruta_completa)) {
            return unlink($ruta_completa);
        }

        return false;
    }

    /**
     * Contar testimonios totales
     *
     * @param bool $solo_activos Si true, solo cuenta testimonios activos
     * @return int Total de testimonios
     */
    public static function contar($solo_activos = true) {
        $sql = "SELECT COUNT(*) as total FROM testimonios";

        if ($solo_activos) {
            $sql .= " WHERE activo = 1";
        }

        $result = fetchOne($sql);
        return $result['total'] ?? 0;
    }
}
