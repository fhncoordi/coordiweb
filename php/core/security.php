<?php
/**
 * Funciones de Seguridad
 * Coordicanarias CMS
 *
 * Funciones de validación, sanitización y protección
 * Reutiliza funciones existentes de enviar_correo.php
 */

// ============================================
// SANITIZACIÓN (ya existen en enviar_correo.php)
// ============================================

/**
 * Sanitiza texto para prevenir XSS
 *
 * @param string $texto Texto a sanitizar
 * @return string Texto sanitizado
 */
function sanitizarTexto($texto) {
    $texto = trim($texto);
    $texto = stripslashes($texto);
    $texto = htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
    // Eliminar saltos de línea para prevenir header injection
    $texto = str_replace(array("\r", "\n", "%0a", "%0d"), '', $texto);
    return $texto;
}

/**
 * Sanitiza contenido multilínea SIN escapar HTML
 * Usar para contenido que se guardará en BD y se escapará al mostrar
 * IMPORTANTE: Este contenido DEBE escaparse con e() al mostrarlo en HTML
 *
 * @param string $texto Texto a sanitizar
 * @return string Texto sanitizado (sin escape HTML)
 */
function sanitizarContenido($texto) {
    $texto = trim($texto);
    $texto = stripslashes($texto);
    // NO aplicar htmlspecialchars aquí - se hará al mostrar
    // Preservar saltos de línea para contenido multilínea
    return $texto;
}

/**
 * Sanitiza email
 *
 * @param string $email Email a sanitizar
 * @return string Email sanitizado
 */
function sanitizarEmail($email) {
    return filter_var($email, FILTER_SANITIZE_EMAIL);
}

/**
 * Validar email
 *
 * @param string $email Email a validar
 * @return string|false Email válido o false
 */
function validarEmail($email) {
    $email = sanitizarEmail($email);
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : false;
}

// ============================================
// FUNCIONES DE ESCAPE PARA OUTPUT
// ============================================

/**
 * Escapar salida HTML
 * Usar para mostrar contenido en HTML
 *
 * @param string $string String a escapar
 * @return string String escapado
 */
function e($string) {
    if ($string === null) {
        return '';
    }
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Escapar salida de atributos HTML
 * Usar en atributos HTML como href, src, alt, etc.
 *
 * @param string $string String a escapar
 * @return string String escapado
 */
function attr($string) {
    if ($string === null) {
        return '';
    }
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// ============================================
// VALIDACIÓN DE ARCHIVOS
// ============================================

/**
 * Validar imagen subida
 *
 * @param array $file Array de $_FILES
 * @return array ['success' => bool, 'error' => string, 'extension' => string, 'mime' => string]
 */
function validarImagen($file) {
    // Verificar que se subió archivo
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['error' => 'No se seleccionó ningún archivo'];
    }

    // Verificar errores de subida
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Error al subir el archivo'];
    }

    // Validar tamaño (máx 5MB)
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        return ['error' => 'El archivo es demasiado grande. Máximo 5MB'];
    }

    // Validar tipo MIME real (no confiar solo en extensión)
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        return ['error' => 'Tipo de archivo no permitido. Solo JPG, PNG, GIF, WEBP'];
    }

    // Validar extensión
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions)) {
        return ['error' => 'Extensión no permitida'];
    }

    return ['success' => true, 'extension' => $extension, 'mime' => $mimeType];
}

/**
 * Generar nombre único para archivo
 *
 * @param string $extension Extensión del archivo
 * @return string Nombre único generado
 */
function generarNombreArchivo($extension) {
    return uniqid('img_', true) . '_' . time() . '.' . $extension;
}

/**
 * Guardar imagen subida
 *
 * @param array $file Array de $_FILES
 * @param string $directorio Directorio destino
 * @return array ['success' => bool, 'error' => string, 'filename' => string, 'path' => string]
 */
function guardarImagen($file, $directorio) {
    // Validar imagen
    $validacion = validarImagen($file);

    if (isset($validacion['error'])) {
        return $validacion;
    }

    // Crear directorio si no existe
    if (!is_dir($directorio)) {
        if (!mkdir($directorio, 0755, true)) {
            return ['error' => 'No se pudo crear el directorio'];
        }
    }

    // Generar nombre único
    $nombreArchivo = generarNombreArchivo($validacion['extension']);
    $rutaDestino = $directorio . '/' . $nombreArchivo;

    // Mover archivo
    if (!move_uploaded_file($file['tmp_name'], $rutaDestino)) {
        return ['error' => 'Error al guardar el archivo'];
    }

    return ['success' => true, 'filename' => $nombreArchivo, 'path' => $rutaDestino];
}

// ============================================
// VALIDACIÓN DE PASSWORDS
// ============================================

/**
 * Validar fortaleza de contraseña
 * Mínimo 8 caracteres, al menos 1 mayúscula, 1 minúscula, 1 número
 *
 * @param string $password Contraseña a validar
 * @return array ['valid' => bool, 'errors' => array]
 */
function validarPassword($password) {
    $errors = [];

    if (strlen($password) < 8) {
        $errors[] = 'La contraseña debe tener al menos 8 caracteres';
    }

    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Debe contener al menos una letra mayúscula';
    }

    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Debe contener al menos una letra minúscula';
    }

    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Debe contener al menos un número';
    }

    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

// ============================================
// PROTECCIÓN XSS
// ============================================
// Las funciones e() y attr() ya proporcionan protección XSS

// ============================================
// PROTECCIÓN SQL INJECTION
// ============================================
// Las prepared statements de PDO en db/connection.php ya protegen contra SQL injection
// Siempre usar query(), fetchOne(), fetchAll(), execute() de connection.php

// ============================================
// HEADERS DE SEGURIDAD
// ============================================

/**
 * Establecer headers de seguridad HTTP
 */
function setSecurityHeaders() {
    // Prevenir clickjacking
    header("X-Frame-Options: SAMEORIGIN");

    // Prevenir MIME sniffing
    header("X-Content-Type-Options: nosniff");

    // Activar protección XSS del navegador
    header("X-XSS-Protection: 1; mode=block");

    // Política de referrer
    header("Referrer-Policy: strict-origin-when-cross-origin");

    // Solo si se usa HTTPS (comentado por defecto)
    // header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
}
