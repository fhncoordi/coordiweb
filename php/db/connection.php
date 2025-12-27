<?php
/**
 * Conexión a Base de Datos MySQL
 * Coordicanarias CMS
 *
 * Implementa patrón Singleton para única instancia PDO
 * Funciones auxiliares para queries seguras con prepared statements
 */

// Cargar configuración de base de datos
require_once __DIR__ . '/../config.php';

// ============================================
// CLASE DE CONEXIÓN SINGLETON
// ============================================
class Database {
    private static $instance = null;
    private $conn;

    /**
     * Constructor privado (Singleton)
     * Establece conexión PDO con MySQL
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];

            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);

        } catch(PDOException $e) {
            // Log del error (no mostrar credenciales al usuario)
            error_log("Error de conexión a BD: " . $e->getMessage());
            die("Error de conexión a la base de datos. Por favor, contacte al administrador.");
        }
    }

    /**
     * Obtener única instancia de la base de datos (Singleton)
     *
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtener conexión PDO
     *
     * @return PDO
     */
    public function getConnection() {
        return $this->conn;
    }

    /**
     * Prevenir clonación del Singleton
     */
    private function __clone() {}

    /**
     * Prevenir unserialize del Singleton
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// ============================================
// FUNCIONES AUXILIARES DE BASE DE DATOS
// ============================================

/**
 * Obtener conexión PDO
 *
 * @return PDO
 */
function getDB() {
    return Database::getInstance()->getConnection();
}

/**
 * Ejecutar query con prepared statements
 *
 * @param string $sql Query SQL con placeholders (?)
 * @param array $params Parámetros para bind
 * @return PDOStatement
 */
function query($sql, $params = []) {
    $db = getDB();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Obtener un solo registro
 *
 * @param string $sql Query SQL
 * @param array $params Parámetros
 * @return array|false Array asociativo o false si no existe
 */
function fetchOne($sql, $params = []) {
    $stmt = query($sql, $params);
    return $stmt->fetch();
}

/**
 * Obtener múltiples registros
 *
 * @param string $sql Query SQL
 * @param array $params Parámetros
 * @return array Array de arrays asociativos
 */
function fetchAll($sql, $params = []) {
    $stmt = query($sql, $params);
    return $stmt->fetchAll();
}

/**
 * Ejecutar INSERT/UPDATE/DELETE
 *
 * @param string $sql Query SQL
 * @param array $params Parámetros
 * @return int Número de filas afectadas
 */
function execute($sql, $params = []) {
    $stmt = query($sql, $params);
    return $stmt->rowCount();
}

/**
 * Obtener último ID insertado
 *
 * @return string ID del último INSERT
 */
function lastInsertId() {
    return getDB()->lastInsertId();
}

/**
 * Iniciar transacción
 *
 * @return bool
 */
function beginTransaction() {
    return getDB()->beginTransaction();
}

/**
 * Confirmar transacción
 *
 * @return bool
 */
function commit() {
    return getDB()->commit();
}

/**
 * Revertir transacción
 *
 * @return bool
 */
function rollback() {
    return getDB()->rollBack();
}
