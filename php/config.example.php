<?php
/**
 * Configuración de SMTP - Coordicanarias
 * ARCHIVO DE EJEMPLO - NO CONTIENE CREDENCIALES REALES
 *
 * INSTRUCCIONES PARA CONFIGURAR:
 * 1. Copia este archivo como "config.php" en el mismo directorio
 * 2. Ve a https://myaccount.google.com/apppasswords
 * 3. Crea una nueva contraseña de aplicación para "Mail"
 * 4. Copia la contraseña generada (16 caracteres)
 * 5. Pégala en SMTP_PASS en tu archivo config.php (sin espacios)
 * 6. Guarda el archivo config.php
 * 7. NUNCA subas config.php a git (ya está en .gitignore)
 */

// ============================================
// CONFIGURACIÓN DE SMTP (Google Workspace)
// ============================================
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 465); // Usa 465 (SSL) si el hosting bloquea 587 (STARTTLS)
define('SMTP_SECURE', 'ssl'); // 'ssl' para puerto 465, 'tls' para puerto 587
define('SMTP_USER', 'noreply@coordicanarias.com');
define('SMTP_PASS', 'TU_CONTRASEÑA_DE_APLICACION_AQUI'); // Reemplaza con contraseña de aplicación de Google
define('SMTP_FROM_NAME', 'Coordicanarias - Formulario Web');

// ============================================
// CONFIGURACIÓN DE EMAILS POR ÁREA
// ============================================
// Para cambiar el email de un área específica, modifica el valor correspondiente abajo
// Por ahora todos apuntan a fhn@coordicanarias.com para pruebas

$emails_por_area = array(
    'inicio'              => 'fhn@coordicanarias.com',  // Página principal
    'transparencia'       => 'fhn@coordicanarias.com',  // Página de transparencia
    'formacion'           => 'fhn@coordicanarias.com',  // Formación e Innovación
    'empleo'              => 'fhn@coordicanarias.com',  // Empleo con Apoyo
    'accesibilidad'       => 'fhn@coordicanarias.com',  // Accesibilidad
    'ocio'                => 'fhn@coordicanarias.com',  // Ocio y Tiempo Libre
    'igualdad'            => 'fhn@coordicanarias.com',  // Igualdad y Promoción de la Mujer
    'aintegral'           => 'fhn@coordicanarias.com',  // Atención Integral
    'alegal'              => 'fhn@coordicanarias.com',  // Asesoramiento Legal
    'participacion'       => 'fhn@coordicanarias.com',  // Participación Ciudadana
    'politica-cookies'    => 'fhn@coordicanarias.com',  // Política de Cookies
    'politica-privacidad' => 'fhn@coordicanarias.com',  // Política de Privacidad
    'default'             => 'fhn@coordicanarias.com'   // Email por defecto si no se especifica área
);

// ============================================
// CONFIGURACIÓN DE SEGURIDAD
// ============================================
// Solo permitir envíos desde el mismo dominio (protección anti-spam)
$dominios_permitidos = array(
    'coordicanarias.com',
    'www.coordicanarias.com',
    'localhost' // Para pruebas locales
);
