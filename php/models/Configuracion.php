<?php
/**
 * Modelo: Configuracion
 * Coordicanarias CMS
 *
 * Gestión de configuración general del sitio (sistema clave-valor)
 */

require_once __DIR__ . '/../db/connection.php';

class Configuracion {
    /**
     * Obtener todas las configuraciones
     *
     * @return array Array asociativo con clave => valor
     */
    public static function getAll() {
        $result = fetchAll("SELECT clave, valor FROM configuracion ORDER BY clave");

        $config = [];
        foreach ($result as $row) {
            $config[$row['clave']] = $row['valor'];
        }

        return $config;
    }

    /**
     * Obtener valor de una configuración específica
     *
     * @param string $clave Clave de la configuración
     * @param mixed $default Valor por defecto si no existe
     * @return mixed Valor de la configuración o default
     */
    public static function get($clave, $default = null) {
        $result = fetchOne("SELECT valor FROM configuracion WHERE clave = ?", [$clave]);
        return $result ? $result['valor'] : $default;
    }

    /**
     * Establecer o actualizar una configuración
     *
     * @param string $clave Clave de la configuración
     * @param string $valor Valor a guardar
     * @param string $descripcion Descripción opcional
     * @param string $tipo Tipo de dato (texto, numero, email, tel, url)
     * @return bool True si se guardó correctamente
     */
    public static function set($clave, $valor, $descripcion = '', $tipo = 'texto') {
        // Verificar si existe
        $existe = fetchOne("SELECT id FROM configuracion WHERE clave = ?", [$clave]);

        if ($existe) {
            // Actualizar
            $sql = "UPDATE configuracion SET valor = ?, descripcion = ?, tipo = ? WHERE clave = ?";
            return execute($sql, [$valor, $descripcion, $tipo, $clave]);
        } else {
            // Insertar
            $sql = "INSERT INTO configuracion (clave, valor, descripcion, tipo) VALUES (?, ?, ?, ?)";
            return execute($sql, [$clave, $valor, $descripcion, $tipo]);
        }
    }

    /**
     * Actualizar múltiples configuraciones de una vez
     *
     * @param array $configuraciones Array asociativo clave => valor
     * @return bool True si todas se actualizaron correctamente
     */
    public static function updateMultiple($configuraciones) {
        $success = true;

        foreach ($configuraciones as $clave => $valor) {
            if (!self::set($clave, $valor)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Eliminar una configuración
     *
     * @param string $clave Clave de la configuración
     * @return bool True si se eliminó correctamente
     */
    public static function delete($clave) {
        return execute("DELETE FROM configuracion WHERE clave = ?", [$clave]);
    }

    /**
     * Obtener configuraciones agrupadas por categoría
     *
     * @return array Array agrupado por categorías
     */
    public static function getAgrupadas() {
        $todas = fetchAll("SELECT * FROM configuracion ORDER BY clave");

        $agrupadas = [
            'contacto' => [],
            'redes_sociales' => [],
            'general' => [],
            'otros' => []
        ];

        foreach ($todas as $config) {
            $clave = $config['clave'];

            // Agrupar por prefijo
            if (strpos($clave, 'contacto_') === 0) {
                $agrupadas['contacto'][] = $config;
            } elseif (strpos($clave, 'redes_') === 0) {
                $agrupadas['redes_sociales'][] = $config;
            } elseif (in_array($clave, ['nombre_sitio', 'descripcion_sitio', 'slogan'])) {
                $agrupadas['general'][] = $config;
            } else {
                $agrupadas['otros'][] = $config;
            }
        }

        return $agrupadas;
    }

    /**
     * Inicializar configuraciones por defecto
     *
     * @return bool True si se inicializaron correctamente
     */
    public static function inicializarDefaults() {
        $defaults = [
            // Información general
            'nombre_sitio' => 'Coordicanarias',
            'descripcion_sitio' => 'Centro de Recuperación de Canarias',
            'slogan' => 'Impulsando la inclusión social',

            // Contacto
            'contacto_telefono' => '928 123 456',
            'contacto_email' => 'info@coordicanarias.com',
            'contacto_direccion' => 'Calle Ejemplo, 123, Las Palmas de Gran Canaria',
            'contacto_horario' => 'Lunes a Viernes: 8:00 - 15:00',

            // Redes sociales
            'redes_facebook' => '',
            'redes_twitter' => '',
            'redes_instagram' => '',
            'redes_linkedin' => '',
            'redes_youtube' => '',
        ];

        $success = true;
        foreach ($defaults as $clave => $valor) {
            // Solo insertar si no existe
            $existe = fetchOne("SELECT id FROM configuracion WHERE clave = ?", [$clave]);
            if (!$existe) {
                if (!self::set($clave, $valor)) {
                    $success = false;
                }
            }
        }

        return $success;
    }

    /**
     * Validar email
     *
     * @param string $email Email a validar
     * @return bool True si es válido
     */
    public static function validarEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validar URL
     *
     * @param string $url URL a validar
     * @return bool True si es válida
     */
    public static function validarURL($url) {
        if (empty($url)) return true; // URLs opcionales
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validar teléfono (formato básico)
     *
     * @param string $telefono Teléfono a validar
     * @return bool True si es válido
     */
    public static function validarTelefono($telefono) {
        // Permitir números, espacios, guiones, paréntesis y símbolo +
        return preg_match('/^[\d\s\-\(\)\+]+$/', $telefono);
    }
}
