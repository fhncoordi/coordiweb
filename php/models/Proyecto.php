<?php
/**
 * Modelo: Proyecto
 * Coordicanarias CMS
 *
 * Gestión de proyectos destacados de la organización
 */

require_once __DIR__ . '/../db/connection.php';

class Proyecto {
    /**
     * Obtener todos los proyectos
     *
     * @param bool $solo_activos Si true, solo retorna proyectos activos
     * @param int $area_id Si se especifica, filtra por área
     * @param bool $solo_destacados Si true, solo proyectos destacados
     * @return array Lista de proyectos ordenados por orden
     */
    public static function getAll($solo_activos = false, $area_id = null, $solo_destacados = false) {
        $sql = "SELECT p.*, a.nombre as area_nombre, a.slug as area_slug
                FROM proyectos p
                LEFT JOIN areas a ON p.area_id = a.id
                WHERE 1=1";

        $params = [];

        if ($solo_activos) {
            $sql .= " AND p.activo = 1";
        }

        if ($area_id !== null) {
            $sql .= " AND p.area_id = ?";
            $params[] = $area_id;
        }

        if ($solo_destacados) {
            $sql .= " AND p.destacado = 1";
        }

        $sql .= " ORDER BY p.orden ASC, p.fecha_creacion DESC";

        return fetchAll($sql, $params);
    }

    /**
     * Obtener proyectos de un área específica
     *
     * @param int $area_id ID del área
     * @param bool $solo_activos Si true, solo proyectos activos
     * @return array Lista de proyectos del área
     */
    public static function getByArea($area_id, $solo_activos = true) {
        return self::getAll($solo_activos, $area_id, false);
    }

    /**
     * Obtener proyectos agrupados por área
     *
     * @param bool $solo_activos Si true, solo proyectos y áreas activas
     * @param int $area_id Si se especifica, filtra por área
     * @return array Array asociativo con áreas como keys y proyectos como values
     */
    public static function getAllAgrupados($solo_activos = true, $area_id = null) {
        $proyectos = self::getAll($solo_activos, $area_id);
        $agrupados = [];

        foreach ($proyectos as $proyecto) {
            $area_nombre = $proyecto['area_nombre'] ?? 'Sin área';
            if (!isset($agrupados[$area_nombre])) {
                $agrupados[$area_nombre] = [];
            }
            $agrupados[$area_nombre][] = $proyecto;
        }

        return $agrupados;
    }

    /**
     * Obtener proyectos destacados para homepage
     *
     * @param int $limite Número de proyectos a obtener
     * @return array Lista de proyectos destacados
     */
    public static function getDestacados($limite = 12) {
        $sql = "SELECT p.*, a.nombre as area_nombre, a.slug as area_slug
                FROM proyectos p
                LEFT JOIN areas a ON p.area_id = a.id
                WHERE p.activo = 1 AND p.destacado = 1
                ORDER BY p.orden ASC
                LIMIT " . (int)$limite;

        return fetchAll($sql);
    }

    /**
     * Obtener proyecto por ID
     *
     * @param int $id ID del proyecto
     * @return array|null Datos del proyecto o null si no existe
     */
    public static function getById($id) {
        $sql = "SELECT p.*, a.nombre as area_nombre
                FROM proyectos p
                LEFT JOIN areas a ON p.area_id = a.id
                WHERE p.id = ?";

        return fetchOne($sql, [$id]);
    }

    /**
     * Crear nuevo proyecto
     *
     * @param array $datos Datos del proyecto
     * @return int|false ID del proyecto creado o false si falla
     */
    public static function create($datos) {
        $sql = "INSERT INTO proyectos (
                    titulo, descripcion, imagen, area_id, categorias,
                    destacado, orden, activo
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $datos['titulo'],
            $datos['descripcion'],
            $datos['imagen'],
            $datos['area_id'],
            $datos['categorias'],
            $datos['destacado'],
            $datos['orden'],
            $datos['activo']
        ];

        if (execute($sql, $params)) {
            return lastInsertId();
        }

