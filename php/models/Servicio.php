<?php
/**
 * Modelo: Servicio
 * Coordicanarias CMS
 *
 * Gestión de servicios por área temática
 */

require_once __DIR__ . '/../db/connection.php';

class Servicio {
    /**
     * Obtener todos los servicios
     *
     * @param bool $solo_activos Si true, solo retorna servicios activos
     * @param int $area_id Si se especifica, filtra por área
     * @return array Lista de servicios ordenados por área y orden
     */
    public static function getAll($solo_activos = false, $area_id = null) {
        $sql = "SELECT s.*, a.nombre as area_nombre, a.slug as area_slug
                FROM servicios s
                LEFT JOIN areas a ON s.area_id = a.id
                WHERE 1=1";

        $params = [];

        if ($solo_activos) {
            $sql .= " AND s.activo = 1";
        }

        if ($area_id !== null) {
            $sql .= " AND s.area_id = ?";
            $params[] = $area_id;
        }

        $sql .= " ORDER BY a.orden ASC, s.orden ASC, s.fecha_creacion DESC";

        return fetchAll($sql, $params);
    }

    /**
     * Obtener servicios agrupados por área
     *
     * @param bool $solo_activos Si true, solo servicios y áreas activas
     * @return array Array asociativo con áreas como keys y servicios como values
     */
    public static function getAllAgrupados($solo_activos = true) {
        $servicios = self::getAll($solo_activos);
        $agrupados = [];

        foreach ($servicios as $servicio) {
            $area_nombre = $servicio['area_nombre'] ?? 'Sin área';
            if (!isset($agrupados[$area_nombre])) {
                $agrupados[$area_nombre] = [];
            }
            $agrupados[$area_nombre][] = $servicio;
        }

        return $agrupados;
    }

    /**
     * Obtener servicio por ID
     *
     * @param int $id ID del servicio
     * @return array|null Datos del servicio o null si no existe
     */
    public static function getById($id) {
        $sql = "SELECT s.*, a.nombre as area_nombre
                FROM servicios s
                LEFT JOIN areas a ON s.area_id = a.id
                WHERE s.id = ?";

        return fetchOne($sql, [$id]);
    }

    /**
     * Crear nuevo servicio
     *
     * @param array $datos Datos del servicio
     * @return int|false ID del servicio creado o false si falla
     */
    public static function create($datos) {
        $sql = "INSERT INTO servicios (
                    area_id, titulo, descripcion, icono, orden, activo
                ) VALUES (?, ?, ?, ?, ?, ?)";

        $params = [
            $datos['area_id'],
            $datos['titulo'],
            $datos['descripcion'],
            $datos['icono'],
            $datos['orden'],
            $datos['activo']
        ];

        if (execute($sql, $params)) {
            return lastInsertId();
        }

