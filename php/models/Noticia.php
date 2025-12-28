<?php
/**
 * Modelo: Noticia
 * Coordicanarias CMS
 *
 * Gestión de noticias, actualidades y comunicados
 */

require_once __DIR__ . '/../db/connection.php';

class Noticia {
    /**
     * Obtener todas las noticias
     *
     * @param bool $solo_activas Si true, solo retorna noticias activas
     * @param int $limite Límite de resultados (0 = sin límite)
     * @return array Lista de noticias ordenadas por fecha de publicación DESC
     */
    public static function getAll($solo_activas = false, $limite = 0) {
        $sql = "SELECT * FROM noticias";

        if ($solo_activas) {
            $sql .= " WHERE activo = 1";
        }

        $sql .= " ORDER BY fecha_publicacion DESC, fecha_creacion DESC";

        if ($limite > 0) {
            $sql .= " LIMIT " . (int)$limite;
        }

        return fetchAll($sql);
    }

    /**
     * Obtener noticias destacadas
     *
     * @param int $limite Número de noticias a obtener
     * @return array Lista de noticias destacadas
     */
    public static function getDestacadas($limite = 3) {
        $sql = "SELECT * FROM noticias
                WHERE activo = 1 AND destacada = 1
                ORDER BY fecha_publicacion DESC
                LIMIT " . (int)$limite;

        return fetchAll($sql);
    }

    /**
     * Obtener noticia por ID
     *
     * @param int $id ID de la noticia
     * @return array|null Datos de la noticia o null si no existe
     */
    public static function getById($id) {
        return fetchOne("SELECT * FROM noticias WHERE id = ?", [$id]);
    }

    /**
     * Obtener noticia por slug
     *
     * @param string $slug Slug de la noticia
     * @return array|null Datos de la noticia o null si no existe
     */
    public static function getBySlug($slug) {
        return fetchOne("SELECT * FROM noticias WHERE slug = ?", [$slug]);
    }

    /**
     * Crear nueva noticia
     *
     * @param array $datos Datos de la noticia
     * @return int|false ID de la noticia creada o false si falla
     */
    public static function create($datos) {
        $sql = "INSERT INTO noticias (
                    titulo, slug, resumen, contenido, imagen_destacada,
                    fecha_publicacion, autor, categoria, destacada, activo
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $datos['titulo'],
            $datos['slug'],
            $datos['resumen'],
            $datos['contenido'],
            $datos['imagen_destacada'],
            $datos['fecha_publicacion'],
            $datos['autor'],
            $datos['categoria'],
            $datos['destacada'],
            $datos['activo']
        ];

        if (execute($sql, $params)) {
            return lastInsertId();
        }

        return false;
    }

    /**
     * Actualizar noticia existente
     *
     * @param int $id ID de la noticia a actualizar
     * @param array $datos Datos a actualizar
     * @return bool True si se actualizó correctamente
     */
    public static function update($id, $datos) {
        // Validar que la noticia existe
        $noticia = self::getById($id);
        if (!$noticia) {
            return false;
        }

        $sql = "UPDATE noticias SET
                titulo = ?,
                slug = ?,
                resumen = ?,
                contenido = ?,
                imagen_destacada = ?,
                fecha_publicacion = ?,
                autor = ?,
                categoria = ?,
                destacada = ?,
                activo = ?
                WHERE id = ?";

        $params = [
            $datos['titulo'],
            $datos['slug'],
            $datos['resumen'],
            $datos['contenido'],
            $datos['imagen_destacada'],
            $datos['fecha_publicacion'],
            $datos['autor'],
            $datos['categoria'],
            $datos['destacada'],
            $datos['activo'],
            $id
        ];

        return execute($sql, $params);
    }

    /**
     * Eliminar noticia (soft delete)
     *
     * @param int $id ID de la noticia
     * @return bool True si se eliminó correctamente
     */
    public static function delete($id) {
        return execute("UPDATE noticias SET activo = 0 WHERE id = ?", [$id]);
    }

    /**
     * Eliminar noticia permanentemente
     *
     * @param int $id ID de la noticia
     * @return bool True si se eliminó correctamente
     */
    public static function deletePermanente($id) {
        return execute("DELETE FROM noticias WHERE id = ?", [$id]);
    }

    /**
     * Cambiar estado activo/inactivo
     *
     * @param int $id ID de la noticia
     * @param int $activo 1 para activar, 0 para desactivar
     * @return bool True si se actualizó correctamente
     */
    public static function toggleActivo($id, $activo) {
        return execute("UPDATE noticias SET activo = ? WHERE id = ?", [$activo, $id]);
    }

    /**
     * Cambiar estado destacado
     *
     * @param int $id ID de la noticia
     * @param int $destacada 1 para destacar, 0 para quitar
     * @return bool True si se actualizó correctamente
     */
    public static function toggleDestacada($id, $destacada) {
        return execute("UPDATE noticias SET destacada = ? WHERE id = ?", [$destacada, $id]);
    }

    /**
     * Validar que el slug es único (excepto para la noticia actual)
     *
     * @param string $slug Slug a validar
     * @param int $id_noticia_actual ID de la noticia actual (para excluir de la validación)
     * @return bool True si el slug está disponible
     */
    public static function isSlugUnico($slug, $id_noticia_actual = null) {
        if ($id_noticia_actual) {
            $result = fetchOne("SELECT id FROM noticias WHERE slug = ? AND id != ?", [$slug, $id_noticia_actual]);
        } else {
            $result = fetchOne("SELECT id FROM noticias WHERE slug = ?", [$slug]);
        }

        return $result === null;
    }

    /**
     * Generar slug desde un título
     *
     * @param string $titulo Título de la noticia
     * @return string Slug generado (URL-friendly)
     */
    public static function generarSlug($titulo) {
        // Convertir a minúsculas
        $slug = mb_strtolower($titulo, 'UTF-8');

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

        // Limitar longitud
        $slug = substr($slug, 0, 200);

        return $slug;
    }

    /**
     * Validar datos de noticia
     *
     * @param array $datos Datos a validar
     * @param int $id_noticia_actual ID de la noticia actual (opcional, para edición)
     * @return array Lista de errores (vacío si todo es válido)
     */
    public static function validar($datos, $id_noticia_actual = null) {
        $errores = [];

        // Validar título
        if (empty($datos['titulo'])) {
            $errores[] = 'El título es requerido';
        } elseif (strlen($datos['titulo']) > 200) {
            $errores[] = 'El título no puede tener más de 200 caracteres';
        }

        // Validar slug
        if (empty($datos['slug'])) {
            $errores[] = 'El slug es requerido';
        } elseif (!preg_match('/^[a-z0-9-]+$/', $datos['slug'])) {
            $errores[] = 'El slug solo puede contener letras minúsculas, números y guiones';
        } elseif (strlen($datos['slug']) > 200) {
            $errores[] = 'El slug no puede tener más de 200 caracteres';
        } elseif (!self::isSlugUnico($datos['slug'], $id_noticia_actual)) {
            $errores[] = 'El slug ya está en uso por otra noticia';
        }

        // Validar fecha de publicación
        if (empty($datos['fecha_publicacion'])) {
            $errores[] = 'La fecha de publicación es requerida';
        }

        return $errores;
    }

    /**
     * Obtener categorías únicas
     *
     * @return array Lista de categorías
     */
    public static function getCategorias() {
        $result = fetchAll("SELECT DISTINCT categoria FROM noticias WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria");
        return array_column($result, 'categoria');
    }
}
