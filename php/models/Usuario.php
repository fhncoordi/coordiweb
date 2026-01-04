<?php
/**
 * Modelo: Usuario
 * Coordicanarias CMS
 *
 * Gestión de usuarios con sistema de roles y permisos
 */

require_once __DIR__ . '/../db/connection.php';

class Usuario {
    /**
     * Obtener todos los usuarios
     *
     * @param bool $solo_activos Si true, solo retorna usuarios activos
     * @return array Lista de usuarios
     */
    public static function getAll($solo_activos = false) {
        $sql = "SELECT u.*, a.nombre as area_nombre
                FROM usuarios u
                LEFT JOIN areas a ON u.area_id = a.id
                WHERE 1=1";

        $params = [];

        if ($solo_activos) {
            $sql .= " AND u.activo = 1";
        }

        $sql .= " ORDER BY
                  CASE u.rol
                      WHEN 'admin' THEN 1
                      WHEN 'editor' THEN 2
                      WHEN 'coordinador' THEN 3
                  END,
                  u.nombre_completo ASC";

        return fetchAll($sql, $params);
    }

    /**
     * Obtener usuario por ID
     *
     * @param int $id ID del usuario
     * @return array|null Datos del usuario o null si no existe
     */
    public static function getById($id) {
        $sql = "SELECT u.*, a.nombre as area_nombre
                FROM usuarios u
                LEFT JOIN areas a ON u.area_id = a.id
                WHERE u.id = ?";

        return fetchOne($sql, [$id]);
    }

    /**
     * Obtener usuario por username
     *
     * @param string $username Username del usuario
     * @return array|null Datos del usuario o null si no existe
     */
    public static function getByUsername($username) {
        $sql = "SELECT u.*, a.nombre as area_nombre
                FROM usuarios u
                LEFT JOIN areas a ON u.area_id = a.id
                WHERE u.username = ?";

        return fetchOne($sql, [$username]);
    }

    /**
     * Obtener usuario por email
     *
     * @param string $email Email del usuario
     * @return array|null Datos del usuario o null si no existe
     */
    public static function getByEmail($email) {
        $sql = "SELECT u.*, a.nombre as area_nombre
                FROM usuarios u
                LEFT JOIN areas a ON u.area_id = a.id
                WHERE u.email = ?";

        return fetchOne($sql, [$email]);
    }

    /**
     * Crear nuevo usuario
     *
     * @param array $datos Datos del usuario
     * @return int|false ID del usuario creado o false si falla
     */
    public static function create($datos) {
        // Hash de la contraseña
        $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);

        // Si es coordinador, debe tener area_id
        $area_id = null;
        if ($datos['rol'] === 'coordinador' && isset($datos['area_id'])) {
            $area_id = $datos['area_id'];
        }

