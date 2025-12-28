<?php
/**
 * Modelo: Area
 * Coordicanarias CMS
 *
 * Gestión de las 6 áreas temáticas de la organización
 */

require_once __DIR__ . '/../db/connection.php';

class Area {
    /**
     * Obtener todas las áreas
     *
     * @param bool $solo_activas Si true, solo retorna áreas activas
     * @return array Lista de áreas ordenadas por campo 'orden'
     */
    public static function getAll($solo_activas = false) {
        $sql = "SELECT * FROM areas";

        if ($solo_activas) {
            $sql .= " WHERE activo = 1";
        }

        $sql .= " ORDER BY orden ASC, nombre ASC";

        return fetchAll($sql);
    }

    /**
     * Obtener área por ID
     *
     * @param int $id ID del área
     * @return array|null Datos del área o null si no existe
     */
    public static function getById($id) {
        return fetchOne("SELECT * FROM areas WHERE id = ?", [$id]);
    }

    /**
     * Obtener área por slug
     *
     * @param string $slug Slug del área
     * @return array|null Datos del área o null si no existe
     */
    public static function getBySlug($slug) {
        return fetchOne("SELECT * FROM areas WHERE slug = ?", [$slug]);
    }

    /**
     * Actualizar área existente
     *
     * @param int $id ID del área a actualizar
     * @param array $datos Datos a actualizar [nombre, slug, descripcion, imagen_banner, color_tema, orden, activo]
     * @return bool True si se actualizó correctamente
     */
    public static function update($id, $datos) {
        // Validar que el área existe
        $area = self::getById($id);
        if (!$area) {
            return false;
        }

        $sql = "UPDATE areas SET
                nombre = ?,
                slug = ?,
                descripcion = ?,
                imagen_banner = ?,
                color_tema = ?,
                orden = ?,
                activo = ?
                WHERE id = ?";

        $params = [
            $datos['nombre'],
            $datos['slug'],
            $datos['descripcion'],
            $datos['imagen_banner'],
            $datos['color_tema'],
            $datos['orden'],
            $datos['activo'],
            $id
        ];

        return execute($sql, $params);
    }

    /**
     * Cambiar estado activo/inactivo de un área
     *
     * @param int $id ID del área
     * @param int $activo 1 para activar, 0 para desactivar
     * @return bool True si se actualizó correctamente
     */
    public static function toggleActivo($id, $activo) {
        return execute("UPDATE areas SET activo = ? WHERE id = ?", [$activo, $id]);
    }

    /**
     * Validar que el slug es único (excepto para el área actual)
     *
     * @param string $slug Slug a validar
     * @param int $id_area_actual ID del área actual (para excluir de la validación)
     * @return bool True si el slug está disponible
     */
    public static function isSlugUnico($slug, $id_area_actual = null) {
        if ($id_area_actual) {
            $result = fetchOne("SELECT id FROM areas WHERE slug = ? AND id != ?", [$slug, $id_area_actual]);
        } else {
            $result = fetchOne("SELECT id FROM areas WHERE slug = ?", [$slug]);
        }

        return $result === null;
    }

    /**
     * Generar slug desde un nombre
     *
     * @param string $nombre Nombre del área
     * @return string Slug generado (URL-friendly)
     */
    public static function generarSlug($nombre) {
        // Convertir a minúsculas
        $slug = mb_strtolower($nombre, 'UTF-8');

        // Reemplazar caracteres especiales
        $slug = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'],
            ['a', 'e', 'i', 'o', 'u', 'n', 'u'],
            $slug
        );

        // Reemplazar espacios y caracteres no válidos con guiones
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

        // Eliminar guiones al inicio y final
        $slug = trim($slug, '-');

        return $slug;
    }

    /**
     * Validar datos de área
     *
     * @param array $datos Datos a validar
     * @param int $id_area_actual ID del área actual (opcional, para edición)
     * @return array Lista de errores (vacío si todo es válido)
     */
    public static function validar($datos, $id_area_actual = null) {
        $errores = [];

        // Validar nombre
        if (empty($datos['nombre'])) {
            $errores[] = 'El nombre del área es requerido';
        } elseif (strlen($datos['nombre']) > 100) {
            $errores[] = 'El nombre no puede tener más de 100 caracteres';
        }

        // Validar slug
        if (empty($datos['slug'])) {
            $errores[] = 'El slug es requerido';
        } elseif (!preg_match('/^[a-z0-9-]+$/', $datos['slug'])) {
            $errores[] = 'El slug solo puede contener letras minúsculas, números y guiones';
        } elseif (strlen($datos['slug']) > 100) {
            $errores[] = 'El slug no puede tener más de 100 caracteres';
        } elseif (!self::isSlugUnico($datos['slug'], $id_area_actual)) {
            $errores[] = 'El slug ya está en uso por otra área';
        }

        // Validar color tema
        if (!empty($datos['color_tema'])) {
            if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $datos['color_tema'])) {
                $errores[] = 'El color tema debe tener formato hexadecimal (#RRGGBB)';
            }
        }

        // Validar orden
        if (!is_numeric($datos['orden'])) {
            $errores[] = 'El orden debe ser un número';
        }

        return $errores;
    }
}
