<?php
/**
 * Página de Área: Atención Integral
 * Coordicanarias CMS
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/db/connection.php';
require_once __DIR__ . '/../php/core/security.php';
require_once __DIR__ . '/../php/models/Area.php';
require_once __DIR__ . '/../php/models/Servicio.php';
require_once __DIR__ . '/../php/models/Beneficio.php';
require_once __DIR__ . '/../php/models/Proyecto.php';
require_once __DIR__ . '/../php/models/ProyectoDocumento.php';
require_once __DIR__ . '/../php/models/Configuracion.php';
require_once __DIR__ . '/../php/models/Noticia.php';
require_once __DIR__ . '/../php/form_security_helper.php';

// Obtener el área actual por slug
$area_slug = 'aintegral';
$area = fetchOne("SELECT * FROM areas WHERE slug = ? AND activo = 1", [$area_slug]);

// Si no existe el área, redirigir
if (!$area) {
    header('Location: ../index.php');
    exit;
}

// Obtener servicios, beneficios y proyectos del área
$servicios = Servicio::getAll(true, $area['id']);
$beneficios = Beneficio::getAll(true, $area['id']);
// Obtener proyectos destacados (activos e históricos)
$proyectos = Proyecto::getByArea($area['id']);
// Obtener noticias activas del área (destacadas o no)
$noticias = Noticia::getAll(true, 0, $area['id']);

// Obtener configuración del sitio (para redes sociales)
$config = Configuracion::getAll();

// Helper functions para escapar HTML
if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('attr')) {
    function attr($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// Helper function para formatear fechas en español
if (!function_exists('formatearFecha')) {
    function formatearFecha($fecha, $formato = 'corto') {
        $meses = [
            'Jan' => 'Ene', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Abr',
            'May' => 'May', 'Jun' => 'Jun', 'Jul' => 'Jul', 'Aug' => 'Ago',
            'Sep' => 'Sep', 'Oct' => 'Oct', 'Nov' => 'Nov', 'Dec' => 'Dic'
        ];

        if ($formato === 'corto') {
            $fecha_en = date('d M, Y', strtotime($fecha));
            return str_replace(array_keys($meses), array_values($meses), $fecha_en);
        } else {
            $meses_largo = [
                'January' => 'enero', 'February' => 'febrero', 'March' => 'marzo', 'April' => 'abril',
                'May' => 'mayo', 'June' => 'junio', 'July' => 'julio', 'August' => 'agosto',
                'September' => 'septiembre', 'October' => 'octubre', 'November' => 'noviembre', 'December' => 'diciembre'
            ];
            $fecha_en = date('d F, Y', strtotime($fecha));
            return str_replace(array_keys($meses_largo), array_values($meses_largo), $fecha_en);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/html">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Coordicanarias</title>
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <!-- Stylesheets -->
    <link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../css/fontawesome-all.min.css" rel="stylesheet" type="text/css">
    <link href="../css/style.css" rel="stylesheet" type="text/css">
    <link href="../css/my.css" rel="stylesheet" type="text/css">
    <!-- reCAPTCHA v3 -->
    <?php echo generar_script_recaptcha(); ?>

    <!-- Configuracion para JavaScript -->
    <script>
        window.RECAPTCHA_SITE_KEY = '<?php echo obtener_recaptcha_site_key(); ?>';
    </script>
</head>

<body id="home" class="sticky-bar menu-standard ">
    <div id="lab-main">
        <!--Accessibility skip menu-->
        <nav id="lab-skip-menu" role="navigation" aria-label="Saltar sección">
            <div class="lab-skip-menu">
                <ul id="menu-skip-menu" class="menu">
                    <li class="menu-item">
                        <a href="#jumbotron">Saltar al contenido principal</a>
                    </li>
                    <li class="menu-item">
                        <a href="#block_settings">Saltar al menú de accesibilidad</a>
                    </li>
                    <li class="menu-item">
                        <a href="#lab-main-menu">Saltar al menú principal</a>
                    </li>
                    <li class="menu-item">
                        <a href="#lab-footer">Saltar al pie de página</a>
                    </li>
                </ul>
            </div>
        </nav>
        <!--Accessibility skip menu end-->
        <?php include __DIR__ . '/../php/components/panel-accesibilidad.php'; ?>
        <!--Header-->
        <header id="lab-header" class="header-full topbar-mode-default topbar-shadow-default" style="padding-top: 112px;">
            <div id="lab-header-in">
                <div id="lab-logo-nav">
                    <!--Header top bar-->
                    <div id="lab-wcag" class="lab-container">
                        <div class="icon-scp-top-left">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                    <path d="M 8.65625 3 C 8.132813 3 7.617188 3.1875 7.1875 3.53125 L 7.125 3.5625 L 7.09375 3.59375 L 3.96875 6.8125 L 4 6.84375 C 3.035156 7.734375 2.738281 9.066406 3.15625 10.21875 C 3.160156 10.226563 3.152344 10.242188 3.15625 10.25 C 4.003906 12.675781 6.171875 17.359375 10.40625 21.59375 C 14.65625 25.84375 19.402344 27.925781 21.75 28.84375 L 21.78125 28.84375 C 22.996094 29.25 24.3125 28.960938 25.25 28.15625 L 28.40625 25 C 29.234375 24.171875 29.234375 22.734375 28.40625 21.90625 L 24.34375 17.84375 L 24.3125 17.78125 C 23.484375 16.953125 22.015625 16.953125 21.1875 17.78125 L 19.1875 19.78125 C 18.464844 19.433594 16.742188 18.542969 15.09375 16.96875 C 13.457031 15.40625 12.621094 13.609375 12.3125 12.90625 L 14.3125 10.90625 C 15.152344 10.066406 15.167969 8.667969 14.28125 7.84375 L 14.3125 7.8125 L 14.21875 7.71875 L 10.21875 3.59375 L 10.1875 3.5625 L 10.125 3.53125 C 9.695313 3.1875 9.179688 3 8.65625 3 Z M 8.65625 5 C 8.730469 5 8.804688 5.035156 8.875 5.09375 L 12.875 9.1875 L 12.96875 9.28125 C 12.960938 9.273438 13.027344 9.378906 12.90625 9.5 L 10.40625 12 L 9.9375 12.4375 L 10.15625 13.0625 C 10.15625 13.0625 11.304688 16.136719 13.71875 18.4375 L 13.9375 18.625 C 16.261719 20.746094 19 21.90625 19 21.90625 L 19.625 22.1875 L 22.59375 19.21875 C 22.765625 19.046875 22.734375 19.046875 22.90625 19.21875 L 27 23.3125 C 27.171875 23.484375 27.171875 23.421875 27 23.59375 L 23.9375 26.65625 C 23.476563 27.050781 22.988281 27.132813 22.40625 26.9375 C 20.140625 26.046875 15.738281 24.113281 11.8125 20.1875 C 7.855469 16.230469 5.789063 11.742188 5.03125 9.5625 C 4.878906 9.15625 4.988281 8.554688 5.34375 8.25 L 5.40625 8.1875 L 8.4375 5.09375 C 8.507813 5.035156 8.582031 5 8.65625 5 Z" />
                                </svg>922 21 59 09 | info@coordicanarias.com </div>
                        </div>
                        <div class="ml-auto icon-scp-top">
                            <a href="https://www.facebook.com/CoordiCanarias/" aria-label="Facebook de Coordicanarias" tabindex="0">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                    <path d="M 19.253906 2 C 15.311906 2 13 4.0821719 13 8.8261719 L 13 13 L 8 13 L 8 18 L 13 18 L 13 30 L 18 30 L 18 18 L 22 18 L 23 13 L 18 13 L 18 9.671875 C 18 7.884875 18.582766 7 20.259766 7 L 23 7 L 23 2.2050781 C 22.526 2.1410781 21.144906 2 19.253906 2 z" />
                                </svg>
                            </a>
                            <a href="https://x.com/coordicanarias" aria-label="X (antes Twitter) de Coordicanarias" tabindex="0">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                    <path d="M 18.42 14.009 L 27.891 3 L 25.703 3 L 17.446 12.588 L 10.894 3 L 3 3 L 12.921 17.411 L 3 29 L 5.188 29 L 13.895 19.006 L 20.806 29 L 28.7 29 L 18.42 14.009 Z M 15.026 17.708 L 14.07 16.393 L 5.95 4.56 L 9.744 4.56 L 16.209 14.011 L 17.165 15.326 L 25.704 27.517 L 21.91 27.517 L 15.026 17.708 Z" />
                                </svg>
                            </a>
                            <a href="https://es.linkedin.com/company/coordicanarias" aria-label="LinkedIn de Coordicanarias" tabindex="0">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                    <path d="M 8.6425781 4 C 7.1835781 4 6 5.181625 6 6.640625 C 6 8.099625 7.182625 9.3085938 8.640625 9.3085938 C 10.098625 9.3085938 11.283203 8.099625 11.283203 6.640625 C 11.283203 5.182625 10.101578 4 8.6425781 4 z M 21.535156 11 C 19.316156 11 18.0465 12.160453 17.4375 13.314453 L 17.373047 13.314453 L 17.373047 11.310547 L 13 11.310547 L 13 26 L 17.556641 26 L 17.556641 18.728516 C 17.556641 16.812516 17.701266 14.960938 20.072266 14.960938 C 22.409266 14.960937 22.443359 17.145609 22.443359 18.849609 L 22.443359 26 L 26.994141 26 L 27 26 L 27 17.931641 C 27 13.983641 26.151156 11 21.535156 11 z M 6.3632812 11.310547 L 6.3632812 26 L 10.923828 26 L 10.923828 11.310547 L 6.3632812 11.310547 z" />
                                </svg>
                            </a>
                            <a href="https://www.instagram.com/coordicanarias"
                               aria-label="Instagram de Coordicanarias"
                               tabindex="0">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 32 32"
                                     role="img"
                                     aria-labelledby="instagram-icon"
                                     focusable="false">
                                    <title id="instagram-icon">Instagram</title>
                                    <path d="M 11.46875 5 C 7.917969 5 5 7.914062 5 11.46875 L 5 20.53125 C 5 24.082031 7.914062 27 11.46875 27 L 20.53125 27 C 24.082031 27 27 24.085938 27 20.53125 L 27 11.46875 C 27 7.917969 24.085938 5 20.53125 5 Z M 11.46875 7 L 20.53125 7 C 23.003906 7 25 8.996094 25 11.46875 L 25 20.53125 C 25 23.003906 23.003906 25 20.53125 25 L 11.46875 25 C 8.996094 25 7 23.003906 7 20.53125 L 7 11.46875 C 7 8.996094 8.996094 7 11.46875 7 Z M 21.90625 9.1875 C 21.402344 9.1875 21 9.589844 21 10.09375 C 21 10.597656 21.402344 11 21.90625 11 C 22.410156 11 22.8125 10.597656 22.8125 10.09375 C 22.8125 9.589844 22.410156 9.1875 21.90625 9.1875 Z M 16 10 C 12.699219 10 10 12.699219 10 16 C 10 19.300781 12.699219 22 16 22 C 19.300781 22 22 19.300781 22 16 C 22 12.699219 19.300781 10 16 10 Z M 16 12 C 18.222656 12 20 13.777344 20 16 C 20 18.222656 18.222656 20 16 20 C 13.777344 20 12 18.222656 12 16 C 12 13.777344 13.777344 12 16 12 Z"/>
                                </svg>
                            </a>
                            <!-- Icono de búsqueda -->
                            <a href="#"
                               id="search-icon-trigger"
                               aria-label="Buscar en el sitio"
                               tabindex="0"
                               role="button"
                               aria-expanded="false"
                               aria-controls="search-modal">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                    <path d="M 19 3 C 13.488281 3 9 7.488281 9 13 C 9 15.394531 9.839844 17.589844 11.25 19.3125 L 3.28125 27.28125 L 4.71875 28.71875 L 12.6875 20.75 C 14.410156 22.160156 16.605469 23 19 23 C 24.511719 23 29 18.511719 29 13 C 29 7.488281 24.511719 3 19 3 Z M 19 5 C 23.429688 5 27 8.570313 27 13 C 27 17.429688 23.429688 21 19 21 C 14.570313 21 11 17.429688 11 13 C 11 8.570313 14.570313 5 19 5 Z"/>
                                </svg>
                            </a>
                        </div>

                        <!-- Modal de búsqueda -->
                        <div id="search-modal" class="search-modal" role="dialog" aria-modal="true" aria-labelledby="search-modal-title">
                            <div class="search-modal-overlay"></div>
                            <div class="search-modal-content">
                                <div class="search-modal-header">
                                    <h2 id="search-modal-title" class="sr-only">Buscar en Coordicanarias</h2>
                                    <button
                                            id="search-modal-close"
                                            class="search-modal-close"
                                            aria-label="Cerrar buscador"
                                            tabindex="0">
                                    </button>
                                </div>
                                <div class="search-modal-body">
                                    <script async src="https://cse.google.com/cse.js?cx=406aa1d8b8d294efe"></script>
                                    <div class="gcse-search"
                                         data-enableAutoComplete="true"
                                         data-autoSearchOnLoad="false"
                                         data-gaCategoryParameter="search-modal">
                                    </div>
                                </div>
                            </div>

                            <!--<a aria-label="Pinterest" tabindex="0">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                    <path d="M 16.09375 4 C 11.01675 4 6 7.3833281 6 12.861328 C 6 16.344328 7.9584844 18.324219 9.1464844 18.324219 C 9.6364844 18.324219 9.9199219 16.958266 9.9199219 16.572266 C 9.9199219 16.112266 8.7460938 15.131797 8.7460938 13.216797 C 8.7460938 9.2387969 11.774359 6.4199219 15.693359 6.4199219 C 19.063359 6.4199219 21.556641 8.3335625 21.556641 11.851562 C 21.556641 14.478563 20.501891 19.40625 17.087891 19.40625 C 15.855891 19.40625 14.802734 18.516234 14.802734 17.240234 C 14.802734 15.370234 16 13.558906 16 11.628906 C 16 8.3529063 11.462891 8.94725 11.462891 12.90625 C 11.462891 13.73725 11.5665 14.657063 11.9375 15.414062 C 11.2555 18.353063 10 23.037406 10 26.066406 C 10 27.001406 10.133656 27.921422 10.222656 28.857422 C 10.390656 29.045422 10.307453 29.025641 10.564453 28.931641 C 13.058453 25.517641 12.827078 24.544172 13.955078 20.076172 C 14.564078 21.234172 16.137766 21.857422 17.384766 21.857422 C 22.639766 21.857422 25 16.736141 25 12.119141 C 25 7.2061406 20.75475 4 16.09375 4 z" />
                                </svg>
                            </a>-->
                        </div>
                    </div>
                    <!--Header top bar end-->
                    <!--Header middle bar-->
                    <div class="lab-container">
                        <div id="lab-bar-left">
                            <div id="lab-logo">
                                <a href="../index.php" class="" title="Logo de Coordicanarias" rel="home">
                                    <span class="">
                                        <picture>
                                            <img src="../images/brand-coordi-black.svg" width="250" alt="Logo de Coordicanarias" class="" />
                                        </picture>
                                    </span>
                                </a>
                            </div>
                        </div>
                        <div id="lab-bar-right">
                            <nav id="lab-main-menu" tabindex="-1" aria-label="Primary menu">
                                <div class="lab-main-menu">
                                    <ul id="menu-main-menu" class="nav-menu">
                                        <li class="menu-item">
                                            <a href="../index.php" class="">Inicio</a>
                                        </li>
                                        <li class="menu-item">
                                            <a href="../transparencia.php" class="">Transparencia</a>
                                        </li>
                                        <li class="menu-item">
                                            <a class="" data-scroll href="#about-ai">Atención integral</a>
                                        </li>
                                        <li class="menu-item">
                                            <a class="" data-scroll href="#services-ai">Servicios</a>
                                        </li>
                                        <li class="menu-item">
                                            <a class="" data-scroll href="#portfolios-ai">Proyectos</a>
                                        </li>
                                        <li class="menu-item">
                                            <a class="" data-scroll href="#beneficios-ai">Beneficios</a>
                                        </li>
                                        <li class="menu-item">
                                        <?php if (count($noticias) > 0): ?>
                                        <li class="menu-item">
                                            <a class="" data-scroll href="#news-ai">Noticias</a>
                                        </li>
                                        <?php endif; ?>
                                            <a class="" data-scroll href="#participa-ai">Participa</a>
                                        </li>
                                        <li class="menu-item">
                                            <a href="../index.php#features" class="">Áreas</a>
                                        </li>
                                    </ul>
                                </div>
                            </nav>
                            <div id="lab-offcanvas-button">
                                <a class="toggle-nav open" tabindex="1">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 4 7 L 4 9 L 28 9 L 28 7 Z M 4 15 L 4 17 L 28 17 L 28 15 Z M 4 23 L 4 25 L 28 25 L 28 23 Z" />
                                    </svg>
                                    <span class="sr-only">Menú lateral</span>
                                </a>
                            </div>
                            <div id="lab-offcanvas" class="off-canvas-right">
                                <div id="lab-offcanvas-toolbar">
                                    <a class="toggle-nav-close close" tabindex="1">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                            <path d="M 7.21875 5.78125 L 5.78125 7.21875 L 14.5625 16 L 5.78125 24.78125 L 7.21875 26.21875 L 16 17.4375 L 24.78125 26.21875 L 26.21875 24.78125 L 17.4375 16 L 26.21875 7.21875 L 24.78125 5.78125 L 16 14.5625 Z" />
                                        </svg>
                                        <span class="sr-only">Cerrar menú lateral</span>
                                    </a>
                                </div>
                                <div id="lab-offcanvas-content" class="">
                                    <div class="row">
                                        <div class="lab-offcanvas-menu lab-widget">
                                            <ul id="menu-main-menu-1" class="nav-menu">
                                                <li class="menu-item current-menu-item">
                                                    <a href="../index.php" class="">Inicio</a>
                                                </li>
                                                <li class="menu-item">
                                                    <a class="" href="../transparencia.php">Transparencia</a>
                                                </li>
                                                <li class="menu-item">
                                                    <a class="" href="#about-ai">Atención integral</a>
                                                </li>
                                                <li class="menu-item">
                                                    <a class="" href="#services-ai">Servicios</a>
                                                </li>
                                                <li class="menu-item">
                                                    <a class="" href="#portfolios-ai">Proyectos</a>
                                                </li>
                                                <li class="menu-item">
                                                    <a class="" href="#beneficios-ai">Beneficios</a>
                                                </li>
                                                <li class="menu-item">
                                        <?php if (count($noticias) > 0): ?>
                                        <li class="menu-item">
                                            <a class="" data-scroll href="#news-ai">Noticias</a>
                                        </li>
                                        <?php endif; ?>
                                                    <a class="" data-scroll href="#participa-ai">Participa</a>
                                                </li>
                                                <li class="menu-item">
                                                    <a class="" href="../index.php#features">Áreas</a>
                                                </li>
                                                <li class="menu-item menu-search-item">
                                                    <form id="mobile-search-form" class="mobile-search-form" role="search" aria-label="Buscar en el sitio">
                                                        <div class="search-input-wrapper">
                                                            <input
                                                                type="search"
                                                                id="mobile-search-input"
                                                                class="mobile-search-input"
                                                                placeholder="Buscar..."
                                                                aria-label="Buscar en Coordicanarias"
                                                                autocomplete="off"
                                                                required>
                                                            <button type="submit" class="mobile-search-button" aria-label="Buscar">
                                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                                                    <path d="M 19 3 C 13.488281 3 9 7.488281 9 13 C 9 15.394531 9.839844 17.589844 11.25 19.3125 L 3.28125 27.28125 L 4.71875 28.71875 L 12.6875 20.75 C 14.410156 22.160156 16.605469 23 19 23 C 24.511719 23 29 18.511719 29 13 C 29 7.488281 24.511719 3 19 3 Z M 19 5 C 23.429688 5 27 8.570313 27 13 C 27 17.429688 23.429688 21 19 21 C 14.570313 21 11 17.429688 11 13 C 11 8.570313 14.570313 5 19 5 Z"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--Header middle bar end-->
                </div>
            </div>
        </header>
        <!--Header end-->
        <main>
        <!--Jumbotron-->
        <div class="jumbotron_ai" id="jumbotron">
            <h1 class="display-4" style="padding-left: 20px; padding-right: 20px">Atención Integral</h1>
            <p class="lead" style="padding-bottom: 60px;">Apoyo multidisciplinario para mejorar la calidad de vida y autonomía personal</p>
            <!--<hr class="my-4">-->
            <!--<p>Me cago en todo lo que se menea</p>-->
            <!--<a class="wm-button button" href="#about-ai" role="button">Atención integral</a>-->
        </div>
        <!--Jumbotron end-->

        <!--About section-->
        <!--Descripción del Área-->
        <section id="about-ai" class="section">
            <div class="main-container">
                <div class="inside-container">
                    <div class="row">
                        <div class="col-12">
                            <h2 style="margin-bottom: 30px;">¿Qué es el Área de Atención Integral?</h2>
                            <p style="font-size: 1.1em; line-height: 1.8; margin-bottom: 20px;">
                                El Área de Atención Integral de Coordicanarias ofrece programas de apoyo multidisciplinario
                                diseñados para mejorar la calidad de vida y fomentar la autonomía personal de las personas
                                con discapacidad. Trabajamos desde un enfoque holístico que contempla todas las dimensiones
                                del bienestar: física, psicológica, social y emocional.
                            </p>
                            <p style="font-size: 1.1em; line-height: 1.8; margin-bottom: 20px;">
                                Nuestro equipo está compuesto por profesionales especializados que brindan asistencia social,
                                psicológica y fisioterapia, acompañando a cada persona en su proceso hacia una vida
                                más independiente y plena.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--About section end-->


        <!--Servicios section-->
        <section id="services-ai" class="section section-bg">
            <div class="main-container">
                <div class="inside-container">
                    <div class="row">
                        <div class="col-12">
                            <h2 style="margin-bottom: 40px;">Nuestros servicios</h2>
                        </div>
                    </div>
                    <div class="row">
                        <?php if (!empty($servicios)): ?>
                            <?php foreach ($servicios as $servicio): ?>
                                <div class="col-md-4 mb-4">
                                    <div style="background: #fff; padding: 30px; border-radius: 8px; height: 100%; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                        <h3 style="margin-bottom: 15px;"><?= e($servicio['titulo']) ?></h3>
                                        <p><?= e($servicio['descripcion']) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <p class="text-center">No hay servicios disponibles en este momento.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Proyectos del área Section-->
        <section id="portfolios-ai" class="section">
            <!-- Container Starts -->
            <div class="main-container">
                <div class="inside-container">
                    <div class="row">
                        <div class="col-12">
                            <h2 style="margin-bottom: 40px;">Proyectos destacados</h2>
                        </div>
                    </div>
                    <div class="row">
                        <?php if (!empty($proyectos)): ?>
                            <?php foreach ($proyectos as $proyecto): ?>
                                <?php $documentos_proyecto = ProyectoDocumento::getByProyecto($proyecto['id']); ?>
                                <div class="col-lg-6 mb-4">
                                    <article style="background: #f8f8f8; padding: 30px; border-radius: 8px; height: 100%;">
                                        <h3 style="margin-bottom: 15px;">
                                            <?= e($proyecto['titulo']) ?>
                                            <?php if ($proyecto['activo'] == 0): ?>
                                                <span class="badge bg-secondary ms-2" style="font-size: 0.6em; vertical-align: middle;">Finalizado</span>
                                            <?php endif; ?>
                                        </h3>
                                        <?php if (!empty($proyecto['imagen'])): ?>
                                            <img src="../<?= e($proyecto['imagen']) ?>"
                                                 alt="<?= attr($proyecto['titulo']) ?>"
                                                 style="width: 100%; height: 250px; object-fit: cover; border-radius: 8px; margin-bottom: 20px;">
                                        <?php endif; ?>
                                        <div style="margin-bottom: 15px;">
                                            <?= nl2br(e($proyecto['descripcion'])) ?>
                                        </div>
                                        <?php if (!empty($documentos_proyecto)): ?>
                                        <div style="margin-top: 25px; padding-top: 25px; border-top: 2px solid #ddd;">
                                            <h4 style="font-size: 1.15em; margin-bottom: 20px; color: #243659; display: flex; align-items: center;">
                                                <i class="fas fa-download" style="margin-right: 10px; color: #27ae60; font-size: 1.3em;"></i>
                                                Documentos disponibles
                                            </h4>
                                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                                <?php foreach ($documentos_proyecto as $doc): ?>
                                                <a href="../<?= e($doc['ruta_completa']) ?>" download="<?= attr($doc['nombre_original']) ?>"
                                                   style="display: flex; align-items: center; padding: 18px 20px;
                                                          background: <?= ProyectoDocumento::getGradiente($doc['extension']) ?>;
                                                          border-radius: 10px; text-decoration: none; color: white;
                                                          transition: all 0.3s ease; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"
                                                   onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 15px rgba(0,0,0,0.2)'"
                                                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)'">
                                                    <div style="min-width: 55px; text-align: center; margin-right: 20px;">
                                                        <i class="fas <?= ProyectoDocumento::getIcono($doc['extension']) ?>" style="font-size: 2.8em; opacity: 0.95;"></i>
                                                    </div>
                                                    <div style="flex: 1;">
                                                        <strong style="font-size: 1.15em; display: block; margin-bottom: 6px; line-height: 1.3;">
                                                            <?= e($doc['titulo']) ?>
                                                        </strong>
                                                        <small style="opacity: 0.85; font-size: 0.9em;">
                                                            <i class="fas fa-file" style="margin-right: 5px;"></i>
                                                            <?= strtoupper($doc['extension']) ?> · <?= ProyectoDocumento::formatearTamano($doc['tamano']) ?>
                                                        </small>
                                                    </div>
                                                    <div style="min-width: 45px; text-align: center; margin-left: 15px;">
                                                        <i class="fas fa-download" style="font-size: 1.6em; opacity: 0.95;"></i>
                                                    </div>
                                                </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <p class="text-center">No hay proyectos disponibles en este momento.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Container Ends -->
        </section>
        <!-- Proyectos del área Section end -->


        <!-- Team Section
        <section id="team" class="team section">
            <div class="main-container section-bg">
                <div class="inside-container">
                    <div class="row">
                        <div class="col-12 our-header">
                            <h2>Equipo</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="team-item">
                                <figure tabindex="0">
                                    <img src="images/team/member-1.jpg" alt="Member 1">
                                    <figcaption>
                                        <div class="info">
                                            <h3>Alice. W</h3>
                                            <p>Chief Executive Officer</p>
                                        </div>
                                        <div class="social">
                                            <a aria-label="Facebook" class="" data-abc="true" tabindex="0"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                                    <path d="M 19.253906 2 C 15.311906 2 13 4.0821719 13 8.8261719 L 13 13 L 8 13 L 8 18 L 13 18 L 13 30 L 18 30 L 18 18 L 22 18 L 23 13 L 18 13 L 18 9.671875 C 18 7.884875 18.582766 7 20.259766 7 L 23 7 L 23 2.2050781 C 22.526 2.1410781 21.144906 2 19.253906 2 z" />
                                                </svg></a><a aria-label="Twitter" class="" data-abc="true" tabindex="0"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                                    <path d="M 28 8.558594 C 27.117188 8.949219 26.167969 9.214844 25.171875 9.332031 C 26.1875 8.722656 26.96875 7.757813 27.335938 6.609375 C 26.386719 7.171875 25.332031 7.582031 24.210938 7.804688 C 23.3125 6.847656 22.03125 6.246094 20.617188 6.246094 C 17.898438 6.246094 15.691406 8.453125 15.691406 11.171875 C 15.691406 11.558594 15.734375 11.933594 15.820313 12.292969 C 11.726563 12.089844 8.097656 10.128906 5.671875 7.148438 C 5.246094 7.875 5.003906 8.722656 5.003906 9.625 C 5.003906 11.332031 5.871094 12.839844 7.195313 13.722656 C 6.386719 13.695313 5.628906 13.476563 4.964844 13.105469 C 4.964844 13.128906 4.964844 13.148438 4.964844 13.167969 C 4.964844 15.554688 6.660156 17.546875 8.914063 17.996094 C 8.5 18.109375 8.066406 18.171875 7.617188 18.171875 C 7.300781 18.171875 6.988281 18.140625 6.691406 18.082031 C 7.316406 20.039063 9.136719 21.460938 11.289063 21.503906 C 9.605469 22.824219 7.480469 23.609375 5.175781 23.609375 C 4.777344 23.609375 4.386719 23.585938 4 23.539063 C 6.179688 24.9375 8.765625 25.753906 11.546875 25.753906 C 20.605469 25.753906 25.558594 18.25 25.558594 11.742188 C 25.558594 11.53125 25.550781 11.316406 25.542969 11.105469 C 26.503906 10.410156 27.339844 9.542969 28 8.558594 Z" />
                                                </svg></a><a aria-label="LinkedIn" class="" data-abc="true" tabindex="0"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                                    <path d="M 8.6425781 4 C 7.1835781 4 6 5.181625 6 6.640625 C 6 8.099625 7.182625 9.3085938 8.640625 9.3085938 C 10.098625 9.3085938 11.283203 8.099625 11.283203 6.640625 C 11.283203 5.182625 10.101578 4 8.6425781 4 z M 21.535156 11 C 19.316156 11 18.0465 12.160453 17.4375 13.314453 L 17.373047 13.314453 L 17.373047 11.310547 L 13 11.310547 L 13 26 L 17.556641 26 L 17.556641 18.728516 C 17.556641 16.812516 17.701266 14.960938 20.072266 14.960938 C 22.409266 14.960937 22.443359 17.145609 22.443359 18.849609 L 22.443359 26 L 26.994141 26 L 27 26 L 27 17.931641 C 27 13.983641 26.151156 11 21.535156 11 z M 6.3632812 11.310547 L 6.3632812 26 L 10.923828 26 L 10.923828 11.310547 L 6.3632812 11.310547 z" />
                                                </svg></a>
                                        </div>
                                    </figcaption>
                                </figure>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="team-item">
                                <figure tabindex="0">
                                    <img src="images/team/member-2.jpg" alt="Member 2">
                                    <figcaption>
                                        <div class="info">
                                            <h3>Ben. H</h3>
                                            <p>Development Lead</p>
                                        </div>
                                        <div class="social">
                                            <a aria-label="Facebook" class="" data-abc="true" tabindex="0"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                                    <path d="M 19.253906 2 C 15.311906 2 13 4.0821719 13 8.8261719 L 13 13 L 8 13 L 8 18 L 13 18 L 13 30 L 18 30 L 18 18 L 22 18 L 23 13 L 18 13 L 18 9.671875 C 18 7.884875 18.582766 7 20.259766 7 L 23 7 L 23 2.2050781 C 22.526 2.1410781 21.144906 2 19.253906 2 z" />
                                                </svg></a><a aria-label="Twitter" class="" data-abc="true" tabindex="0"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                                    <path d="M 28 8.558594 C 27.117188 8.949219 26.167969 9.214844 25.171875 9.332031 C 26.1875 8.722656 26.96875 7.757813 27.335938 6.609375 C 26.386719 7.171875 25.332031 7.582031 24.210938 7.804688 C 23.3125 6.847656 22.03125 6.246094 20.617188 6.246094 C 17.898438 6.246094 15.691406 8.453125 15.691406 11.171875 C 15.691406 11.558594 15.734375 11.933594 15.820313 12.292969 C 11.726563 12.089844 8.097656 10.128906 5.671875 7.148438 C 5.246094 7.875 5.003906 8.722656 5.003906 9.625 C 5.003906 11.332031 5.871094 12.839844 7.195313 13.722656 C 6.386719 13.695313 5.628906 13.476563 4.964844 13.105469 C 4.964844 13.128906 4.964844 13.148438 4.964844 13.167969 C 4.964844 15.554688 6.660156 17.546875 8.914063 17.996094 C 8.5 18.109375 8.066406 18.171875 7.617188 18.171875 C 7.300781 18.171875 6.988281 18.140625 6.691406 18.082031 C 7.316406 20.039063 9.136719 21.460938 11.289063 21.503906 C 9.605469 22.824219 7.480469 23.609375 5.175781 23.609375 C 4.777344 23.609375 4.386719 23.585938 4 23.539063 C 6.179688 24.9375 8.765625 25.753906 11.546875 25.753906 C 20.605469 25.753906 25.558594 18.25 25.558594 11.742188 C 25.558594 11.53125 25.550781 11.316406 25.542969 11.105469 C 26.503906 10.410156 27.339844 9.542969 28 8.558594 Z" />
                                                </svg></a><a aria-label="LinkedIn" class="" data-abc="true" tabindex="0"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                                    <path d="M 8.6425781 4 C 7.1835781 4 6 5.181625 6 6.640625 C 6 8.099625 7.182625 9.3085938 8.640625 9.3085938 C 10.098625 9.3085938 11.283203 8.099625 11.283203 6.640625 C 11.283203 5.182625 10.101578 4 8.6425781 4 z M 21.535156 11 C 19.316156 11 18.0465 12.160453 17.4375 13.314453 L 17.373047 13.314453 L 17.373047 11.310547 L 13 11.310547 L 13 26 L 17.556641 26 L 17.556641 18.728516 C 17.556641 16.812516 17.701266 14.960938 20.072266 14.960938 C 22.409266 14.960937 22.443359 17.145609 22.443359 18.849609 L 22.443359 26 L 26.994141 26 L 27 26 L 27 17.931641 C 27 13.983641 26.151156 11 21.535156 11 z M 6.3632812 11.310547 L 6.3632812 26 L 10.923828 26 L 10.923828 11.310547 L 6.3632812 11.310547 z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </figcaption>
                                </figure>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="team-item">
                                <figure tabindex="0">
                                    <img src="images/team/member-3.jpg" alt="Member 3">
                                    <figcaption>
                                        <div class="info">
                                            <h3>Catherine. G</h3>
                                            <p>Marketing Manager</p>
                                        </div>
                                        <div class="social">
                                            <a aria-label="Facebook" class="" data-abc="true" tabindex="0"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                                    <path d="M 19.253906 2 C 15.311906 2 13 4.0821719 13 8.8261719 L 13 13 L 8 13 L 8 18 L 13 18 L 13 30 L 18 30 L 18 18 L 22 18 L 23 13 L 18 13 L 18 9.671875 C 18 7.884875 18.582766 7 20.259766 7 L 23 7 L 23 2.2050781 C 22.526 2.1410781 21.144906 2 19.253906 2 z" />
                                                </svg></a><a aria-label="Twitter" class="" data-abc="true" tabindex="0"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                                    <path d="M 28 8.558594 C 27.117188 8.949219 26.167969 9.214844 25.171875 9.332031 C 26.1875 8.722656 26.96875 7.757813 27.335938 6.609375 C 26.386719 7.171875 25.332031 7.582031 24.210938 7.804688 C 23.3125 6.847656 22.03125 6.246094 20.617188 6.246094 C 17.898438 6.246094 15.691406 8.453125 15.691406 11.171875 C 15.691406 11.558594 15.734375 11.933594 15.820313 12.292969 C 11.726563 12.089844 8.097656 10.128906 5.671875 7.148438 C 5.246094 7.875 5.003906 8.722656 5.003906 9.625 C 5.003906 11.332031 5.871094 12.839844 7.195313 13.722656 C 6.386719 13.695313 5.628906 13.476563 4.964844 13.105469 C 4.964844 13.128906 4.964844 13.148438 4.964844 13.167969 C 4.964844 15.554688 6.660156 17.546875 8.914063 17.996094 C 8.5 18.109375 8.066406 18.171875 7.617188 18.171875 C 7.300781 18.171875 6.988281 18.140625 6.691406 18.082031 C 7.316406 20.039063 9.136719 21.460938 11.289063 21.503906 C 9.605469 22.824219 7.480469 23.609375 5.175781 23.609375 C 4.777344 23.609375 4.386719 23.585938 4 23.539063 C 6.179688 24.9375 8.765625 25.753906 11.546875 25.753906 C 20.605469 25.753906 25.558594 18.25 25.558594 11.742188 C 25.558594 11.53125 25.550781 11.316406 25.542969 11.105469 C 26.503906 10.410156 27.339844 9.542969 28 8.558594 Z" />
                                                </svg></a><a aria-label="LinkedIn" class="" data-abc="true" tabindex="0"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                                    <path d="M 8.6425781 4 C 7.1835781 4 6 5.181625 6 6.640625 C 6 8.099625 7.182625 9.3085938 8.640625 9.3085938 C 10.098625 9.3085938 11.283203 8.099625 11.283203 6.640625 C 11.283203 5.182625 10.101578 4 8.6425781 4 z M 21.535156 11 C 19.316156 11 18.0465 12.160453 17.4375 13.314453 L 17.373047 13.314453 L 17.373047 11.310547 L 13 11.310547 L 13 26 L 17.556641 26 L 17.556641 18.728516 C 17.556641 16.812516 17.701266 14.960938 20.072266 14.960938 C 22.409266 14.960937 22.443359 17.145609 22.443359 18.849609 L 22.443359 26 L 26.994141 26 L 27 26 L 27 17.931641 C 27 13.983641 26.151156 11 21.535156 11 z M 6.3632812 11.310547 L 6.3632812 26 L 10.923828 26 L 10.923828 11.310547 L 6.3632812 11.310547 z" />
                                                </svg></a>
                                        </div>
                                    </figcaption>
                                </figure>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        Team Section end -->




        <!--Beneficios-->
        <section id="beneficios-ai" class="section section-bg">
            <div class="main-container">
                <div class="inside-container">
                    <div class="row">
                        <div class="col-12">
                            <h2 style="margin-bottom: 40px;">Beneficios de nuestros proyectos</h2>
                        </div>
                    </div>
                    <div class="row">
                        <?php if (!empty($beneficios)): ?>
                            <?php foreach ($beneficios as $beneficio): ?>
                                <div class="col-md-6 mb-4">
                                    <div style="display: flex; align-items: start; padding: 20px; background: #fff; border-radius: 8px;">
                                        <div style="min-width: 50px; font-size: 2em; color: #243659; margin-right: 20px;">
                                            <?php if (!empty($beneficio['icono'])): ?>
                                                <i class="<?= e($beneficio['icono']) ?>" aria-hidden="true"></i>
                                            <?php else: ?>
                                                ✔
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <h3 style="margin-bottom: 10px;"><?= e($beneficio['titulo']) ?></h3>
                                            <p><?= e($beneficio['descripcion']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        <!--Beneficios end-->

        <!--News section-->
        <?php if (count($noticias) > 0): ?>
        <section id="news-ai" class="section">
            <div class="main-container section-bg">
                <div class="inside-container">
                    <div class="row">
                        <div class="col-12 our-header">
                            <h2>Novedades de Atención Integral</h2>
                        </div>
                    </div>
                    <div class="row">
                        <?php foreach ($noticias as $noticia): ?>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="item">
                                <?php if (!empty($noticia['imagen_destacada'])): ?>
                                <div class="lab-bs-item-image" style="margin-bottom: 15px;">
                                    <img src="<?= '../' . e($noticia['imagen_destacada']) ?>"
                                         alt="<?= attr($noticia['titulo']) ?>"
                                         style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">
                                </div>
                                <?php endif; ?>
                                <div class="lab-part-content">
                                    <div class="lab-bs-item-content">
                                        <div class="lab-bs-item-date">
                                            <span><?= formatearFecha($noticia['fecha_publicacion']) ?></span>
                                        </div>
                                        <div class="">
                                            <h3 class="lab-bs-item-title"><?= e($noticia['titulo']) ?></h3>
                                            <p class="lab-bs-item-excerpt"><?= e($noticia['resumen']) ?></p>
                                            <?php if (!empty($noticia['contenido'])): ?>
                                            <div class="lab-bs-item-content-full" style="line-height: 1.6; color: #fff;">
                                                <?= nl2br(e($noticia['contenido'])) ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>
        <!--News section end-->
        


        <!--Contact section-->
                                        <?php if (count($noticias) > 0): ?>
                                        <li class="menu-item">
                                            <a class="" data-scroll href="#news-ai">Noticias</a>
                                        </li>
                                        <?php endif; ?>
        <section id="participa-ai" class="section">
            <div class="main-container section-bg">
                <div class="inside-container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="contact-con">
                                <h2>¿Cómo puedo acceder a estos servicios?</h2>
                                <p style="margin-bottom: 10px;">
                                    Si estás interesado en participar en nuestros proyectos de Atención Integral,
                                    el proceso es sencillo:
                                </p>
                                <ol style="line-height: 1.8; margin-bottom: 30px; padding-left: 20px;">
                                    <li style="margin-bottom: 15px;">
                                        <strong>Contacta con nosotros:</strong> Rellena este formulario y nos pondremos en contacto
                                        contigo lo antes posible.
                                    </li>
                                    <li style="margin-bottom: 15px;">
                                        <strong>Primera entrevista:</strong> Realizaremos una entrevista inicial para
                                        conocer tus necesidades y objetivos
                                    </li>
                                    <li style="margin-bottom: 15px;">
                                        <strong>Plan personalizado:</strong> Diseñaremos un plan de intervención
                                        adaptado a tu situación específica
                                    </li>
                                    <li style="margin-bottom: 15px;">
                                        <strong>Seguimiento continuo:</strong> Te acompañaremos durante todo el proceso,
                                        ajustando el plan según tus avances y necesidades
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h3>Formulario de contacto</h3>
                            <br>

                            <!-- Mensaje de éxito/error -->
                            <div id="form-message" class="form-message" style="display: none;"></div>

                            <div class="contact-form">
                                <form method="post" action="../php/enviar_correo.php" id="contact-form">
                                    <input type="hidden" name="area" value="aintegral">
                                    <!-- CAMPOS DE SEGURIDAD ANTI-BOT -->
                                    <?php echo generar_campos_seguridad(); ?>
                                    <!-- FIN CAMPOS DE SEGURIDAD -->

                                    <label for="fname">Nombre:</label>
                                    <input type="text" id="fname" name="txtName" placeholder="Tu nombre y apellidos" title="Nombre" required />
                                    <label for="email">Email:</label>
                                    <input type="email" id="email" name="txtEmail" placeholder="Tu correo electrónico" title="Email" required />
                                    <label for="subject">Mensaje:</label>
                                    <textarea id="subject" name="txtMsg" placeholder="Tu mensaje" title="Mensaje" style="height:200px" required></textarea>
                                    <input type="submit" value="Enviar">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Contact section end-->
        </main>


        <!--Footer-->
        <footer id="lab-footer" class="page-footer footer-bg">
            <!--Footer Three columns-->
            <div class="main-container">
                <div class="inside-container">
                    <div class="row">
                        <div class="col-sm-6 col-lg-4 foot-col-padd">
                            <div class="foot-logo">
                                <img src="../images/brand-coordi-black.svg" width="250" alt="Logotipo de Coordicanarias" class="float-center img-fluid">
                            </div>
                            <div class="dream-text">
                                <p>Coordinadora de Personas con Discapacidad Física de Canarias.
                                    Acompañamos, defendemos los derechos y promovemos la inclusión de
                                    las personas con discapacidad.</p>
                            </div>
                            <div class="foot-icon">
                                <?php if (!empty($config['redes_facebook'])): ?>
                                <a href="<?= attr($config['redes_facebook']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook de Coordicanarias" tabindex="0">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 19.253906 2 C 15.311906 2 13 4.0821719 13 8.8261719 L 13 13 L 8 13 L 8 18 L 13 18 L 13 30 L 18 30 L 18 18 L 22 18 L 23 13 L 18 13 L 18 9.671875 C 18 7.884875 18.582766 7 20.259766 7 L 23 7 L 23 2.2050781 C 22.526 2.1410781 21.144906 2 19.253906 2 z" />
                                    </svg>
                                </a>
                                <?php endif; ?>
                                <?php if (!empty($config['redes_twitter'])): ?>
                                <a href="<?= attr($config['redes_twitter']) ?>" target="_blank" rel="noopener noreferrer" aria-label="X (antes Twitter) de Coordicanarias" tabindex="0">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 18.42 14.009 L 27.891 3 L 25.703 3 L 17.446 12.588 L 10.894 3 L 3 3 L 12.921 17.411 L 3 29 L 5.188 29 L 13.895 19.006 L 20.806 29 L 28.7 29 L 18.42 14.009 Z M 15.026 17.708 L 14.07 16.393 L 5.95 4.56 L 9.744 4.56 L 16.209 14.011 L 17.165 15.326 L 25.704 27.517 L 21.91 27.517 L 15.026 17.708 Z" />
                                    </svg>
                                </a>
                                <?php endif; ?>
                                <?php if (!empty($config['redes_linkedin'])): ?>
                                <a href="<?= attr($config['redes_linkedin']) ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn de Coordicanarias" tabindex="0">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 8.6425781 4 C 7.1835781 4 6 5.181625 6 6.640625 C 6 8.099625 7.182625 9.3085938 8.640625 9.3085938 C 10.098625 9.3085938 11.283203 8.099625 11.283203 6.640625 C 11.283203 5.182625 10.101578 4 8.6425781 4 z M 21.535156 11 C 19.316156 11 18.0465 12.160453 17.4375 13.314453 L 17.373047 13.314453 L 17.373047 11.310547 L 13 11.310547 L 13 26 L 17.556641 26 L 17.556641 18.728516 C 17.556641 16.812516 17.701266 14.960938 20.072266 14.960938 C 22.409266 14.960937 22.443359 17.145609 22.443359 18.849609 L 22.443359 26 L 26.994141 26 L 27 26 L 27 17.931641 C 27 13.983641 26.151156 11 21.535156 11 z M 6.3632812 11.310547 L 6.3632812 26 L 10.923828 26 L 10.923828 11.310547 L 6.3632812 11.310547 z" />
                                    </svg>
                                </a>
                                <?php endif; ?>
                                <?php if (!empty($config['redes_instagram'])): ?>
                                <a href="<?= attr($config['redes_instagram']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram de Coordicanarias" tabindex="0">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" role="img" aria-labelledby="instagram-icon" focusable="false">
                                        <title id="instagram-icon">Instagram</title>
                                        <path d="M 11.46875 5 C 7.917969 5 5 7.914062 5 11.46875 L 5 20.53125 C 5 24.082031 7.914062 27 11.46875 27 L 20.53125 27 C 24.082031 27 27 24.085938 27 20.53125 L 27 11.46875 C 27 7.917969 24.085938 5 20.53125 5 Z M 11.46875 7 L 20.53125 7 C 23.003906 7 25 8.996094 25 11.46875 L 25 20.53125 C 25 23.003906 23.003906 25 20.53125 25 L 11.46875 25 C 8.996094 25 7 23.003906 7 20.53125 L 7 11.46875 C 7 8.996094 8.996094 7 11.46875 7 Z M 21.90625 9.1875 C 21.402344 9.1875 21 9.589844 21 10.09375 C 21 10.597656 21.402344 11 21.90625 11 C 22.410156 11 22.8125 10.597656 22.8125 10.09375 C 22.8125 9.589844 22.410156 9.1875 21.90625 9.1875 Z M 16 10 C 12.699219 10 10 12.699219 10 16 C 10 19.300781 12.699219 22 16 22 C 19.300781 22 22 19.300781 22 16 C 22 12.699219 19.300781 10 16 10 Z M 16 12 C 18.222656 12 20 13.777344 20 16 C 20 18.222656 18.222656 20 16 20 C 13.777344 20 12 18.222656 12 16 C 12 13.777344 13.777344 12 16 12 Z"/>
                                    </svg>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4" style="padding-top: 20px">
                            <h3 style="text-align: center; padding-bottom: 15px;
                            border-bottom: 1px solid #000; margin-bottom: 20px">Enlaces Rápidos</h3>

                            <div class="row">
                                <div class="col-6 pop-link" style="text-align: center">
                                    <a class="" data-scroll href="#home">Inicio</a>
                                    <a class="" data-scroll href="#about-ai">Atención integral</a>
                                    <a class="" data-scroll href="#services-ai">Servicios</a>
                                </div>
                                <div class="col-6 pop-link" style="text-align: center">
                                    <a class="" data-scroll href="#portfolios-ai">Proyectos</a>
                                    <a class="" data-scroll href="#beneficios-ai">Beneficios</a>
                                    <?php if (count($noticias) > 0): ?>
                                    <a class="" data-scroll href="#news-ai">Noticias</a>
                                    <?php endif; ?>
                                    <a class="" data-scroll href="#participa-ai">Participa</a>
                                    <a href="../index.php#features" class="">Áreas</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4" style="padding-top: 20px">
                            <h3 style="text-align: center; padding-bottom: 15px;
                            border-bottom: 1px solid #000; margin-bottom: 20px">Información Legal</h3>
                            <div class="row">
                                <div class="col-12 pop-link" style="text-align: center">
                                    <a href="politica-cookies.php">Política de cookies</a>
                                    <a href="politica-privacidad.php">Política de privacidad</a>
                                    <a href="alegal.php">Aviso legal</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Footer three columns end-->


            <!--Footer copyright-->
            <div class="footer-container footer-dark">
                <div class="inside-container">
                    <div class="row footer-dark">
                        <div class="col-12 copyright-text" style="text-align: center;">
                            <a href="https://www.gobiernodecanarias.org/bienestarsocial/dependencia/" target="_blank" rel="noopener">
                                <img src="../images/logos_gobcan/Logo_GobCan_claim_negro_mod1.png" alt="Gobierno de Canarias - Dirección General de Dependencia" width="120" style="margin: 15px 20px 30px 15px" class="logo-gobcan">
                            </a>
                            <a href="accesibilidad.php" class="accesibilidad-badge">
                                        <span class="wcag-badge">
                                            <strong>accesibilidad WCAG 2.2 AA</strong>
                                        </span>
                            </a>
                            <p>&#169; <script>document.write(new Date().getFullYear());</script> <a href="https://coordicanarias.com/" target="_blank" rel="noopener">Coordicanarias</a><br> Sitio Web subvencionado por la Consejería de Bienestar Social, Igualdad, Juventud, Infancia y Discapacidad del Gobierno de Canarias.</p>
                            <div id="lab-back-top" style="display: none;">
                                <a id="backtotop" tabindex="0" aria-label="Volver arriba">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="isolation:isolate" viewBox="0 0 285 285">
                                        <defs />
                                        <path fill="#FFF" d="M282 195L149 62a9 9 0 00-13 0L3 195a9 9 0 000 13l14 15a9 9 0 0013 0l112-113 113 113a9 9 0 0013 0l14-15a9 9 0 000-13z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Footer copyright-->
        </footer>
        <!--Footer end-->
    </div>

    <!-- Banner de Cookies -->
    <div id="cookie-banner" class="cookie-banner" role="dialog" aria-live="polite" aria-label="Aviso de cookies" aria-describedby="cookie-banner-description">
        <div class="cookie-banner-content">
            <div class="cookie-banner-text">
                <p id="cookie-banner-description">
                    Este sitio web utiliza cookies propias para mejorar su experiencia de navegación y recordar sus preferencias de accesibilidad.
                    Al continuar navegando, acepta su uso.
                    <a href="politica-cookies.php" target="_blank" rel="noopener">Más información sobre cookies</a>
                </p>
            </div>
            <div class="cookie-banner-buttons">
                <button id="cookie-accept" class="cookie-btn cookie-accept" aria-label="Aceptar cookies">
                    Aceptar
                </button>
            </div>
        </div>
    </div>
    <!-- Fin Banner de Cookies -->

    <script src="../js/jquery-3.7.1.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/mixitup.min.js"></script>
    <script src="../js/js-cookie.js"></script>
    <script src="../js/main.js?v=DISABLED"></script>

    <!-- Script de seguridad de formularios -->
    <script src="<?= url('../js/form-security.js') ?>"></script>

</body>

</html>
