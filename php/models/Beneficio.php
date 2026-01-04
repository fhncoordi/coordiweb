<?php
/**
 * Modelo: Beneficio
 * Coordicanarias CMS
 *
 * Gestión de beneficios por área temática
 */

require_once __DIR__ . '/../db/connection.php';

class Beneficio {
    /**
     * Obtener todos los beneficios
     *
     * @param bool $solo_activos Si true, solo retorna beneficios activos
     * @param int $area_id Si se especifica, filtra por área
     * @return array Lista de beneficios ordenados por área y orden
     */
    public static function getAll($solo_activos = false, $area_id = null) {
        $sql = "SELECT b.*, a.nombre as area_nombre, a.slug as area_slug
                FROM beneficios b
                LEFT JOIN areas a ON b.area_id = a.id
                WHERE 1=1";

        $params = [];

        if ($solo_activos) {
            $sql .= " AND b.activo = 1";
        }

        if ($area_id !== null) {
            $sql .= " AND b.area_id = ?";
            $params[] = $area_id;
        }

        $sql .= " ORDER BY a.orden ASC, b.orden ASC, b.fecha_creacion DESC";

        return fetchAll($sql, $params);
    }

    /**
     * Obtener beneficios agrupados por área
     *
     * @param bool $solo_activos Si true, solo beneficios y áreas activas
     * @param int $area_id Si se especifica, filtra por área
     * @return array Array asociativo con áreas como keys y beneficios como values
     */
    public static function getAllAgrupados($solo_activos = true, $area_id = null) {
        $beneficios = self::getAll($solo_activos, $area_id);
        $agrupados = [];

        foreach ($beneficios as $beneficio) {
            $area_nombre = $beneficio['area_nombre'] ?? 'Sin área';
            if (!isset($agrupados[$area_nombre])) {
                $agrupados[$area_nombre] = [];
            }
            $agrupados[$area_nombre][] = $beneficio;
        }

        return $agrupados;
    }

    /**
     * Obtener beneficio por ID
     *
     * @param int $id ID del beneficio
     * @return array|null Datos del beneficio o null si no existe
     */
    public static function getById($id) {
        $sql = "SELECT b.*, a.nombre as area_nombre
                FROM beneficios b
                LEFT JOIN areas a ON b.area_id = a.id
                WHERE b.id = ?";

        return fetchOne($sql, [$id]);
    }

    /**
     * Crear nuevo beneficio
     *
     * @param array $datos Datos del beneficio
     * @return int|false ID del beneficio creado o false si falla
     */
    public static function create($datos) {
        $sql = "INSERT INTO beneficios (
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
     * Actualizar beneficio existente
     *
     * @param int $id ID del beneficio a actualizar
     * @param array $datos Datos a actualizar
     * @return bool True si se actualizó correctamente
     */
    public static function update($id, $datos) {
        // Validar que el beneficio existe
        $beneficio = self::getById($id);
        if (!$beneficio) {
            return false;
        }

        $sql = "UPDATE beneficios SET
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
     * Eliminar beneficio (soft delete)
     *
     * @param int $id ID del beneficio
     * @return bool True si se eliminó correctamente
     */
    public static function delete($id) {
        return execute("UPDATE beneficios SET activo = 0 WHERE id = ?", [$id]);
    }

    /**
     * Eliminar beneficio permanentemente
     *
     * @param int $id ID del beneficio
     * @return bool True si se eliminó correctamente
     */
    public static function deletePermanente($id) {
        return execute("DELETE FROM beneficios WHERE id = ?", [$id]);
    }

    /**
     * Cambiar estado activo/inactivo
     *
     * @param int $id ID del beneficio
     * @param int $activo 1 para activar, 0 para desactivar
     * @return bool True si se actualizó correctamente
     */
    public static function toggleActivo($id, $activo) {
        return execute("UPDATE beneficios SET activo = ? WHERE id = ?", [$activo, $id]);
    }

    /**
     * Validar datos de beneficio
     *
     * @param array $datos Datos a validar
     * @param int $id_beneficio_actual ID del beneficio actual (opcional, para edición)
     * @return array Lista de errores (vacío si todo es válido)
     */
    public static function validar($datos, $id_beneficio_actual = null) {
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
     * Obtener el siguiente número de orden disponible para un área
     *
     * @param int $area_id ID del área
     * @return int Siguiente número de orden
     */
    public static function getSiguienteOrden($area_id) {
        $result = fetchOne("SELECT MAX(orden) as max_orden FROM beneficios WHERE area_id = ?", [$area_id]);
        return ($result['max_orden'] ?? 0) + 1;
    }

    /**
     * Contar beneficios por área
     *
     * @param bool $solo_activos Si true, solo cuenta beneficios activos
     * @param int $area_id Si se especifica, filtra por área
     * @return array Array asociativo con area_id como key y count como value
     */
    public static function contarPorArea($solo_activos = true, $area_id = null) {
        $sql = "SELECT a.id, a.nombre, COUNT(b.id) as total_beneficios
                FROM areas a
                LEFT JOIN beneficios b ON a.id = b.area_id";

        $params = [];
        $where_conditions = [];

        if ($solo_activos) {
            $where_conditions[] = "b.activo = 1";
        }

        if ($area_id !== null) {
            $where_conditions[] = "a.id = ?";
            $params[] = $area_id;
        }

        if (!empty($where_conditions)) {
            $sql .= " WHERE " . implode(" AND ", $where_conditions);
        }

        $sql .= " GROUP BY a.id, a.nombre ORDER BY a.orden ASC";

        return fetchAll($sql, $params);
    }

    /**
     * Obtener iconos sugeridos para beneficios
     *
     * @return array Lista de iconos sugeridos
     */
    public static function getIconosSugeridos() {
        return [
            'fas fa-check-circle' => 'Check (General)',
            'fas fa-star' => 'Estrella (Destacado)',
            'fas fa-heart' => 'Corazón (Bienestar)',
            'fas fa-users' => 'Usuarios (Inclusión)',
            'fas fa-hand-holding-heart' => 'Apoyo',
            'fas fa-smile' => 'Sonrisa (Satisfacción)',
            'fas fa-trophy' => 'Trofeo (Logro)',
            'fas fa-rocket' => 'Cohete (Desarrollo)',
            'fas fa-lightbulb' => 'Bombilla (Innovación)',
            'fas fa-graduation-cap' => 'Birrete (Formación)',
            'fas fa-handshake' => 'Apretón de manos (Colaboración)',
            'fas fa-shield-alt' => 'Escudo (Protección)',
            'fas fa-balance-scale' => 'Balanza (Igualdad)',
            'fas fa-bullhorn' => 'Megáfono (Visibilidad)',
            'fas fa-hands-helping' => 'Manos (Ayuda)',
            'fas fa-chart-line' => 'Gráfico (Progreso)',
            'fas fa-leaf' => 'Hoja (Bienestar)',
            'fas fa-dove' => 'Paloma (Paz)',
            'fas fa-sun' => 'Sol (Alegría)',
            'fas fa-home' => 'Casa (Hogar)',
        ];
    }
}
