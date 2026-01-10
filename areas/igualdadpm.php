<?php
/**
 * Página de Área: Igualdad y Promoción de la Mujer
 * Coordicanarias CMS
 */

require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/db/connection.php';
require_once __DIR__ . '/../php/core/security.php';
require_once __DIR__ . '/../php/models/Area.php';
require_once __DIR__ . '/../php/models/Servicio.php';
require_once __DIR__ . '/../php/models/Beneficio.php';
require_once __DIR__ . '/../php/models/Proyecto.php';
require_once __DIR__ . '/../php/models/Configuracion.php';

// Obtener el área actual por slug
$area_slug = 'igualdadpm';
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
?>
<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/html">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Coordicanarias - Igualdad y Promoción de la Mujer</title>
    <link rel="icon" href="../favicon.ico" type="image/x-icon">
    <!-- Stylesheets -->
    <link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../css/fontawesome-all.min.css" rel="stylesheet" type="text/css">
    <link href="../css/style.css" rel="stylesheet" type="text/css">
    <link href="../css/my.css" rel="stylesheet" type="text/css">
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
    <!--Accessibility-->
    <div class="block-settings-wrapper">
        <div id="block_settings" class="block_settings">
            <a id="settings_close" tabindex="0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="currentColor" width="1em">
                    <path d="M50 8.1c23.2 0 41.9 18.8 41.9 41.9 0 23.2-18.8 41.9-41.9 41.9C26.8 91.9 8.1 73.2 8.1 50S26.8 8.1 50 8.1M50 0C22.4 0 0 22.4 0 50s22.4 50 50 50 50-22.4 50-50S77.6 0 50 0zm0 11.3c-21.4 0-38.7 17.3-38.7 38.7S28.6 88.7 50 88.7 88.7 71.4 88.7 50 71.4 11.3 50 11.3zm0 8.9c4 0 7.3 3.2 7.3 7.3S54 34.7 50 34.7s-7.3-3.2-7.3-7.3 3.3-7.2 7.3-7.2zm23.7 19.7c-5.8 1.4-11.2 2.6-16.6 3.2.2 20.4 2.5 24.8 5 31.4.7 1.9-.2 4-2.1 4.7-1.9.7-4-.2-4.7-2.1-1.8-4.5-3.4-8.2-4.5-15.8h-2c-1 7.6-2.7 11.3-4.5 15.8-.7 1.9-2.8 2.8-4.7 2.1-1.9-.7-2.8-2.8-2.1-4.7 2.6-6.6 4.9-11 5-31.4-5.4-.6-10.8-1.8-16.6-3.2-1.7-.4-2.8-2.1-2.4-3.9.4-1.7 2.1-2.8 3.9-2.4 19.5 4.6 25.1 4.6 44.5 0 1.7-.4 3.5.7 3.9 2.4.7 1.8-.3 3.5-2.1 3.9z">
                    </path>
                </svg>
            </a>
            <div class="open-accessibility">
                <ul class="lab-wcag-settings clearfix">
                    <!--Visual accessibility menu-->
                    <li class="fontfamily-label">Visualización</li>
                    <li>
                        <ul class="access-float-fontfamily">
                            <li>
                                <button class="lab-high-contrast" aria-label="Activar alto contraste" aria-pressed="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 16 3 C 8.832031 3 3 8.832031 3 16 C 3 23.167969 8.832031 29 16 29 C 23.167969 29 29 23.167969 29 16 C 29 8.832031 23.167969 3 16 3 Z M 16 5 C 22.085938 5 27 9.914063 27 16 C 27 22.085938 22.085938 27 16 27 L 16 5 Z" />
                                    </svg>
                                    <span class="lab-only">Alto Contraste</span>
                                    <span class="lab-button-label">Alto Contraste</span>
                                </button>
                            </li>
                            <li>
                                <button class="lab-dark-mode" aria-label="Activar modo oscuro" aria-pressed="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 14.78125 3.03125 C 8.046875 3.777344 2.75 9.519531 2.75 16.5 C 2.75 23.953125 8.796875 30 16.25 30 C 23.230469 30 28.972656 24.703125 29.71875 17.96875 C 27.582031 19.273438 25.054688 20.03125 22.34375 20.03125 C 14.890625 20.03125 8.84375 13.984375 8.84375 6.53125 C 8.84375 5.121094 9.0625 3.761719 9.46875 2.46875 C 6.761719 3.488281 4.433594 5.421875 2.902344 7.984375 C 2.113281 9.390625 1.625 11.003906 1.625 12.75 C 1.625 18.132813 5.992188 22.5 11.375 22.5 C 16.757813 22.5 21.125 18.132813 21.125 12.75 C 21.125 11.003906 20.636719 9.390625 19.847656 7.984375 C 18.316406 5.421875 15.988281 3.488281 13.28125 2.46875 Z" />
                                    </svg>
                                    <span class="lab-only">Modo Oscuro</span>
                                    <span class="lab-button-label">Modo Oscuro</span>
                                </button>
                            </li>
                            <li>
                                <button class="lab-screen-reader" aria-label="Activar lector de voz" aria-pressed="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 16 4 C 15.476563 4 14.941406 4.183594 14.5625 4.5625 L 8.28125 10.84375 L 4 11 L 4 21 L 8.28125 21.15625 L 14.5625 27.4375 C 15.320313 28.195313 16.53125 27.902344 16.84375 26.875 C 16.941406 26.589844 17 26.308594 17 26 L 17 6 C 17 4.894531 16.105469 4 15 4 Z M 20 9 L 20 11 C 21.65625 11 23 12.34375 23 14 C 23 15.65625 21.65625 17 20 17 L 20 19 C 22.753906 19 25 16.753906 25 14 C 25 11.246094 22.753906 9 20 9 Z M 20 13 L 20 15 C 20.550781 15 21 14.550781 21 14 C 21 13.449219 20.550781 13 20 13 Z" />
                                    </svg>
                                    <span class="lab-only">Lector de Voz</span>
                                    <span class="lab-button-label">Lector de Voz</span>
                                </button>
                            </li>
                        </ul>
                    </li>
                    <!--Visual accessibility menu end-->
                    <!--Font type accessibility menu-->
                    <li class="fontfamily-label">Tipo de fuente</li>
                    <li>
                        <ul class="access-float-fontfamily">
                            <li>
                                <button class="lab-link-default">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 15 6 L 8 26 L 10 26 L 12.09375 20 L 19.90625 20 L 22 26 L 24 26 L 17 6 Z M 16 8.84375 L 19.1875 18 L 12.8125 18 Z" />
                                    </svg>
                                    <span class="lab-only">Open Sans</span>
                                </button>
                            </li>
                            <li>
                                <button class="lab-font-inter">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 15 6 L 8 26 L 10 26 L 12.09375 20 L 19.90625 20 L 22 26 L 24 26 L 17 6 Z M 16 8.84375 L 19.1875 18 L 12.8125 18 Z" />
                                    </svg>
                                    <span class="lab-only">Inter</span>
                                </button>
                            </li>
                            <li>
                                <button class="lab-font-andika">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 15 6 L 8 26 L 10 26 L 12.09375 20 L 19.90625 20 L 22 26 L 24 26 L 17 6 Z M 16 8.84375 L 19.1875 18 L 12.8125 18 Z" />
                                    </svg>
                                    <span class="lab-only">Andika</span>
                                </button>
                            </li>
                            <li>
                                <button class="lab-font-fsme">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 15 6 L 8 26 L 10 26 L 12.09375 20 L 19.90625 20 L 22 26 L 24 26 L 17 6 Z M 16 8.84375 L 19.1875 18 L 12.8125 18 Z" />
                                    </svg>
                                    <span class="lab-only">FSMe</span>
                                </button>
                            </li>
                            <li>
                                <button class="lab-font-tiresias">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 15 6 L 8 26 L 10 26 L 12.09375 20 L 19.90625 20 L 22 26 L 24 26 L 17 6 Z M 16 8.84375 L 19.1875 18 L 12.8125 18 Z" />
                                    </svg>
                                    <span class="lab-only">Tiresias</span>
                                </button>
                            </li>
                            <li>
                                <button class="lab-font-opendyslexic">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 15 6 L 8 26 L 10 26 L 12.09375 20 L 19.90625 20 L 22 26 L 24 26 L 17 6 Z M 16 8.84375 L 19.1875 18 L 12.8125 18 Z" />
                                    </svg>
                                    <span class="lab-only">OpenDyslexic</span>
                                </button>
                            </li>
                        </ul>
                    </li>
                    <!--Font type accessibility menu end-->
                    <!--Font size accessibility menu-->
                    <li class="resizer-label">Ajustes de fuente</li>
                    <li>
                        <ul class="access-float-font">
                            <li>
                                <button class="lab-font-smaller">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 16 3 C 8.832031 3 3 8.832031 3 16 C 3 23.167969 8.832031 29 16 29 C 23.167969 29 29 23.167969 29 16 C 29 8.832031 23.167969 3 16 3 Z M 16 5 C 22.085938 5 27 9.914063 27 16 C 27 22.085938 22.085938 27 16 27 C 9.914063 27 5 22.085938 5 16 C 5 9.914063 9.914063 5 16 5 Z M 10 15 L 10 17 L 22 17 L 22 15 Z" />
                                    </svg>
                                    <span class="lab-only">Reducir</span>
                                </button>
                            </li>
                            <li>
                                <button class="lab-font-larger">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 16 3 C 8.832031 3 3 8.832031 3 16 C 3 23.167969 8.832031 29 16 29 C 23.167969 29 29 23.167969 29 16 C 29 8.832031 23.167969 3 16 3 Z M 16 5 C 22.085938 5 27 9.914063 27 16 C 27 22.085938 22.085938 27 16 27 C 9.914063 27 5 22.085938 5 16 C 5 9.914063 9.914063 5 16 5 Z M 15 10 L 15 15 L 10 15 L 10 17 L 15 17 L 15 22 L 17 22 L 17 17 L 22 17 L 22 15 L 17 15 L 17 10 Z" />
                                    </svg>
                                    <span class="lab-only">Aumentar</span>
                                </button>
                            </li>
                            <li>
                                <button class="lab-link-underline">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 21.75 4 C 20.078125 4 18.492188 4.660156 17.3125 5.84375 L 15.84375 7.3125 C 14.660156 8.496094 14 10.078125 14 11.75 C 14 12.542969 14.152344 13.316406 14.4375 14.03125 L 16.0625 12.40625 C 15.859375 11.109375 16.253906 9.714844 17.25 8.71875 L 18.71875 7.25 C 19.523438 6.445313 20.613281 6 21.75 6 C 22.886719 6 23.945313 6.445313 24.75 7.25 C 26.410156 8.910156 26.410156 11.621094 24.75 13.28125 L 23.28125 14.75 C 22.476563 15.554688 21.386719 16 20.25 16 C 20.027344 16 19.808594 15.976563 19.59375 15.9375 L 17.96875 17.5625 C 18.683594 17.847656 19.457031 18 20.25 18 C 21.921875 18 23.507813 17.339844 24.6875 16.15625 L 26.15625 14.6875 C 27.339844 13.503906 28 11.921875 28 10.25 C 28 8.578125 27.339844 7.027344 26.15625 5.84375 C 24.976563 4.660156 23.421875 4 21.75 4 Z M 19.28125 11.28125 L 11.28125 19.28125 L 12.71875 20.71875 L 20.71875 12.71875 Z M 11.75 14 C 10.078125 14 8.492188 14.660156 7.3125 15.84375 L 5.84375 17.3125 C 4.660156 18.496094 4 20.078125 4 21.75 C 4 23.421875 4.660156 24.972656 5.84375 26.15625 C 7.023438 27.339844 8.578125 28 10.25 28 C 11.921875 28 13.507813 27.339844 14.6875 26.15625 L 16.15625 24.6875 C 17.339844 23.503906 18 21.921875 18 20.25 C 18 19.457031 17.847656 18.683594 17.5625 17.96875 L 15.9375 19.59375 C 16.140625 20.890625 15.746094 22.285156 14.75 23.28125 L 13.28125 24.75 C 12.476563 25.554688 11.386719 26 10.25 26 C 9.113281 26 8.054688 25.554688 7.25 24.75 C 5.589844 23.089844 5.589844 20.378906 7.25 18.71875 L 8.71875 17.25 C 9.523438 16.445313 10.613281 16 11.75 16 C 11.972656 16 12.191406 16.023438 12.40625 16.0625 L 14.03125 14.4375 C 13.316406 14.152344 12.542969 14 11.75 14 Z" />
                                    </svg>
                                    <span class="lab-only">Subrayar enlaces</span>
                                </button>
                            </li>
                            <li>
                                <button class="lab-font-readable">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 8 6 L 8 8 L 15 8 L 15 22 L 17 22 L 17 8 L 24 8 L 24 6 Z M 10 21.5 L 5.625 25 L 10 28.5 L 10 26 L 22 26 L 22 28.5 L 26.375 25 L 22 21.5 L 22 24 L 10 24 Z" />
                                    </svg>
                                    <span class="lab-only">Legible</span>
                                </button>
                            </li>
                            <li>
                                <button class="lab-font-normal">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                        <path d="M 12.78125 5.28125 L 4.78125 13.28125 L 4.09375 14 L 4.78125 14.71875 L 12.78125 22.71875 L 14.21875 21.28125 L 7.9375 15 L 21 15 C 23.753906 15 26 17.246094 26 20 L 26 27 L 28 27 L 28 20 C 28 16.15625 24.84375 13 21 13 L 7.9375 13 L 14.21875 6.71875 Z" />
                                    </svg>
                                    <span class="lab-only">Fuente por defecto</span>
                                </button>
                            </li>
                        </ul>
                    </li>
                    <!--Font size accessibility menu-->
                    <!--Accessibility reset-->
                    <li>
                        <button class="lab-reset">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                <path d="M 16 3 C 8.832031 3 3 8.832031 3 16 C 3 23.167969 8.832031 29 16 29 C 23.167969 29 29 23.167969 29 16 L 27 16 C 27 22.085938 22.085938 27 16 27 C 9.914063 27 5 22.085938 5 16 C 5 9.914063 9.914063 5 16 5 C 19.875 5 23.261719 6.984375 25.21875 10 L 20 10 L 20 12 L 28 12 L 28 4 L 26 4 L 26 7.71875 C 23.617188 4.84375 20.019531 3 16 3 Z" />
                            </svg>
                            <span class="lab-only">Restablecer todo</span>
                        </button>
                    </li>
                    <!--Accessibility reset end-->
                </ul>
            </div>
        </div>
    </div>
    <!--Accessibility end-->
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
                                        <a class="" data-scroll href="#about-ipm">Igualdad</a>
                                    </li>
                                    <li class="menu-item">
                                        <a class="" data-scroll href="#services-ipm">Servicios</a>
                                    </li>
                                    <li class="menu-item">
                                        <a class="" data-scroll href="#portfolios-ipm">Proyectos</a>
                                    </li>
                                    <li class="menu-item">
                                        <a class="" data-scroll href="#beneficios-ipm">Beneficios</a>
                                    </li>
                                    <li class="menu-item">
                                        <a class="" data-scroll href="#participa-ipm">Participa</a>
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
                                                <a class="" href="#about-ipm">Igualdad</a>
                                            </li>
                                            <li class="menu-item">
                                                <a class="" href="#services-ipm">Servicios</a>
                                            </li>
                                            <li class="menu-item">
                                                <a class="" href="#portfolios-ipm">Proyectos</a>
                                            </li>
                                            <li class="menu-item">
                                                <a class="" href="#beneficios-ipm">Beneficios</a>
                                            </li>
                                            <li class="menu-item">
                                                <a class="" data-scroll href="#participa-ipm">Participa</a>
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
    <div class="jumbotron_ipm" id="jumbotron">
        <h1 class="display-4" style="padding-left: 20px; padding-right: 20px">Igualdad y Promoción de la Mujer</h1>
        <p class="lead" style="padding-bottom: 60px;">Trabajando por la igualdad de género y el empoderamiento de las mujeres con discapacidad</p>
    </div>
    <!--Jumbotron end-->

    <!--About section-->
    <!--Descripción del Área-->
    <section id="about-ipm" class="section">
        <div class="main-container">
            <div class="inside-container">
                <div class="row">
                    <div class="col-12">
                        <h2 style="margin-bottom: 30px;">¿Qué es el Área de Igualdad y Promoción de la Mujer?</h2>
                        <p style="font-size: 1.1em; line-height: 1.8; margin-bottom: 20px;">
                            El Área de Igualdad y Promoción de la Mujer con Discapacidad de Coordicanarias trabaja para
                            eliminar la discriminación múltiple que enfrentan las mujeres con discapacidad y promover
                            su pleno desarrollo personal, social y profesional. Reconocemos que las mujeres con discapacidad
                            experimentan barreras específicas derivadas de la intersección entre género y discapacidad.
                        </p>
                        <p style="font-size: 1.1em; line-height: 1.8; margin-bottom: 20px;">
                            Desarrollamos programas de empoderamiento, sensibilización y defensa de derechos que visibilizan
                            la realidad de las mujeres con discapacidad y promueven su participación activa en todos los
                            ámbitos de la sociedad. Trabajamos por una igualdad real y efectiva.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--About section end-->


    <!--Servicios section-->
    <section id="services-ipm" class="section section-bg">
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
    <section id="portfolios-ipm" class="section">
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


    <!--Beneficios-->
    <section id="beneficios-ipm" class="section section-bg">
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



    <!--Contact section-->
    <section id="participa-ipm" class="section">
        <div class="main-container section-bg">
            <div class="inside-container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="contact-con">
                            <h2>¿Cómo puedo participar?</h2>
                            <p style="margin-bottom: 10px;">
                                Si quieres formar parte de nuestros proyectos de igualdad y empoderamiento de la mujer
                                con discapacidad, hay muchas formas de participar:
                            </p>
                            <ol style="line-height: 1.8; margin-bottom: 30px; padding-left: 20px;">
                                <li style="margin-bottom: 15px;">
                                    <strong>Únete a nuestros grupos:</strong> Participa en grupos de empoderamiento, talleres
                                    creativos y espacios de encuentro entre mujeres.
                                </li>
                                <li style="margin-bottom: 15px;">
                                    <strong>Forma parte del Parlamento:</strong> Si quieres participar activamente en la
                                    propuesta de políticas públicas, puedes sumarte al Parlamento Canario de la Mujer.
                                </li>
                                <li style="margin-bottom: 15px;">
                                    <strong>Difunde y sensibiliza:</strong> Ayúdanos a visibilizar la realidad de las mujeres
                                    con discapacidad compartiendo información y participando en campañas.
                                </li>
                                <li style="margin-bottom: 15px;">
                                    <strong>Contacta con nosotras:</strong> Rellena el formulario y te informaremos sobre
                                    las próximas actividades y cómo puedes involucrarte.
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
                                <input type="hidden" name="area" value="igualdad">
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
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-4" style="padding-top: 20px">
                        <h3 style="text-align: center; padding-bottom: 15px;
                            border-bottom: 1px solid #000; margin-bottom: 20px">Enlaces Rápidos</h3>

                        <div class="row">
                            <div class="col-6 pop-link" style="text-align: center">
                                <a class="" data-scroll href="#home">Inicio</a>
                                <a class="" data-scroll href="#about-ipm">Igualdad</a>
                                <a class="" data-scroll href="#services-ipm">Servicios</a>
                            </div>
                            <div class="col-6 pop-link" style="text-align: center">
                                <a class="" data-scroll href="#portfolios-ipm">Proyectos</a>
                                <a class="" data-scroll href="#beneficios-ipm">Beneficios</a>
                                <a class="" data-scroll href="#participa-ipm">Participa</a>
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
<script src="../js/main.js"></script>

</body>

</html>