        return false;
    }

    /**
     * Actualizar servicio existente
     *
     * @param int $id ID del servicio a actualizar
     * @param array $datos Datos a actualizar
     * @return bool True si se actualizó correctamente
     */
    public static function update($id, $datos) {
        // Validar que el servicio existe
        $servicio = self::getById($id);
        if (!$servicio) {
            return false;
        }

        $sql = "UPDATE servicios SET
                area_id = ?,
                titulo = ?,
                descripcion = ?,
                icono = ?,
                orden = ?,
                activo = ?
                WHERE id = ?";

        $params = [
            $datos['area_id'],
            $datos['titulo'],
            $datos['descripcion'],
            $datos['icono'],
            $datos['orden'],
            $datos['activo'],
            $id
        ];

        return execute($sql, $params);
    }

    /**
     * Eliminar servicio (soft delete)
     *
     * @param int $id ID del servicio
     * @return bool True si se eliminó correctamente
     */
    public static function delete($id) {
        return execute("UPDATE servicios SET activo = 0 WHERE id = ?", [$id]);
    }

    /**
     * Eliminar servicio permanentemente
     *
     * @param int $id ID del servicio
     * @return bool True si se eliminó correctamente
     */
    public static function deletePermanente($id) {
        return execute("DELETE FROM servicios WHERE id = ?", [$id]);
    }

    /**
     * Cambiar estado activo/inactivo
     *
     * @param int $id ID del servicio
     * @param int $activo 1 para activar, 0 para desactivar
     * @return bool True si se actualizó correctamente
     */
    public static function toggleActivo($id, $activo) {
        return execute("UPDATE servicios SET activo = ? WHERE id = ?", [$activo, $id]);
    }

    /**
     * Validar datos de servicio
     *
     * @param array $datos Datos a validar
     * @param int $id_servicio_actual ID del servicio actual (opcional, para edición)
     * @return array Lista de errores (vacío si todo es válido)
     */
    public static function validar($datos, $id_servicio_actual = null) {
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

        // Validar icono (opcional, pero si existe debe ser válido)
        if (!empty($datos['icono'])) {
            // Validar formato de Font Awesome (debe empezar con fa- o fas fa-)
            if (!preg_match('/^(fa-|fas fa-|far fa-|fab fa-)/', $datos['icono']) && strlen($datos['icono']) > 0) {
                // Si no sigue el formato de Font Awesome, advertir
                // Pero no es un error crítico, puede ser un emoji
            }
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
     * Obtener el siguiente número de orden disponible para un área
     *
     * @param int $area_id ID del área
     * @return int Siguiente número de orden
     */
    public static function getSiguienteOrden($area_id) {
        $result = fetchOne("SELECT MAX(orden) as max_orden FROM servicios WHERE area_id = ?", [$area_id]);
        return ($result['max_orden'] ?? 0) + 1;
    }

    /**
     * Contar servicios por área
     *
     * @param bool $solo_activos Si true, solo cuenta servicios activos
     * @return array Array asociativo con area_id como key y count como value
     */
    public static function contarPorArea($solo_activos = true) {
        $sql = "SELECT a.id, a.nombre, COUNT(s.id) as total_servicios
                FROM areas a
                LEFT JOIN servicios s ON a.id = s.area_id";

        if ($solo_activos) {
            $sql .= " AND s.activo = 1";
        }

        $sql .= " GROUP BY a.id, a.nombre ORDER BY a.orden ASC";

        return fetchAll($sql);
    }

    /**
     * Obtener iconos de Font Awesome más comunes para servicios
     *
     * @return array Lista de iconos sugeridos
     */
    public static function getIconosSugeridos() {
        return [
            'fas fa-briefcase' => 'Maletín (Trabajo)',
            'fas fa-graduation-cap' => 'Birrete (Formación)',
            'fas fa-users' => 'Usuarios (Grupo)',
            'fas fa-heart' => 'Corazón (Cuidado)',
            'fas fa-hands-helping' => 'Manos (Ayuda)',
            'fas fa-user-md' => 'Médico (Salud)',
            'fas fa-home' => 'Casa (Hogar)',
            'fas fa-book' => 'Libro (Educación)',
            'fas fa-comments' => 'Comentarios (Comunicación)',
            'fas fa-laptop' => 'Portátil (Tecnología)',
            'fas fa-phone' => 'Teléfono (Contacto)',
            'fas fa-calendar-alt' => 'Calendario (Eventos)',
            'fas fa-clipboard-list' => 'Portapapeles (Gestión)',
            'fas fa-handshake' => 'Apretón de manos (Acuerdo)',
            'fas fa-bullhorn' => 'Megáfono (Comunicación)',
            'fas fa-lightbulb' => 'Bombilla (Ideas)',
            'fas fa-wheelchair' => 'Silla de ruedas (Accesibilidad)',
            'fas fa-universal-access' => 'Accesibilidad universal',
            'fas fa-child' => 'Niño (Infantil)',
            'fas fa-user-friends' => 'Amigos (Social)',
        ];
    }
}