        return false;
    }

    /**
     * Actualizar proyecto existente
     *
     * @param int $id ID del proyecto a actualizar
     * @param array $datos Datos a actualizar
     * @return bool True si se actualizó correctamente
     */
    public static function update($id, $datos) {
        // Validar que el proyecto existe
        $proyecto = self::getById($id);
        if (!$proyecto) {
            return false;
        }

        $sql = "UPDATE proyectos SET
                titulo = ?,
                descripcion = ?,
                imagen = ?,
                area_id = ?,
                categorias = ?,
                destacado = ?,
                orden = ?,
                activo = ?
                WHERE id = ?";

        $params = [
            $datos['titulo'],
            $datos['descripcion'],
            $datos['imagen'],
            $datos['area_id'],
            $datos['categorias'],
            $datos['destacado'],
            $datos['orden'],
            $datos['activo'],
            $id
        ];

        return execute($sql, $params);
    }

    /**
     * Eliminar proyecto (soft delete)
     *
     * @param int $id ID del proyecto
     * @return bool True si se eliminó correctamente
     */
    public static function delete($id) {
        return execute("UPDATE proyectos SET activo = 0 WHERE id = ?", [$id]);
    }

    /**
     * Eliminar proyecto permanentemente
     *
     * @param int $id ID del proyecto
     * @return bool True si se eliminó correctamente
     */
    public static function deletePermanente($id) {
        return execute("DELETE FROM proyectos WHERE id = ?", [$id]);
    }

    /**
     * Cambiar estado activo/inactivo
     *
     * @param int $id ID del proyecto
     * @param int $activo 1 para activar, 0 para desactivar
     * @return bool True si se actualizó correctamente
     */
    public static function toggleActivo($id, $activo) {
        return execute("UPDATE proyectos SET activo = ? WHERE id = ?", [$activo, $id]);
    }

    /**
     * Cambiar estado destacado
     *
     * @param int $id ID del proyecto
     * @param int $destacado 1 para destacar, 0 para quitar
     * @return bool True si se actualizó correctamente
     */
    public static function toggleDestacado($id, $destacado) {
        return execute("UPDATE proyectos SET destacado = ? WHERE id = ?", [$destacado, $id]);
    }

    /**
     * Validar datos de proyecto
     *
     * @param array $datos Datos a validar
     * @param int $id_proyecto_actual ID del proyecto actual (opcional, para edición)
     * @return array Lista de errores (vacío si todo es válido)
     */
    public static function validar($datos, $id_proyecto_actual = null) {
        $errores = [];

        // Validar título
        if (empty($datos['titulo'])) {
            $errores[] = 'El título es requerido';
        } elseif (strlen($datos['titulo']) > 200) {
            $errores[] = 'El título no puede tener más de 200 caracteres';
        }

        // Validar área
        if (empty($datos['area_id'])) {
            $errores[] = 'Debe seleccionar un área';
        } else {
            // Verificar que el área existe
            $area = fetchOne("SELECT id FROM areas WHERE id = ?", [$datos['area_id']]);
            if (!$area) {
                $errores[] = 'El área seleccionada no existe';
            }
        }

        // Validar orden (debe ser numérico)
        if (isset($datos['orden']) && !is_numeric($datos['orden'])) {
            $errores[] = 'El orden debe ser un número';
        }

        return $errores;
    }

    /**
     * Obtener todas las áreas para el selector
     *
     * @return array Lista de áreas activas
     */
    public static function getAreas() {
        return fetchAll("SELECT id, nombre FROM areas WHERE activo = 1 ORDER BY orden ASC");
    }

    /**
     * Obtener categorías únicas de todos los proyectos
     *
     * @return array Lista de categorías únicas
     */
    public static function getCategorias() {
        $proyectos = fetchAll("SELECT categorias FROM proyectos WHERE categorias IS NOT NULL AND categorias != ''");

        $categorias_unicas = [];
        foreach ($proyectos as $proyecto) {
            $cats = explode(',', $proyecto['categorias']);
            foreach ($cats as $cat) {
                $cat = trim($cat);
                if ($cat && !in_array($cat, $categorias_unicas)) {
                    $categorias_unicas[] = $cat;
                }
            }
        }

        sort($categorias_unicas);
        return $categorias_unicas;
    }

    /**
     * Obtener el siguiente número de orden disponible
     *
     * @return int Siguiente número de orden
     */
    public static function getSiguienteOrden() {
        $result = fetchOne("SELECT MAX(orden) as max_orden FROM proyectos");
        return ($result['max_orden'] ?? 0) + 1;
    }

    /**
     * Subir imagen de proyecto
     *
     * @param array $archivo Archivo $_FILES['imagen']
     * @param string $nombre_antiguo Nombre del archivo antiguo (para edición)
     * @return array ['success' => bool, 'message' => string, 'filename' => string|null]
     */
    public static function subirImagen($archivo, $nombre_antiguo = null) {
        // Directorio de destino
        $upload_dir = __DIR__ . '/../../uploads/proyectos/';

        // Crear directorio si no existe
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Validar que se subió un archivo
        if (!isset($archivo) || $archivo['error'] === UPLOAD_ERR_NO_FILE) {
            // Si hay nombre antiguo, mantenerlo
            if ($nombre_antiguo) {
                return [
                    'success' => true,
                    'message' => 'Se mantuvo la imagen anterior',
                    'filename' => $nombre_antiguo
                ];
            }
            return [
                'success' => false,
                'message' => 'No se seleccionó ningún archivo',
                'filename' => null
            ];
        }

        // Validar errores de subida
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'message' => 'Error al subir el archivo: ' . $archivo['error'],
                'filename' => null
            ];
        }

        // Validar tamaño (máximo 5MB)
        $max_size = 5 * 1024 * 1024; // 5MB
        if ($archivo['size'] > $max_size) {
            return [
                'success' => false,
                'message' => 'El archivo es demasiado grande. Máximo 5MB',
                'filename' => null
            ];
        }

        // Validar tipo de archivo
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime_type, $allowed_types)) {
            return [
                'success' => false,
                'message' => 'Tipo de archivo no permitido. Solo JPG, PNG, GIF, WEBP',
                'filename' => null
            ];
        }

        // Generar nombre único
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombre_archivo = 'proyecto_' . uniqid() . '.' . $extension;
        $ruta_completa = $upload_dir . $nombre_archivo;

        // Mover archivo
        if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
            // Eliminar imagen antigua si existe y es diferente
            if ($nombre_antiguo && $nombre_antiguo !== $nombre_archivo) {
                $ruta_antigua = $upload_dir . basename($nombre_antiguo);
                if (file_exists($ruta_antigua)) {
                    unlink($ruta_antigua);
                }
            }

            return [
                'success' => true,
                'message' => 'Imagen subida correctamente',
                'filename' => 'uploads/proyectos/' . $nombre_archivo
            ];
        }

        return [
            'success' => false,
            'message' => 'Error al mover el archivo al directorio de destino',
            'filename' => null
        ];
    }
}
