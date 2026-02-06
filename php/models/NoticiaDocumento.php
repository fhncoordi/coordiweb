<?php
/**
 * Modelo: NoticiaDocumento
 * Coordicanarias CMS
 *
 * Gestión de documentos adjuntos a noticias
 */

require_once __DIR__ . '/../db/connection.php';

class NoticiaDocumento {
    /**
     * Tipos de archivo permitidos y sus extensiones
     */
    const TIPOS_PERMITIDOS = [
        // Imágenes
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'image/gif' => ['gif'],
        'image/webp' => ['webp'],

        // Documentos PDF
        'application/pdf' => ['pdf'],

        // Microsoft Word
        'application/msword' => ['doc'],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],

        // Microsoft Excel
        'application/vnd.ms-excel' => ['xls'],
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['xlsx'],

        // Otros
        'text/plain' => ['txt'],
        'application/zip' => ['zip'],
        'application/x-zip-compressed' => ['zip']
    ];

    /**
     * Tamaño máximo de archivo: 10MB
     */
    const TAMANO_MAX = 10485760; // 10MB en bytes

    /**
     * Obtener todos los documentos de una noticia
     *
     * @param int $noticia_id ID de la noticia
     * @return array Lista de documentos ordenados
     */
    public static function getByNoticia($noticia_id) {
        $sql = "SELECT d.*, u.nombre_completo as subido_por_nombre
                FROM noticia_documentos d
                LEFT JOIN usuarios u ON d.subido_por = u.id
                WHERE d.noticia_id = ?
                ORDER BY d.orden ASC, d.fecha_subida DESC";

        return fetchAll($sql, [$noticia_id]);
    }

    /**
     * Obtener documento por ID
     *
     * @param int $id ID del documento
     * @return array|null Datos del documento o null
     */
    public static function getById($id) {
        $sql = "SELECT * FROM noticia_documentos WHERE id = ?";
        return fetchOne($sql, [$id]);
    }

    /**
     * Crear nuevo documento
     *
     * @param array $datos Datos del documento
     * @return int|false ID del documento creado o false
     */
    public static function create($datos) {
        $sql = "INSERT INTO noticia_documentos (
                    noticia_id, titulo, nombre_original, nombre_archivo, ruta_completa,
                    tipo_mime, extension, tamano, orden, subido_por
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $datos['noticia_id'],
            $datos['titulo'],
            $datos['nombre_original'],
            $datos['nombre_archivo'],
            $datos['ruta_completa'],
            $datos['tipo_mime'],
            $datos['extension'],
            $datos['tamano'],
            $datos['orden'] ?? 0,
            $datos['subido_por']
        ];

        if (execute($sql, $params)) {
            return lastInsertId();
        }

        return false;
    }

    /**
     * Eliminar documento (físicamente y de BD)
     *
     * @param int $id ID del documento
     * @return bool True si se eliminó correctamente
     */
    public static function delete($id) {
        // Obtener info del documento para eliminar archivo físico
        $doc = self::getById($id);
        if (!$doc) {
            return false;
        }

        // Eliminar de BD
        $sql = "DELETE FROM noticia_documentos WHERE id = ?";
        if (execute($sql, [$id])) {
            // Eliminar archivo físico del servidor
            $ruta_completa = __DIR__ . '/../../' . $doc['ruta_completa'];
            if (file_exists($ruta_completa)) {
                unlink($ruta_completa);
            }
            return true;
        }

        return false;
    }

    /**
     * Subir documento al servidor y guardar en BD
     *
     * @param array $archivo Archivo de $_FILES
     * @param int $noticia_id ID de la noticia
     * @param string $titulo Título descriptivo del documento
     * @return array ['success' => bool, 'message' => string, 'documento_id' => int|null]
     */
    public static function subirDocumento($archivo, $noticia_id, $titulo) {
        // Directorio de destino
        $upload_dir = __DIR__ . '/../../uploads/documentos/';

        // Crear directorio si no existe
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Validar que se subió un archivo
        if (!isset($archivo) || $archivo['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'message' => 'Error al subir el archivo. Por favor, inténtalo de nuevo.',
                'documento_id' => null
            ];
        }

        // Validar tamaño (máximo 10MB)
        if ($archivo['size'] > self::TAMANO_MAX) {
            return [
                'success' => false,
                'message' => 'El archivo es demasiado grande. El tamaño máximo permitido es 10MB.',
                'documento_id' => null
            ];
        }

        // Validar tipo MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        // Verificar que el tipo MIME y extensión sean válidos
        $extension_valida = false;
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

        foreach (self::TIPOS_PERMITIDOS as $mime => $extensiones) {
            if ($mime_type === $mime && in_array($extension, $extensiones)) {
                $extension_valida = true;
                break;
            }
        }

        if (!$extension_valida) {
            return [
                'success' => false,
                'message' => 'Tipo de archivo no permitido. Formatos aceptados: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF, WEBP, ZIP, TXT',
                'documento_id' => null
            ];
        }

        // Generar nombre único para el archivo
        $nombre_archivo = 'ndoc_' . $noticia_id . '_' . uniqid() . '.' . $extension;
        $ruta_completa = $upload_dir . $nombre_archivo;

        // Mover archivo al directorio de destino
        if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
            // Obtener ID del usuario actual
            $usuario_id = function_exists('getCurrentUserId') ? getCurrentUserId() : null;

            // Guardar en base de datos
            $datos = [
                'noticia_id' => $noticia_id,
                'titulo' => $titulo,
                'nombre_original' => $archivo['name'],
                'nombre_archivo' => $nombre_archivo,
                'ruta_completa' => 'uploads/documentos/' . $nombre_archivo,
                'tipo_mime' => $mime_type,
                'extension' => $extension,
                'tamano' => $archivo['size'],
                'orden' => 0,
                'subido_por' => $usuario_id
            ];

            $documento_id = self::create($datos);

            if ($documento_id) {
                return [
                    'success' => true,
                    'message' => 'Documento subido correctamente',
                    'documento_id' => $documento_id
                ];
            } else {
                // Si falla la BD, eliminar el archivo físico
                if (file_exists($ruta_completa)) {
                    unlink($ruta_completa);
                }
                return [
                    'success' => false,
                    'message' => 'Error al guardar el documento en la base de datos',
                    'documento_id' => null
                ];
            }
        }

        return [
            'success' => false,
            'message' => 'Error al mover el archivo al servidor',
            'documento_id' => null
        ];
    }

    /**
     * Formatear tamaño de archivo en formato legible
     *
     * @param int $bytes Tamaño en bytes
     * @return string Tamaño formateado (ej: "2.5 MB")
     */
    public static function formatearTamano($bytes) {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Obtener icono de FontAwesome según la extensión del archivo
     *
     * @param string $extension Extensión del archivo
     * @return string Clase de icono FontAwesome
     */
    public static function getIcono($extension) {
        $iconos = [
            'pdf' => 'fa-file-pdf',
            'doc' => 'fa-file-word',
            'docx' => 'fa-file-word',
            'xls' => 'fa-file-excel',
            'xlsx' => 'fa-file-excel',
            'jpg' => 'fa-file-image',
            'jpeg' => 'fa-file-image',
            'png' => 'fa-file-image',
            'gif' => 'fa-file-image',
            'webp' => 'fa-file-image',
            'zip' => 'fa-file-archive',
            'txt' => 'fa-file-alt'
        ];

        return $iconos[$extension] ?? 'fa-file';
    }

    /**
     * Obtener gradiente de color según la extensión del archivo
     *
     * @param string $extension Extensión del archivo
     * @return string CSS gradient
     */
    public static function getGradiente($extension) {
        $gradientes = [
            'pdf' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            'doc' => 'linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%)',
            'docx' => 'linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%)',
            'xls' => 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)',
            'xlsx' => 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)',
            'jpg' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
            'jpeg' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
            'png' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
            'gif' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
            'webp' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
            'zip' => 'linear-gradient(135deg, #434343 0%, #000000 100%)',
            'txt' => 'linear-gradient(135deg, #868f96 0%, #596164 100%)'
        ];

        return $gradientes[$extension] ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
    }

    /**
     * Contar documentos de una noticia
     *
     * @param int $noticia_id ID de la noticia
     * @return int Número de documentos
     */
    public static function contar($noticia_id) {
        $sql = "SELECT COUNT(*) as total FROM noticia_documentos WHERE noticia_id = ?";
        $resultado = fetchOne($sql, [$noticia_id]);
        return $resultado['total'] ?? 0;
    }
}