        $sql = "INSERT INTO usuarios (
                    username, email, password_hash, nombre_completo, rol, area_id, activo
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $datos['username'],
            $datos['email'],
            $password_hash,
            $datos['nombre_completo'],
            $datos['rol'],
            $area_id,
            $datos['activo'] ?? 1
        ];

        if (execute($sql, $params)) {
            return lastInsertId();
        }

        return false;
    }

    /**
     * Actualizar usuario existente
     *
     * @param int $id ID del usuario a actualizar
     * @param array $datos Datos a actualizar
     * @return bool True si se actualizó correctamente
     */
    public static function update($id, $datos) {
        // Validar que el usuario existe
        $usuario = self::getById($id);
        if (!$usuario) {
            return false;
        }

        // Si es coordinador, debe tener area_id
        $area_id = null;
        if ($datos['rol'] === 'coordinador' && isset($datos['area_id'])) {
            $area_id = $datos['area_id'];
        }

        // Si se proporciona nueva contraseña, hashearla
        if (!empty($datos['password'])) {
            $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);

            $sql = "UPDATE usuarios SET
                    username = ?,
                    email = ?,
                    password_hash = ?,
                    nombre_completo = ?,
                    rol = ?,
                    area_id = ?,
                    activo = ?
                    WHERE id = ?";

            $params = [
                $datos['username'],
                $datos['email'],
                $password_hash,
                $datos['nombre_completo'],
                $datos['rol'],
                $area_id,
                $datos['activo'],
                $id
            ];
        } else {
            // No actualizar contraseña
            $sql = "UPDATE usuarios SET
                    username = ?,
                    email = ?,
                    nombre_completo = ?,
                    rol = ?,
                    area_id = ?,
                    activo = ?
                    WHERE id = ?";

            $params = [
                $datos['username'],
                $datos['email'],
                $datos['nombre_completo'],
                $datos['rol'],
                $area_id,
                $datos['activo'],
                $id
            ];
        }

        return execute($sql, $params);
    }

    /**
     * Eliminar usuario (soft delete)
     *
     * @param int $id ID del usuario
     * @return bool True si se eliminó correctamente
     */
    public static function delete($id) {
        return execute("UPDATE usuarios SET activo = 0 WHERE id = ?", [$id]);
    }

    /**
     * Eliminar usuario permanentemente
     *
     * @param int $id ID del usuario
     * @return bool True si se eliminó correctamente
     */
    public static function deletePermanente($id) {
        return execute("DELETE FROM usuarios WHERE id = ?", [$id]);
    }

    /**
     * Cambiar estado activo/inactivo
     *
     * @param int $id ID del usuario
     * @param int $activo 1 para activar, 0 para desactivar
     * @return bool True si se actualizó correctamente
     */
    public static function toggleActivo($id, $activo) {
        return execute("UPDATE usuarios SET activo = ? WHERE id = ?", [$activo, $id]);
    }

    /**
     * Actualizar último acceso
     *
     * @param int $id ID del usuario
     * @return bool True si se actualizó correctamente
     */
    public static function actualizarUltimoAcceso($id) {
        return execute("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?", [$id]);
    }

    /**
     * Validar datos de usuario
     *
     * @param array $datos Datos a validar
     * @param int $id_usuario_actual ID del usuario actual (opcional, para edición)
     * @return array Lista de errores (vacío si todo es válido)
     */
    public static function validar($datos, $id_usuario_actual = null) {
        $errores = [];

        // Validar username
        if (empty($datos['username'])) {
            $errores[] = 'El nombre de usuario es requerido';
        } elseif (strlen($datos['username']) < 3) {
            $errores[] = 'El nombre de usuario debe tener al menos 3 caracteres';
        } elseif (strlen($datos['username']) > 100) {
            $errores[] = 'El nombre de usuario no puede tener más de 100 caracteres';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $datos['username'])) {
            $errores[] = 'El nombre de usuario solo puede contener letras, números y guiones bajos';
        } else {
            // Verificar que el username no exista
            $usuario_existente = self::getByUsername($datos['username']);
            if ($usuario_existente && (!$id_usuario_actual || $usuario_existente['id'] != $id_usuario_actual)) {
                $errores[] = 'El nombre de usuario ya está en uso';
            }
        }

        // Validar email
        if (empty($datos['email'])) {
            $errores[] = 'El email es requerido';
        } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no es válido';
        } elseif (strlen($datos['email']) > 200) {
            $errores[] = 'El email no puede tener más de 200 caracteres';
        } else {
            // Verificar que el email no exista
            $usuario_existente = self::getByEmail($datos['email']);
            if ($usuario_existente && (!$id_usuario_actual || $usuario_existente['id'] != $id_usuario_actual)) {
                $errores[] = 'El email ya está en uso';
            }
        }

        // Validar nombre completo
        if (empty($datos['nombre_completo'])) {
            $errores[] = 'El nombre completo es requerido';
        } elseif (strlen($datos['nombre_completo']) > 200) {
            $errores[] = 'El nombre completo no puede tener más de 200 caracteres';
        }

        // Validar contraseña (solo en creación o si se proporciona en edición)
        if (!$id_usuario_actual || !empty($datos['password'])) {
            if (empty($datos['password'])) {
                $errores[] = 'La contraseña es requerida';
            } elseif (strlen($datos['password']) < 8) {
                $errores[] = 'La contraseña debe tener al menos 8 caracteres';
            }
        }

        // Validar rol
        if (empty($datos['rol'])) {
            $errores[] = 'Debe seleccionar un rol';
        } elseif (!in_array($datos['rol'], ['admin', 'coordinador', 'editor'])) {
            $errores[] = 'El rol seleccionado no es válido';
        }

        // Validar área (obligatoria para coordinadores)
        if ($datos['rol'] === 'coordinador') {
            if (empty($datos['area_id'])) {
                $errores[] = 'Debe seleccionar un área para el coordinador';
            } else {
                // Verificar que el área existe
                $area = fetchOne("SELECT id FROM areas WHERE id = ?", [$datos['area_id']]);
                if (!$area) {
                    $errores[] = 'El área seleccionada no existe';
                }
            }
        }

        return $errores;
    }

    /**
     * Verificar si un usuario puede gestionar contenido de un área específica
     *
     * @param array $usuario Usuario actual (con datos completos)
     * @param int $area_id ID del área a verificar
     * @return bool True si puede gestionar
     */
    public static function puedeGestionarArea($usuario, $area_id) {
        // Admin puede gestionar todo
        if ($usuario['rol'] === 'admin') {
            return true;
        }

        // Editor puede gestionar todas las áreas
        if ($usuario['rol'] === 'editor') {
            return true;
        }

        // Coordinador solo puede gestionar su área
        if ($usuario['rol'] === 'coordinador') {
            return $usuario['area_id'] == $area_id;
        }

        return false;
    }

    /**
     * Obtener áreas que un usuario puede gestionar
     *
     * @param array $usuario Usuario actual (con datos completos)
     * @return array Lista de IDs de áreas que puede gestionar
     */
    public static function getAreasGestionables($usuario) {
        // Admin y editor pueden gestionar todas las áreas
        if ($usuario['rol'] === 'admin' || $usuario['rol'] === 'editor') {
            $areas = fetchAll("SELECT id FROM areas WHERE activo = 1");
            return array_column($areas, 'id');
        }

        // Coordinador solo puede gestionar su área
        if ($usuario['rol'] === 'coordinador' && $usuario['area_id']) {
            return [$usuario['area_id']];
        }

        return [];
    }

    /**
     * Verificar si un usuario es admin
     *
     * @param array $usuario Usuario actual
     * @return bool True si es admin
     */
    public static function esAdmin($usuario) {
        return $usuario['rol'] === 'admin';
    }

    /**
     * Verificar si un usuario es coordinador
     *
     * @param array $usuario Usuario actual
     * @return bool True si es coordinador
     */
    public static function esCoordinador($usuario) {
        return $usuario['rol'] === 'coordinador';
    }

    /**
     * Verificar si un usuario es editor
     *
     * @param array $usuario Usuario actual
     * @return bool True si es editor
     */
    public static function esEditor($usuario) {
        return $usuario['rol'] === 'editor';
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
     * Contar usuarios por rol
     *
     * @param bool $solo_activos Si true, solo cuenta usuarios activos
     * @return array Array asociativo con rol como key y count como value
     */
    public static function contarPorRol($solo_activos = true) {
        $sql = "SELECT rol, COUNT(*) as total
                FROM usuarios";

        if ($solo_activos) {
            $sql .= " WHERE activo = 1";
        }

        $sql .= " GROUP BY rol
                  ORDER BY
                      CASE rol
                          WHEN 'admin' THEN 1
                          WHEN 'editor' THEN 2
                          WHEN 'coordinador' THEN 3
                      END";

        return fetchAll($sql);
    }

    /**
     * Obtener nombre amigable del rol
     *
     * @param string $rol Rol del usuario
     * @return string Nombre amigable del rol
     */
    public static function getNombreRol($rol) {
        $nombres = [
            'admin' => 'Administrador',
            'coordinador' => 'Coordinador de Área',
            'editor' => 'Editor'
        ];

        return $nombres[$rol] ?? $rol;
    }

    /**
     * Obtener badge HTML para el rol
     *
     * @param string $rol Rol del usuario
     * @return string HTML del badge
     */
    public static function getBadgeRol($rol) {
        $badges = [
            'admin' => '<span class="badge bg-danger">Administrador</span>',
            'coordinador' => '<span class="badge bg-primary">Coordinador</span>',
            'editor' => '<span class="badge bg-success">Editor</span>'
        ];

        return $badges[$rol] ?? '<span class="badge bg-secondary">' . e($rol) . '</span>';
    }
}
