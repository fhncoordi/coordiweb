-- Migración: Tabla de documentos adjuntos a noticias
-- Fecha: 2025-02-05
-- Descripción: Permite a los coordinadores adjuntar documentos a las noticias
USE coordica_crc;

CREATE TABLE IF NOT EXISTS noticia_documentos (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    noticia_id INT(11) UNSIGNED NOT NULL COMMENT 'ID de la noticia a la que pertenece',

    -- Información visible (lo que pone el coordinador)
    titulo VARCHAR(255) NOT NULL COMMENT 'Nombre descriptivo del documento (obligatorio) - se muestra al público',

    -- Información interna del archivo
    nombre_original VARCHAR(255) NOT NULL COMMENT 'Nombre del archivo original subido',
    nombre_archivo VARCHAR(255) NOT NULL COMMENT 'Nombre único del archivo en el servidor',
    ruta_completa VARCHAR(500) NOT NULL COMMENT 'Ruta completa: uploads/documentos/...',

    -- Metadatos del archivo
    tipo_mime VARCHAR(100) NOT NULL COMMENT 'Tipo MIME: application/pdf, image/jpeg, etc.',
    extension VARCHAR(10) NOT NULL COMMENT 'Extensión: pdf, jpg, docx, etc.',
    tamano INT(11) UNSIGNED NOT NULL COMMENT 'Tamaño en bytes',

    -- Ordenamiento y auditoría
    orden INT(11) DEFAULT 0 COMMENT 'Orden de visualización',
    fecha_subida DATETIME DEFAULT CURRENT_TIMESTAMP,
    subido_por INT(11) UNSIGNED NULL COMMENT 'ID del usuario que subió el documento',

    -- Claves foráneas
    FOREIGN KEY (noticia_id) REFERENCES noticias(id) ON DELETE CASCADE,
    FOREIGN KEY (subido_por) REFERENCES usuarios(id) ON DELETE SET NULL,

    -- Índices para optimizar consultas
    INDEX idx_noticia (noticia_id),
    INDEX idx_extension (extension),
    INDEX idx_fecha (fecha_subida)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Documentos adjuntos a noticias (PDFs, imágenes, hojas de cálculo, etc.)';
