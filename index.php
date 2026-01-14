<?php
/**
 * Página Principal - Coordicanarias
 * Muestra proyectos destacados e información general
 */

require_once __DIR__ . '/php/config.php';
require_once __DIR__ . '/php/db/connection.php';
require_once __DIR__ . '/php/core/security.php';
require_once __DIR__ . '/php/models/Proyecto.php';
require_once __DIR__ . '/php/models/Configuracion.php';
require_once __DIR__ . '/php/models/Noticia.php';
require_once __DIR__ . '/php/form_security_helper.php';

// Obtener proyectos activos y destacados
$proyectos = Proyecto::getAll(true); // Solo activos

// Obtener configuración del sitio
$config = Configuracion::getAll();

// Obtener noticias destacadas (últimas 3)
$noticias_destacadas = Noticia::getDestacadas(3);

// Helper function para escapar HTML (si no existe ya)
if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// Helper function para atributos HTML (si no existe ya)
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
            // Formato: "10 Ene, 2026"
            $fecha_en = date('d M, Y', strtotime($fecha));
            return str_replace(array_keys($meses), array_values($meses), $fecha_en);
        } else {
            // Formato: "10 de enero de 2026"
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

// Mapeo de slugs de áreas a IDs de secciones de portfolios
$portfolio_ids = [
    'aintegral' => 'ai',
    'empleo' => 'em',
    'forminno' => 'fi',
    'igualdadpm' => 'ipm',
    'ocio' => 'o',
    'participaca' => 'pca'
];
?>
<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Coordicanarias</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <!-- Stylesheets -->
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link href="css/my.css" rel="stylesheet" type="text/css">
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
        <?php include __DIR__ . '/php/components/panel-accesibilidad.php'; ?>
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
                                <a href="" class="" title="Logo" rel="home">
                                    <span class="">
                                        <picture>
                                            <img src="images/brand-coordi-black.svg" width="250" alt="Logo de Coordicanarias" class="logo-coordicanarias" />
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
                                            <a href="#home" data-scroll class="">Inicio</a>
                                        </li>
                                        <li class="menu-item">
                                            <a class="" data-scroll href="#about">Conócenos</a>
                                        </li>
                                        <li class="menu-item">
                                            <a class="" data-scroll href="#features">Áreas</a>
                                        </li>
                                        <li class="menu-item">
                                            <a class="" data-scroll href="#portfolios">Proyectos</a>
                                        </li>
                                        <?php if (count($noticias_destacadas) > 0): ?>
                                        <li class="menu-item">
                                            <a class="" data-scroll href="#news">Noticias</a>
                                        </li>
                                        <?php endif; ?>
                                        <li class="menu-item">
                                            <a class="" data-scroll href="#colabora">Colabora</a>
                                        </li>
                                        <li class="menu-item">
                                            <a class="" data-scroll href="#contact">Contacto</a>
                                        </li>
                                        <li class="menu-item">
                                            <a class="" href="transparencia.php">Transparencia</a>
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
                                                    <a href="#home" data-scroll class="">Inicio</a>
                                                </li>
                                                <li class="menu-item">
                                                    <a class="" href="#about">Conócenos</a>
                                                </li>
                                                <li class="menu-item">
                                                    <a class="" href="#features">Áreas</a>
                                                </li>
                                                <li class="menu-item">
                                                    <a class="" href="#portfolios">Proyectos</a>
                                                </li>
                                                <?php if (count($noticias_destacadas) > 0): ?>
                                                <li class="menu-item">
                                                    <a class="" data-scroll href="#news">Noticias</a>
                                                </li>
                                                <?php endif; ?>
                                                <li class="menu-item">
                                                    <a class="" data-scroll href="#colabora">Colabora</a>
                                                </li>
                                                <li class="menu-item">
                                                    <a class="" href="#contact">Contacto</a>
                                                </li>
                                                <li class="menu-item">
                                                    <a class="" href="transparencia.php">Transparencia</a>
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
        <div class="jumbotron_index" id="jumbotron">
            <h1 class="display-4" style="padding-left: 20px; padding-right: 20px">Coordinadora de Personas con Discapacidad Física de Canarias</h1>
            <p class="lead" style="padding-bottom: 60px;">Acompañamos, defendemos los derechos y promovemos la inclusión de las personas con Discapacidad en Canarias.</p>
            <!--<hr class="my-4">-->
            <!--<p>Me cago en todo lo que se menea</p>-->
            <!--<a class="wm-button button" href="#about" role="button">Conócenos</a>-->
        </div>
        <!--Jumbotron end-->

        <!--About section-->
        <section id="about" class="contact section">
            <div class="main-container contact-bg" >
                <div class="inside-container">
                    <div class="row">
                        <div class="col-12 our-header">
                            <h2 style="padding-top: 1px" >Conócenos</h2>
                        </div>
                    </div>
                    <div class="row" style="padding-top: 1px" >
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="contact-con">
                                <h2>Nuestra misión</h2>
                                <p>CoordiCanarias es una asociación sin ánimo de lucro fundada en 1980. Creada e impulsada por personas con discapacidad física, nuestra misión es clara: ser un eje que contribuya a mejorar las condiciones de vida de las personas con discapacidad y sus familias mediante acciones de diferente naturaleza.
                                <br><br>A través de una oferta de formación diversa, alternativas de ocio atractivas, terapias de atención innovadoras y mucho más, queremos contribuir al cambio de vida de las personas con discapacidad, brindando las herramientas clave para que cada individuo alcance la autonomía personal necesaria, y consiga llevar una vida independiente y enriquecedora.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="contact-con">
                                <h2>Nuestra visión</h2>
                                <p>Aspiramos a ser una organización de referencia en Canarias en la defensa de los derechos y la promoción de la plena inclusión social de las personas con discapacidad física. Trabajamos por construir una sociedad más accesible, justa e igualitaria, donde cada persona pueda desarrollar plenamente su potencial sin barreras.
                                    Nuestra visión es que las personas con discapacidad participen activamente en todos los ámbitos de la vida: educación, empleo, cultura, ocio y vida comunitaria. Queremos ser agentes de cambio social que contribuyan a eliminar estereotipos y promuevan la igualdad de oportunidades real, impulsando políticas públicas inclusivas y sensibilizando a la sociedad canaria hacia un modelo de convivencia basado en la diversidad, el respeto y la dignidad de todas las personas.<br><br></p>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <!-- begin valores -->
                            <div class="contact-con">
                                <h2>Nuestros valores</h2>
                                <p>Nuestro trabajo se fundamenta en cinco valores esenciales que guían cada una de nuestras acciones. El <strong>compromiso</strong> con las personas con discapacidad y sus familias es el motor que impulsa nuestras iniciativas. La <strong>cooperación</strong> entre entidades y profesionales nos permite aunar esfuerzos para alcanzar objetivos comunes. La <strong>igualdad</strong> de oportunidades es un derecho fundamental que defendemos con determinación.<br><br>Promovemos la <strong>concienciación</strong> social sobre la diversidad funcional, sensibilizando a la comunidad canaria. Y la <strong>normalización</strong> es nuestro horizonte: construir una sociedad donde todas las personas puedan participar plenamente en la vida comunitaria sin barreras ni exclusiones.
                                </p>
                            </div>
                            <!-- end valores -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--About section end-->


        <!--Features section-->
        <section id="features" class="section">
            <div class="main-container section-bg">
                <div class="inside-container">
                    <div class="row">
                        <div class="col-12 our-header">
                            <h2 style="padding-bottom: 1px">Áreas</h2>
                        </div>
                    </div>
                    <!-- Primera fila con 3 elementos -->
                    <div class="row row-pad justify-content-center">
                        <div class="col-sm-6 col-md-4 our-spec">
                            <div class="text-center" tabindex="0">
                                <a href="areas/aintegral.php" aria-label="Ir al área de Atención Integral">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" role="img" aria-labelledby="atencion-integral" width="104px" height="104px" viewBox="0 0 104 104" version="1.1">
                                        <g id="surface1">
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 95.429688 24.386719 C 97.953125 26.539062 98.324219 29.527344 98.679688 32.648438 C 98.710938 32.917969 98.742188 33.1875 98.777344 33.457031 C 98.863281 34.183594 98.945312 34.914062 99.03125 35.640625 C 99.101562 36.253906 99.171875 36.867188 99.246094 37.476562 C 99.414062 38.925781 99.582031 40.378906 99.746094 41.828125 C 99.917969 43.308594 100.089844 44.789062 100.265625 46.269531 C 100.417969 47.554688 100.566406 48.835938 100.714844 50.117188 C 100.800781 50.878906 100.886719 51.640625 100.980469 52.402344 C 102.417969 64.515625 102.417969 64.515625 98.808594 69.128906 C 96.953125 71.414062 94.75 73.355469 92.5625 75.308594 C 88.667969 78.773438 88.667969 78.773438 86.734375 83.484375 C 86.710938 84.148438 86.714844 84.8125 86.722656 85.476562 C 86.722656 85.746094 86.722656 85.746094 86.722656 86.019531 C 86.726562 86.460938 86.730469 86.902344 86.734375 87.34375 C 86.84375 87.382812 86.953125 87.417969 87.0625 87.457031 C 88.878906 88.113281 89.765625 88.945312 90.605469 90.703125 C 90.992188 91.714844 91.03125 92.695312 91.035156 93.773438 C 91.035156 93.917969 91.035156 94.066406 91.035156 94.21875 C 91.039062 94.53125 91.039062 94.84375 91.039062 95.15625 C 91.039062 95.628906 91.042969 96.101562 91.046875 96.578125 C 91.046875 96.882812 91.046875 97.1875 91.046875 97.492188 C 91.046875 97.628906 91.050781 97.769531 91.050781 97.914062 C 91.042969 99.613281 90.652344 101.246094 89.460938 102.511719 C 87.898438 103.933594 86.496094 104.089844 84.441406 104.085938 C 84.191406 104.085938 83.941406 104.089844 83.6875 104.089844 C 83.007812 104.09375 82.328125 104.09375 81.652344 104.09375 C 81.082031 104.09375 80.515625 104.097656 79.945312 104.097656 C 78.605469 104.101562 77.261719 104.101562 75.921875 104.101562 C 74.539062 104.101562 73.160156 104.105469 71.78125 104.113281 C 70.589844 104.117188 69.402344 104.121094 68.214844 104.121094 C 67.503906 104.121094 66.796875 104.121094 66.089844 104.125 C 65.296875 104.132812 64.507812 104.128906 63.714844 104.125 C 63.484375 104.128906 63.253906 104.128906 63.015625 104.132812 C 61.355469 104.117188 59.96875 103.773438 58.722656 102.628906 C 57.445312 101.273438 57.1875 99.902344 57.191406 98.105469 C 57.191406 97.910156 57.191406 97.71875 57.191406 97.519531 C 57.1875 97.113281 57.1875 96.710938 57.191406 96.304688 C 57.191406 95.6875 57.1875 95.070312 57.179688 94.453125 C 57.179688 94.058594 57.179688 93.664062 57.179688 93.265625 C 57.179688 93.085938 57.175781 92.902344 57.171875 92.714844 C 57.191406 91.050781 57.757812 89.777344 58.90625 88.5625 C 59.691406 87.964844 60.40625 87.640625 61.34375 87.34375 C 61.34375 87.144531 61.347656 86.945312 61.347656 86.738281 C 61.359375 84.847656 61.375 82.957031 61.390625 81.066406 C 61.402344 80.09375 61.410156 79.121094 61.414062 78.148438 C 61.417969 77.210938 61.425781 76.269531 61.4375 75.328125 C 61.441406 74.972656 61.445312 74.617188 61.445312 74.261719 C 61.472656 66.851562 64.8125 61.65625 69.875 56.46875 C 69.980469 56.359375 70.085938 56.25 70.199219 56.136719 C 70.957031 55.359375 71.722656 54.582031 72.488281 53.808594 C 72.75 53.546875 73.011719 53.28125 73.273438 53.011719 C 77.394531 48.824219 77.394531 48.824219 80.566406 48.726562 C 80.816406 48.730469 81.066406 48.734375 81.324219 48.734375 C 81.703125 48.738281 81.703125 48.738281 82.089844 48.742188 C 82.28125 48.746094 82.472656 48.746094 82.671875 48.75 C 82.679688 48.523438 82.691406 48.296875 82.699219 48.0625 C 82.789062 45.921875 82.878906 43.777344 82.96875 41.636719 C 83.015625 40.535156 83.0625 39.433594 83.105469 38.332031 C 83.148438 37.265625 83.195312 36.203125 83.242188 35.140625 C 83.257812 34.734375 83.273438 34.328125 83.289062 33.925781 C 83.554688 27.300781 83.554688 27.300781 85.761719 24.902344 C 88.53125 22.433594 92.386719 22.238281 95.429688 24.386719 Z M 88.613281 28.375 C 87.714844 29.542969 87.683594 31.070312 87.628906 32.488281 C 87.621094 32.664062 87.613281 32.835938 87.605469 33.015625 C 87.578125 33.585938 87.554688 34.160156 87.53125 34.730469 C 87.515625 35.128906 87.496094 35.53125 87.480469 35.933594 C 87.441406 36.769531 87.40625 37.609375 87.371094 38.449219 C 87.328125 39.515625 87.28125 40.585938 87.234375 41.652344 C 87.199219 42.480469 87.160156 43.308594 87.125 44.136719 C 87.109375 44.53125 87.09375 44.921875 87.074219 45.316406 C 87.023438 46.527344 86.976562 47.742188 86.9375 48.953125 C 86.929688 49.171875 86.929688 49.171875 86.921875 49.390625 C 86.910156 49.785156 86.902344 50.175781 86.894531 50.566406 C 86.890625 50.78125 86.886719 50.992188 86.882812 51.210938 C 86.960938 52.050781 87.28125 52.6875 87.710938 53.40625 C 88.523438 54.824219 88.328125 56.960938 88.039062 58.53125 C 87.328125 60.789062 85.296875 62.40625 83.664062 64.035156 C 83.496094 64.203125 83.328125 64.371094 83.15625 64.546875 C 82.804688 64.894531 82.453125 65.246094 82.101562 65.597656 C 81.5625 66.132812 81.023438 66.671875 80.488281 67.210938 C 80.144531 67.554688 79.804688 67.894531 79.460938 68.238281 C 79.300781 68.398438 79.140625 68.5625 78.972656 68.726562 C 78.824219 68.875 78.675781 69.023438 78.523438 69.175781 C 78.390625 69.304688 78.261719 69.4375 78.125 69.570312 C 77.46875 70.175781 76.867188 70.148438 76.007812 70.132812 C 75.5625 70.078125 75.5625 70.078125 75.054688 69.773438 C 74.644531 69.089844 74.433594 68.4375 74.546875 67.640625 C 75.242188 66.410156 76.246094 65.492188 77.246094 64.507812 C 77.429688 64.328125 77.613281 64.148438 77.800781 63.960938 C 78.382812 63.382812 78.964844 62.808594 79.546875 62.234375 C 80.320312 61.472656 81.085938 60.714844 81.855469 59.953125 C 82.03125 59.777344 82.210938 59.605469 82.390625 59.425781 C 82.558594 59.261719 82.722656 59.097656 82.890625 58.929688 C 83.035156 58.789062 83.179688 58.644531 83.328125 58.5 C 84.101562 57.625 84.183594 56.886719 84.15625 55.75 C 84.03125 54.757812 83.480469 54.210938 82.761719 53.5625 C 81.917969 52.980469 81.238281 52.949219 80.234375 53.015625 C 79.097656 53.460938 78.332031 54.199219 77.46875 55.035156 C 77.320312 55.175781 77.171875 55.316406 77.019531 55.460938 C 76.53125 55.929688 76.046875 56.402344 75.5625 56.875 C 75.300781 57.128906 75.300781 57.128906 75.035156 57.390625 C 72.207031 60.148438 72.207031 60.148438 69.671875 63.171875 C 69.519531 63.359375 69.367188 63.546875 69.210938 63.738281 C 66.464844 67.191406 65.710938 70.738281 65.753906 75.066406 C 65.753906 75.382812 65.753906 75.695312 65.753906 76.011719 C 65.757812 77.171875 65.765625 78.328125 65.773438 79.484375 C 65.785156 82.078125 65.800781 84.671875 65.8125 87.34375 C 71.242188 87.34375 76.671875 87.34375 82.265625 87.34375 C 82.332031 85.9375 82.398438 84.527344 82.46875 83.078125 C 83.3125 77.605469 87.59375 73.695312 91.613281 70.230469 C 94.453125 67.734375 97.0625 65.253906 97.476562 61.316406 C 97.527344 60.035156 97.335938 58.761719 97.191406 57.496094 C 97.152344 57.160156 97.117188 56.824219 97.078125 56.488281 C 96.972656 55.542969 96.867188 54.59375 96.757812 53.644531 C 96.660156 52.757812 96.558594 51.875 96.460938 50.988281 C 96.183594 48.515625 95.902344 46.039062 95.621094 43.566406 C 95.46875 42.273438 95.324219 40.980469 95.175781 39.683594 C 95.03125 38.40625 94.886719 37.132812 94.738281 35.855469 C 94.683594 35.375 94.628906 34.894531 94.574219 34.414062 C 94.5 33.75 94.421875 33.082031 94.34375 32.414062 C 94.324219 32.222656 94.304688 32.027344 94.28125 31.824219 C 94.117188 30.449219 93.914062 28.882812 92.789062 27.941406 C 91.316406 26.804688 89.90625 27.109375 88.613281 28.375 Z M 61.546875 92.21875 C 61.480469 92.765625 61.453125 93.265625 61.453125 93.8125 C 61.449219 93.96875 61.449219 94.121094 61.445312 94.28125 C 61.445312 94.613281 61.445312 94.941406 61.445312 95.269531 C 61.445312 95.773438 61.4375 96.273438 61.429688 96.777344 C 61.425781 97.097656 61.425781 97.414062 61.425781 97.734375 C 61.421875 97.886719 61.417969 98.035156 61.417969 98.191406 C 61.410156 98.667969 61.410156 98.667969 61.546875 99.328125 C 62.46875 100.167969 63.597656 100.023438 64.769531 100.015625 C 65.101562 100.019531 65.101562 100.019531 65.441406 100.019531 C 66.175781 100.023438 66.914062 100.019531 67.648438 100.019531 C 68.15625 100.019531 68.667969 100.019531 69.179688 100.019531 C 70.25 100.019531 71.320312 100.019531 72.390625 100.015625 C 73.625 100.011719 74.863281 100.015625 76.097656 100.019531 C 77.289062 100.019531 78.480469 100.019531 79.671875 100.019531 C 80.175781 100.019531 80.683594 100.019531 81.1875 100.019531 C 81.894531 100.023438 82.601562 100.019531 83.308594 100.015625 C 83.519531 100.019531 83.726562 100.019531 83.945312 100.019531 C 85.34375 100.019531 85.34375 100.019531 86.53125 99.328125 C 86.875 98.296875 86.773438 97.195312 86.773438 96.113281 C 86.773438 95.6875 86.777344 95.261719 86.78125 94.832031 C 86.78125 94.558594 86.78125 94.289062 86.78125 94.015625 C 86.78125 93.769531 86.78125 93.523438 86.785156 93.265625 C 86.738281 92.65625 86.667969 92.316406 86.328125 91.8125 C 85.835938 91.566406 85.523438 91.582031 84.976562 91.582031 C 84.773438 91.578125 84.574219 91.578125 84.371094 91.578125 C 84.148438 91.578125 83.929688 91.578125 83.703125 91.578125 C 83.359375 91.574219 83.359375 91.574219 83.003906 91.574219 C 82.238281 91.570312 81.472656 91.570312 80.703125 91.566406 C 80.175781 91.566406 79.644531 91.566406 79.113281 91.566406 C 78.140625 91.5625 77.167969 91.5625 76.199219 91.5625 C 74.769531 91.558594 73.335938 91.554688 71.90625 91.550781 C 70.667969 91.546875 69.429688 91.542969 68.191406 91.542969 C 67.664062 91.542969 67.136719 91.542969 66.609375 91.539062 C 65.875 91.535156 65.136719 91.535156 64.402344 91.535156 C 64.070312 91.535156 64.070312 91.535156 63.734375 91.53125 C 63.535156 91.53125 63.335938 91.535156 63.128906 91.535156 C 62.953125 91.535156 62.78125 91.535156 62.601562 91.535156 C 62.050781 91.628906 61.871094 91.769531 61.546875 92.21875 Z M 61.546875 92.21875 "/>
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 17.417969 24.296875 C 19.15625 25.640625 20.0625 27.316406 20.414062 29.46875 C 20.527344 30.355469 20.578125 31.230469 20.613281 32.125 C 20.621094 32.304688 20.628906 32.484375 20.636719 32.671875 C 20.664062 33.261719 20.6875 33.855469 20.714844 34.449219 C 20.730469 34.859375 20.75 35.273438 20.765625 35.683594 C 20.8125 36.765625 20.859375 37.851562 20.90625 38.933594 C 20.953125 40.039062 21 41.144531 21.050781 42.25 C 21.144531 44.414062 21.234375 46.582031 21.328125 48.75 C 21.640625 48.746094 21.640625 48.746094 21.957031 48.742188 C 22.234375 48.742188 22.507812 48.738281 22.785156 48.738281 C 22.988281 48.734375 22.988281 48.734375 23.199219 48.730469 C 26.3125 48.71875 28.242188 50.542969 30.351562 52.621094 C 30.816406 53.085938 31.28125 53.550781 31.746094 54.023438 C 32.21875 54.5 32.695312 54.976562 33.171875 55.449219 C 34.730469 57.011719 36.234375 58.574219 37.578125 60.328125 C 37.75 60.546875 37.925781 60.761719 38.097656 60.980469 C 41.367188 65.242188 42.5 69.378906 42.539062 74.707031 C 42.542969 75.074219 42.546875 75.4375 42.550781 75.804688 C 42.558594 76.761719 42.566406 77.714844 42.574219 78.671875 C 42.582031 79.648438 42.59375 80.625 42.605469 81.605469 C 42.625 83.515625 42.640625 85.429688 42.65625 87.34375 C 42.78125 87.398438 42.90625 87.453125 43.035156 87.507812 C 43.199219 87.582031 43.367188 87.652344 43.535156 87.726562 C 43.699219 87.800781 43.863281 87.871094 44.03125 87.945312 C 45.265625 88.519531 45.890625 89.421875 46.515625 90.59375 C 46.699219 91.148438 46.75 91.488281 46.757812 92.058594 C 46.761719 92.234375 46.765625 92.40625 46.769531 92.582031 C 46.777344 93.375 46.785156 94.164062 46.789062 94.957031 C 46.792969 95.371094 46.796875 95.785156 46.804688 96.203125 C 46.816406 96.804688 46.820312 97.40625 46.824219 98.007812 C 46.828125 98.191406 46.835938 98.375 46.839844 98.5625 C 46.835938 100.089844 46.390625 101.417969 45.347656 102.5625 C 43.839844 103.972656 42.347656 104.089844 40.363281 104.085938 C 40.113281 104.085938 39.863281 104.089844 39.609375 104.089844 C 38.929688 104.09375 38.25 104.09375 37.574219 104.09375 C 37.003906 104.09375 36.4375 104.097656 35.867188 104.097656 C 34.527344 104.101562 33.183594 104.101562 31.84375 104.101562 C 30.460938 104.101562 29.082031 104.105469 27.703125 104.113281 C 26.511719 104.117188 25.324219 104.121094 24.136719 104.121094 C 23.425781 104.121094 22.71875 104.121094 22.011719 104.125 C 21.21875 104.132812 20.429688 104.128906 19.636719 104.125 C 19.40625 104.128906 19.175781 104.128906 18.9375 104.132812 C 17.023438 104.113281 15.597656 103.597656 14.230469 102.234375 C 12.769531 100.617188 12.902344 98.289062 12.921875 96.25 C 12.921875 95.773438 12.921875 95.300781 12.917969 94.824219 C 12.914062 92.457031 13.078125 90.324219 14.828125 88.5625 C 15.601562 87.964844 16.3125 87.566406 17.265625 87.34375 C 17.378906 83.238281 16.757812 80.761719 13.867188 77.679688 C 13.578125 77.378906 13.289062 77.078125 13 76.78125 C 12.734375 76.507812 12.734375 76.507812 12.460938 76.226562 C 11.613281 75.363281 10.746094 74.542969 9.832031 73.753906 C 6.46875 70.808594 2.75 67.429688 2.402344 62.671875 C 2.273438 59.074219 2.648438 55.527344 3.074219 51.957031 C 3.164062 51.1875 3.253906 50.417969 3.339844 49.648438 C 3.488281 48.359375 3.640625 47.070312 3.792969 45.78125 C 3.96875 44.300781 4.140625 42.816406 4.308594 41.332031 C 4.476562 39.894531 4.640625 38.453125 4.808594 37.011719 C 4.882812 36.40625 4.953125 35.796875 5.019531 35.191406 C 5.105469 34.476562 5.1875 33.761719 5.273438 33.046875 C 5.316406 32.664062 5.363281 32.28125 5.40625 31.898438 C 5.734375 29.1875 6.148438 26.636719 8.238281 24.707031 C 11.058594 22.523438 14.433594 22.273438 17.417969 24.296875 Z M 10.816406 28.398438 C 10.136719 29.363281 9.914062 30.300781 9.78125 31.457031 C 9.753906 31.691406 9.726562 31.925781 9.699219 32.167969 C 9.671875 32.421875 9.640625 32.675781 9.613281 32.933594 C 9.582031 33.207031 9.546875 33.480469 9.515625 33.75 C 9.429688 34.488281 9.347656 35.226562 9.261719 35.960938 C 9.175781 36.738281 9.082031 37.511719 8.996094 38.285156 C 8.84375 39.585938 8.695312 40.886719 8.546875 42.1875 C 8.375 43.6875 8.199219 45.1875 8.027344 46.683594 C 7.839844 48.285156 7.65625 49.886719 7.472656 51.488281 C 7.421875 51.949219 7.367188 52.40625 7.3125 52.863281 C 7.230469 53.589844 7.148438 54.3125 7.066406 55.039062 C 7.019531 55.425781 6.976562 55.816406 6.929688 56.203125 C 6.304688 61.535156 6.304688 61.535156 8.503906 66.308594 C 8.96875 66.839844 9.453125 67.34375 9.953125 67.84375 C 10.199219 68.09375 10.445312 68.347656 10.691406 68.597656 C 12.207031 70.136719 12.207031 70.136719 13.800781 71.589844 C 16.125 73.605469 18.328125 75.75 19.90625 78.40625 C 20.007812 78.578125 20.113281 78.75 20.21875 78.925781 C 21.675781 81.582031 21.589844 84.3125 21.734375 87.34375 C 27.164062 87.34375 32.59375 87.34375 38.1875 87.34375 C 38.203125 84.761719 38.222656 82.183594 38.238281 79.523438 C 38.246094 78.714844 38.253906 77.90625 38.261719 77.074219 C 38.261719 76.335938 38.261719 76.335938 38.265625 75.597656 C 38.265625 75.273438 38.269531 74.949219 38.273438 74.628906 C 38.355469 67.832031 35.023438 63.574219 30.46875 58.90625 C 29.796875 58.222656 29.125 57.542969 28.449219 56.863281 C 28.292969 56.703125 28.132812 56.542969 27.96875 56.378906 C 25.84375 54.074219 25.84375 54.074219 23.054688 52.9375 C 21.957031 53.058594 21.265625 53.414062 20.554688 54.246094 C 20.011719 54.976562 19.867188 55.320312 19.84375 56.214844 C 19.835938 56.386719 19.828125 56.554688 19.820312 56.730469 C 20.0625 58.269531 21.554688 59.378906 22.605469 60.414062 C 22.789062 60.59375 22.972656 60.773438 23.160156 60.960938 C 23.742188 61.539062 24.324219 62.113281 24.90625 62.6875 C 25.679688 63.449219 26.445312 64.207031 27.214844 64.96875 C 27.390625 65.144531 27.570312 65.316406 27.75 65.496094 C 27.917969 65.660156 28.082031 65.824219 28.25 65.992188 C 28.394531 66.132812 28.539062 66.277344 28.6875 66.421875 C 29.210938 67.015625 29.433594 67.449219 29.515625 68.238281 C 29.445312 68.941406 29.261719 69.308594 28.84375 69.875 C 28.113281 70.238281 27.402344 70.21875 26.609375 70.078125 C 26.222656 69.820312 26.222656 69.820312 25.851562 69.472656 C 25.710938 69.34375 25.570312 69.214844 25.425781 69.082031 C 25.28125 68.941406 25.132812 68.800781 24.984375 68.65625 C 24.765625 68.449219 24.765625 68.449219 24.542969 68.234375 C 23.527344 67.261719 22.53125 66.265625 21.53125 65.277344 C 21.167969 64.914062 20.800781 64.554688 20.433594 64.195312 C 19.898438 63.671875 19.367188 63.144531 18.835938 62.617188 C 18.59375 62.378906 18.59375 62.378906 18.34375 62.136719 C 16.496094 60.28125 15.792969 58.5 15.777344 55.917969 C 15.800781 54.382812 16.207031 53.078125 17.0625 51.796875 C 17.117188 51.242188 17.117188 51.242188 17.097656 50.601562 C 17.09375 50.34375 17.089844 50.089844 17.085938 49.824219 C 17.042969 48.132812 16.976562 46.441406 16.90625 44.746094 C 16.894531 44.453125 16.894531 44.453125 16.882812 44.152344 C 16.839844 43.113281 16.792969 42.078125 16.75 41.039062 C 16.703125 39.972656 16.660156 38.90625 16.617188 37.84375 C 16.585938 37.015625 16.550781 36.191406 16.511719 35.363281 C 16.496094 34.972656 16.480469 34.578125 16.464844 34.183594 C 16.484375 30.796875 16.484375 30.796875 14.992188 27.917969 C 13.507812 26.8125 12.082031 27.105469 10.816406 28.398438 Z M 17.46875 92.015625 C 17.117188 93.074219 17.21875 94.207031 17.21875 95.316406 C 17.214844 95.757812 17.207031 96.199219 17.203125 96.640625 C 17.199219 96.921875 17.199219 97.203125 17.199219 97.488281 C 17.195312 97.742188 17.195312 97.996094 17.191406 98.261719 C 17.269531 98.964844 17.371094 99.246094 17.875 99.734375 C 18.457031 99.929688 18.820312 99.964844 19.421875 99.964844 C 19.710938 99.96875 19.710938 99.96875 20.003906 99.96875 C 20.214844 99.96875 20.425781 99.96875 20.644531 99.96875 C 20.976562 99.972656 20.976562 99.972656 21.316406 99.972656 C 22.054688 99.976562 22.792969 99.976562 23.527344 99.980469 C 24.039062 99.980469 24.550781 99.980469 25.0625 99.980469 C 26 99.984375 26.933594 99.984375 27.871094 99.984375 C 29.246094 99.988281 30.621094 99.992188 32 99.996094 C 33.191406 100 34.382812 100.003906 35.578125 100.003906 C 36.082031 100.003906 36.589844 100.003906 37.097656 100.007812 C 37.808594 100.011719 38.515625 100.011719 39.226562 100.011719 C 39.433594 100.011719 39.644531 100.011719 39.859375 100.015625 C 41.269531 100.027344 41.269531 100.027344 42.453125 99.328125 C 42.554688 98.757812 42.585938 98.308594 42.574219 97.734375 C 42.574219 97.578125 42.574219 97.425781 42.574219 97.265625 C 42.574219 96.933594 42.570312 96.605469 42.5625 96.277344 C 42.554688 95.773438 42.554688 95.273438 42.554688 94.769531 C 42.554688 94.449219 42.550781 94.132812 42.546875 93.8125 C 42.546875 93.660156 42.546875 93.511719 42.546875 93.355469 C 42.539062 92.566406 42.539062 92.566406 42.203125 91.875 C 41.734375 91.527344 41.453125 91.535156 40.871094 91.535156 C 40.574219 91.53125 40.574219 91.53125 40.265625 91.527344 C 40.046875 91.53125 39.828125 91.53125 39.601562 91.535156 C 39.371094 91.53125 39.140625 91.53125 38.902344 91.53125 C 38.136719 91.53125 37.371094 91.53125 36.605469 91.535156 C 36.078125 91.535156 35.546875 91.535156 35.015625 91.535156 C 33.902344 91.535156 32.789062 91.539062 31.675781 91.542969 C 30.390625 91.546875 29.105469 91.546875 27.816406 91.542969 C 26.582031 91.542969 25.34375 91.542969 24.105469 91.546875 C 23.578125 91.546875 23.050781 91.546875 22.523438 91.546875 C 21.789062 91.546875 21.054688 91.550781 20.320312 91.554688 C 20.101562 91.554688 19.882812 91.550781 19.65625 91.550781 C 19.355469 91.554688 19.355469 91.554688 19.050781 91.554688 C 18.789062 91.558594 18.789062 91.558594 18.523438 91.558594 C 18.019531 91.582031 18.019531 91.582031 17.46875 92.015625 Z M 17.46875 92.015625 "/>
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 60.40625 2.355469 C 63.703125 4.800781 65.886719 8.015625 66.726562 12.050781 C 67.28125 16.0625 66.410156 19.402344 64.59375 22.953125 C 64.742188 23.003906 64.890625 23.054688 65.042969 23.109375 C 67.683594 24.277344 69.554688 27.496094 70.652344 30.042969 C 71.515625 32.3125 71.734375 34.484375 71.746094 36.890625 C 71.746094 37.113281 71.746094 37.339844 71.746094 37.570312 C 71.75 38.039062 71.75 38.507812 71.75 38.980469 C 71.753906 39.695312 71.761719 40.410156 71.765625 41.125 C 71.769531 41.585938 71.769531 42.042969 71.769531 42.503906 C 71.773438 42.714844 71.773438 42.925781 71.777344 43.144531 C 71.773438 44.941406 71.496094 46.4375 70.257812 47.796875 C 70.042969 48.042969 70.042969 48.042969 69.824219 48.289062 C 68.109375 49.703125 66.363281 49.695312 64.230469 49.679688 C 63.730469 49.675781 63.226562 49.683594 62.722656 49.691406 C 62.402344 49.691406 62.082031 49.691406 61.757812 49.691406 C 61.609375 49.691406 61.460938 49.695312 61.308594 49.699219 C 60.515625 49.683594 60.15625 49.589844 59.527344 49.089844 C 59 48.40625 58.90625 47.988281 58.90625 47.125 C 59.164062 46.339844 59.355469 46.003906 60.0625 45.570312 C 60.800781 45.269531 61.324219 45.222656 62.121094 45.222656 C 62.378906 45.21875 62.640625 45.214844 62.90625 45.210938 C 63.3125 45.210938 63.722656 45.207031 64.128906 45.207031 C 64.527344 45.207031 64.921875 45.203125 65.320312 45.195312 C 65.554688 45.195312 65.792969 45.195312 66.035156 45.195312 C 66.636719 45.152344 66.636719 45.152344 66.960938 44.738281 C 67.269531 44.222656 67.3125 43.949219 67.3125 43.355469 C 67.316406 43.082031 67.316406 43.082031 67.320312 42.800781 C 67.316406 42.605469 67.316406 42.410156 67.316406 42.207031 C 67.316406 42 67.316406 41.796875 67.316406 41.585938 C 67.316406 41.152344 67.316406 40.71875 67.3125 40.285156 C 67.3125 39.628906 67.3125 38.976562 67.316406 38.320312 C 67.320312 34.386719 66.894531 31.066406 64.0625 28.085938 C 63.335938 27.394531 62.566406 26.785156 61.75 26.203125 C 61.617188 26.292969 61.484375 26.378906 61.347656 26.472656 C 57.226562 29.160156 53.273438 30.230469 48.410156 29.257812 C 46.136719 28.75 44.257812 27.652344 42.453125 26.203125 C 41.570312 26.679688 40.917969 27.101562 40.21875 27.828125 C 40.085938 27.828125 39.949219 27.828125 39.8125 27.828125 C 39.757812 27.941406 39.707031 28.058594 39.652344 28.175781 C 39.382812 28.683594 39.066406 29.117188 38.71875 29.578125 C 37.125 31.84375 36.703125 34.265625 36.703125 36.992188 C 36.703125 37.199219 36.703125 37.40625 36.703125 37.621094 C 36.699219 38.054688 36.699219 38.488281 36.703125 38.921875 C 36.703125 39.585938 36.699219 40.25 36.691406 40.914062 C 36.691406 41.339844 36.691406 41.761719 36.691406 42.183594 C 36.691406 42.382812 36.6875 42.582031 36.6875 42.785156 C 36.6875 43.0625 36.6875 43.0625 36.691406 43.34375 C 36.691406 43.507812 36.691406 43.667969 36.691406 43.835938 C 36.785156 44.398438 36.980469 44.679688 37.375 45.09375 C 37.984375 45.214844 37.984375 45.214844 38.679688 45.195312 C 38.9375 45.199219 39.195312 45.203125 39.464844 45.207031 C 39.734375 45.207031 40.007812 45.207031 40.28125 45.207031 C 40.816406 45.210938 41.347656 45.214844 41.878906 45.222656 C 42.234375 45.222656 42.234375 45.222656 42.597656 45.222656 C 43.355469 45.308594 43.839844 45.503906 44.484375 45.90625 C 44.980469 46.710938 45.121094 47.136719 44.992188 48.078125 C 44.640625 48.855469 44.386719 49.109375 43.671875 49.5625 C 43.117188 49.644531 42.644531 49.679688 42.089844 49.675781 C 41.9375 49.679688 41.785156 49.679688 41.628906 49.679688 C 41.308594 49.683594 40.988281 49.683594 40.667969 49.679688 C 40.183594 49.675781 39.703125 49.683594 39.21875 49.691406 C 37.140625 49.703125 35.65625 49.464844 34 48.113281 C 33.878906 47.96875 33.753906 47.828125 33.628906 47.683594 C 33.503906 47.542969 33.378906 47.40625 33.25 47.261719 C 32.304688 45.765625 32.207031 44.242188 32.214844 42.507812 C 32.214844 42.285156 32.214844 42.058594 32.214844 41.828125 C 32.214844 41.355469 32.214844 40.878906 32.21875 40.40625 C 32.21875 39.691406 32.21875 38.976562 32.214844 38.261719 C 32.207031 33.179688 33.078125 28.679688 36.695312 24.835938 C 38.644531 22.953125 38.644531 22.953125 39.40625 22.953125 C 39.265625 22.679688 39.265625 22.679688 39.125 22.402344 C 37.214844 18.570312 36.429688 14.527344 37.703125 10.339844 C 38.582031 7.746094 39.875 5.75 41.84375 3.859375 C 41.984375 3.71875 42.125 3.582031 42.269531 3.4375 C 47.082031 -1.089844 55.0625 -1.199219 60.40625 2.355469 Z M 43.875 7.832031 C 42.09375 10.007812 41.273438 12.695312 41.359375 15.484375 C 41.636719 18.246094 42.835938 20.929688 44.972656 22.761719 C 47.335938 24.488281 49.828125 25.640625 52.8125 25.390625 C 52.972656 25.378906 53.132812 25.367188 53.296875 25.355469 C 54.554688 25.199219 55.609375 24.769531 56.734375 24.210938 C 56.871094 24.144531 57.007812 24.074219 57.148438 24.007812 C 59.679688 22.695312 61.234375 20.515625 62.179688 17.855469 C 63.066406 14.921875 62.554688 12.117188 61.21875 9.40625 C 59.847656 7.054688 57.675781 5.441406 55.148438 4.480469 C 50.941406 3.390625 46.828125 4.710938 43.875 7.832031 Z M 43.875 7.832031 "/>
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 53.015625 45.5 C 53.609375 45.964844 53.949219 46.304688 54.222656 47.011719 C 54.246094 47.941406 54.015625 48.539062 53.398438 49.234375 C 52.644531 49.65625 52.019531 49.742188 51.1875 49.5625 C 50.296875 49.085938 50.296875 49.085938 49.96875 48.546875 C 49.808594 47.835938 49.808594 47.226562 49.96875 46.515625 C 50.855469 45.34375 51.609375 45.160156 53.015625 45.5 Z M 53.015625 45.5 "/>
                                        </g>
                                    </svg>
                                </a>
                            </div>
                            <h3>Atención Integral</h3>
                            <p></p>
                        </div>
                        <div class="col-sm-6 col-md-4 our-spec">
                            <div class="text-center" tabindex="0">
                                <a href="areas/empleo.php" aria-label="Ir al área de Empleo">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="104px" height="104px" viewBox="0 0 104 104" version="1.1">
                                        <g id="surface1">
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 37.621094 26.75 C 38.160156 26.75 38.699219 26.746094 39.238281 26.738281 C 42.570312 26.726562 45.664062 27.152344 48.21875 29.503906 C 49.730469 31.136719 50.636719 32.953125 50.984375 35.140625 C 51.230469 35.140625 51.480469 35.136719 51.734375 35.136719 C 54.070312 35.117188 56.40625 35.105469 58.746094 35.097656 C 59.945312 35.09375 61.148438 35.089844 62.347656 35.078125 C 63.507812 35.070312 64.667969 35.066406 65.828125 35.0625 C 66.269531 35.0625 66.714844 35.058594 67.15625 35.054688 C 67.777344 35.046875 68.394531 35.046875 69.015625 35.046875 C 69.289062 35.042969 69.289062 35.042969 69.566406 35.039062 C 71.476562 35.050781 73.023438 35.703125 74.40625 37.011719 C 75.800781 38.492188 76.066406 39.964844 76.03125 41.9375 C 75.878906 43.707031 75.085938 45.160156 73.734375 46.3125 C 72.085938 47.347656 70.671875 47.5625 68.761719 47.554688 C 68.476562 47.554688 68.476562 47.554688 68.179688 47.554688 C 67.546875 47.554688 66.914062 47.550781 66.277344 47.550781 C 65.839844 47.550781 65.398438 47.550781 64.957031 47.550781 C 63.800781 47.550781 62.640625 47.546875 61.484375 47.546875 C 60.09375 47.542969 58.703125 47.542969 57.316406 47.539062 C 55.203125 47.539062 53.09375 47.535156 50.984375 47.53125 C 50.984375 51.621094 50.984375 55.710938 50.984375 59.921875 C 52.191406 59.914062 53.394531 59.902344 54.636719 59.894531 C 55.40625 59.890625 56.171875 59.886719 56.941406 59.882812 C 58.15625 59.878906 59.371094 59.871094 60.589844 59.859375 C 61.570312 59.851562 62.550781 59.847656 63.53125 59.84375 C 63.90625 59.84375 64.277344 59.839844 64.652344 59.835938 C 67.273438 59.804688 69.472656 59.882812 71.53125 61.714844 C 72.53125 62.890625 73.070312 64.011719 73.324219 65.527344 C 73.355469 65.703125 73.386719 65.875 73.417969 66.054688 C 73.488281 66.4375 73.558594 66.824219 73.625 67.210938 C 73.742188 67.882812 73.863281 68.550781 73.984375 69.222656 C 74.273438 70.816406 74.554688 72.410156 74.835938 74.003906 C 74.886719 74.296875 74.9375 74.589844 74.988281 74.878906 C 75.203125 76.09375 75.417969 77.304688 75.628906 78.519531 C 75.929688 80.238281 76.234375 81.957031 76.539062 83.679688 C 76.753906 84.898438 76.96875 86.121094 77.183594 87.339844 C 77.308594 88.066406 77.4375 88.789062 77.566406 89.515625 C 77.710938 90.320312 77.851562 91.128906 77.992188 91.9375 C 78.054688 92.289062 78.054688 92.289062 78.117188 92.644531 C 78.589844 95.398438 78.730469 97.792969 77.160156 100.191406 C 75.457031 101.820312 73.792969 102.269531 71.503906 102.226562 C 69.929688 102.152344 68.746094 101.441406 67.621094 100.367188 C 66.375 98.984375 66.007812 97.242188 65.710938 95.457031 C 65.671875 95.222656 65.628906 94.984375 65.585938 94.742188 C 65.449219 93.96875 65.316406 93.195312 65.1875 92.417969 C 65.09375 91.878906 65 91.335938 64.902344 90.796875 C 64.707031 89.667969 64.511719 88.535156 64.320312 87.40625 C 64.074219 85.957031 63.820312 84.507812 63.570312 83.058594 C 63.351562 81.8125 63.136719 80.5625 62.917969 79.3125 C 62.851562 78.910156 62.78125 78.511719 62.710938 78.113281 C 62.582031 77.367188 62.457031 76.621094 62.328125 75.875 C 62.269531 75.542969 62.269531 75.542969 62.210938 75.207031 C 62.046875 74.230469 61.902344 73.304688 61.953125 72.3125 C 61.417969 72.378906 60.878906 72.445312 60.328125 72.515625 C 60.296875 72.773438 60.269531 73.03125 60.238281 73.296875 C 59.390625 80.144531 56.839844 86.046875 52 91 C 51.839844 91.171875 51.679688 91.34375 51.515625 91.523438 C 46.671875 96.621094 39.476562 99.609375 32.484375 99.796875 C 32 99.800781 31.511719 99.800781 31.027344 99.796875 C 30.769531 99.796875 30.511719 99.796875 30.246094 99.792969 C 28.234375 99.769531 26.328125 99.613281 24.375 99.125 C 24.214844 99.085938 24.054688 99.046875 23.890625 99.007812 C 16.589844 97.148438 10.105469 92.542969 6.09375 86.125 C 2.125 79.429688 0.5625 71.3125 2.398438 63.660156 C 2.476562 63.363281 2.558594 63.0625 2.640625 62.765625 C 2.675781 62.628906 2.710938 62.496094 2.75 62.355469 C 4.726562 55.152344 9.757812 48.859375 16.160156 45.078125 C 18.753906 43.613281 22.144531 41.84375 25.1875 41.84375 C 25.183594 41.644531 25.179688 41.445312 25.179688 41.242188 C 25.167969 40.496094 25.160156 39.75 25.152344 39.003906 C 25.152344 38.683594 25.148438 38.363281 25.140625 38.042969 C 25.113281 36.238281 25.097656 34.601562 25.796875 32.90625 C 25.855469 32.761719 25.910156 32.621094 25.96875 32.472656 C 26.863281 30.328125 28.882812 28.574219 30.9375 27.574219 C 33.097656 26.714844 35.332031 26.742188 37.621094 26.75 Z M 29.910156 32.054688 C 28.476562 33.832031 28.113281 35.644531 28.128906 37.882812 C 28.128906 38.128906 28.125 38.375 28.125 38.625 C 28.121094 39.292969 28.121094 39.960938 28.125 40.632812 C 28.128906 41.335938 28.125 42.039062 28.125 42.742188 C 28.121094 43.921875 28.125 45.105469 28.128906 46.285156 C 28.132812 47.644531 28.132812 49.007812 28.128906 50.367188 C 28.125 51.539062 28.121094 52.710938 28.125 53.886719 C 28.125 54.585938 28.125 55.28125 28.125 55.980469 C 28.121094 56.761719 28.125 57.539062 28.128906 58.320312 C 28.128906 58.546875 28.125 58.773438 28.125 59.007812 C 28.152344 61.789062 29.039062 64.269531 30.941406 66.320312 C 33.398438 68.660156 36.164062 69.359375 39.488281 69.34375 C 39.894531 69.34375 40.296875 69.347656 40.699219 69.347656 C 41.128906 69.351562 41.558594 69.351562 41.988281 69.347656 C 42.882812 69.347656 43.777344 69.351562 44.675781 69.355469 C 46.738281 69.363281 48.796875 69.367188 50.859375 69.367188 C 52.757812 69.371094 54.652344 69.375 56.546875 69.382812 C 57.4375 69.386719 58.328125 69.386719 59.21875 69.382812 C 59.640625 69.382812 60.058594 69.386719 60.480469 69.386719 C 60.992188 69.390625 61.503906 69.390625 62.015625 69.386719 C 62.242188 69.386719 62.464844 69.390625 62.699219 69.390625 C 62.992188 69.390625 62.992188 69.390625 63.289062 69.390625 C 63.867188 69.480469 64.046875 69.613281 64.390625 70.078125 C 64.542969 70.582031 64.542969 70.582031 64.65625 71.199219 C 64.699219 71.4375 64.742188 71.675781 64.789062 71.917969 C 64.835938 72.1875 64.882812 72.457031 64.929688 72.722656 C 64.980469 73.011719 65.03125 73.296875 65.082031 73.582031 C 65.261719 74.578125 65.4375 75.578125 65.609375 76.578125 C 65.640625 76.765625 65.675781 76.953125 65.707031 77.148438 C 66.347656 80.839844 66.980469 84.535156 67.609375 88.230469 C 67.796875 89.347656 67.988281 90.464844 68.179688 91.582031 C 68.253906 92.003906 68.324219 92.425781 68.394531 92.847656 C 68.496094 93.429688 68.59375 94.015625 68.695312 94.597656 C 68.722656 94.769531 68.753906 94.941406 68.78125 95.117188 C 69.054688 96.664062 69.445312 97.867188 70.6875 98.921875 C 71.820312 99.300781 72.929688 99.308594 74.011719 98.808594 C 74.917969 97.96875 75.332031 97.328125 75.410156 96.109375 C 75.382812 94.785156 75.132812 93.492188 74.902344 92.191406 C 74.855469 91.90625 74.804688 91.621094 74.753906 91.335938 C 74.625 90.5625 74.488281 89.789062 74.355469 89.015625 C 74.242188 88.371094 74.128906 87.726562 74.019531 87.078125 C 73.753906 85.554688 73.488281 84.03125 73.222656 82.503906 C 72.949219 80.9375 72.675781 79.367188 72.40625 77.796875 C 72.175781 76.445312 71.941406 75.09375 71.703125 73.742188 C 71.566406 72.933594 71.425781 72.128906 71.285156 71.324219 C 71.15625 70.566406 71.023438 69.808594 70.890625 69.050781 C 70.84375 68.773438 70.792969 68.496094 70.746094 68.21875 C 70.316406 65.492188 70.316406 65.492188 68.65625 63.375 C 67.726562 62.882812 66.84375 62.910156 65.820312 62.910156 C 65.636719 62.910156 65.457031 62.90625 65.269531 62.90625 C 64.875 62.902344 64.484375 62.902344 64.089844 62.902344 C 63.46875 62.898438 62.847656 62.894531 62.226562 62.890625 C 60.683594 62.878906 59.140625 62.871094 57.597656 62.863281 C 56.292969 62.859375 54.988281 62.851562 53.683594 62.839844 C 53.070312 62.835938 52.457031 62.832031 51.84375 62.832031 C 51.472656 62.828125 51.097656 62.828125 50.722656 62.824219 C 50.464844 62.824219 50.464844 62.824219 50.203125 62.824219 C 49.410156 62.816406 49.015625 62.808594 48.34375 62.359375 C 48.042969 61.625 48.109375 60.816406 48.109375 60.035156 C 48.109375 59.882812 48.109375 59.734375 48.109375 59.578125 C 48.105469 59.078125 48.105469 58.582031 48.105469 58.082031 C 48.105469 57.734375 48.105469 57.386719 48.105469 57.039062 C 48.101562 56.3125 48.101562 55.585938 48.101562 54.859375 C 48.101562 53.929688 48.101562 52.996094 48.097656 52.066406 C 48.09375 51.347656 48.09375 50.632812 48.09375 49.914062 C 48.09375 49.570312 48.09375 49.230469 48.089844 48.886719 C 48.089844 48.40625 48.089844 47.925781 48.089844 47.445312 C 48.089844 47.304688 48.089844 47.160156 48.085938 47.015625 C 48.09375 46.222656 48.171875 45.597656 48.546875 44.890625 C 49.015625 44.65625 49.289062 44.660156 49.8125 44.660156 C 50.082031 44.65625 50.082031 44.65625 50.355469 44.65625 C 50.652344 44.65625 50.652344 44.65625 50.957031 44.65625 C 51.164062 44.65625 51.371094 44.652344 51.585938 44.652344 C 52.039062 44.648438 52.492188 44.648438 52.945312 44.648438 C 53.664062 44.644531 54.378906 44.640625 55.097656 44.636719 C 57.136719 44.625 59.175781 44.613281 61.214844 44.605469 C 62.460938 44.601562 63.707031 44.59375 64.953125 44.585938 C 65.429688 44.582031 65.90625 44.582031 66.378906 44.582031 C 67.046875 44.578125 67.710938 44.574219 68.375 44.570312 C 68.671875 44.570312 68.671875 44.570312 68.972656 44.570312 C 70.179688 44.554688 71.113281 44.425781 72.039062 43.5625 C 72.878906 42.460938 73.078125 41.800781 72.921875 40.421875 C 72.601562 39.425781 72.042969 38.851562 71.117188 38.351562 C 70.28125 38.03125 69.425781 38.078125 68.539062 38.085938 C 68.222656 38.085938 68.222656 38.085938 67.902344 38.082031 C 67.445312 38.082031 66.988281 38.082031 66.53125 38.082031 C 65.808594 38.085938 65.085938 38.082031 64.363281 38.078125 C 63.09375 38.074219 61.820312 38.074219 60.550781 38.074219 C 58.511719 38.074219 56.472656 38.070312 54.4375 38.0625 C 53.722656 38.058594 53.011719 38.0625 52.300781 38.0625 C 51.863281 38.0625 51.429688 38.0625 50.996094 38.058594 C 50.695312 38.0625 50.695312 38.0625 50.390625 38.0625 C 50.210938 38.0625 50.027344 38.0625 49.84375 38.058594 C 49.605469 38.058594 49.605469 38.058594 49.363281 38.058594 C 48.839844 37.964844 48.664062 37.792969 48.34375 37.375 C 48.1875 36.765625 48.1875 36.765625 48.0625 36.042969 C 47.605469 33.765625 46.820312 32.230469 44.890625 30.875 C 43.378906 30.027344 42.007812 29.828125 40.285156 29.816406 C 40.117188 29.816406 39.945312 29.816406 39.773438 29.816406 C 39.414062 29.8125 39.054688 29.8125 38.695312 29.8125 C 38.15625 29.808594 37.613281 29.800781 37.070312 29.796875 C 34.328125 29.777344 31.921875 29.972656 29.910156 32.054688 Z M 13.4375 50.984375 C 13 51.390625 13 51.390625 12.492188 51.785156 C 8.90625 54.738281 6.71875 59.621094 5.484375 63.984375 C 5.414062 64.222656 5.34375 64.464844 5.273438 64.710938 C 4.316406 68.757812 4.511719 73.621094 5.6875 77.59375 C 5.757812 77.847656 5.824219 78.105469 5.894531 78.367188 C 7.113281 82.464844 9.488281 85.886719 12.390625 88.96875 C 12.511719 89.105469 12.632812 89.238281 12.757812 89.378906 C 16.382812 93.234375 21.648438 95.632812 26.8125 96.484375 C 27.121094 96.539062 27.121094 96.539062 27.4375 96.597656 C 34.632812 97.414062 41.660156 95.574219 47.34375 91.058594 C 49.648438 89.160156 51.625 87.027344 53.21875 84.5 C 53.292969 84.382812 53.367188 84.269531 53.441406 84.148438 C 55.425781 80.964844 57.105469 76.992188 57.082031 73.1875 C 57.082031 73.023438 57.082031 72.859375 57.082031 72.691406 C 57.078125 72.566406 57.078125 72.441406 57.078125 72.3125 C 56.828125 72.3125 56.578125 72.3125 56.320312 72.308594 C 53.953125 72.300781 51.589844 72.289062 49.222656 72.273438 C 48.003906 72.265625 46.789062 72.261719 45.574219 72.257812 C 44.398438 72.253906 43.21875 72.246094 42.042969 72.238281 C 41.597656 72.234375 41.152344 72.234375 40.707031 72.234375 C 36.648438 72.230469 32.628906 71.914062 29.558594 68.925781 C 29.265625 68.613281 29.265625 68.613281 28.84375 68.65625 C 28.761719 68.476562 28.761719 68.476562 28.675781 68.292969 C 28.445312 67.855469 28.191406 67.515625 27.878906 67.132812 C 25.636719 64.171875 25.300781 61.085938 25.285156 57.507812 C 25.285156 57.140625 25.28125 56.777344 25.277344 56.410156 C 25.265625 55.457031 25.261719 54.503906 25.253906 53.550781 C 25.25 52.574219 25.238281 51.597656 25.230469 50.621094 C 25.210938 48.710938 25.199219 46.800781 25.1875 44.890625 C 21.136719 44.890625 16.222656 48.332031 13.4375 50.984375 Z M 13.4375 50.984375 "/>
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 67.074219 51.46875 C 67.222656 51.46875 67.367188 51.464844 67.515625 51.464844 C 68.003906 51.460938 68.488281 51.46875 68.976562 51.472656 C 69.324219 51.472656 69.671875 51.472656 70.023438 51.46875 C 70.96875 51.46875 71.917969 51.472656 72.863281 51.476562 C 73.855469 51.480469 74.84375 51.480469 75.835938 51.480469 C 77.5 51.484375 79.164062 51.488281 80.828125 51.496094 C 82.753906 51.503906 84.675781 51.507812 86.601562 51.507812 C 88.648438 51.507812 90.699219 51.511719 92.746094 51.515625 C 93.335938 51.519531 93.925781 51.519531 94.519531 51.519531 C 95.445312 51.519531 96.375 51.523438 97.300781 51.527344 C 97.640625 51.53125 97.980469 51.53125 98.324219 51.53125 C 98.785156 51.53125 99.25 51.535156 99.714844 51.539062 C 99.976562 51.539062 100.234375 51.539062 100.503906 51.539062 C 101.117188 51.589844 101.464844 51.65625 101.96875 52 C 102.40625 52.875 102.21875 54.035156 102.21875 55.007812 C 102.222656 55.4375 102.230469 55.867188 102.234375 56.300781 C 102.238281 56.574219 102.238281 56.847656 102.238281 57.121094 C 102.242188 57.371094 102.242188 57.621094 102.246094 57.878906 C 102.171875 58.5 102.171875 58.5 101.953125 58.839844 C 101.355469 59.253906 100.84375 59.1875 100.121094 59.1875 C 99.972656 59.1875 99.828125 59.191406 99.675781 59.191406 C 99.179688 59.195312 98.683594 59.191406 98.1875 59.191406 C 97.832031 59.191406 97.476562 59.195312 97.121094 59.195312 C 96.15625 59.199219 95.191406 59.199219 94.226562 59.199219 C 93.421875 59.199219 92.617188 59.199219 91.8125 59.199219 C 89.910156 59.203125 88.011719 59.203125 86.113281 59.199219 C 84.152344 59.199219 82.191406 59.199219 80.230469 59.207031 C 78.550781 59.210938 76.867188 59.210938 75.183594 59.210938 C 74.179688 59.210938 73.175781 59.210938 72.167969 59.214844 C 71.222656 59.21875 70.277344 59.214844 69.335938 59.210938 C 68.988281 59.210938 68.640625 59.210938 68.292969 59.214844 C 67.820312 59.21875 67.347656 59.214844 66.871094 59.210938 C 66.605469 59.210938 66.34375 59.210938 66.070312 59.210938 C 65.292969 59.09375 65.050781 58.921875 64.59375 58.296875 C 64.425781 57.363281 64.472656 56.414062 64.480469 55.464844 C 64.476562 55.207031 64.46875 54.949219 64.464844 54.679688 C 64.464844 54.429688 64.464844 54.179688 64.464844 53.925781 C 64.464844 53.695312 64.464844 53.46875 64.464844 53.234375 C 64.761719 51.792969 65.722656 51.464844 67.074219 51.46875 Z M 67.640625 54.640625 C 67.640625 55.109375 67.640625 55.578125 67.640625 56.0625 C 78.097656 56.0625 88.554688 56.0625 99.328125 56.0625 C 99.328125 55.59375 99.328125 55.125 99.328125 54.640625 C 88.871094 54.640625 78.414062 54.640625 67.640625 54.640625 Z M 67.640625 54.640625 "/>
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 44.363281 3.648438 C 46.414062 5.253906 47.761719 7.410156 48.34375 9.953125 C 48.675781 13.484375 48.046875 16.132812 45.929688 19 C 44.425781 20.746094 42.09375 22.007812 39.8125 22.34375 C 36.414062 22.558594 33.730469 21.941406 31.035156 19.742188 C 28.96875 17.835938 27.859375 15.34375 27.746094 12.558594 C 27.699219 9.65625 28.59375 6.964844 30.554688 4.773438 C 34.402344 1.125 39.976562 0.671875 44.363281 3.648438 Z M 33.050781 6.699219 C 31.597656 8.199219 30.832031 9.90625 30.761719 11.996094 C 30.851562 14.140625 31.5625 15.957031 33.109375 17.46875 C 35.097656 19 36.90625 19.527344 39.40625 19.296875 C 41.386719 18.863281 42.988281 17.820312 44.15625 16.160156 C 44.339844 15.855469 44.515625 15.546875 44.6875 15.234375 C 44.757812 15.113281 44.824219 14.996094 44.898438 14.871094 C 45.652344 13.28125 45.570312 10.902344 45.003906 9.265625 C 44.0625 7.296875 42.417969 5.871094 40.433594 5.027344 C 37.707031 4.359375 35.214844 4.832031 33.050781 6.699219 Z M 33.050781 6.699219 "/>
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 100.75 25.695312 C 101.359375 25.796875 101.359375 25.796875 101.84375 26.101562 C 102.296875 26.800781 102.265625 27.433594 102.171875 28.234375 C 101.976562 28.777344 101.78125 29.246094 101.523438 29.757812 C 101.457031 29.898438 101.386719 30.039062 101.316406 30.1875 C 101.167969 30.492188 101.019531 30.796875 100.871094 31.097656 C 100.550781 31.75 100.234375 32.402344 99.917969 33.054688 C 99.757812 33.394531 99.59375 33.730469 99.429688 34.066406 C 98.722656 35.53125 98.042969 37.007812 97.378906 38.492188 C 96.210938 41.089844 95.011719 43.671875 93.792969 46.25 C 93.71875 46.40625 93.644531 46.566406 93.566406 46.730469 C 93.351562 47.179688 93.140625 47.628906 92.925781 48.078125 C 92.863281 48.210938 92.800781 48.34375 92.738281 48.480469 C 92.566406 48.839844 92.390625 49.203125 92.21875 49.5625 C 92.113281 49.789062 92.003906 50.015625 91.894531 50.25 C 91.609375 50.78125 91.609375 50.78125 91.203125 50.984375 C 90.890625 51.003906 90.578125 51.011719 90.265625 51.015625 C 89.96875 51.015625 89.96875 51.015625 89.667969 51.019531 C 89.449219 51.019531 89.230469 51.019531 89.007812 51.019531 C 88.777344 51.023438 88.550781 51.023438 88.3125 51.023438 C 87.554688 51.027344 86.796875 51.03125 86.039062 51.03125 C 85.777344 51.035156 85.519531 51.035156 85.253906 51.035156 C 84.027344 51.039062 82.800781 51.042969 81.574219 51.042969 C 80.15625 51.046875 78.742188 51.050781 77.324219 51.058594 C 76.097656 51.066406 74.871094 51.070312 73.644531 51.070312 C 73.121094 51.070312 72.601562 51.074219 72.078125 51.078125 C 71.347656 51.082031 70.617188 51.082031 69.890625 51.078125 C 69.671875 51.082031 69.457031 51.085938 69.234375 51.085938 C 67.773438 51.078125 67.773438 51.078125 67.101562 50.609375 C 66.726562 50.007812 66.710938 49.652344 66.828125 48.953125 C 67.109375 48.515625 67.109375 48.515625 67.640625 48.140625 C 68.335938 48.015625 69.023438 48.027344 69.726562 48.03125 C 69.941406 48.027344 70.152344 48.027344 70.371094 48.023438 C 71.070312 48.015625 71.773438 48.015625 72.472656 48.015625 C 72.960938 48.015625 73.445312 48.011719 73.933594 48.007812 C 75.210938 47.996094 76.492188 47.992188 77.773438 47.992188 C 79.078125 47.988281 80.382812 47.976562 81.6875 47.96875 C 84.25 47.953125 86.8125 47.945312 89.375 47.9375 C 89.839844 46.957031 90.304688 45.976562 90.765625 44.996094 C 90.921875 44.664062 91.082031 44.332031 91.238281 44.003906 C 92.234375 41.894531 93.21875 39.78125 94.179688 37.65625 C 95.273438 35.226562 96.382812 32.804688 97.511719 30.390625 C 97.664062 30.066406 97.816406 29.738281 97.964844 29.410156 C 98.175781 28.957031 98.386719 28.503906 98.601562 28.050781 C 98.664062 27.917969 98.722656 27.785156 98.789062 27.644531 C 99.246094 26.675781 99.640625 25.878906 100.75 25.695312 Z M 100.75 25.695312 "/>
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 84.5 20.3125 C 84.964844 20.613281 84.964844 20.613281 85.414062 21.046875 C 85.585938 21.214844 85.757812 21.378906 85.933594 21.546875 C 86.132812 21.746094 86.332031 21.941406 86.53125 22.140625 C 86.785156 22.386719 87.035156 22.632812 87.289062 22.882812 C 87.796875 23.382812 88.300781 23.882812 88.804688 24.386719 C 88.910156 24.492188 89.015625 24.597656 89.125 24.707031 C 89.546875 25.171875 89.582031 25.433594 89.65625 26.078125 C 89.578125 26.8125 89.578125 26.8125 89.160156 27.320312 C 88.371094 27.722656 87.996094 27.632812 87.140625 27.421875 C 86.554688 26.988281 86.554688 26.988281 85.988281 26.421875 C 85.886719 26.324219 85.785156 26.222656 85.679688 26.117188 C 85.46875 25.90625 85.257812 25.691406 85.046875 25.476562 C 84.730469 25.148438 84.402344 24.828125 84.078125 24.507812 C 83.871094 24.300781 83.664062 24.09375 83.460938 23.882812 C 83.273438 23.695312 83.089844 23.507812 82.898438 23.3125 C 82.421875 22.6875 82.296875 22.3125 82.265625 21.53125 C 82.796875 20.394531 83.261719 20.199219 84.5 20.3125 Z M 84.5 20.3125 "/>
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 97.90625 15.03125 C 98.25 15.242188 98.25 15.242188 98.515625 15.640625 C 98.578125 16.214844 98.601562 16.746094 98.597656 17.324219 C 98.597656 17.570312 98.597656 17.570312 98.597656 17.820312 C 98.597656 18.167969 98.597656 18.515625 98.59375 18.863281 C 98.59375 19.398438 98.59375 19.929688 98.597656 20.460938 C 98.597656 20.796875 98.597656 21.136719 98.597656 21.472656 C 98.597656 21.714844 98.597656 21.714844 98.601562 21.957031 C 98.585938 23.054688 98.585938 23.054688 98.242188 23.554688 C 97.71875 23.882812 97.300781 23.945312 96.6875 23.96875 C 96.117188 23.714844 96.117188 23.714844 95.671875 23.359375 C 95.433594 22.648438 95.433594 22 95.425781 21.261719 C 95.425781 21.113281 95.425781 20.96875 95.425781 20.820312 C 95.421875 20.511719 95.421875 20.203125 95.421875 19.894531 C 95.417969 19.425781 95.410156 18.957031 95.40625 18.488281 C 95.402344 18.191406 95.402344 17.890625 95.402344 17.589844 C 95.398438 17.453125 95.398438 17.3125 95.394531 17.167969 C 95.398438 16.285156 95.539062 15.734375 96.078125 15.03125 C 96.71875 14.710938 97.222656 14.886719 97.90625 15.03125 Z M 97.90625 15.03125 "/>
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 79.394531 33.421875 C 79.550781 33.417969 79.703125 33.417969 79.863281 33.414062 C 80.1875 33.414062 80.515625 33.414062 80.839844 33.414062 C 81.335938 33.414062 81.832031 33.40625 82.328125 33.398438 C 82.644531 33.394531 82.964844 33.394531 83.28125 33.394531 C 83.429688 33.390625 83.574219 33.386719 83.726562 33.386719 C 84.523438 33.394531 84.878906 33.496094 85.515625 33.984375 C 85.921875 34.53125 85.921875 34.53125 85.933594 35.265625 C 85.691406 36.042969 85.601562 36.183594 84.90625 36.5625 C 84.355469 36.625 83.851562 36.652344 83.300781 36.652344 C 83.144531 36.652344 82.992188 36.652344 82.832031 36.652344 C 82.503906 36.65625 82.179688 36.65625 81.851562 36.652344 C 81.351562 36.652344 80.855469 36.65625 80.355469 36.664062 C 80.039062 36.664062 79.71875 36.664062 79.402344 36.664062 C 79.179688 36.667969 79.179688 36.667969 78.953125 36.671875 C 78.242188 36.660156 77.863281 36.617188 77.296875 36.175781 C 76.769531 35.453125 76.871094 35 76.984375 34.125 C 77.65625 33.375 78.453125 33.421875 79.394531 33.421875 Z M 79.394531 33.421875 "/>
                                        </g>
                                    </svg>
                                </a>
                            </div>
                            <h3>Empleo</h3>
                            <p></p>
                        </div>
                        <div class="col-sm-6 col-md-4 our-spec">
                            <div class="text-center" tabindex="0">
                                <a href="areas/forminno.php" aria-label="Ir al área de Formación e innovación">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="104px" height="104px" viewBox="0 0 104 104" version="1.1">
                                        <g id="surface1">
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 78.214844 2.15625 C 78.355469 2.160156 78.492188 2.160156 78.636719 2.160156 C 80.878906 2.167969 82.96875 2.347656 85.109375 3.046875 C 85.246094 3.089844 85.386719 3.132812 85.527344 3.179688 C 91.167969 4.996094 96.007812 8.972656 98.792969 14.21875 C 99.621094 15.855469 100.253906 17.527344 100.75 19.296875 C 100.847656 19.628906 100.847656 19.628906 100.945312 19.964844 C 102.316406 25.488281 101.335938 31.710938 98.554688 36.640625 C 96.597656 39.863281 93.953125 42.976562 90.980469 45.308594 C 89.691406 46.625 89.148438 48.347656 88.558594 50.050781 C 88.410156 50.476562 88.257812 50.898438 88.105469 51.324219 C 88.007812 51.597656 87.914062 51.871094 87.816406 52.144531 C 87.730469 52.390625 87.644531 52.636719 87.554688 52.886719 C 87.203125 54.113281 87.015625 55.070312 87.546875 56.265625 C 87.808594 59.046875 87.386719 62.085938 86.125 64.59375 C 86.058594 64.660156 85.992188 64.726562 85.921875 64.796875 C 85.894531 65.230469 85.875 65.667969 85.859375 66.105469 C 85.722656 68.101562 85.195312 69.71875 83.6875 71.09375 C 82.835938 71.738281 82.136719 71.726562 81.105469 71.714844 C 80.867188 71.714844 80.628906 71.710938 80.382812 71.710938 C 80.199219 71.707031 80.015625 71.707031 79.828125 71.703125 C 79.832031 71.875 79.832031 72.046875 79.835938 72.222656 C 79.855469 73.851562 79.871094 75.480469 79.878906 77.105469 C 79.886719 77.945312 79.894531 78.78125 79.90625 79.617188 C 79.914062 80.425781 79.921875 81.238281 79.925781 82.046875 C 79.925781 82.355469 79.929688 82.660156 79.933594 82.964844 C 79.972656 85.183594 79.605469 86.894531 78.8125 88.96875 C 77.824219 92.277344 78.339844 95.960938 79.445312 99.175781 C 79.695312 99.957031 79.679688 100.542969 79.625 101.359375 C 79.085938 101.898438 78.835938 101.816406 78.082031 101.816406 C 77.839844 101.820312 77.597656 101.820312 77.347656 101.820312 C 77.074219 101.820312 76.804688 101.820312 76.53125 101.820312 C 76.246094 101.820312 75.957031 101.820312 75.671875 101.824219 C 74.878906 101.824219 74.089844 101.824219 73.296875 101.824219 C 72.445312 101.824219 71.589844 101.828125 70.738281 101.828125 C 69.066406 101.832031 67.394531 101.832031 65.722656 101.832031 C 64.363281 101.832031 63.003906 101.835938 61.644531 101.835938 C 57.792969 101.839844 53.941406 101.839844 50.085938 101.839844 C 49.773438 101.839844 49.773438 101.839844 49.457031 101.839844 C 49.25 101.839844 49.039062 101.839844 48.828125 101.839844 C 45.457031 101.839844 42.085938 101.84375 38.714844 101.851562 C 35.253906 101.855469 31.796875 101.859375 28.335938 101.859375 C 26.394531 101.859375 24.449219 101.859375 22.507812 101.863281 C 20.851562 101.867188 19.199219 101.867188 17.542969 101.867188 C 16.699219 101.863281 15.855469 101.863281 15.011719 101.867188 C 14.238281 101.871094 13.464844 101.871094 12.691406 101.867188 C 12.28125 101.867188 11.871094 101.871094 11.460938 101.871094 C 8.714844 101.851562 6.859375 101.035156 4.886719 99.15625 C 3.382812 97.574219 2.617188 95.664062 2.613281 93.496094 C 2.613281 93.359375 2.613281 93.222656 2.613281 93.082031 C 2.609375 92.617188 2.609375 92.15625 2.609375 91.695312 C 2.609375 91.359375 2.609375 91.023438 2.609375 90.691406 C 2.605469 89.769531 2.605469 88.851562 2.605469 87.933594 C 2.605469 86.941406 2.601562 85.949219 2.601562 84.957031 C 2.597656 82.5625 2.59375 80.167969 2.59375 77.769531 C 2.59375 76.644531 2.589844 75.515625 2.589844 74.386719 C 2.585938 70.636719 2.582031 66.886719 2.582031 63.136719 C 2.582031 62.164062 2.582031 61.191406 2.582031 60.21875 C 2.582031 59.976562 2.582031 59.734375 2.582031 59.484375 C 2.578125 55.566406 2.574219 51.648438 2.566406 47.730469 C 2.558594 43.710938 2.554688 39.6875 2.554688 35.664062 C 2.554688 33.40625 2.554688 31.148438 2.546875 28.890625 C 2.542969 26.964844 2.542969 25.042969 2.542969 23.117188 C 2.546875 22.136719 2.546875 21.15625 2.542969 20.175781 C 2.539062 19.277344 2.539062 18.378906 2.539062 17.476562 C 2.542969 17.152344 2.539062 16.828125 2.539062 16.503906 C 2.519531 13.847656 2.851562 11.582031 4.671875 9.546875 C 4.773438 9.425781 4.875 9.300781 4.980469 9.175781 C 6.550781 7.417969 8.796875 6.621094 11.105469 6.464844 C 11.6875 6.449219 12.273438 6.449219 12.859375 6.453125 C 13.191406 6.453125 13.191406 6.453125 13.53125 6.453125 C 14.273438 6.453125 15.015625 6.457031 15.753906 6.457031 C 16.289062 6.457031 16.820312 6.457031 17.355469 6.457031 C 18.5 6.457031 19.644531 6.460938 20.789062 6.460938 C 22.601562 6.464844 24.414062 6.464844 26.226562 6.464844 C 26.847656 6.464844 27.46875 6.464844 28.089844 6.460938 C 28.246094 6.460938 28.402344 6.460938 28.5625 6.460938 C 30.324219 6.460938 32.085938 6.460938 33.851562 6.460938 C 34.09375 6.460938 34.09375 6.460938 34.339844 6.460938 C 36.949219 6.460938 39.5625 6.464844 42.171875 6.46875 C 44.855469 6.472656 47.542969 6.472656 50.226562 6.46875 C 51.878906 6.464844 53.535156 6.46875 55.1875 6.472656 C 56.457031 6.480469 57.722656 6.476562 58.992188 6.472656 C 59.511719 6.472656 60.03125 6.472656 60.550781 6.476562 C 64.179688 6.589844 64.179688 6.589844 67.234375 4.875 C 70.621094 2.902344 74.335938 2.152344 78.214844 2.15625 Z M 69.671875 6.703125 C 69.453125 6.808594 69.234375 6.917969 69.007812 7.027344 C 67.523438 7.785156 66.261719 8.667969 65 9.75 C 64.878906 9.839844 64.761719 9.929688 64.636719 10.023438 C 60.761719 13.019531 58.453125 18.421875 57.777344 23.132812 C 57.15625 28.382812 58.496094 33.328125 61.746094 37.484375 C 63.121094 39.078125 64.722656 40.484375 66.269531 41.914062 C 66.503906 42.136719 66.730469 42.363281 66.953125 42.597656 C 67.21875 42.90625 67.21875 42.90625 67.640625 42.859375 C 67.695312 42.976562 67.75 43.09375 67.808594 43.214844 C 68.046875 43.675781 68.324219 44.074219 68.625 44.492188 C 69.324219 45.53125 69.75 46.609375 70.167969 47.785156 C 70.242188 47.996094 70.320312 48.203125 70.398438 48.421875 C 71.144531 50.480469 71.839844 52.558594 72.515625 54.640625 C 73.253906 54.640625 73.992188 54.640625 74.75 54.640625 C 74.75 48.070312 74.75 41.503906 74.75 34.734375 C 73.34375 34.601562 71.933594 34.464844 70.484375 34.328125 C 68.703125 33.339844 68.003906 32.84375 67.15625 31.117188 C 66.839844 29.492188 66.933594 28.101562 67.640625 26.609375 C 67.835938 26.332031 68.039062 26.058594 68.25 25.796875 C 68.25 25.664062 68.25 25.527344 68.25 25.390625 C 70.09375 24.441406 71.96875 23.71875 74.042969 24.320312 C 75.503906 24.914062 76.328125 25.792969 77.027344 27.1875 C 77.421875 28.261719 77.441406 29.410156 77.503906 30.546875 C 77.511719 30.695312 77.523438 30.84375 77.53125 30.996094 C 77.554688 31.363281 77.574219 31.726562 77.59375 32.09375 C 78.0625 32.09375 78.53125 32.09375 79.015625 32.09375 C 79.015625 31.980469 79.019531 31.867188 79.019531 31.75 C 79.03125 31.226562 79.050781 30.699219 79.066406 30.175781 C 79.070312 30 79.070312 29.820312 79.074219 29.636719 C 79.128906 28.144531 79.464844 26.917969 80.402344 25.734375 C 81.734375 24.613281 83.078125 24.039062 84.839844 24.097656 C 85.730469 24.238281 86.53125 24.601562 87.34375 24.984375 C 87.492188 25.050781 87.640625 25.121094 87.796875 25.191406 C 88.269531 25.453125 88.351562 25.707031 88.5625 26.203125 C 88.71875 26.453125 88.71875 26.453125 88.878906 26.710938 C 89.574219 27.917969 89.511719 29.316406 89.375 30.671875 C 88.953125 32.054688 88.1875 33.179688 86.9375 33.921875 C 85.253906 34.714844 83.519531 34.617188 81.65625 34.734375 C 81.65625 41.304688 81.65625 47.871094 81.65625 54.640625 C 82.460938 54.640625 83.265625 54.640625 84.09375 54.640625 C 84.476562 53.652344 84.828125 52.65625 85.164062 51.652344 C 85.25 51.40625 85.332031 51.15625 85.417969 50.898438 C 85.59375 50.382812 85.765625 49.867188 85.933594 49.355469 C 87.355469 45.164062 87.355469 45.164062 88.84375 43.609375 C 89 43.441406 89.15625 43.277344 89.320312 43.105469 C 89.714844 42.71875 90.121094 42.355469 90.542969 41.996094 C 94.257812 38.835938 97.152344 34.621094 98.3125 29.859375 C 98.378906 29.589844 98.378906 29.589844 98.449219 29.3125 C 99.542969 24.0625 98.402344 18.425781 95.5 13.953125 C 92.234375 9.199219 87.363281 6.03125 81.6875 4.96875 C 77.605469 4.367188 73.375 4.882812 69.671875 6.703125 Z M 7.074219 10.976562 C 5.3125 12.84375 5.191406 14.492188 5.207031 16.972656 C 5.207031 17.273438 5.207031 17.574219 5.207031 17.875 C 5.203125 18.703125 5.207031 19.527344 5.210938 20.351562 C 5.214844 21.242188 5.210938 22.132812 5.210938 23.027344 C 5.210938 24.570312 5.214844 26.113281 5.21875 27.65625 C 5.222656 29.886719 5.222656 32.117188 5.222656 34.347656 C 5.226562 37.96875 5.230469 41.585938 5.234375 45.207031 C 5.242188 48.722656 5.246094 52.238281 5.246094 55.753906 C 5.246094 55.972656 5.25 56.1875 5.25 56.410156 C 5.25 57.5 5.25 58.585938 5.25 59.675781 C 5.257812 68.695312 5.269531 77.714844 5.28125 86.734375 C 5.46875 86.621094 5.660156 86.507812 5.851562 86.394531 C 6.105469 86.246094 6.359375 86.097656 6.613281 85.945312 C 6.800781 85.835938 6.800781 85.835938 6.988281 85.722656 C 7.175781 85.613281 7.175781 85.613281 7.363281 85.503906 C 7.476562 85.4375 7.585938 85.371094 7.703125 85.304688 C 9.609375 84.425781 11.203125 84.617188 13.40625 84.5 C 13.40625 59.632812 13.40625 34.761719 13.40625 9.140625 C 10.75 9.140625 9.003906 9.160156 7.074219 10.976562 Z M 16.046875 9.140625 C 16.046875 34.007812 16.046875 58.878906 16.046875 84.5 C 36.15625 84.5 56.265625 84.5 76.984375 84.5 C 76.984375 80.277344 76.984375 76.054688 76.984375 71.703125 C 76.046875 71.636719 75.109375 71.570312 74.140625 71.5 C 72.859375 71.042969 72.101562 70.5 71.5 69.265625 C 71.089844 68.164062 70.878906 67.097656 70.8125 65.925781 C 70.742188 64.835938 70.394531 63.976562 69.925781 62.992188 C 69.195312 61.175781 69.214844 59.414062 69.242188 57.472656 C 69.242188 57.195312 69.242188 56.921875 69.246094 56.644531 C 69.25 55.976562 69.257812 55.308594 69.265625 54.640625 C 69.398438 54.640625 69.535156 54.640625 69.671875 54.640625 C 69.359375 53.023438 68.84375 51.507812 68.289062 49.957031 C 68.203125 49.707031 68.113281 49.457031 68.027344 49.199219 C 67.148438 46.667969 67.148438 46.667969 65.40625 44.6875 C 65.066406 44.75 64.726562 44.820312 64.390625 44.890625 C 63.902344 44.910156 63.410156 44.917969 62.921875 44.921875 C 62.777344 44.921875 62.628906 44.921875 62.480469 44.921875 C 61.992188 44.925781 61.507812 44.925781 61.019531 44.925781 C 60.667969 44.929688 60.320312 44.929688 59.96875 44.929688 C 58.824219 44.933594 57.675781 44.9375 56.527344 44.9375 C 56.132812 44.941406 55.738281 44.941406 55.34375 44.941406 C 53.484375 44.945312 51.628906 44.949219 49.769531 44.949219 C 47.628906 44.953125 45.484375 44.957031 43.34375 44.964844 C 41.683594 44.972656 40.027344 44.976562 38.371094 44.976562 C 37.378906 44.976562 36.390625 44.976562 35.402344 44.984375 C 34.472656 44.988281 33.539062 44.988281 32.609375 44.984375 C 32.269531 44.984375 31.929688 44.988281 31.585938 44.988281 C 28.257812 45.015625 28.257812 45.015625 27.015625 44.078125 C 26.136719 43.09375 26.085938 42.050781 26.097656 40.785156 C 26.097656 40.601562 26.09375 40.417969 26.09375 40.230469 C 26.089844 39.632812 26.09375 39.035156 26.09375 38.433594 C 26.09375 38.015625 26.09375 37.597656 26.09375 37.179688 C 26.089844 36.304688 26.09375 35.429688 26.097656 34.554688 C 26.101562 33.4375 26.097656 32.316406 26.09375 31.199219 C 26.089844 30.335938 26.09375 29.472656 26.09375 28.609375 C 26.09375 28.195312 26.09375 27.785156 26.09375 27.371094 C 26.089844 26.792969 26.09375 26.214844 26.097656 25.636719 C 26.097656 25.46875 26.09375 25.300781 26.09375 25.125 C 26.109375 24.03125 26.265625 23.175781 27.015625 22.34375 C 28.261719 21.324219 29.753906 21.414062 31.285156 21.421875 C 31.546875 21.417969 31.808594 21.417969 32.078125 21.414062 C 32.792969 21.410156 33.507812 21.40625 34.21875 21.40625 C 34.964844 21.40625 35.714844 21.402344 36.460938 21.398438 C 37.875 21.390625 39.285156 21.386719 40.699219 21.382812 C 42.308594 21.378906 43.917969 21.371094 45.527344 21.363281 C 48.835938 21.347656 52.144531 21.335938 55.453125 21.328125 C 55.484375 21.214844 55.519531 21.101562 55.550781 20.984375 C 55.703125 20.460938 55.859375 19.933594 56.011719 19.410156 C 56.066406 19.234375 56.117188 19.054688 56.167969 18.871094 C 57.164062 15.5 58.816406 12.453125 61.25 9.902344 C 61.589844 9.570312 61.589844 9.570312 61.546875 9.140625 C 46.53125 9.140625 31.515625 9.140625 16.046875 9.140625 Z M 28.882812 24.234375 C 28.464844 24.828125 28.554688 25.523438 28.5625 26.222656 C 28.5625 26.390625 28.558594 26.558594 28.558594 26.730469 C 28.554688 27.285156 28.558594 27.839844 28.558594 28.394531 C 28.558594 28.777344 28.558594 29.164062 28.558594 29.546875 C 28.558594 30.355469 28.558594 31.160156 28.5625 31.96875 C 28.566406 33.003906 28.5625 34.035156 28.558594 35.070312 C 28.558594 35.867188 28.558594 36.660156 28.558594 37.457031 C 28.558594 37.835938 28.558594 38.21875 28.558594 38.601562 C 28.554688 39.132812 28.558594 39.667969 28.5625 40.199219 C 28.558594 40.359375 28.558594 40.515625 28.558594 40.679688 C 28.558594 41.476562 28.558594 41.476562 28.867188 42.1875 C 29.441406 42.585938 29.910156 42.527344 30.601562 42.527344 C 30.742188 42.527344 30.878906 42.527344 31.023438 42.527344 C 31.488281 42.527344 31.949219 42.527344 32.414062 42.523438 C 32.746094 42.523438 33.078125 42.523438 33.410156 42.523438 C 34.3125 42.523438 35.21875 42.519531 36.121094 42.515625 C 37.0625 42.511719 38.007812 42.511719 38.949219 42.511719 C 40.738281 42.507812 42.523438 42.503906 44.3125 42.496094 C 46.347656 42.488281 48.378906 42.488281 50.414062 42.484375 C 54.597656 42.476562 58.785156 42.464844 62.96875 42.453125 C 62.476562 41.957031 61.984375 41.464844 61.472656 40.988281 C 57.226562 36.933594 55.40625 31.558594 55.136719 25.796875 C 55.128906 25.621094 55.117188 25.445312 55.109375 25.261719 C 55.085938 24.832031 55.066406 24.398438 55.046875 23.96875 C 51.695312 23.953125 48.339844 23.945312 44.988281 23.9375 C 43.429688 23.933594 41.875 23.929688 40.316406 23.921875 C 38.960938 23.917969 37.605469 23.914062 36.246094 23.910156 C 35.527344 23.910156 34.808594 23.910156 34.09375 23.90625 C 33.289062 23.898438 32.488281 23.898438 31.6875 23.898438 C 31.449219 23.898438 31.210938 23.894531 30.964844 23.894531 C 30.746094 23.894531 30.527344 23.894531 30.300781 23.894531 C 30.113281 23.894531 29.921875 23.894531 29.726562 23.894531 C 29.242188 23.933594 29.242188 23.933594 28.882812 24.234375 Z M 70.015625 27.546875 C 69.496094 28.277344 69.433594 28.976562 69.46875 29.859375 C 69.808594 30.917969 70.328125 31.367188 71.296875 31.890625 C 72.070312 32.09375 72.796875 32.113281 73.59375 32.105469 C 73.8125 32.105469 74.027344 32.101562 74.253906 32.101562 C 74.5 32.097656 74.5 32.097656 74.75 32.09375 C 74.765625 31.472656 74.777344 30.851562 74.789062 30.226562 C 74.792969 30.050781 74.796875 29.875 74.804688 29.695312 C 74.816406 28.703125 74.804688 28.015625 74.140625 27.21875 C 72.589844 26.21875 71.457031 26.535156 70.015625 27.546875 Z M 82.203125 27.625 C 81.703125 28.214844 81.628906 28.710938 81.636719 29.457031 C 81.636719 29.609375 81.636719 29.761719 81.636719 29.921875 C 81.640625 30.082031 81.640625 30.242188 81.644531 30.40625 C 81.644531 30.566406 81.644531 30.730469 81.644531 30.894531 C 81.648438 31.296875 81.652344 31.695312 81.65625 32.09375 C 84.21875 32.234375 84.21875 32.234375 86.40625 31.078125 C 87.023438 30.316406 87.003906 29.589844 86.9375 28.640625 C 86.570312 27.808594 86.078125 27.300781 85.3125 26.8125 C 83.980469 26.449219 83.226562 26.738281 82.203125 27.625 Z M 77.59375 34.734375 C 77.59375 41.304688 77.59375 47.871094 77.59375 54.640625 C 78.0625 54.640625 78.53125 54.640625 79.015625 54.640625 C 79.015625 48.070312 79.015625 41.503906 79.015625 34.734375 C 78.546875 34.734375 78.078125 34.734375 77.59375 34.734375 Z M 71.703125 57.28125 C 71.703125 57.617188 71.703125 57.953125 71.703125 58.296875 C 71.878906 58.300781 72.054688 58.304688 72.238281 58.308594 C 72.890625 58.324219 73.542969 58.34375 74.199219 58.363281 C 74.480469 58.367188 74.761719 58.375 75.046875 58.382812 C 75.453125 58.390625 75.859375 58.402344 76.265625 58.414062 C 76.632812 58.425781 76.632812 58.425781 77.007812 58.433594 C 77.59375 58.5 77.59375 58.5 78 58.90625 C 78.0625 59.617188 78.0625 59.617188 78 60.328125 C 77.292969 60.800781 76.96875 60.792969 76.132812 60.820312 C 75.886719 60.828125 75.640625 60.835938 75.386719 60.84375 C 75.003906 60.851562 75.003906 60.851562 74.609375 60.859375 C 74.222656 60.875 74.222656 60.875 73.824219 60.886719 C 73.1875 60.90625 72.546875 60.921875 71.90625 60.9375 C 72.105469 61.675781 72.308594 62.414062 72.515625 63.171875 C 76.269531 63.171875 80.023438 63.171875 83.890625 63.171875 C 83.957031 62.902344 84.023438 62.636719 84.09375 62.359375 C 83.980469 62.355469 83.867188 62.351562 83.753906 62.347656 C 83.238281 62.328125 82.726562 62.304688 82.214844 62.28125 C 81.949219 62.273438 81.949219 62.273438 81.675781 62.265625 C 81.507812 62.257812 81.335938 62.25 81.15625 62.242188 C 81 62.234375 80.84375 62.226562 80.679688 62.222656 C 80.191406 62.148438 79.847656 61.992188 79.421875 61.75 C 79.421875 61.214844 79.421875 60.675781 79.421875 60.125 C 80.0625 59.753906 80.554688 59.660156 81.292969 59.632812 C 81.492188 59.625 81.6875 59.617188 81.894531 59.609375 C 82.101562 59.605469 82.308594 59.597656 82.519531 59.59375 C 82.730469 59.582031 82.9375 59.574219 83.15625 59.566406 C 83.671875 59.550781 84.1875 59.53125 84.703125 59.515625 C 84.769531 58.777344 84.835938 58.039062 84.90625 57.28125 C 80.550781 57.28125 76.191406 57.28125 71.703125 57.28125 Z M 73.53125 65.8125 C 73.464844 67.597656 73.464844 67.597656 74.34375 68.859375 C 75.296875 69.335938 76.714844 69.125 77.773438 69.132812 C 78.109375 69.136719 78.445312 69.140625 78.78125 69.148438 C 79.269531 69.160156 79.753906 69.164062 80.238281 69.167969 C 80.390625 69.171875 80.539062 69.179688 80.695312 69.183594 C 81.46875 69.179688 81.789062 69.132812 82.351562 68.578125 C 82.972656 67.550781 83.078125 67.066406 83.078125 65.8125 C 79.925781 65.8125 76.777344 65.8125 73.53125 65.8125 Z M 6.5 89.375 C 5.355469 90.9375 5.089844 92.558594 5.28125 94.453125 C 5.714844 96.105469 6.8125 97.359375 8.136719 98.390625 C 9.132812 98.9375 10.007812 99.175781 11.140625 99.175781 C 11.375 99.175781 11.609375 99.175781 11.847656 99.175781 C 12.105469 99.175781 12.359375 99.175781 12.621094 99.175781 C 12.894531 99.175781 13.171875 99.175781 13.445312 99.175781 C 14.199219 99.175781 14.957031 99.175781 15.710938 99.171875 C 16.523438 99.171875 17.339844 99.171875 18.152344 99.171875 C 19.5625 99.171875 20.972656 99.171875 22.382812 99.167969 C 24.421875 99.164062 26.460938 99.164062 28.5 99.164062 C 31.808594 99.164062 35.117188 99.160156 38.421875 99.15625 C 41.636719 99.152344 44.851562 99.148438 48.0625 99.148438 C 48.359375 99.148438 48.359375 99.148438 48.664062 99.148438 C 49.65625 99.144531 50.652344 99.144531 51.644531 99.144531 C 59.886719 99.140625 68.132812 99.132812 76.375 99.125 C 76.117188 97.566406 75.851562 96.007812 75.5625 94.453125 C 75.449219 94.453125 75.335938 94.453125 75.21875 94.453125 C 68.480469 94.4375 61.746094 94.417969 55.011719 94.394531 C 54.203125 94.394531 53.394531 94.390625 52.585938 94.386719 C 52.425781 94.386719 52.265625 94.386719 52.097656 94.386719 C 49.492188 94.378906 46.882812 94.371094 44.277344 94.367188 C 41.601562 94.359375 38.929688 94.351562 36.253906 94.34375 C 34.601562 94.335938 32.953125 94.332031 31.300781 94.328125 C 30.035156 94.328125 28.773438 94.324219 27.507812 94.316406 C 26.988281 94.316406 26.46875 94.316406 25.949219 94.3125 C 25.242188 94.3125 24.535156 94.308594 23.828125 94.304688 C 23.617188 94.308594 23.410156 94.308594 23.195312 94.308594 C 23.007812 94.304688 22.820312 94.304688 22.625 94.304688 C 22.378906 94.300781 22.378906 94.300781 22.128906 94.300781 C 21.734375 94.25 21.734375 94.25 21.328125 93.84375 C 21.277344 93.132812 21.277344 93.132812 21.328125 92.421875 C 21.824219 91.925781 21.945312 91.964844 22.625 91.960938 C 22.8125 91.960938 23 91.960938 23.195312 91.957031 C 23.507812 91.957031 23.507812 91.957031 23.828125 91.960938 C 24.046875 91.957031 24.265625 91.957031 24.492188 91.957031 C 25.238281 91.953125 25.980469 91.953125 26.722656 91.949219 C 27.253906 91.949219 27.78125 91.945312 28.3125 91.945312 C 29.457031 91.941406 30.601562 91.9375 31.746094 91.933594 C 33.402344 91.933594 35.054688 91.925781 36.710938 91.921875 C 39.394531 91.910156 42.078125 91.90625 44.761719 91.898438 C 47.371094 91.890625 49.980469 91.886719 52.585938 91.878906 C 52.746094 91.875 52.910156 91.875 53.074219 91.875 C 53.878906 91.871094 54.6875 91.871094 55.492188 91.867188 C 62.183594 91.847656 68.871094 91.828125 75.5625 91.8125 C 75.699219 91.148438 75.832031 90.484375 75.96875 89.820312 C 76.007812 89.628906 76.046875 89.441406 76.085938 89.246094 C 76.121094 89.066406 76.160156 88.886719 76.195312 88.699219 C 76.230469 88.53125 76.265625 88.363281 76.300781 88.191406 C 76.390625 87.722656 76.390625 87.722656 76.375 87.140625 C 67.945312 87.121094 59.515625 87.109375 51.082031 87.097656 C 50.085938 87.097656 49.09375 87.097656 48.097656 87.097656 C 47.898438 87.097656 47.699219 87.09375 47.496094 87.09375 C 44.289062 87.09375 41.078125 87.085938 37.871094 87.078125 C 34.578125 87.070312 31.285156 87.066406 27.996094 87.0625 C 25.964844 87.0625 23.933594 87.058594 21.902344 87.054688 C 20.507812 87.046875 19.113281 87.046875 17.722656 87.046875 C 16.917969 87.050781 16.113281 87.046875 15.308594 87.042969 C 14.574219 87.039062 13.835938 87.039062 13.101562 87.042969 C 12.710938 87.042969 12.320312 87.039062 11.925781 87.035156 C 9.65625 87.050781 8.054688 87.699219 6.5 89.375 Z M 6.5 89.375 "/>
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 29.855469 71.0625 C 30 71.0625 30.148438 71.0625 30.300781 71.0625 C 30.796875 71.058594 31.292969 71.058594 31.789062 71.058594 C 32.144531 71.054688 32.5 71.054688 32.855469 71.054688 C 33.820312 71.050781 34.785156 71.046875 35.753906 71.046875 C 36.355469 71.046875 36.960938 71.042969 37.5625 71.042969 C 39.449219 71.039062 41.335938 71.035156 43.21875 71.035156 C 45.398438 71.03125 47.578125 71.027344 49.757812 71.019531 C 51.441406 71.011719 53.125 71.007812 54.804688 71.007812 C 55.8125 71.007812 56.820312 71.007812 57.824219 71 C 58.769531 70.996094 59.714844 70.996094 60.664062 71 C 61.007812 71 61.355469 70.996094 61.703125 70.996094 C 62.179688 70.992188 62.652344 70.992188 63.125 70.996094 C 63.390625 70.996094 63.65625 70.992188 63.929688 70.992188 C 64.59375 71.09375 64.59375 71.09375 65.09375 71.46875 C 65.40625 71.90625 65.40625 71.90625 65.394531 72.527344 C 65.191406 73.15625 65.121094 73.359375 64.59375 73.734375 C 63.957031 73.8125 63.957031 73.8125 63.152344 73.8125 C 63.003906 73.8125 62.855469 73.8125 62.703125 73.816406 C 62.207031 73.816406 61.710938 73.816406 61.214844 73.8125 C 60.859375 73.8125 60.503906 73.816406 60.148438 73.816406 C 59.183594 73.820312 58.21875 73.816406 57.25 73.816406 C 56.242188 73.816406 55.230469 73.816406 54.222656 73.816406 C 52.527344 73.816406 50.828125 73.816406 49.132812 73.8125 C 47.171875 73.808594 45.210938 73.8125 43.246094 73.816406 C 41.5625 73.816406 39.878906 73.816406 38.199219 73.816406 C 37.191406 73.816406 36.183594 73.816406 35.179688 73.816406 C 34.234375 73.820312 33.289062 73.816406 32.34375 73.8125 C 31.996094 73.8125 31.648438 73.8125 31.300781 73.816406 C 30.824219 73.816406 30.351562 73.816406 29.878906 73.8125 C 29.613281 73.8125 29.347656 73.8125 29.074219 73.8125 C 28.4375 73.734375 28.4375 73.734375 28.054688 73.449219 C 27.777344 73.050781 27.667969 72.796875 27.625 72.3125 C 27.789062 71.765625 27.789062 71.765625 28.03125 71.296875 C 28.636719 70.996094 29.179688 71.066406 29.855469 71.0625 Z M 29.855469 71.0625 "/>
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 30.039062 63.34375 C 30.183594 63.34375 30.332031 63.34375 30.480469 63.339844 C 30.972656 63.339844 31.460938 63.335938 31.953125 63.335938 C 32.304688 63.335938 32.652344 63.332031 33.003906 63.332031 C 34.15625 63.324219 35.3125 63.324219 36.464844 63.320312 C 36.863281 63.320312 37.257812 63.316406 37.65625 63.316406 C 39.304688 63.3125 40.953125 63.308594 42.605469 63.304688 C 44.972656 63.300781 47.339844 63.296875 49.703125 63.285156 C 51.367188 63.277344 53.03125 63.273438 54.695312 63.269531 C 55.6875 63.269531 56.679688 63.269531 57.675781 63.261719 C 58.609375 63.253906 59.542969 63.253906 60.480469 63.257812 C 60.824219 63.257812 61.164062 63.253906 61.507812 63.25 C 61.976562 63.246094 62.445312 63.25 62.914062 63.25 C 63.175781 63.25 63.4375 63.25 63.707031 63.25 C 64.570312 63.40625 64.878906 63.703125 65.40625 64.390625 C 65.390625 65.152344 65.339844 65.472656 64.796875 66.015625 C 64.3125 66.054688 63.847656 66.070312 63.363281 66.066406 C 63.214844 66.066406 63.0625 66.070312 62.910156 66.070312 C 62.40625 66.070312 61.902344 66.070312 61.402344 66.066406 C 61.042969 66.070312 60.683594 66.070312 60.320312 66.070312 C 59.34375 66.070312 58.363281 66.070312 57.386719 66.070312 C 56.363281 66.070312 55.339844 66.070312 54.320312 66.070312 C 52.601562 66.070312 50.882812 66.070312 49.167969 66.070312 C 47.179688 66.066406 45.191406 66.066406 43.203125 66.070312 C 41.5 66.070312 39.796875 66.070312 38.09375 66.070312 C 37.074219 66.070312 36.054688 66.070312 35.035156 66.070312 C 34.078125 66.070312 33.121094 66.070312 32.164062 66.070312 C 31.8125 66.070312 31.457031 66.070312 31.105469 66.070312 C 30.628906 66.070312 30.148438 66.070312 29.667969 66.066406 C 29.527344 66.066406 29.386719 66.070312 29.242188 66.070312 C 28.28125 66.0625 28.28125 66.0625 27.828125 65.609375 C 27.59375 64.9375 27.582031 64.6875 27.890625 64.035156 C 28.503906 63.21875 29.0625 63.347656 30.039062 63.34375 Z M 30.039062 63.34375 "/>
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 29.878906 55.78125 C 30.027344 55.78125 30.171875 55.777344 30.324219 55.777344 C 30.820312 55.773438 31.316406 55.777344 31.8125 55.777344 C 32.167969 55.777344 32.523438 55.773438 32.878906 55.773438 C 33.84375 55.769531 34.808594 55.769531 35.773438 55.769531 C 36.578125 55.769531 37.382812 55.769531 38.1875 55.769531 C 40.089844 55.765625 41.988281 55.765625 43.886719 55.769531 C 45.847656 55.769531 47.808594 55.769531 49.769531 55.761719 C 51.449219 55.757812 53.132812 55.757812 54.816406 55.757812 C 55.820312 55.757812 56.824219 55.757812 57.832031 55.753906 C 58.777344 55.75 59.722656 55.753906 60.664062 55.757812 C 61.011719 55.757812 61.359375 55.757812 61.707031 55.753906 C 62.179688 55.75 62.652344 55.753906 63.128906 55.757812 C 63.523438 55.757812 63.523438 55.757812 63.929688 55.757812 C 64.59375 55.859375 64.59375 55.859375 65.09375 56.234375 C 65.40625 56.671875 65.40625 56.671875 65.394531 57.292969 C 65.191406 57.921875 65.121094 58.125 64.59375 58.5 C 63.960938 58.578125 63.960938 58.578125 63.160156 58.578125 C 63.015625 58.578125 62.867188 58.578125 62.714844 58.582031 C 62.222656 58.582031 61.730469 58.582031 61.238281 58.578125 C 60.882812 58.578125 60.53125 58.582031 60.179688 58.582031 C 59.21875 58.585938 58.257812 58.582031 57.296875 58.582031 C 56.296875 58.582031 55.292969 58.582031 54.289062 58.582031 C 52.601562 58.582031 50.917969 58.582031 49.230469 58.578125 C 47.28125 58.574219 45.332031 58.578125 43.382812 58.582031 C 41.710938 58.582031 40.039062 58.582031 38.363281 58.582031 C 37.367188 58.582031 36.367188 58.582031 35.367188 58.582031 C 34.425781 58.585938 33.488281 58.582031 32.546875 58.578125 C 32.203125 58.578125 31.855469 58.578125 31.511719 58.582031 C 31.039062 58.582031 30.570312 58.582031 30.097656 58.578125 C 29.703125 58.578125 29.703125 58.578125 29.300781 58.578125 C 28.601562 58.496094 28.304688 58.402344 27.828125 57.890625 C 27.679688 56.699219 27.679688 56.699219 28.046875 56.125 C 28.644531 55.71875 29.160156 55.78125 29.878906 55.78125 Z M 29.878906 55.78125 "/>
                                        </g>
                                    </svg>
                                </a>
                            </div>
                            <h3>Formación e innovación</h3>
                            <p></p>
                        </div>
                    </div>

                    <!-- Segunda fila con 3 elementos -->
                    <div class="row row-pad justify-content-center">
                        <div class="col-sm-6 col-md-4 our-spec">
                            <div class="text-center" tabindex="0">
                                <a href="areas/ocio.php" aria-label="Ir al área de Ocio">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="104px" height="104px" viewBox="0 0 104 104" version="1.1">
                                        <g id="surface1">
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 37.621094 26.75 C 38.160156 26.75 38.699219 26.746094 39.238281 26.738281 C 42.570312 26.726562 45.664062 27.152344 48.21875 29.503906 C 49.730469 31.136719 50.636719 32.953125 50.984375 35.140625 C 51.230469 35.140625 51.480469 35.136719 51.734375 35.136719 C 54.070312 35.117188 56.40625 35.105469 58.746094 35.097656 C 59.945312 35.09375 61.148438 35.089844 62.347656 35.078125 C 63.507812 35.070312 64.667969 35.066406 65.828125 35.0625 C 66.269531 35.0625 66.714844 35.058594 67.15625 35.054688 C 67.777344 35.046875 68.394531 35.046875 69.015625 35.046875 C 69.289062 35.042969 69.289062 35.042969 69.566406 35.039062 C 71.476562 35.050781 73.023438 35.703125 74.40625 37.011719 C 75.800781 38.492188 76.066406 39.964844 76.03125 41.9375 C 75.878906 43.707031 75.085938 45.160156 73.734375 46.3125 C 72.085938 47.347656 70.671875 47.5625 68.761719 47.554688 C 68.476562 47.554688 68.476562 47.554688 68.179688 47.554688 C 67.546875 47.554688 66.914062 47.550781 66.277344 47.550781 C 65.839844 47.550781 65.398438 47.550781 64.957031 47.550781 C 63.800781 47.550781 62.640625 47.546875 61.484375 47.546875 C 60.09375 47.542969 58.703125 47.542969 57.316406 47.539062 C 55.203125 47.539062 53.09375 47.535156 50.984375 47.53125 C 50.984375 51.621094 50.984375 55.710938 50.984375 59.921875 C 52.191406 59.914062 53.394531 59.902344 54.636719 59.894531 C 55.40625 59.890625 56.171875 59.886719 56.941406 59.882812 C 58.15625 59.878906 59.371094 59.871094 60.589844 59.859375 C 61.570312 59.851562 62.550781 59.847656 63.53125 59.84375 C 63.90625 59.84375 64.277344 59.839844 64.652344 59.835938 C 67.273438 59.804688 69.472656 59.882812 71.53125 61.714844 C 72.53125 62.890625 73.070312 64.011719 73.324219 65.527344 C 73.355469 65.703125 73.386719 65.875 73.417969 66.054688 C 73.488281 66.4375 73.558594 66.824219 73.625 67.210938 C 73.742188 67.882812 73.863281 68.550781 73.984375 69.222656 C 74.273438 70.816406 74.554688 72.410156 74.835938 74.003906 C 74.886719 74.296875 74.9375 74.589844 74.988281 74.878906 C 75.203125 76.09375 75.417969 77.304688 75.628906 78.519531 C 75.929688 80.238281 76.234375 81.957031 76.539062 83.679688 C 76.753906 84.898438 76.96875 86.121094 77.183594 87.339844 C 77.308594 88.066406 77.4375 88.789062 77.566406 89.515625 C 77.710938 90.320312 77.851562 91.128906 77.992188 91.9375 C 78.054688 92.289062 78.054688 92.289062 78.117188 92.644531 C 78.589844 95.398438 78.730469 97.792969 77.160156 100.191406 C 75.457031 101.820312 73.792969 102.269531 71.503906 102.226562 C 69.929688 102.152344 68.746094 101.441406 67.621094 100.367188 C 66.375 98.984375 66.007812 97.242188 65.710938 95.457031 C 65.671875 95.222656 65.628906 94.984375 65.585938 94.742188 C 65.449219 93.96875 65.316406 93.195312 65.1875 92.417969 C 65.09375 91.878906 65 91.335938 64.902344 90.796875 C 64.707031 89.667969 64.511719 88.535156 64.320312 87.40625 C 64.074219 85.957031 63.820312 84.507812 63.570312 83.058594 C 63.351562 81.8125 63.136719 80.5625 62.917969 79.3125 C 62.851562 78.910156 62.78125 78.511719 62.710938 78.113281 C 62.582031 77.367188 62.457031 76.621094 62.328125 75.875 C 62.269531 75.542969 62.269531 75.542969 62.210938 75.207031 C 62.046875 74.230469 61.902344 73.304688 61.953125 72.3125 C 61.417969 72.378906 60.878906 72.445312 60.328125 72.515625 C 60.296875 72.773438 60.269531 73.03125 60.238281 73.296875 C 59.390625 80.144531 56.839844 86.046875 52 91 C 51.839844 91.171875 51.679688 91.34375 51.515625 91.523438 C 46.671875 96.621094 39.476562 99.609375 32.484375 99.796875 C 32 99.800781 31.511719 99.800781 31.027344 99.796875 C 30.769531 99.796875 30.511719 99.796875 30.246094 99.792969 C 28.234375 99.769531 26.328125 99.613281 24.375 99.125 C 24.214844 99.085938 24.054688 99.046875 23.890625 99.007812 C 16.589844 97.148438 10.105469 92.542969 6.09375 86.125 C 2.125 79.429688 0.5625 71.3125 2.398438 63.660156 C 2.476562 63.363281 2.558594 63.0625 2.640625 62.765625 C 2.675781 62.628906 2.710938 62.496094 2.75 62.355469 C 4.726562 55.152344 9.757812 48.859375 16.160156 45.078125 C 18.753906 43.613281 22.144531 41.84375 25.1875 41.84375 C 25.183594 41.644531 25.179688 41.445312 25.179688 41.242188 C 25.167969 40.496094 25.160156 39.75 25.152344 39.003906 C 25.152344 38.683594 25.148438 38.363281 25.140625 38.042969 C 25.113281 36.238281 25.097656 34.601562 25.796875 32.90625 C 25.855469 32.761719 25.910156 32.621094 25.96875 32.472656 C 26.863281 30.328125 28.882812 28.574219 30.9375 27.574219 C 33.097656 26.714844 35.332031 26.742188 37.621094 26.75 Z M 29.910156 32.054688 C 28.476562 33.832031 28.113281 35.644531 28.128906 37.882812 C 28.128906 38.128906 28.125 38.375 28.125 38.625 C 28.121094 39.292969 28.121094 39.960938 28.125 40.632812 C 28.128906 41.335938 28.125 42.039062 28.125 42.742188 C 28.121094 43.921875 28.125 45.105469 28.128906 46.285156 C 28.132812 47.644531 28.132812 49.007812 28.128906 50.367188 C 28.125 51.539062 28.121094 52.710938 28.125 53.886719 C 28.125 54.585938 28.125 55.28125 28.125 55.980469 C 28.121094 56.761719 28.125 57.539062 28.128906 58.320312 C 28.128906 58.546875 28.125 58.773438 28.125 59.007812 C 28.152344 61.789062 29.039062 64.269531 30.941406 66.320312 C 33.398438 68.660156 36.164062 69.359375 39.488281 69.34375 C 39.894531 69.34375 40.296875 69.347656 40.699219 69.347656 C 41.128906 69.351562 41.558594 69.351562 41.988281 69.347656 C 42.882812 69.347656 43.777344 69.351562 44.675781 69.355469 C 46.738281 69.363281 48.796875 69.367188 50.859375 69.367188 C 52.757812 69.371094 54.652344 69.375 56.546875 69.382812 C 57.4375 69.386719 58.328125 69.386719 59.21875 69.382812 C 59.640625 69.382812 60.058594 69.386719 60.480469 69.386719 C 60.992188 69.390625 61.503906 69.390625 62.015625 69.386719 C 62.242188 69.386719 62.464844 69.390625 62.699219 69.390625 C 62.992188 69.390625 62.992188 69.390625 63.289062 69.390625 C 63.867188 69.480469 64.046875 69.613281 64.390625 70.078125 C 64.542969 70.582031 64.542969 70.582031 64.65625 71.199219 C 64.699219 71.4375 64.742188 71.675781 64.789062 71.917969 C 64.835938 72.1875 64.882812 72.457031 64.929688 72.722656 C 64.980469 73.011719 65.03125 73.296875 65.082031 73.582031 C 65.261719 74.578125 65.4375 75.578125 65.609375 76.578125 C 65.640625 76.765625 65.675781 76.953125 65.707031 77.148438 C 66.347656 80.839844 66.980469 84.535156 67.609375 88.230469 C 67.796875 89.347656 67.988281 90.464844 68.179688 91.582031 C 68.253906 92.003906 68.324219 92.425781 68.394531 92.847656 C 68.496094 93.429688 68.59375 94.015625 68.695312 94.597656 C 68.722656 94.769531 68.753906 94.941406 68.78125 95.117188 C 69.054688 96.664062 69.445312 97.867188 70.6875 98.921875 C 71.820312 99.300781 72.929688 99.308594 74.011719 98.808594 C 74.917969 97.96875 75.332031 97.328125 75.410156 96.109375 C 75.382812 94.785156 75.132812 93.492188 74.902344 92.191406 C 74.855469 91.90625 74.804688 91.621094 74.753906 91.335938 C 74.625 90.5625 74.488281 89.789062 74.355469 89.015625 C 74.242188 88.371094 74.128906 87.726562 74.019531 87.078125 C 73.753906 85.554688 73.488281 84.03125 73.222656 82.503906 C 72.949219 80.9375 72.675781 79.367188 72.40625 77.796875 C 72.175781 76.445312 71.941406 75.09375 71.703125 73.742188 C 71.566406 72.933594 71.425781 72.128906 71.285156 71.324219 C 71.15625 70.566406 71.023438 69.808594 70.890625 69.050781 C 70.84375 68.773438 70.792969 68.496094 70.746094 68.21875 C 70.316406 65.492188 70.316406 65.492188 68.65625 63.375 C 67.726562 62.882812 66.84375 62.910156 65.820312 62.910156 C 65.636719 62.910156 65.457031 62.90625 65.269531 62.90625 C 64.875 62.902344 64.484375 62.902344 64.089844 62.902344 C 63.46875 62.898438 62.847656 62.894531 62.226562 62.890625 C 60.683594 62.878906 59.140625 62.871094 57.597656 62.863281 C 56.292969 62.859375 54.988281 62.851562 53.683594 62.839844 C 53.070312 62.835938 52.457031 62.832031 51.84375 62.832031 C 51.472656 62.828125 51.097656 62.828125 50.722656 62.824219 C 50.464844 62.824219 50.464844 62.824219 50.203125 62.824219 C 49.410156 62.816406 49.015625 62.808594 48.34375 62.359375 C 48.042969 61.625 48.109375 60.816406 48.109375 60.035156 C 48.109375 59.882812 48.109375 59.734375 48.109375 59.578125 C 48.105469 59.078125 48.105469 58.582031 48.105469 58.082031 C 48.105469 57.734375 48.105469 57.386719 48.105469 57.039062 C 48.101562 56.3125 48.101562 55.585938 48.101562 54.859375 C 48.101562 53.929688 48.101562 52.996094 48.097656 52.066406 C 48.09375 51.347656 48.09375 50.632812 48.09375 49.914062 C 48.09375 49.570312 48.09375 49.230469 48.089844 48.886719 C 48.089844 48.40625 48.089844 47.925781 48.089844 47.445312 C 48.089844 47.304688 48.089844 47.160156 48.085938 47.015625 C 48.09375 46.222656 48.171875 45.597656 48.546875 44.890625 C 49.015625 44.65625 49.289062 44.660156 49.8125 44.660156 C 50.082031 44.65625 50.082031 44.65625 50.355469 44.65625 C 50.652344 44.65625 50.652344 44.65625 50.957031 44.65625 C 51.164062 44.65625 51.371094 44.652344 51.585938 44.652344 C 52.039062 44.648438 52.492188 44.648438 52.945312 44.648438 C 53.664062 44.644531 54.378906 44.640625 55.097656 44.636719 C 57.136719 44.625 59.175781 44.613281 61.214844 44.605469 C 62.460938 44.601562 63.707031 44.59375 64.953125 44.585938 C 65.429688 44.582031 65.90625 44.582031 66.378906 44.582031 C 67.046875 44.578125 67.710938 44.574219 68.375 44.570312 C 68.671875 44.570312 68.671875 44.570312 68.972656 44.570312 C 70.179688 44.554688 71.113281 44.425781 72.039062 43.5625 C 72.878906 42.460938 73.078125 41.800781 72.921875 40.421875 C 72.601562 39.425781 72.042969 38.851562 71.117188 38.351562 C 70.28125 38.03125 69.425781 38.078125 68.539062 38.085938 C 68.222656 38.085938 68.222656 38.085938 67.902344 38.082031 C 67.445312 38.082031 66.988281 38.082031 66.53125 38.082031 C 65.808594 38.085938 65.085938 38.082031 64.363281 38.078125 C 63.09375 38.074219 61.820312 38.074219 60.550781 38.074219 C 58.511719 38.074219 56.472656 38.070312 54.4375 38.0625 C 53.722656 38.058594 53.011719 38.0625 52.300781 38.0625 C 51.863281 38.0625 51.429688 38.0625 50.996094 38.058594 C 50.695312 38.0625 50.695312 38.0625 50.390625 38.0625 C 50.210938 38.0625 50.027344 38.0625 49.84375 38.058594 C 49.605469 38.058594 49.605469 38.058594 49.363281 38.058594 C 48.839844 37.964844 48.664062 37.792969 48.34375 37.375 C 48.1875 36.765625 48.1875 36.765625 48.0625 36.042969 C 47.605469 33.765625 46.820312 32.230469 44.890625 30.875 C 43.378906 30.027344 42.007812 29.828125 40.285156 29.816406 C 40.117188 29.816406 39.945312 29.816406 39.773438 29.816406 C 39.414062 29.8125 39.054688 29.8125 38.695312 29.8125 C 38.15625 29.808594 37.613281 29.800781 37.070312 29.796875 C 34.328125 29.777344 31.921875 29.972656 29.910156 32.054688 Z M 13.4375 50.984375 C 13 51.390625 13 51.390625 12.492188 51.785156 C 8.90625 54.738281 6.71875 59.621094 5.484375 63.984375 C 5.414062 64.222656 5.34375 64.464844 5.273438 64.710938 C 4.316406 68.757812 4.511719 73.621094 5.6875 77.59375 C 5.757812 77.847656 5.824219 78.105469 5.894531 78.367188 C 7.113281 82.464844 9.488281 85.886719 12.390625 88.96875 C 12.511719 89.105469 12.632812 89.238281 12.757812 89.378906 C 16.382812 93.234375 21.648438 95.632812 26.8125 96.484375 C 27.121094 96.539062 27.121094 96.539062 27.4375 96.597656 C 34.632812 97.414062 41.660156 95.574219 47.34375 91.058594 C 49.648438 89.160156 51.625 87.027344 53.21875 84.5 C 53.292969 84.382812 53.367188 84.269531 53.441406 84.148438 C 55.425781 80.964844 57.105469 76.992188 57.082031 73.1875 C 57.082031 73.023438 57.082031 72.859375 57.082031 72.691406 C 57.078125 72.566406 57.078125 72.441406 57.078125 72.3125 C 56.828125 72.3125 56.578125 72.3125 56.320312 72.308594 C 53.953125 72.300781 51.589844 72.289062 49.222656 72.273438 C 48.003906 72.265625 46.789062 72.261719 45.574219 72.257812 C 44.398438 72.253906 43.21875 72.246094 42.042969 72.238281 C 41.597656 72.234375 41.152344 72.234375 40.707031 72.234375 C 36.648438 72.230469 32.628906 71.914062 29.558594 68.925781 C 29.265625 68.613281 29.265625 68.613281 28.84375 68.65625 C 28.761719 68.476562 28.761719 68.476562 28.675781 68.292969 C 28.445312 67.855469 28.191406 67.515625 27.878906 67.132812 C 25.636719 64.171875 25.300781 61.085938 25.285156 57.507812 C 25.285156 57.140625 25.28125 56.777344 25.277344 56.410156 C 25.265625 55.457031 25.261719 54.503906 25.253906 53.550781 C 25.25 52.574219 25.238281 51.597656 25.230469 50.621094 C 25.210938 48.710938 25.199219 46.800781 25.1875 44.890625 C 21.136719 44.890625 16.222656 48.332031 13.4375 50.984375 Z M 13.4375 50.984375 "/>
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0.392157%,0.392157%,0.392157%);fill-opacity:1;" d="M 89.578125 11.578125 C 92.160156 13.464844 93.902344 16.355469 94.453125 19.5 C 94.757812 23.589844 94.144531 26.953125 91.484375 30.203125 C 89.511719 32.296875 86.726562 33.941406 83.816406 34.160156 C 79.6875 34.265625 76.296875 33.699219 73.15625 30.742188 C 70.277344 27.613281 69.496094 24.203125 69.636719 20.046875 C 69.828125 17.125 71.398438 14.351562 73.53125 12.390625 C 78.210938 8.605469 84.554688 8.101562 89.578125 11.578125 Z M 74.265625 14.625 C 72.113281 17.277344 71.417969 19.917969 71.648438 23.28125 C 71.875 25.296875 72.828125 27.109375 74.140625 28.640625 C 74.207031 28.640625 74.273438 28.640625 74.34375 28.640625 C 74.347656 28.371094 74.347656 28.371094 74.351562 28.09375 C 74.5625 22.644531 77.253906 17.671875 81.136719 13.914062 C 82.214844 12.96875 83.386719 12.347656 84.703125 11.78125 C 81.65625 9.75 76.425781 12.300781 74.265625 14.625 Z M 79.84375 18.335938 C 77.589844 21.65625 76.015625 25.175781 76.375 29.25 C 76.71875 29.917969 76.71875 29.917969 77.378906 29.984375 C 79.253906 29.890625 80.90625 28.109375 82.125 26.839844 C 84.964844 23.628906 87.054688 19.34375 87.003906 15.023438 C 86.941406 14.449219 86.828125 14.097656 86.53125 13.609375 C 83.757812 13.179688 81.292969 16.292969 79.84375 18.335938 Z M 88.96875 14.015625 C 88.960938 14.359375 88.960938 14.359375 88.953125 14.707031 C 88.765625 20.503906 86.238281 25.644531 82.0625 29.65625 C 81.167969 30.375 80.253906 30.988281 79.21875 31.484375 C 79.21875 31.617188 79.21875 31.753906 79.21875 31.890625 C 81.730469 32.589844 84.644531 32.261719 86.9375 31.078125 C 89.566406 29.515625 91.246094 27.40625 92.246094 24.523438 C 92.910156 21.660156 92.550781 18.710938 91.003906 16.210938 C 90.402344 15.34375 89.84375 14.613281 88.96875 14.015625 Z M 88.96875 14.015625 "/>
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 44.363281 3.648438 C 46.414062 5.253906 47.761719 7.410156 48.34375 9.953125 C 48.675781 13.484375 48.046875 16.132812 45.929688 19 C 44.425781 20.746094 42.09375 22.007812 39.8125 22.34375 C 36.414062 22.558594 33.730469 21.941406 31.035156 19.742188 C 28.96875 17.835938 27.859375 15.34375 27.746094 12.558594 C 27.699219 9.65625 28.59375 6.964844 30.554688 4.773438 C 34.402344 1.125 39.976562 0.671875 44.363281 3.648438 Z M 33.050781 6.699219 C 31.597656 8.199219 30.832031 9.90625 30.761719 11.996094 C 30.851562 14.140625 31.5625 15.957031 33.109375 17.46875 C 35.097656 19 36.90625 19.527344 39.40625 19.296875 C 41.386719 18.863281 42.988281 17.820312 44.15625 16.160156 C 44.339844 15.855469 44.515625 15.546875 44.6875 15.234375 C 44.757812 15.113281 44.824219 14.996094 44.898438 14.871094 C 45.652344 13.28125 45.570312 10.902344 45.003906 9.265625 C 44.0625 7.296875 42.417969 5.871094 40.433594 5.027344 C 37.707031 4.359375 35.214844 4.832031 33.050781 6.699219 Z M 33.050781 6.699219 "/>
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 65.40625 58.90625 C 66.277344 58.90625 67.148438 58.90625 68.046875 58.90625 C 68.046875 58.972656 68.046875 59.039062 68.046875 59.109375 C 67.175781 59.109375 66.304688 59.109375 65.40625 59.109375 C 65.40625 59.042969 65.40625 58.976562 65.40625 58.90625 Z M 65.40625 58.90625 "/>
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 67.640625 48.140625 C 68.042969 48.140625 68.445312 48.140625 68.859375 48.140625 C 68.859375 48.207031 68.859375 48.273438 68.859375 48.34375 C 68.457031 48.34375 68.054688 48.34375 67.640625 48.34375 C 67.640625 48.277344 67.640625 48.210938 67.640625 48.140625 Z M 67.640625 48.140625 "/>
                                        </g>
                                    </svg>
                                </a>
                            </div>
                            <h3>Ocio</h3>
                            <p></p>
                        </div>
                        <div class="col-sm-6 col-md-4 our-spec">
                            <div class="text-center" tabindex="0">
                                <a href="areas/participaca.php" aria-label="Ir al área de participación y cultura accesible">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="104px" height="104px" viewBox="0 0 104 104" version="1.1">
                                        <g id="surface1">
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 23.117188 -0.113281 C 23.316406 -0.121094 23.515625 -0.132812 23.71875 -0.140625 C 25.238281 0.183594 26.128906 1.339844 27.085938 2.492188 C 29.34375 5.339844 29.34375 5.339844 32.523438 6.792969 C 34.042969 6.90625 35.558594 6.730469 37.070312 6.5625 C 41.757812 6.160156 47.144531 8.898438 50.683594 11.792969 C 51.0625 12.121094 51.433594 12.453125 51.796875 12.796875 C 52.453125 12.503906 52.941406 12.132812 53.484375 11.667969 C 56.742188 9.039062 60.496094 7.464844 64.59375 6.703125 C 64.832031 6.65625 65.074219 6.609375 65.320312 6.558594 C 66.105469 6.460938 66.8125 6.574219 67.59375 6.6875 C 70.730469 7.082031 70.730469 7.082031 73.582031 5.933594 C 75.136719 4.65625 76.488281 3.128906 77.761719 1.578125 C 78.460938 0.730469 79.074219 0.0976562 80.199219 -0.101562 C 80.398438 -0.105469 80.601562 -0.109375 80.804688 -0.113281 C 81.003906 -0.121094 81.203125 -0.132812 81.40625 -0.140625 C 83.132812 0.230469 84.472656 1.945312 85.671875 3.148438 C 85.863281 3.34375 86.058594 3.535156 86.25 3.726562 C 86.773438 4.246094 87.292969 4.765625 87.8125 5.289062 C 88.355469 5.832031 88.902344 6.378906 89.449219 6.925781 C 90.363281 7.839844 91.28125 8.757812 92.195312 9.671875 C 93.25 10.730469 94.308594 11.789062 95.367188 12.84375 C 96.277344 13.753906 97.1875 14.664062 98.097656 15.574219 C 98.640625 16.117188 99.183594 16.660156 99.726562 17.203125 C 100.238281 17.710938 100.746094 18.222656 101.253906 18.734375 C 101.53125 19.007812 101.804688 19.285156 102.082031 19.558594 C 102.328125 19.808594 102.328125 19.808594 102.582031 20.058594 C 102.722656 20.203125 102.867188 20.34375 103.011719 20.492188 C 103.75 21.332031 104.09375 21.996094 104.113281 23.117188 C 104.121094 23.316406 104.132812 23.515625 104.140625 23.71875 C 103.816406 25.238281 102.660156 26.128906 101.507812 27.085938 C 98.660156 29.34375 98.660156 29.34375 97.207031 32.523438 C 97.09375 34.042969 97.269531 35.558594 97.4375 37.070312 C 97.839844 41.757812 95.09375 47.242188 92.144531 50.742188 C 90.902344 52.117188 89.601562 53.3125 88.167969 54.476562 C 81.960938 59.375 81.960938 59.375 78.9375 66.324219 C 78.730469 68.871094 78.953125 71.410156 79.253906 73.945312 C 79.488281 76.039062 79.246094 77.832031 78.609375 79.828125 C 78.5625 79.976562 78.515625 80.121094 78.46875 80.273438 C 77.503906 83.261719 76.015625 85.996094 73.9375 88.359375 C 73.800781 88.523438 73.664062 88.6875 73.523438 88.855469 C 72.769531 89.734375 72.078125 90.410156 71.09375 91.027344 C 70.269531 91.589844 69.605469 92.140625 69.328125 93.125 C 69.125 94.566406 69.195312 96.015625 69.253906 97.460938 C 69.269531 98.117188 69.28125 98.769531 69.289062 99.421875 C 69.292969 99.828125 69.304688 100.230469 69.320312 100.636719 C 69.347656 101.796875 69.269531 102.492188 68.453125 103.390625 C 67.667969 103.960938 67.117188 104.078125 66.152344 104.078125 C 65.890625 104.082031 65.632812 104.082031 65.367188 104.085938 C 65.078125 104.082031 64.792969 104.082031 64.507812 104.082031 C 64.203125 104.082031 63.902344 104.085938 63.601562 104.085938 C 62.78125 104.089844 61.960938 104.089844 61.140625 104.089844 C 60.457031 104.089844 59.769531 104.089844 59.085938 104.089844 C 57.46875 104.09375 55.851562 104.09375 54.238281 104.089844 C 52.570312 104.089844 50.902344 104.089844 49.238281 104.097656 C 47.804688 104.101562 46.375 104.101562 44.941406 104.101562 C 44.085938 104.101562 43.234375 104.101562 42.378906 104.105469 C 41.574219 104.109375 40.769531 104.105469 39.96875 104.101562 C 39.671875 104.101562 39.378906 104.101562 39.082031 104.105469 C 38.679688 104.109375 38.277344 104.105469 37.875 104.101562 C 37.648438 104.101562 37.425781 104.101562 37.191406 104.101562 C 36.238281 103.949219 35.597656 103.472656 34.964844 102.761719 C 34.613281 101.863281 34.695312 101.019531 34.730469 100.070312 C 34.738281 99.648438 34.742188 99.230469 34.746094 98.808594 C 34.757812 98.148438 34.773438 97.492188 34.800781 96.832031 C 34.996094 94.152344 34.996094 94.152344 33.921875 91.820312 C 33.34375 91.285156 32.714844 90.851562 32.046875 90.433594 C 28.550781 88.03125 26.226562 82.914062 25.1875 79.015625 C 25.125 78.828125 25.066406 78.636719 25 78.441406 C 24.515625 76.652344 24.648438 75.179688 24.910156 73.351562 C 25.410156 69.28125 25.832031 65.042969 23.183594 61.621094 C 20.542969 58.375 17.316406 55.539062 14.097656 52.875 C 11.957031 51.085938 10.273438 49.058594 9.078125 46.515625 C 9.011719 46.378906 8.945312 46.246094 8.878906 46.105469 C 8.257812 44.785156 7.722656 43.449219 7.3125 42.046875 C 7.273438 41.910156 7.234375 41.777344 7.191406 41.636719 C 6.695312 39.863281 6.4375 38.28125 6.65625 36.441406 C 7.058594 33.285156 7.058594 33.285156 5.933594 30.417969 C 4.660156 28.859375 3.136719 27.503906 1.578125 26.234375 C 0.726562 25.535156 0.0976562 24.925781 -0.101562 23.800781 C -0.105469 23.601562 -0.109375 23.398438 -0.113281 23.195312 C -0.121094 22.996094 -0.132812 22.796875 -0.140625 22.59375 C 0.230469 20.867188 1.945312 19.527344 3.148438 18.328125 C 3.34375 18.136719 3.535156 17.941406 3.726562 17.75 C 4.246094 17.226562 4.765625 16.707031 5.289062 16.1875 C 5.832031 15.644531 6.378906 15.097656 6.925781 14.550781 C 7.839844 13.636719 8.757812 12.71875 9.671875 11.804688 C 10.730469 10.75 11.789062 9.691406 12.84375 8.632812 C 13.753906 7.722656 14.664062 6.8125 15.574219 5.902344 C 16.117188 5.359375 16.660156 4.816406 17.203125 4.273438 C 17.710938 3.761719 18.222656 3.253906 18.734375 2.746094 C 19.007812 2.46875 19.285156 2.195312 19.558594 1.917969 C 19.722656 1.753906 19.890625 1.589844 20.058594 1.417969 C 20.203125 1.277344 20.34375 1.132812 20.492188 0.988281 C 21.332031 0.25 21.996094 -0.09375 23.117188 -0.113281 Z M 22.664062 7.957031 C 22.554688 8.070312 22.441406 8.179688 22.324219 8.296875 C 22.195312 8.425781 22.070312 8.554688 21.9375 8.683594 C 21.792969 8.828125 21.652344 8.96875 21.507812 9.117188 C 21.359375 9.265625 21.210938 9.414062 21.058594 9.566406 C 20.566406 10.0625 20.074219 10.554688 19.582031 11.050781 C 19.238281 11.394531 18.898438 11.734375 18.558594 12.074219 C 17.660156 12.976562 16.761719 13.878906 15.863281 14.78125 C 14.949219 15.699219 14.03125 16.617188 13.113281 17.539062 C 11.316406 19.339844 9.515625 21.148438 7.71875 22.953125 C 8.074219 23.71875 8.511719 24.1875 9.121094 24.773438 C 9.320312 24.96875 9.519531 25.160156 9.71875 25.355469 C 10.03125 25.660156 10.347656 25.960938 10.660156 26.261719 C 10.964844 26.554688 11.269531 26.851562 11.570312 27.148438 C 11.753906 27.320312 11.933594 27.496094 12.121094 27.675781 C 12.804688 28.480469 13.164062 29.25 13.265625 30.316406 C 13.214844 30.917969 13.128906 31.433594 13 32.015625 C 12.136719 36.566406 13.078125 41.347656 15.621094 45.210938 C 16.960938 47.171875 18.640625 48.804688 20.328125 50.464844 C 20.839844 50.964844 21.347656 51.472656 21.855469 51.976562 C 22.179688 52.296875 22.503906 52.617188 22.828125 52.9375 C 22.980469 53.089844 23.132812 53.238281 23.289062 53.394531 C 23.429688 53.53125 23.570312 53.671875 23.714844 53.8125 C 23.835938 53.933594 23.960938 54.054688 24.085938 54.179688 C 24.335938 54.460938 24.335938 54.460938 24.578125 54.4375 C 24.597656 54.265625 24.617188 54.09375 24.636719 53.917969 C 24.917969 51.804688 25.441406 50.230469 27.015625 48.75 C 28.527344 47.605469 29.941406 47.25 31.8125 47.25 C 32.082031 47.25 32.082031 47.25 32.359375 47.25 C 33.839844 47.273438 34.988281 47.574219 36.359375 48.140625 C 36.492188 48.140625 36.628906 48.140625 36.765625 48.140625 C 36.765625 48.019531 36.765625 47.898438 36.765625 47.773438 C 36.769531 46.496094 36.78125 45.222656 36.796875 43.945312 C 36.800781 43.472656 36.804688 42.996094 36.804688 42.523438 C 36.8125 39.695312 36.855469 37.425781 38.226562 34.902344 C 38.460938 34.378906 38.316406 34.070312 38.1875 33.515625 C 38.007812 30.152344 38.476562 27.105469 40.789062 24.484375 C 42.082031 23.078125 43.445312 21.734375 44.800781 20.390625 C 45.066406 20.125 45.332031 19.859375 45.597656 19.59375 C 46.242188 18.953125 46.886719 18.3125 47.53125 17.671875 C 44.5 14.628906 40.394531 12.792969 36.066406 12.757812 C 35.882812 12.757812 35.699219 12.757812 35.507812 12.757812 C 35.320312 12.757812 35.128906 12.757812 34.933594 12.757812 C 33.761719 12.765625 32.660156 12.859375 31.515625 13.109375 C 30.378906 13.351562 29.550781 13.25 28.519531 12.71875 C 27.421875 11.980469 26.53125 10.957031 25.617188 10.003906 C 25.300781 9.675781 24.984375 9.347656 24.664062 9.019531 C 24.378906 8.726562 24.09375 8.429688 23.808594 8.132812 C 23.257812 7.609375 23.257812 7.609375 22.664062 7.957031 Z M 80.191406 8.132812 C 80.007812 8.324219 79.824219 8.515625 79.636719 8.710938 C 79.433594 8.921875 79.226562 9.128906 79.023438 9.339844 C 78.703125 9.671875 78.382812 10 78.066406 10.332031 C 75.425781 13.082031 75.425781 13.082031 73.671875 13.28125 C 73.082031 13.21875 72.558594 13.125 71.984375 13 C 67.601562 12.171875 62.859375 12.988281 59.113281 15.425781 C 57.234375 16.710938 55.636719 18.183594 54.042969 19.796875 C 53.78125 20.054688 53.523438 20.3125 53.265625 20.570312 C 52.730469 21.105469 52.195312 21.644531 51.660156 22.183594 C 50.976562 22.871094 50.292969 23.554688 49.605469 24.242188 C 49.074219 24.769531 48.542969 25.304688 48.011719 25.835938 C 47.757812 26.089844 47.507812 26.34375 47.253906 26.59375 C 46.898438 26.949219 46.550781 27.300781 46.199219 27.65625 C 46.097656 27.757812 45.992188 27.859375 45.886719 27.96875 C 45.011719 28.859375 44.484375 29.605469 44.484375 30.875 C 44.707031 30.878906 44.929688 30.886719 45.160156 30.890625 C 48.054688 30.988281 50.453125 31.546875 52.570312 33.683594 C 52.867188 34.027344 53.148438 34.375 53.421875 34.734375 C 53.582031 34.929688 53.742188 35.128906 53.90625 35.332031 C 54.011719 35.46875 54.121094 35.609375 54.234375 35.75 C 54.34375 35.707031 54.453125 35.664062 54.566406 35.621094 C 55.746094 35.1875 56.789062 35.03125 58.042969 35.039062 C 58.21875 35.039062 58.398438 35.042969 58.578125 35.042969 C 60.933594 35.105469 62.902344 35.929688 64.59375 37.578125 C 65.660156 38.738281 66.382812 40.007812 67.03125 41.4375 C 67.191406 41.40625 67.351562 41.375 67.519531 41.339844 C 70.554688 40.898438 73.386719 41.074219 75.945312 42.910156 C 77.804688 44.5 79.011719 46.484375 79.320312 48.945312 C 79.375 49.78125 79.378906 50.617188 79.382812 51.453125 C 79.386719 51.746094 79.394531 52.035156 79.398438 52.328125 C 79.410156 53.03125 79.417969 53.734375 79.421875 54.4375 C 80.425781 53.59375 81.378906 52.71875 82.308594 51.792969 C 82.445312 51.660156 82.578125 51.527344 82.71875 51.390625 C 83 51.109375 83.28125 50.828125 83.5625 50.546875 C 83.988281 50.125 84.417969 49.703125 84.847656 49.277344 C 86.683594 47.457031 88.261719 45.65625 89.414062 43.316406 C 89.480469 43.179688 89.550781 43.042969 89.617188 42.902344 C 90.765625 40.503906 91.242188 38.042969 91.242188 35.394531 C 91.242188 35.125 91.242188 35.125 91.242188 34.851562 C 91.230469 33.707031 91.132812 32.640625 90.890625 31.519531 C 90.648438 30.382812 90.746094 29.558594 91.28125 28.523438 C 92.019531 27.414062 93.042969 26.515625 93.996094 25.59375 C 94.21875 25.375 94.4375 25.160156 94.660156 24.941406 C 95.199219 24.410156 95.738281 23.882812 96.28125 23.359375 C 96.011719 22.765625 95.722656 22.34375 95.265625 21.886719 C 95.140625 21.761719 95.015625 21.632812 94.886719 21.503906 C 94.75 21.367188 94.613281 21.234375 94.472656 21.09375 C 94.332031 20.949219 94.1875 20.804688 94.039062 20.65625 C 93.5625 20.175781 93.082031 19.699219 92.605469 19.222656 C 92.273438 18.894531 91.945312 18.5625 91.613281 18.230469 C 90.921875 17.535156 90.226562 16.84375 89.53125 16.148438 C 88.640625 15.261719 87.75 14.367188 86.859375 13.476562 C 86.175781 12.792969 85.492188 12.109375 84.808594 11.425781 C 84.480469 11.097656 84.152344 10.769531 83.824219 10.441406 C 83.367188 9.980469 82.90625 9.523438 82.449219 9.066406 C 82.3125 8.929688 82.175781 8.792969 82.035156 8.652344 C 81.910156 8.527344 81.785156 8.402344 81.65625 8.277344 C 81.546875 8.167969 81.4375 8.058594 81.328125 7.949219 C 80.8125 7.527344 80.660156 7.699219 80.191406 8.132812 Z M 43.671875 37.984375 C 42.964844 38.9375 42.835938 39.863281 42.832031 41.027344 C 42.832031 41.222656 42.828125 41.421875 42.828125 41.625 C 42.828125 41.949219 42.828125 41.949219 42.828125 42.277344 C 42.828125 42.507812 42.824219 42.734375 42.824219 42.96875 C 42.820312 43.464844 42.820312 43.960938 42.820312 44.457031 C 42.816406 45.238281 42.8125 46.023438 42.808594 46.804688 C 42.796875 49.035156 42.785156 51.261719 42.777344 53.492188 C 42.773438 54.851562 42.765625 56.214844 42.757812 57.578125 C 42.753906 58.097656 42.753906 58.617188 42.753906 59.136719 C 42.75 59.863281 42.746094 60.589844 42.742188 61.316406 C 42.742188 61.53125 42.742188 61.746094 42.742188 61.96875 C 42.722656 63.792969 42.722656 63.792969 42.046875 64.59375 C 41.300781 65.167969 40.746094 65.285156 39.8125 65.277344 C 39.605469 65.28125 39.402344 65.28125 39.191406 65.285156 C 38.496094 65.191406 38.132812 65.023438 37.578125 64.59375 C 37.035156 63.839844 36.890625 63.308594 36.878906 62.386719 C 36.875 62.160156 36.867188 61.933594 36.863281 61.699219 C 36.859375 61.464844 36.855469 61.230469 36.855469 60.988281 C 36.8125 57.703125 36.8125 57.703125 35.34375 54.84375 C 34.34375 53.894531 33.40625 53.386719 32.015625 53.382812 C 31.703125 53.394531 31.390625 53.40625 31.078125 53.421875 C 30.542969 54.214844 30.613281 55.066406 30.621094 55.984375 C 30.617188 56.160156 30.617188 56.335938 30.617188 56.519531 C 30.617188 57.105469 30.617188 57.6875 30.617188 58.273438 C 30.617188 58.683594 30.617188 59.089844 30.617188 59.5 C 30.617188 60.359375 30.617188 61.21875 30.617188 62.078125 C 30.621094 63.171875 30.621094 64.261719 30.617188 65.355469 C 30.617188 66.199219 30.617188 67.046875 30.617188 67.894531 C 30.617188 68.296875 30.617188 68.699219 30.617188 69.097656 C 30.574219 73.851562 30.574219 73.851562 31.609375 78.457031 C 31.664062 78.605469 31.71875 78.757812 31.773438 78.910156 C 33.1875 82.75 35.90625 86.277344 39.546875 88.25 C 40.015625 88.5625 40.015625 88.5625 40.625 89.578125 C 40.691406 92.328125 40.757812 95.074219 40.828125 97.90625 C 48.203125 97.90625 55.574219 97.90625 63.171875 97.90625 C 63.238281 95.15625 63.304688 92.410156 63.375 89.578125 C 63.976562 88.574219 64.285156 88.296875 65.226562 87.6875 C 69.164062 85.050781 71.871094 81.230469 72.921875 76.578125 C 73.398438 73.609375 73.386719 70.636719 73.382812 67.636719 C 73.382812 67.136719 73.382812 66.636719 73.382812 66.132812 C 73.382812 65.089844 73.382812 64.046875 73.382812 63.003906 C 73.378906 61.667969 73.378906 60.332031 73.382812 58.996094 C 73.382812 57.964844 73.382812 56.933594 73.382812 55.902344 C 73.382812 55.410156 73.382812 54.917969 73.382812 54.421875 C 73.382812 53.738281 73.382812 53.050781 73.378906 52.363281 C 73.382812 52.0625 73.382812 52.0625 73.382812 51.75 C 73.375 50.394531 73.351562 49.246094 72.425781 48.179688 C 71.488281 47.378906 70.972656 47.238281 69.773438 47.265625 C 69.023438 47.359375 68.605469 47.636719 68.046875 48.140625 C 67.261719 49.195312 67.210938 50.21875 67.203125 51.5 C 67.199219 51.765625 67.199219 52.03125 67.195312 52.304688 C 67.195312 52.445312 67.195312 52.585938 67.195312 52.730469 C 67.191406 53.171875 67.1875 53.617188 67.183594 54.058594 C 67.171875 55.316406 67.160156 56.574219 67.152344 57.828125 C 67.148438 58.601562 67.140625 59.371094 67.132812 60.140625 C 67.128906 60.433594 67.128906 60.726562 67.128906 61.019531 C 67.125 61.429688 67.121094 61.839844 67.117188 62.25 C 67.113281 62.597656 67.113281 62.597656 67.109375 62.953125 C 67.023438 63.652344 66.835938 64.03125 66.421875 64.59375 C 65.675781 65.167969 65.121094 65.285156 64.1875 65.277344 C 63.980469 65.28125 63.777344 65.28125 63.566406 65.285156 C 62.6875 65.164062 62.175781 64.871094 61.625 64.179688 C 61.234375 63.34375 61.257812 62.609375 61.261719 61.695312 C 61.261719 61.507812 61.257812 61.320312 61.257812 61.125 C 61.253906 60.503906 61.253906 59.882812 61.253906 59.261719 C 61.25 58.832031 61.25 58.398438 61.246094 57.964844 C 61.238281 56.828125 61.234375 55.691406 61.230469 54.550781 C 61.226562 52.730469 61.21875 50.910156 61.207031 49.089844 C 61.203125 48.457031 61.203125 47.820312 61.203125 47.1875 C 61.199219 46.796875 61.199219 46.40625 61.195312 46.011719 C 61.195312 45.835938 61.195312 45.664062 61.199219 45.480469 C 61.1875 44.1875 61.117188 43.097656 60.238281 42.085938 C 59.300781 41.285156 58.785156 41.144531 57.585938 41.171875 C 56.835938 41.265625 56.417969 41.542969 55.859375 42.046875 C 54.960938 43.253906 55.015625 44.523438 55.015625 45.96875 C 55.015625 46.15625 55.011719 46.34375 55.011719 46.539062 C 55.007812 46.949219 55.007812 47.355469 55.007812 47.765625 C 55.003906 48.414062 55 49.058594 54.996094 49.707031 C 54.984375 51.546875 54.972656 53.386719 54.964844 55.226562 C 54.960938 56.351562 54.953125 57.476562 54.945312 58.605469 C 54.941406 59.03125 54.941406 59.460938 54.941406 59.890625 C 54.9375 60.488281 54.933594 61.089844 54.929688 61.6875 C 54.929688 61.953125 54.929688 61.953125 54.929688 62.226562 C 54.917969 63.183594 54.855469 63.816406 54.234375 64.59375 C 53.488281 65.167969 52.933594 65.285156 52 65.277344 C 51.792969 65.28125 51.589844 65.28125 51.378906 65.285156 C 50.511719 65.167969 50 64.871094 49.4375 64.203125 C 49.011719 63.253906 49.070312 62.351562 49.074219 61.324219 C 49.074219 60.980469 49.074219 60.980469 49.070312 60.632812 C 49.066406 59.878906 49.066406 59.128906 49.066406 58.375 C 49.0625 57.851562 49.0625 57.328125 49.058594 56.804688 C 49.050781 55.285156 49.046875 53.765625 49.042969 52.246094 C 49.039062 50.179688 49.03125 48.117188 49.019531 46.054688 C 49.015625 45.28125 49.015625 44.511719 49.015625 43.742188 C 49.011719 43.269531 49.011719 42.796875 49.007812 42.324219 C 49.007812 42.109375 49.007812 41.898438 49.011719 41.675781 C 49 39.703125 49 39.703125 48.050781 38.023438 C 47.113281 37.222656 46.597656 37.082031 45.398438 37.109375 C 44.648438 37.203125 44.230469 37.480469 43.671875 37.984375 Z M 43.671875 37.984375 "/>
                                        </g>
                                    </svg>
                                </a>
                            </div>
                            <h3>Participación y cultura accesible</h3>
                            <p></p>
                        </div>
                        <div class="col-sm-6 col-md-4 our-spec">
                            <div class="text-center" tabindex="0">
                                <a href="areas/igualdadpm.php" aria-label="Ir al área de igualdad y promoción de la mujer con discapacidad">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="104px" height="104px" viewBox="0 0 104 104" version="1.1">
                                        <g id="surface1">
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 83.199219 17.222656 C 83.53125 17.527344 83.863281 17.824219 84.199219 18.121094 C 92.878906 25.730469 97.78125 37.390625 98.679688 48.730469 C 99.066406 54.902344 98.367188 61.195312 96.28125 67.03125 C 96.226562 67.1875 96.171875 67.34375 96.117188 67.507812 C 94.691406 71.609375 92.75 75.5 90.1875 79.015625 C 90.105469 79.128906 90.023438 79.242188 89.9375 79.359375 C 88.859375 80.835938 87.6875 82.207031 86.4375 83.542969 C 86 84.007812 85.585938 84.484375 85.171875 84.96875 C 84.46875 85.777344 83.691406 86.453125 82.875 87.140625 C 82.691406 87.304688 82.503906 87.46875 82.3125 87.640625 C 75.324219 93.8125 66.273438 97.433594 57.078125 98.515625 C 56.859375 98.542969 56.636719 98.570312 56.410156 98.597656 C 44.421875 99.765625 32.027344 96.074219 22.714844 88.445312 C 20.574219 86.667969 18.410156 84.839844 16.65625 82.671875 C 16.472656 82.449219 16.285156 82.226562 16.101562 82.003906 C 8.261719 72.464844 4.34375 60.351562 5.382812 48.007812 C 6.382812 37.898438 10.300781 28.304688 17.222656 20.800781 C 17.527344 20.46875 17.824219 20.136719 18.121094 19.800781 C 25.613281 11.257812 37.03125 6.417969 48.179688 5.34375 C 60.980469 4.5625 73.695312 8.453125 83.199219 17.222656 Z M 22.972656 24.371094 C 22.621094 24.773438 22.277344 25.183594 21.9375 25.59375 C 21.785156 25.769531 21.628906 25.945312 21.472656 26.128906 C 20.128906 27.691406 18.957031 29.328125 17.875 31.078125 C 17.792969 31.214844 17.707031 31.347656 17.621094 31.488281 C 12.117188 40.542969 10.652344 51.492188 13.113281 61.75 C 14.78125 68.238281 17.980469 73.800781 22.34375 78.8125 C 22.542969 79.046875 22.542969 79.046875 22.742188 79.289062 C 23.554688 80.246094 24.433594 81.054688 25.390625 81.859375 C 25.582031 82.027344 25.777344 82.199219 25.972656 82.375 C 27.582031 83.78125 29.257812 85.003906 31.078125 86.125 C 31.214844 86.207031 31.347656 86.292969 31.488281 86.378906 C 40.542969 91.882812 51.492188 93.347656 61.75 90.886719 C 68.238281 89.21875 73.800781 86.019531 78.8125 81.65625 C 78.96875 81.523438 79.125 81.390625 79.289062 81.257812 C 80.246094 80.445312 81.054688 79.566406 81.859375 78.609375 C 82.027344 78.417969 82.199219 78.222656 82.375 78.027344 C 83.78125 76.417969 85.003906 74.742188 86.125 72.921875 C 86.207031 72.785156 86.292969 72.652344 86.378906 72.511719 C 91.882812 63.457031 93.347656 52.507812 90.886719 42.25 C 89.21875 35.765625 86.027344 30.1875 81.65625 25.1875 C 81.507812 25.015625 81.363281 24.84375 81.210938 24.667969 C 80.34375 23.660156 79.425781 22.785156 78.40625 21.9375 C 78.140625 21.707031 78.140625 21.707031 77.867188 21.46875 C 76.308594 20.128906 74.667969 18.964844 72.921875 17.875 C 72.796875 17.796875 72.675781 17.71875 72.546875 17.640625 C 67.644531 14.621094 62.152344 12.90625 56.46875 12.1875 C 56.277344 12.160156 56.085938 12.132812 55.890625 12.105469 C 43.835938 10.691406 31.285156 15.648438 22.972656 24.371094 Z M 22.972656 24.371094 "/>
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 35.355469 57.25 C 35.578125 57.25 35.578125 57.25 35.800781 57.25 C 36.296875 57.246094 36.789062 57.246094 37.285156 57.246094 C 37.636719 57.242188 37.992188 57.242188 38.34375 57.242188 C 39.507812 57.238281 40.671875 57.234375 41.832031 57.234375 C 42.234375 57.230469 42.632812 57.230469 43.035156 57.230469 C 44.914062 57.226562 46.796875 57.222656 48.675781 57.222656 C 50.847656 57.21875 53.019531 57.214844 55.1875 57.207031 C 56.867188 57.199219 58.542969 57.195312 60.222656 57.195312 C 61.226562 57.195312 62.226562 57.195312 63.230469 57.1875 C 64.171875 57.183594 65.113281 57.183594 66.058594 57.1875 C 66.402344 57.1875 66.75 57.183594 67.09375 57.183594 C 70.371094 57.15625 70.371094 57.15625 71.703125 58.09375 C 72.550781 59.15625 72.828125 59.988281 72.71875 61.34375 C 72.351562 62.414062 71.859375 63.160156 70.890625 63.78125 C 70.167969 64.023438 69.589844 64.011719 68.828125 64.011719 C 68.679688 64.011719 68.53125 64.015625 68.375 64.015625 C 67.875 64.015625 67.375 64.015625 66.875 64.015625 C 66.515625 64.019531 66.160156 64.019531 65.800781 64.019531 C 64.824219 64.023438 63.851562 64.023438 62.875 64.023438 C 62.269531 64.027344 61.660156 64.027344 61.050781 64.027344 C 59.144531 64.03125 57.242188 64.03125 55.339844 64.03125 C 53.140625 64.035156 50.941406 64.039062 48.742188 64.042969 C 47.042969 64.046875 45.34375 64.050781 43.644531 64.050781 C 42.628906 64.050781 41.613281 64.050781 40.601562 64.054688 C 39.644531 64.058594 38.691406 64.058594 37.734375 64.058594 C 37.386719 64.058594 37.035156 64.058594 36.683594 64.058594 C 36.207031 64.0625 35.726562 64.0625 35.25 64.058594 C 34.980469 64.058594 34.714844 64.058594 34.4375 64.058594 C 33.414062 63.953125 32.75 63.636719 32.042969 62.894531 C 31.375 61.894531 31.15625 61.132812 31.28125 59.921875 C 31.671875 58.742188 32.195312 58.078125 33.3125 57.484375 C 34.03125 57.246094 34.597656 57.253906 35.355469 57.25 Z M 35.355469 57.25 "/>
                                            <path style=" stroke:none;fill-rule:nonzero;fill:rgb(0%,0%,0%);fill-opacity:1;" d="M 35.171875 39.988281 C 35.320312 39.988281 35.46875 39.984375 35.625 39.984375 C 36.125 39.984375 36.625 39.984375 37.125 39.984375 C 37.484375 39.980469 37.839844 39.980469 38.199219 39.980469 C 39.175781 39.976562 40.148438 39.976562 41.125 39.976562 C 41.730469 39.972656 42.339844 39.972656 42.949219 39.972656 C 44.855469 39.96875 46.757812 39.96875 48.660156 39.96875 C 50.859375 39.964844 53.058594 39.960938 55.257812 39.957031 C 56.957031 39.953125 58.65625 39.949219 60.355469 39.949219 C 61.371094 39.949219 62.386719 39.949219 63.398438 39.945312 C 64.355469 39.941406 65.308594 39.941406 66.265625 39.941406 C 66.613281 39.941406 66.964844 39.941406 67.316406 39.941406 C 67.792969 39.9375 68.273438 39.9375 68.75 39.941406 C 69.019531 39.941406 69.285156 39.941406 69.5625 39.941406 C 70.585938 40.046875 71.25 40.363281 71.957031 41.105469 C 72.625 42.105469 72.84375 42.867188 72.71875 44.078125 C 72.328125 45.257812 71.804688 45.921875 70.6875 46.515625 C 69.96875 46.753906 69.402344 46.746094 68.644531 46.75 C 68.496094 46.75 68.347656 46.75 68.199219 46.75 C 67.703125 46.753906 67.210938 46.753906 66.714844 46.753906 C 66.363281 46.757812 66.007812 46.757812 65.65625 46.757812 C 64.492188 46.761719 63.328125 46.765625 62.167969 46.765625 C 61.765625 46.769531 61.367188 46.769531 60.964844 46.769531 C 59.085938 46.773438 57.203125 46.777344 55.324219 46.777344 C 53.152344 46.78125 50.980469 46.785156 48.8125 46.792969 C 47.132812 46.800781 45.457031 46.804688 43.777344 46.804688 C 42.773438 46.804688 41.773438 46.804688 40.769531 46.8125 C 39.828125 46.816406 38.886719 46.816406 37.941406 46.8125 C 37.597656 46.8125 37.25 46.816406 36.90625 46.816406 C 33.628906 46.84375 33.628906 46.84375 32.296875 45.90625 C 31.449219 44.84375 31.171875 44.011719 31.28125 42.65625 C 31.648438 41.585938 32.140625 40.839844 33.109375 40.21875 C 33.832031 39.976562 34.410156 39.988281 35.171875 39.988281 Z M 35.171875 39.988281 "/>
                                        </g>
                                    </svg>
                                </a>
                            </div>
                            <h3>Igualdad y promoción de la mujer con discapacidad</h3>
                            <p></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Features section end-->

        <!-- Portfolio Section-->
        <section id="portfolios" class="section">
            <!-- Container Starts -->
            <div class="main-container">
                <div class="inside-container">
                    <div class="col-12 our-header">
                        <h2 style="padding-bottom: 10px">Proyectos destacados</h2>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Portfolio Controller/Buttons -->
                            <div class="controls text-center">
                                <a class="active btn btn-common" data-filter="all" tabindex="0"> Todos </a>
                                <a class="btn btn-common" data-filter=".empleo" tabindex="0">Empleo</a>
                                <a class="btn btn-common" data-filter=".igualdad" tabindex="0">Igualdad</a>
                                <a class="btn btn-common" data-filter=".forma-innova" tabindex="0">Formación e innovación </a>
                                <a class="btn btn-common" data-filter=".integral" tabindex="0">Atención integral </a>
                                <a class="btn btn-common" data-filter=".ocio" tabindex="0">Ocio</a>
                                <a class="btn btn-common" data-filter=".part-cultura-accesible" tabindex="0">Participación y cultura accesible</a>
                            </div>
                            <!-- Portfolio Controller/Buttons Ends-->
                        </div>
                    </div>
                    <!-- Portfolio Recent Projects -->
                    <div id="portfolio" class="row">
                        <?php if (!empty($proyectos)): ?>
                            <?php foreach ($proyectos as $proyecto): ?>
                                <div class="col-lg-4 col-md-6 col-xs-12 mix <?= e(str_replace(',', ' ', $proyecto['categorias'] ?? '')) ?>">
                                    <a href="areas/<?= e($proyecto['area_slug']) ?>.php#portfolios-<?= e($portfolio_ids[$proyecto['area_slug']] ?? '') ?>" class="portfolio-link">
                                        <div class="portfolio-item" tabindex="0">
                                            <div class="shot-item">
                                                <img src="<?= e($proyecto['imagen']) ?>" alt="<?= attr($proyecto['titulo']) ?>" />
                                                <div class="single-content">
                                                    <div class="fancy-table">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
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
        <!-- Portfolio Section end -->


        <!-- Team Section -->
        <!-- <section id="team" class="team section">
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
        </section> -->
        <!-- Team Section end -->


        <!--Testimonial container-->
        <!--<section id="testimonials" class="section">
            <div class="main-container">
                <div class="inside-container">
                    <div class="row">
                        <div class="col-12 our-header">
                            <h2>Testimonials</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="item">
                                <div class="all-testimonial">
                                    <div class="text-center"><img class="img-box" src="images/testimonials/customer-1.jpg" alt="client image1"></div>
                                    <div class="testimonial">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                            <path d="M 10 8 C 6.699219 8 4 10.699219 4 14 L 4 24 L 14 24 L 14 14 L 6 14 C 6 11.78125 7.78125 10 10 10 Z M 24 8 C 20.699219 8 18 10.699219 18 14 L 18 24 L 28 24 L 28 14 L 20 14 C 20 11.78125 21.78125 10 24 10 Z M 6 16 L 12 16 L 12 22 L 6 22 Z M 20 16 L 26 16 L 26 22 L 20 22 Z" />
                                        </svg>
                                        <p>It is such a great template.<br>At last a Bootstrap template that is perfectly optimized, accessible and easy to use.</p>
                                        <div class="stars-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512.001 512.001">
                                                <path d="M499.92 188.26l-165.84-15.38L268.205 19.9c-4.612-10.71-19.8-10.71-24.41 0L177.92 172.88 12.08 188.26c-11.612 1.077-16.305 15.52-7.544 23.216l125.126 109.922-36.618 162.476c-2.564 11.376 9.722 20.302 19.75 14.348L256 413.188l143.207 85.034c10.027 5.954 22.314-2.972 19.75-14.348l-36.62-162.476 125.126-109.922c8.76-7.696 4.068-22.14-7.544-23.216z" fill="#ffdc64" />
                                                <path d="M268.205 19.9c-4.612-10.71-19.8-10.71-24.41 0L177.92 172.88 12.08 188.26c-11.612 1.077-16.305 15.52-7.544 23.216l125.126 109.922-36.618 162.476c-2.564 11.376 9.722 20.302 19.75 14.348l31.963-18.98c4.424-182.1 89.034-310.338 156.022-383.697L268.205 19.9z" fill="#ffc850" /></svg>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512.001 512.001">
                                                <path d="M499.92 188.26l-165.84-15.38L268.205 19.9c-4.612-10.71-19.8-10.71-24.41 0L177.92 172.88 12.08 188.26c-11.612 1.077-16.305 15.52-7.544 23.216l125.126 109.922-36.618 162.476c-2.564 11.376 9.722 20.302 19.75 14.348L256 413.188l143.207 85.034c10.027 5.954 22.314-2.972 19.75-14.348l-36.62-162.476 125.126-109.922c8.76-7.696 4.068-22.14-7.544-23.216z" fill="#ffdc64" />
                                                <path d="M268.205 19.9c-4.612-10.71-19.8-10.71-24.41 0L177.92 172.88 12.08 188.26c-11.612 1.077-16.305 15.52-7.544 23.216l125.126 109.922-36.618 162.476c-2.564 11.376 9.722 20.302 19.75 14.348l31.963-18.98c4.424-182.1 89.034-310.338 156.022-383.697L268.205 19.9z" fill="#ffc850" /></svg>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512.001 512.001">
                                                <path d="M499.92 188.26l-165.84-15.38L268.205 19.9c-4.612-10.71-19.8-10.71-24.41 0L177.92 172.88 12.08 188.26c-11.612 1.077-16.305 15.52-7.544 23.216l125.126 109.922-36.618 162.476c-2.564 11.376 9.722 20.302 19.75 14.348L256 413.188l143.207 85.034c10.027 5.954 22.314-2.972 19.75-14.348l-36.62-162.476 125.126-109.922c8.76-7.696 4.068-22.14-7.544-23.216z" fill="#ffdc64" />
                                                <path d="M268.205 19.9c-4.612-10.71-19.8-10.71-24.41 0L177.92 172.88 12.08 188.26c-11.612 1.077-16.305 15.52-7.544 23.216l125.126 109.922-36.618 162.476c-2.564 11.376 9.722 20.302 19.75 14.348l31.963-18.98c4.424-182.1 89.034-310.338 156.022-383.697L268.205 19.9z" fill="#ffc850" /></svg>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512.001 512.001">
                                                <path d="M499.92 188.26l-165.84-15.38L268.205 19.9c-4.612-10.71-19.8-10.71-24.41 0L177.92 172.88 12.08 188.26c-11.612 1.077-16.305 15.52-7.544 23.216l125.126 109.922-36.618 162.476c-2.564 11.376 9.722 20.302 19.75 14.348L256 413.188l143.207 85.034c10.027 5.954 22.314-2.972 19.75-14.348l-36.62-162.476 125.126-109.922c8.76-7.696 4.068-22.14-7.544-23.216z" fill="#ffdc64" />
                                                <path d="M268.205 19.9c-4.612-10.71-19.8-10.71-24.41 0L177.92 172.88 12.08 188.26c-11.612 1.077-16.305 15.52-7.544 23.216l125.126 109.922-36.618 162.476c-2.564 11.376 9.722 20.302 19.75 14.348l31.963-18.98c4.424-182.1 89.034-310.338 156.022-383.697L268.205 19.9z" fill="#ffc850" /></svg>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512.001 512.001">
                                                <path d="M499.92 188.26l-165.84-15.38L268.205 19.9c-4.612-10.71-19.8-10.71-24.41 0L177.92 172.88 12.08 188.26c-11.612 1.077-16.305 15.52-7.544 23.216l125.126 109.922-36.618 162.476c-2.564 11.376 9.722 20.302 19.75 14.348L256 413.188l143.207 85.034c10.027 5.954 22.314-2.972 19.75-14.348l-36.62-162.476 125.126-109.922c8.76-7.696 4.068-22.14-7.544-23.216z" fill="#ffdc64" />
                                                <path d="M268.205 19.9c-4.612-10.71-19.8-10.71-24.41 0L177.92 172.88 12.08 188.26c-11.612 1.077-16.305 15.52-7.544 23.216l125.126 109.922-36.618 162.476c-2.564 11.376 9.722 20.302 19.75 14.348l31.963-18.98c4.424-182.1 89.034-310.338 156.022-383.697L268.205 19.9z" fill="#ffc850" /></svg>
                                        </div>
                                    </div>
                                    <div class="author">
                                        <span>Matthew. N</span>
                                        <p>Web Designer</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="item">
                                <div class="all-testimonial">
                                    <div class="text-center"><img class="img-box" src="images/testimonials/customer-2.jpg" alt="client image2"></div>
                                    <div class="testimonial">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                            <path d="M 10 8 C 6.699219 8 4 10.699219 4 14 L 4 24 L 14 24 L 14 14 L 6 14 C 6 11.78125 7.78125 10 10 10 Z M 24 8 C 20.699219 8 18 10.699219 18 14 L 18 24 L 28 24 L 28 14 L 20 14 C 20 11.78125 21.78125 10 24 10 Z M 6 16 L 12 16 L 12 22 L 6 22 Z M 20 16 L 26 16 L 26 22 L 20 22 Z" />
                                        </svg>
                                        <p>This is exactly what I was looking for! Responsive, multipurpose, and fully accessible.<br>Thank you for creating such an amazing template!</p>
                                        <div class="stars-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512.001 512.001">
                                                <path d="M499.92 188.26l-165.84-15.38L268.205 19.9c-4.612-10.71-19.8-10.71-24.41 0L177.92 172.88 12.08 188.26c-11.612 1.077-16.305 15.52-7.544 23.216l125.126 109.922-36.618 162.476c-2.564 11.376 9.722 20.302 19.75 14.348L256 413.188l143.207 85.034c10.027 5.954 22.314-2.972 19.75-14.348l-36.62-162.476 125.126-109.922c8.76-7.696 4.068-22.14-7.544-23.216z" fill="#ffdc64" />
                                                <path d="M268.205 19.9c-4.612-10.71-19.8-10.71-24.41 0L177.92 172.88 12.08 188.26c-11.612 1.077-16.305 15.52-7.544 23.216l125.126 109.922-36.618 162.476c-2.564 11.376 9.722 20.302 19.75 14.348l31.963-18.98c4.424-182.1 89.034-310.338 156.022-383.697L268.205 19.9z" fill="#ffc850" /></svg>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512.001 512.001">
                                                <path d="M499.92 188.26l-165.84-15.38L268.205 19.9c-4.612-10.71-19.8-10.71-24.41 0L177.92 172.88 12.08 188.26c-11.612 1.077-16.305 15.52-7.544 23.216l125.126 109.922-36.618 162.476c-2.564 11.376 9.722 20.302 19.75 14.348L256 413.188l143.207 85.034c10.027 5.954 22.314-2.972 19.75-14.348l-36.62-162.476 125.126-109.922c8.76-7.696 4.068-22.14-7.544-23.216z" fill="#ffdc64" />
                                                <path d="M268.205 19.9c-4.612-10.71-19.8-10.71-24.41 0L177.92 172.88 12.08 188.26c-11.612 1.077-16.305 15.52-7.544 23.216l125.126 109.922-36.618 162.476c-2.564 11.376 9.722 20.302 19.75 14.348l31.963-18.98c4.424-182.1 89.034-310.338 156.022-383.697L268.205 19.9z" fill="#ffc850" /></svg>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512.001 512.001">
                                                <path d="M499.92 188.26l-165.84-15.38L268.205 19.9c-4.612-10.71-19.8-10.71-24.41 0L177.92 172.88 12.08 188.26c-11.612 1.077-16.305 15.52-7.544 23.216l125.126 109.922-36.618 162.476c-2.564 11.376 9.722 20.302 19.75 14.348L256 413.188l143.207 85.034c10.027 5.954 22.314-2.972 19.75-14.348l-36.62-162.476 125.126-109.922c8.76-7.696 4.068-22.14-7.544-23.216z" fill="#ffdc64" />
                                                <path d="M268.205 19.9c-4.612-10.71-19.8-10.71-24.41 0L177.92 172.88 12.08 188.26c-11.612 1.077-16.305 15.52-7.544 23.216l125.126 109.922-36.618 162.476c-2.564 11.376 9.722 20.302 19.75 14.348l31.963-18.98c4.424-182.1 89.034-310.338 156.022-383.697L268.205 19.9z" fill="#ffc850" /></svg>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512.001 512.001">
                                                <path d="M499.92 188.26l-165.84-15.38L268.205 19.9c-4.612-10.71-19.8-10.71-24.41 0L177.92 172.88 12.08 188.26c-11.612 1.077-16.305 15.52-7.544 23.216l125.126 109.922-36.618 162.476c-2.564 11.376 9.722 20.302 19.75 14.348L256 413.188l143.207 85.034c10.027 5.954 22.314-2.972 19.75-14.348l-36.62-162.476 125.126-109.922c8.76-7.696 4.068-22.14-7.544-23.216z" fill="#ffdc64" />
                                                <path d="M268.205 19.9c-4.612-10.71-19.8-10.71-24.41 0L177.92 172.88 12.08 188.26c-11.612 1.077-16.305 15.52-7.544 23.216l125.126 109.922-36.618 162.476c-2.564 11.376 9.722 20.302 19.75 14.348l31.963-18.98c4.424-182.1 89.034-310.338 156.022-383.697L268.205 19.9z" fill="#ffc850" /></svg>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512.001 512.001">
                                                <path d="M499.92 188.26l-165.84-15.38L268.205 19.9c-4.612-10.71-19.8-10.71-24.41 0L177.92 172.88 12.08 188.26c-11.612 1.077-16.305 15.52-7.544 23.216l125.126 109.922-36.618 162.476c-2.564 11.376 9.722 20.302 19.75 14.348L256 413.188l143.207 85.034c10.027 5.954 22.314-2.972 19.75-14.348l-36.62-162.476 125.126-109.922c8.76-7.696 4.068-22.14-7.544-23.216z" fill="#ffdc64" />
                                                <path d="M268.205 19.9c-4.612-10.71-19.8-10.71-24.41 0L177.92 172.88 12.08 188.26c-11.612 1.077-16.305 15.52-7.544 23.216l125.126 109.922-36.618 162.476c-2.564 11.376 9.722 20.302 19.75 14.348l31.963-18.98c4.424-182.1 89.034-310.338 156.022-383.697L268.205 19.9z" fill="#ffc850" /></svg>
                                        </div>
                                    </div>
                                    <div class="author">
                                        <span>May. Y</span>
                                        <p>Web Developer</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>-->
        <!--Testimonial container end-->


        <!--News section-->
        <?php if (count($noticias_destacadas) > 0): ?>
        <section id="news" class="section">
            <div class="main-container section-bg">
                <div class="inside-container">
                    <div class="row">
                        <div class="col-12 our-header">
                            <h2>Novedades</h2>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                        // Mapeo de slugs de áreas a sufijos de IDs de secciones de noticias
                        $news_id_map = [
                            'forminno' => 'fi',
                            'empleo' => 'em',
                            'aintegral' => 'ai',
                            'igualdadpm' => 'ig',
                            'ocio' => 'oc',
                            'participaca' => 'pa'
                        ];

                        foreach ($noticias_destacadas as $noticia):
                        ?>
                        <div class="col-lg-4 col-md-6 col-12">
                            <div class="item">
                                <?php if (!empty($noticia['imagen_destacada'])): ?>
                                <div class="lab-bs-item-image" style="margin-bottom: 15px;">
                                    <img src="<?= e($noticia['imagen_destacada']) ?>"
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
                                            <?php if (!empty($noticia['area_slug'])): ?>
                                            <?php $news_suffix = $news_id_map[$noticia['area_slug']] ?? substr($noticia['area_slug'], 0, 2); ?>
                                            <a href="areas/<?= e($noticia['area_slug']) ?>.php#news-<?= e($news_suffix) ?>"
                                               class="btn btn-sm btn-primary"
                                               style="margin-top: 10px;">
                                                Leer más <i class="fas fa-arrow-right"></i>
                                            </a>
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

        <!--Colabora section-->
        <section id="colabora" class="section">
            <div class="main-container">
                <div class="inside-container">
                    <div class="row">
                        <div class="col-12 our-header">
                            <h2 class="content-title" style="padding-bottom: 1px">Tu contribución, nuestro impulso</h2>
                            <p class="content-desc" style="text-align: center">
                                Para nosotros, cada gesto de apoyo se convierte en un poderoso motor de cambio.
                                Como organización sin ánimo de lucro, dependemos en gran medida de la generosidad
                                y el compromiso de personas como tú para continuar nuestra labor.
                                Tu colaboración nos permite ampliar horizontes, romper barreras y crear un futuro más inclusivo y
                                justo para las personas con discapacidad en Canarias.
                            </p>
                        </div>
                    </div>

                    <!-- Pricing table -->
                    <ul class="pricing-table pricing-col-4">
                        <li>
                            <div class="pricing-container">
                                <h3>Donaciones</h3>
                                <div class="price">
                                    <div class="price-figure">
                                        <span class="price-number">Dona</span>
                                    </div>
                                </div>
                                <ul class="features">
                                    <li>Cada euro donado mantiene y amplía nuestros programas,
                                        servicios y proyectos que transforman vidas.
                                        Cada euro donado es una inversión en esperanza y posibilidad.
                                        Tus donaciones nos ayudan a mantener y ampliar nuestros programas,
                                        servicios y proyectos que influyen directamente
                                        en la vida de muchas personas y sus familias.
                                    </li>
                                </ul>
                                <div class="footer">
                                    <button type="button"
                                       class="btn-inverse btn-white btn-block"
                                       data-bs-toggle="modal"
                                       data-bs-target="#donacionModal"
                                       aria-label="Realizar una donación a CoordiCanarias">
                                        Dona ahora
                                    </button>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="pricing-container">
                                <h3>Colaboraciones</h3>
                                <div class="price">
                                    <div class="price-figure">
                                        <span class="price-number">Colabora</span>
                                    </div>
                                </div>
                                <ul class="features">
                                    <li>Tu empresa puede amplificar nuestro impacto.
                                        Juntos desarrollamos proyectos que generan
                                        cambios reales en nuestra comunidad.
                                        Si eres una empresa o entidad,
                                        tu colaboración puede amplificar
                                        nuestro alcance y eficacia.
                                        Juntos, podemos desarrollar proyectos
                                        que generen un impacto positivo
                                        sustancial en nuestra comunidad.
                                    </li>
                                </ul>
                                <div class="footer">
                                    <a href="#contact"
                                       class="btn-inverse btn-white btn-block btn-contacto-colaboraciones"
                                       data-tipo="colaboraciones"
                                       aria-label="Información sobre colaboraciones empresariales">
                                        Colabora ahora
                                    </a>
                                </div>
                            </div>
                        </li>

                        <li class="highlight">
                            <div class="pricing-container">
                                <h3>Socios</h3>
                                <div class="price">
                                    <div class="price-figure">
                                        <span class="price-number">5 €</span>
                                        <span class="price-tenure">mensuales</span>
                                    </div>
                                </div>
                                <ul class="features">
                                    <li>Únete a una comunidad comprometida con la inclusión.
                                        Tu aportación regular nos da la estabilidad para
                                        planificar iniciativas a largo plazo.
                                        Al convertirte en socio de CoordiCanarias,
                                        te unes a una comunidad comprometida con
                                        la inclusión y el cambio social. Tu aportación
                                        regular nos brinda una base sólida para
                                        planificar y desarrollar iniciativas a largo plazo.
                                    </li>
                                </ul>
                                <div class="footer">
                                    <button type="button"
                                       class="btn-inverse btn-white btn-block"
                                       data-bs-toggle="modal"
                                       data-bs-target="#socioModal"
                                       aria-label="Hacerse socio de CoordiCanarias por 5 euros al mes">
                                        Asóciate ahora
                                    </button>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="pricing-container">
                                <h3>Voluntariado</h3>
                                <div class="price">
                                    <div class="price-figure">
                                        <span class="price-number">Voluntari@</span>
                                    </div>
                                </div>
                                <ul class="features">
                                    <li>Tu tiempo y habilidades marcan la diferencia.
                                        Involúcrate directamente en nuestras actividades
                                        y proyectos.
                                        Tu tiempo y habilidades pueden marcar
                                        una gran diferencia. Como voluntari@,
                                        tienes la oportunidad de involucrarte
                                        directamente en nuestras actividades y
                                        proyectos, aportando un valor inestimable
                                        a nuestra labor.
                                    </li>
                                </ul>
                                <div class="footer">
                                    <a href="#contact"
                                       class="btn-inverse btn-white btn-block btn-contacto-voluntariado"
                                       data-tipo="voluntariado"
                                       aria-label="Información sobre programa de voluntariado">
                                        Más información
                                    </a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
        <!--Colabora section end-->


        <!--Contact section-->
        <section id="contact" class="section">
            <div class="main-container section-bg">
                <div class="inside-container">
                    <div class="row no-gutters">
                        <div class="col-md-6">
                            <div class="contact-con">
                                <h2>Estamos aquí para ayudarte</h2>
                                <p>Puedes visitarnos en nuestra sede o enviarnos un mensaje mediante el formulario.<br><br></p>
                                <p>
                                    <strong>Dirección:</strong> <?= e($config['contacto_direccion'] ?? 'C/ Zurbarán, 7, Los Andenes, La Laguna 38108, Santa Cruz de Tenerife') ?><br />
                                    <strong>Teléfono:</strong> <?= e($config['contacto_telefono'] ?? '922 21 59 09') ?><br />
                                    <strong>Email:</strong> <a href="mailto:<?= attr($config['contacto_email'] ?? 'info@coordicanarias.com') ?>" class="text-theme"><?= e($config['contacto_email'] ?? 'info@coordicanarias.com') ?></a><br />
                                    <strong>Horario:</strong> <?= e($config['contacto_horario'] ?? 'Lunes a viernes de 8:00 a 15:00') ?>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h3>Formulario de contacto</h3>
                            <br>

                            <!-- Mensajes de éxito/error -->
                            <div id="contactMessage" style="display:none; padding: 15px; margin-bottom: 20px; border-radius: 5px;"></div>

                            <div class="contact-form">
                                <form action="<?= url('php/enviar_correo.php') ?>" method="POST" id="contactForm">
                                    <!-- Campo oculto para identificar el área -->
                                    <input type="hidden" name="area" value="inicio">
                                    <!-- CAMPOS DE SEGURIDAD ANTI-BOT -->
                                    <?php echo generar_campos_seguridad(); ?>
                                    <!-- FIN CAMPOS DE SEGURIDAD -->


                                    <label for="fname">Nombre:</label>
                                    <input type="text" id="fname" name="txtName" placeholder="Tu nombre y apellidos" title="FirstName" required />

                                    <label for="email">Email:</label>
                                    <input type="email" id="email" name="txtEmail" placeholder="Tu correo electrónico" title="Email" required />

                                    <label for="subject">Mensaje:</label>
                                    <textarea id="subject" name="txtMsg" placeholder="Tu mensaje" title="Message" style="height:200px" required></textarea>

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
            <!--Footer call-to-action-->
            <!-- <div class="main-container call-to-action">
                <div id="transparencia" class="inside-container">
                    <div class="row">
                        <div class="col-lg-6">
                            <h2>Transparencia</h2>
                            <p>Aquí encontrarás todos los detalles organizativos, sociales y económicos de nuestra asociación.
                                Para conocer todos los aspectos sobre la regulación de la Transparencia en Canarias puede visitar la página web del <a href="https://transparenciacanarias.org/" target="_blank" rel="noopener noreferrer">Comisionado de Transparencia</a>
                            </p>
                            <p><strong></strong></p>
                        </div>
                        <div class="col-lg-6">
                            <div class="call-to-action-button">
                                <a class="wm-button button size-large" href="transparencia.php">
                                    <span class="icon-thumbs-up-alt"></span>¡Accede a nuestro portal de transparencia ahora!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <!--Footer call-to-action end-->
            <!--Footer contacts-->
            <div class="main-container footer-light contacts">
                <div class="inside-container">
                    <div class="row">
                        <div class="col-md-4 icon-footer-contact">
                            <a href="https://maps.app.goo.gl/q93ALLMNT7fWwzsU9" target="_blank" rel="noopener noreferrer" aria-label="Ver ubicación en Google Maps" tabindex="0">
                                <div class="footer-contact-icon aligncenter">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 16 3 C 11.042969 3 7 7.042969 7 12 C 7 13.40625 7.570313 15.019531 8.34375 16.78125 C 9.117188 18.542969 10.113281 20.414063 11.125 22.15625 C 13.148438 25.644531 15.1875 28.5625 15.1875 28.5625 L 16 29.75 L 16.8125 28.5625 C 16.8125 28.5625 18.851563 25.644531 20.875 22.15625 C 21.886719 20.414063 22.882813 18.542969 23.65625 16.78125 C 24.429688 15.019531 25 13.40625 25 12 C 25 7.042969 20.957031 3 16 3 Z M 16 5 C 19.878906 5 23 8.121094 23 12 C 23 12.800781 22.570313 14.316406 21.84375 15.96875 C 21.117188 17.621094 20.113281 19.453125 19.125 21.15625 C 17.554688 23.867188 16.578125 25.300781 16 26.15625 C 15.421875 25.300781 14.445313 23.867188 12.875 21.15625 C 11.886719 19.453125 10.882813 17.621094 10.15625 15.96875 C 9.429688 14.316406 9 12.800781 9 12 C 9 8.121094 12.121094 5 16 5 Z M 16 10 C 14.894531 10 14 10.894531 14 12 C 14 13.105469 14.894531 14 16 14 C 17.105469 14 18 13.105469 18 12 C 18 10.894531 17.105469 10 16 10 Z" />
                                    </svg>
                                </div>
                            </a>
                            <p><?= nl2br(e($config['contacto_direccion'] ?? 'Calle Zurbarán, 7<br>local 3. 38108<br>San Cristóbal de La Laguna')) ?></p>
                        </div>
                        <div class="col-md-4 icon-footer-contact">
                            <a href="tel:<?= attr(str_replace(' ', '', $config['contacto_telefono'] ?? '+34922215909')) ?>" aria-label="Llamar al <?= attr($config['contacto_telefono'] ?? '922 215 909') ?>" tabindex="0">
                                <div class="footer-contact-icon aligncenter">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 8.65625 3 C 8.132813 3 7.617188 3.1875 7.1875 3.53125 L 7.125 3.5625 L 7.09375 3.59375 L 3.96875 6.8125 L 4 6.84375 C 3.035156 7.734375 2.738281 9.066406 3.15625 10.21875 C 3.160156 10.226563 3.152344 10.242188 3.15625 10.25 C 4.003906 12.675781 6.171875 17.359375 10.40625 21.59375 C 14.65625 25.84375 19.402344 27.925781 21.75 28.84375 L 21.78125 28.84375 C 22.996094 29.25 24.3125 28.960938 25.25 28.15625 L 28.40625 25 C 29.234375 24.171875 29.234375 22.734375 28.40625 21.90625 L 24.34375 17.84375 L 24.3125 17.78125 C 23.484375 16.953125 22.015625 16.953125 21.1875 17.78125 L 19.1875 19.78125 C 18.464844 19.433594 16.742188 18.542969 15.09375 16.96875 C 13.457031 15.40625 12.621094 13.609375 12.3125 12.90625 L 14.3125 10.90625 C 15.152344 10.066406 15.167969 8.667969 14.28125 7.84375 L 14.3125 7.8125 L 14.21875 7.71875 L 10.21875 3.59375 L 10.1875 3.5625 L 10.125 3.53125 C 9.695313 3.1875 9.179688 3 8.65625 3 Z M 8.65625 5 C 8.730469 5 8.804688 5.035156 8.875 5.09375 L 12.875 9.1875 L 12.96875 9.28125 C 12.960938 9.273438 13.027344 9.378906 12.90625 9.5 L 10.40625 12 L 9.9375 12.4375 L 10.15625 13.0625 C 10.15625 13.0625 11.304688 16.136719 13.71875 18.4375 L 13.9375 18.625 C 16.261719 20.746094 19 21.90625 19 21.90625 L 19.625 22.1875 L 22.59375 19.21875 C 22.765625 19.046875 22.734375 19.046875 22.90625 19.21875 L 27 23.3125 C 27.171875 23.484375 27.171875 23.421875 27 23.59375 L 23.9375 26.65625 C 23.476563 27.050781 22.988281 27.132813 22.40625 26.9375 C 20.140625 26.046875 15.738281 24.113281 11.8125 20.1875 C 7.855469 16.230469 5.789063 11.742188 5.03125 9.5625 C 4.878906 9.15625 4.988281 8.554688 5.34375 8.25 L 5.40625 8.1875 L 8.4375 5.09375 C 8.507813 5.035156 8.582031 5 8.65625 5 Z" />
                                    </svg>
                                </div>
                            </a>
                            <p><?= e($config['contacto_telefono'] ?? '922 215 909') ?></p>
                        </div>
                        <div class="col-md-4 icon-footer-contact">
                            <a href="mailto:<?= attr($config['contacto_email'] ?? 'info@coordicanarias.com') ?>" aria-label="Enviar correo a <?= attr($config['contacto_email'] ?? 'info@coordicanarias.com') ?>" tabindex="0">
                                <div class="footer-contact-icon aligncenter">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 3 8 L 3 26 L 29 26 L 29 8 Z M 7.3125 10 L 24.6875 10 L 16 15.78125 Z M 5 10.875 L 15.4375 17.84375 L 16 18.1875 L 16.5625 17.84375 L 27 10.875 L 27 24 L 5 24 Z" />
                                    </svg>
                                </div>
                            </a>
                            <p><?= e($config['contacto_email'] ?? 'info@coordicanarias.com') ?></p>
                        </div>
                    </div>
                    <div class="row ">
                    </div>
                </div>
            </div>
            <!--Footer contacts end-->
            <!--Footer Three columns-->
            <div class="main-container footer-dark">
                <div class="inside-container">
                    <div class="row footer-dark">
                        <div class="col-sm-6 col-lg-4 foot-col-padd">
                            <div class="foot-logo">
                                <img src="images/brand-coordi-black.svg" width="250" alt="Logotipo de Coordicanarias" class="float-center img-fluid logo-coordicanarias">
                            </div>
                            <div class="dream-text">
                                <p>Coordinadora de Personas con Discapacidad Física de Canarias. Acompañamos, defendemos los derechos y promovemos la inclusión de las personas con discapacidad.</p>
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
                                <a href="<?= attr($config['redes_instagram']) ?>" target="_blank" rel="noopener noreferrer"
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
                                <?php endif; ?>
                                <!--<a aria-label="Pinterest" tabindex="0">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 16.09375 4 C 11.01675 4 6 7.3833281 6 12.861328 C 6 16.344328 7.9584844 18.324219 9.1464844 18.324219 C 9.6364844 18.324219 9.9199219 16.958266 9.9199219 16.572266 C 9.9199219 16.112266 8.7460938 15.131797 8.7460938 13.216797 C 8.7460938 9.2387969 11.774359 6.4199219 15.693359 6.4199219 C 19.063359 6.4199219 21.556641 8.3335625 21.556641 11.851562 C 21.556641 14.478563 20.501891 19.40625 17.087891 19.40625 C 15.855891 19.40625 14.802734 18.516234 14.802734 17.240234 C 14.802734 15.370234 16 13.558906 16 11.628906 C 16 8.3529063 11.462891 8.94725 11.462891 12.90625 C 11.462891 13.73725 11.5665 14.657063 11.9375 15.414062 C 11.2555 18.353063 10 23.037406 10 26.066406 C 10 27.001406 10.133656 27.921422 10.222656 28.857422 C 10.390656 29.045422 10.307453 29.025641 10.564453 28.931641 C 13.058453 25.517641 12.827078 24.544172 13.955078 20.076172 C 14.564078 21.234172 16.137766 21.857422 17.384766 21.857422 C 22.639766 21.857422 25 16.736141 25 12.119141 C 25 7.2061406 20.75475 4 16.09375 4 z" />
                                    </svg>
                                </a>-->
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <h3 style="text-align: center">Enlaces Rápidos</h3>
                            <div class="row">
                                <div class="col-6 pop-link" style="text-align: center">
                                    <a class="" data-scroll href="#home">Inicio</a>
                                    <a class="" data-scroll href="#about">Conócenos</a>
                                    <a class="" data-scroll href="#features">Áreas</a>
                                </div>
                                <div class="col-6 pop-link" style="text-align: center">
                                    <a class="" data-scroll href="#portfolios">Proyectos</a>
                                    <a class="" data-scroll href="#colabora">Colabora</a>
                                    <a class="" data-scroll href="#contact">Contacto</a>
                                    <a class="" href="transparencia.php">Transparencia</a>
                                </div>
                                <div class="col-6 pop-link" style="text-align: center">
                                    <?php if (count($noticias_destacadas) > 0): ?>
                                        <a class="" data-scroll href="#news">Noticias</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <h3 style="text-align: center">Información Legal</h3>
                            <div class="row">
                                <div class="col-12 pop-link" style="text-align: center">
                                    <a href="areas/politica-cookies.php">Política de cookies</a>
                                    <a href="areas/politica-privacidad.php">Política de privacidad</a>
                                    <a href="areas/alegal.php">Aviso legal</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Footer three columns end-->
            <!-- Footer three columns
            <div class="main-container footer-light">
                <div class="inside-container">
                    <div class="row footer-light">
                        <div class=" col-sm-6 col-lg-4">
                            <h3>Tag Cloud</h3>
                            <div class="tagcloud">
                                <ul class="tagcloudul">
                                    <li><a><span>One Page</span></a></li>
                                    <li><a><span>WCAG 2.2 (Level AA)</span></a></li>
                                    <li><a><span>HTML5</span></a></li>
                                    <li><a><span>Bootstrap 4</span></a></li>
                                    <li><a><span>Template</span></a></li>
                                    <li><a><span>ADA Compliant</span></a></li>
                                    <li><a><span>Accessibility Ready</span></a></li>
                                    <li><a><span>jQuery</span></a></li>
                                    <li><a><span>Fully Responsive</span></a></li>
                                    <li><a><span>CSS3</span></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class=" col-sm-6 col-lg-4">
                            <h3>Opening Hour</h3>
                            <div class="footer-widget">
                                <div class="footer-widget-content">
                                    <div class="open-time">
                                        <ul class="opening-time">
                                            <li>
                                                <p class="clock-time">
                                                    <strong>Monday - Friday:</strong> 8am - 6pm </p>
                                            </li>
                                            <li>
                                                <p>
                                                    <strong>Saturday:</strong> 8am - 2pm </p>
                                            </li>
                                            <li>
                                                <p>
                                                    <strong>Sunday:</strong> Closed </p>
                                            </li>
                                            <li>
                                                <p>
                                                    <strong>Public Holidays:</strong> Closed </p>
                                            </li>
                                        </ul>
                                        <div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>-->

            <!--Footer three columns end-->
            <!--Footer menu end -->

            <!--Footer copyright-->
            <div class="footer-container footer-dark">
                <div class="inside-container">
                    <div class="row footer-dark">
                        <div class="col-12 copyright-text" style="text-align: center;">
                            <a href="https://www.gobiernodecanarias.org/bienestarsocial/dependencia/" target="_blank" rel="noopener">
                                        <img src="images/logos_gobcan/Logo_GobCan_claim_negro_mod1.png" alt="Gobierno de Canarias - Dirección General de Dependencia" width="120" style="margin: 15px 20px 30px 15px" class="logo-gobcan">
                            </a>
                            <a href="areas/accesibilidad.php" class="accesibilidad-badge">
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
                    <a href="areas/politica-cookies.php" target="_blank" rel="noopener">Más información sobre cookies</a>
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

    <!-- Modal de Donación -->
    <div class="modal fade" id="donacionModal" tabindex="-1" aria-labelledby="donacionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h3 class="modal-title" id="donacionModalLabel">Hacer una Donación</h3>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" style="padding: 30px;">
                    <p class="text-center mb-4">
                        Tu donación nos ayuda a continuar nuestra labor de apoyo a personas con discapacidad en Canarias.
                        <strong>¡Gracias por tu generosidad!</strong>
                    </p>

                    <form id="donacionForm">
                        <!-- Importes predefinidos -->
                        <div class="mb-4">
                            <label class="form-label"><strong>Selecciona un importe o ingresa uno personalizado:</strong></label>
                            <div class="row g-2">
                                <div class="col-6 col-md-3">
                                    <button type="button" class="btn btn-outline-primary w-100 btn-importe" data-amount="10">10 €</button>
                                </div>
                                <div class="col-6 col-md-3">
                                    <button type="button" class="btn btn-outline-primary w-100 btn-importe" data-amount="25">25 €</button>
                                </div>
                                <div class="col-6 col-md-3">
                                    <button type="button" class="btn btn-outline-primary w-100 btn-importe" data-amount="50">50 €</button>
                                </div>
                                <div class="col-6 col-md-3">
                                    <button type="button" class="btn btn-outline-primary w-100 btn-importe" data-amount="100">100 €</button>
                                </div>
                            </div>
                        </div>

                        <!-- Importe personalizado -->
                        <div class="mb-3">
                            <label for="importePersonalizado" class="form-label">O ingresa un importe personalizado (€):</label>
                            <input type="number" class="form-control" id="importePersonalizado" name="amount"
                                   min="1" max="10000" step="1" placeholder="Ej: 30" required>
                            <div class="form-text">Importe mínimo: 1€ - Máximo: 10,000€</div>
                        </div>

                        <!-- Datos del donante -->
                        <div class="mb-3">
                            <label for="nombreDonante" class="form-label">Nombre completo:</label>
                            <input type="text" class="form-control" id="nombreDonante" name="name"
                                   placeholder="Tu nombre" required>
                        </div>

                        <div class="mb-3">
                            <label for="emailDonante" class="form-label">Email:</label>
                            <input type="email" class="form-control" id="emailDonante" name="email"
                                   placeholder="tu@email.com" required>
                            <div class="form-text">Recibirás un recibo de tu donación en este email</div>
                        </div>

                        <div class="mb-3">
                            <label for="mensajeDonante" class="form-label">Mensaje (opcional):</label>
                            <textarea class="form-control" id="mensajeDonante" name="message"
                                      rows="3" placeholder="Deja un mensaje si lo deseas..."></textarea>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="donacionAnonima" name="anonymous">
                            <label class="form-check-label" for="donacionAnonima">
                                Quiero que mi donación sea anónima
                            </label>
                        </div>

                        <div class="alert alert-info" role="alert">
                            <strong>Métodos de pago disponibles:</strong> Tarjeta de crédito/débito
                            <small class="d-block mt-1">(Bizum estará disponible cuando se active el modo de producción)</small>
                        </div>

                        <div id="donacionError" class="alert alert-danger" style="display:none;" role="alert"></div>
                        <div id="donacionLoading" style="display:none; text-align: center;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Procesando...</span>
                            </div>
                            <p>Redirigiendo a la pasarela de pago segura...</p>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="donacionForm" class="btn btn-primary" id="btnProcederPago"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                        Proceder al pago
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin Modal de Donación -->

    <!-- Modal de Suscripción de Socio -->
    <div class="modal fade" id="socioModal" tabindex="-1" aria-labelledby="socioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h3 class="modal-title" id="socioModalLabel">Hacerte Socio de Coordicanarias</h3>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" style="padding: 30px;">
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="font-size: 48px; color: #667eea; font-weight: bold;">5 €</div>
                        <div style="font-size: 18px; color: #666; margin-top: 5px;">al mes</div>
                    </div>

                    <p class="text-center mb-4">
                        Al convertirte en socio de Coordicanarias, te unes a una comunidad comprometida con
                        la inclusión y el cambio social. Tu aportación regular nos brinda una base sólida para
                        planificar y desarrollar iniciativas a largo plazo.
                        <strong>¡Gracias por tu compromiso!</strong>
                    </p>

                    <div class="alert alert-info" role="alert" style="margin-bottom: 25px;">
                        <strong>✓ Ventajas de ser socio:</strong>
                        <ul style="margin: 10px 0 0 20px;">
                            <li>Apoyo continuado a nuestros programas y servicios</li>
                            <li>Cancela tu suscripción cuando quieras (sin permanencia)</li>
                            <li>Gestiona tu suscripción desde tu portal personal</li>
                            <li>Recibo mensual automático por email</li>
                        </ul>
                    </div>

                    <form id="socioForm">
                        <!-- Datos del socio -->
                        <div class="mb-3">
                            <label for="nombreSocio" class="form-label">Nombre completo: <span style="color: red;">*</span></label>
                            <input type="text" class="form-control" id="nombreSocio" name="name"
                                   placeholder="Tu nombre y apellidos" required>
                        </div>

                        <div class="mb-3">
                            <label for="emailSocio" class="form-label">Email: <span style="color: red;">*</span></label>
                            <input type="email" class="form-control" id="emailSocio" name="email"
                                   placeholder="tu@email.com" required>
                            <div class="form-text">Recibirás los recibos mensuales y podrás gestionar tu suscripción</div>
                        </div>

                        <div class="mb-3">
                            <label for="telefonoSocio" class="form-label">Teléfono (opcional):</label>
                            <input type="tel" class="form-control" id="telefonoSocio" name="phone"
                                   placeholder="Ej: 922 123 456">
                        </div>

                        <div class="alert alert-warning" role="alert">
                            <strong>Métodos de pago:</strong> Tarjeta de crédito/débito
                            <small class="d-block mt-1">(En modo TEST - Bizum estará disponible en producción)</small>
                        </div>

                        <div class="alert alert-success" role="alert">
                            <strong>💳 Pago seguro con Stripe</strong><br>
                            <small>Tus datos de pago están protegidos con encriptación de nivel bancario.
                            Nunca almacenamos información de tu tarjeta en nuestros servidores.</small>
                        </div>

                        <div id="socioError" class="alert alert-danger" style="display:none;" role="alert"></div>
                        <div id="socioLoading" style="display:none; text-align: center;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Procesando...</span>
                            </div>
                            <p>Redirigiendo a la pasarela de pago segura...</p>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="socioForm" class="btn btn-primary" id="btnProcederSuscripcion"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                        Continuar al pago
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin Modal de Socio -->

    <script src="js/jquery-3.7.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/mixitup.min.js"></script>
    <script src="js/js-cookie.js"></script>
    <script src="js/main.js?v=DISABLED"></script>

    <!-- Stripe Integration -->
    <script>
        // Manejar selección de importes predefinidos
        document.querySelectorAll('.btn-importe').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remover selección previa
                document.querySelectorAll('.btn-importe').forEach(b => b.classList.remove('active'));
                // Marcar como activo
                this.classList.add('active');
                // Establecer valor en el input
                document.getElementById('importePersonalizado').value = this.dataset.amount;
            });
        });

        // Manejar envío del formulario
        document.getElementById('donacionForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const errorDiv = document.getElementById('donacionError');
            const loadingDiv = document.getElementById('donacionLoading');
            const btnSubmit = document.getElementById('btnProcederPago');

            // Ocultar errores previos
            errorDiv.style.display = 'none';

            // Obtener datos del formulario
            const formData = {
                amount: parseFloat(document.getElementById('importePersonalizado').value),
                name: document.getElementById('nombreDonante').value.trim(),
                email: document.getElementById('emailDonante').value.trim(),
                message: document.getElementById('mensajeDonante').value.trim(),
                anonymous: document.getElementById('donacionAnonima').checked
            };

            // Validaciones
            if (!formData.amount || formData.amount < 1) {
                errorDiv.textContent = 'Por favor, ingresa un importe válido (mínimo 1€)';
                errorDiv.style.display = 'block';
                return;
            }

            if (formData.amount > 10000) {
                errorDiv.textContent = 'El importe máximo permitido es 10,000€';
                errorDiv.style.display = 'block';
                return;
            }

            if (!formData.name || !formData.email) {
                errorDiv.textContent = 'Por favor, completa todos los campos obligatorios';
                errorDiv.style.display = 'block';
                return;
            }

            // Mostrar loading
            btnSubmit.disabled = true;
            loadingDiv.style.display = 'block';

            try {
                // Llamar al endpoint para crear sesión de Stripe
                const response = await fetch('<?= url('stripe/create-checkout-session.php') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success && data.url) {
                    // Redirigir a Stripe Checkout
                    window.location.href = data.url;
                } else {
                    throw new Error(data.error || 'Error al procesar la donación');
                }
            } catch (error) {
                console.error('Error:', error);
                errorDiv.textContent = error.message || 'Error al conectar con el servidor. Por favor, inténtalo de nuevo.';
                errorDiv.style.display = 'block';
                btnSubmit.disabled = false;
                loadingDiv.style.display = 'none';
            }
        });

        // Limpiar formulario al cerrar modal
        document.getElementById('donacionModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('donacionForm').reset();
            document.querySelectorAll('.btn-importe').forEach(b => b.classList.remove('active'));
            document.getElementById('donacionError').style.display = 'none';
            document.getElementById('donacionLoading').style.display = 'none';
            document.getElementById('btnProcederPago').disabled = false;
        });

        // ============================================
        // SUSCRIPCIÓN DE SOCIO
        // ============================================

        // Manejar envío del formulario de suscripción
        document.getElementById('socioForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const errorDiv = document.getElementById('socioError');
            const loadingDiv = document.getElementById('socioLoading');
            const btnSubmit = document.getElementById('btnProcederSuscripcion');

            // Ocultar errores previos
            errorDiv.style.display = 'none';

            // Obtener datos del formulario
            const formData = {
                name: document.getElementById('nombreSocio').value.trim(),
                email: document.getElementById('emailSocio').value.trim(),
                phone: document.getElementById('telefonoSocio').value.trim() || null
            };

            // Validaciones
            if (!formData.name) {
                errorDiv.textContent = 'Por favor, ingresa tu nombre completo';
                errorDiv.style.display = 'block';
                return;
            }

            if (!formData.email) {
                errorDiv.textContent = 'Por favor, ingresa tu email';
                errorDiv.style.display = 'block';
                return;
            }

            // Validar formato de email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(formData.email)) {
                errorDiv.textContent = 'Por favor, ingresa un email válido';
                errorDiv.style.display = 'block';
                return;
            }

            // Mostrar loading
            btnSubmit.disabled = true;
            loadingDiv.style.display = 'block';

            try {
                // Llamar al endpoint para crear sesión de suscripción en Stripe
                const response = await fetch('<?= url('stripe/create-subscription-session.php') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success && data.url) {
                    // Redirigir a Stripe Checkout
                    window.location.href = data.url;
                } else {
                    throw new Error(data.error || 'Error al procesar la suscripción');
                }
            } catch (error) {
                console.error('Error:', error);
                errorDiv.textContent = error.message || 'Error al conectar con el servidor. Por favor, inténtalo de nuevo.';
                errorDiv.style.display = 'block';
                btnSubmit.disabled = false;
                loadingDiv.style.display = 'none';
            }
        });

        // Limpiar formulario de socio al cerrar modal
        document.getElementById('socioModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('socioForm').reset();
            document.getElementById('socioError').style.display = 'none';
            document.getElementById('socioLoading').style.display = 'none';
            document.getElementById('btnProcederSuscripcion').disabled = false;
        });
    </script>

    <!-- Mensajes de éxito/error del formulario de contacto -->
    <script>
        // Mostrar mensaje de éxito o error después de enviar el formulario
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const messageDiv = document.getElementById('contactMessage');
            const contactForm = document.getElementById('contactForm');

            if (urlParams.get('success') === '1') {
                // Mostrar mensaje de éxito
                messageDiv.innerHTML = '<strong style="color: #28a745;">✓ ¡Mensaje enviado!</strong><br>Gracias por contactarnos. Te responderemos lo antes posible.';
                messageDiv.style.display = 'block';
                messageDiv.style.backgroundColor = '#d4edda';
                messageDiv.style.borderLeft = '4px solid #28a745';
                messageDiv.style.color = '#155724';

                // Limpiar el formulario
                if (contactForm) {
                    contactForm.reset();
                }

                // Ocultar el mensaje después de 8 segundos
                setTimeout(function() {
                    messageDiv.style.display = 'none';
                    // Limpiar la URL sin recargar
                    window.history.replaceState({}, document.title, window.location.pathname + window.location.hash);
                }, 8000);

            } else if (urlParams.get('error')) {
                // Mostrar mensaje de error
                const errorMsg = urlParams.get('error');
                let textoError = '✗ Error al enviar el mensaje';

                if (errorMsg === 'error_envio') {
                    textoError = '✗ Error al enviar el mensaje. Por favor, inténtalo de nuevo o contáctanos por teléfono.';
                } else if (errorMsg.includes('obligatorio')) {
                    textoError = '✗ ' + decodeURIComponent(errorMsg);
                }

                messageDiv.innerHTML = '<strong>' + textoError + '</strong>';
                messageDiv.style.display = 'block';
                messageDiv.style.backgroundColor = '#f8d7da';
                messageDiv.style.borderLeft = '4px solid #dc3545';
                messageDiv.style.color = '#721c24';

                // Ocultar el mensaje después de 10 segundos
                setTimeout(function() {
                    messageDiv.style.display = 'none';
                    window.history.replaceState({}, document.title, window.location.pathname + window.location.hash);
                }, 10000);
            }
        });
    </script>

    <!-- Pre-llenar formulario de contacto desde sección Colabora -->
    <script>
        // Manejar clics en botones de Colaboraciones y Voluntariado
        document.addEventListener('DOMContentLoaded', function() {
            // Mensajes predefinidos
            const mensajes = {
                'colaboraciones': 'Buenos días,\n\nMe gustaría recibir información sobre cómo mi empresa puede colaborar con Coordicanarias y apoyar su labor en favor de las personas con discapacidad en Canarias.\n\nQuedo a la espera de su respuesta.\n\nGracias.',
                'voluntariado': 'Buenos días,\n\nEstoy interesado/a en participar como voluntario/a en Coordicanarias. Me gustaría recibir más información sobre las oportunidades disponibles y cómo puedo colaborar en sus proyectos.\n\nQuedo a la espera de su respuesta.\n\nGracias.'
            };

            // Obtener todos los botones con data-tipo
            const botonesContacto = document.querySelectorAll('[data-tipo]');

            botonesContacto.forEach(function(boton) {
                boton.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevenir comportamiento por defecto

                    const tipo = this.getAttribute('data-tipo');
                    const textarea = document.getElementById('subject');
                    const seccionContacto = document.getElementById('contact');

                    if (textarea && mensajes[tipo]) {
                        // Pre-llenar el mensaje
                        textarea.value = mensajes[tipo];

                        // Hacer scroll suave a la sección de contacto
                        seccionContacto.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });

                        // Después del scroll, hacer focus en el textarea
                        setTimeout(function() {
                            textarea.focus();
                        }, 800);
                    }
                });
            });
        });
    </script>

    <!-- Script de seguridad de formularios -->
    <script src="<?= url('js/form-security.js') ?>"></script>

</body>

</html>
