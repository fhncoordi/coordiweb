<?php
/**
 * Panel de Accesibilidad - Componente reutilizable
 *
 * Este componente contiene el panel de herramientas de accesibilidad
 * que se muestra en todas las páginas del sitio.
 *
 * Incluye:
 * - Alto contraste
 * - Modo oscuro
 * - Lector de voz
 * - Tipos de fuente (Open Sans, Inter, Andika, FSMe, Tiresias, OpenDyslexic)
 * - Ajustes de fuente (tamaño, subrayar enlaces, modo legible)
 * - Restablecer todo
 *
 * Uso: <?php include __DIR__ . '/../php/components/panel-accesibilidad.php'; ?>
 *      o ajustar la ruta según la ubicación del archivo que lo incluye
 */
?>
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
                            <button class="lab-high-contrast" aria-label="Alto contraste: aumenta el contraste entre texto y fondo para mejorar la visibilidad" aria-pressed="false">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                    <path d="M 16 3 C 8.832031 3 3 8.832031 3 16 C 3 23.167969 8.832031 29 16 29 C 23.167969 29 29 23.167969 29 16 C 29 8.832031 23.167969 3 16 3 Z M 16 5 C 22.085938 5 27 9.914063 27 16 C 27 22.085938 22.085938 27 16 27 L 16 5 Z" />
                                </svg>
                                <span class="lab-only">Alto Contraste</span>
                                <span class="lab-button-label">Alto Contraste</span>
                            </button>
                        </li>
                        <li>
                            <button class="lab-dark-mode" aria-label="Modo oscuro: cambia el fondo a colores oscuros para reducir la fatiga visual" aria-pressed="false">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                    <path d="M 14.78125 3.03125 C 8.046875 3.777344 2.75 9.519531 2.75 16.5 C 2.75 23.953125 8.796875 30 16.25 30 C 23.230469 30 28.972656 24.703125 29.71875 17.96875 C 27.582031 19.273438 25.054688 20.03125 22.34375 20.03125 C 14.890625 20.03125 8.84375 13.984375 8.84375 6.53125 C 8.84375 5.121094 9.0625 3.761719 9.46875 2.46875 C 6.761719 3.488281 4.433594 5.421875 2.902344 7.984375 C 2.113281 9.390625 1.625 11.003906 1.625 12.75 C 1.625 18.132813 5.992188 22.5 11.375 22.5 C 16.757813 22.5 21.125 18.132813 21.125 12.75 C 21.125 11.003906 20.636719 9.390625 19.847656 7.984375 C 18.316406 5.421875 15.988281 3.488281 13.28125 2.46875 Z" />
                                </svg>
                                <span class="lab-only">Modo Oscuro</span>
                                <span class="lab-button-label">Modo Oscuro</span>
                            </button>
                        </li>
                        <li>
                            <button class="lab-screen-reader" aria-label="Lector de voz: lee en voz alta el contenido al pasar el cursor sobre los elementos" aria-pressed="false">
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
                            <button class="lab-link-default" aria-label="Fuente Open Sans: tipografía predeterminada del sitio, clara y legible">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                    <path d="M 15 6 L 8 26 L 10 26 L 12.09375 20 L 19.90625 20 L 22 26 L 24 26 L 17 6 Z M 16 8.84375 L 19.1875 18 L 12.8125 18 Z" />
                                </svg>
                                <span class="lab-only">Open Sans</span>
                            </button>
                        </li>
                        <li>
                            <button class="lab-font-inter" aria-label="Fuente Inter: tipografía optimizada para pantallas con mejor legibilidad">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                    <path d="M 15 6 L 8 26 L 10 26 L 12.09375 20 L 19.90625 20 L 22 26 L 24 26 L 17 6 Z M 16 8.84375 L 19.1875 18 L 12.8125 18 Z" />
                                </svg>
                                <span class="lab-only">Inter</span>
                            </button>
                        </li>
                        <li>
                            <button class="lab-font-andika" aria-label="Fuente Andika: diseñada para lectores principiantes y personas con dislexia">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                    <path d="M 15 6 L 8 26 L 10 26 L 12.09375 20 L 19.90625 20 L 22 26 L 24 26 L 17 6 Z M 16 8.84375 L 19.1875 18 L 12.8125 18 Z" />
                                </svg>
                                <span class="lab-only">Andika</span>
                            </button>
                        </li>
                        <li>
                            <button class="lab-font-fsme" aria-label="Fuente FS Me: diseñada para personas con dificultades de aprendizaje">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                    <path d="M 15 6 L 8 26 L 10 26 L 12.09375 20 L 19.90625 20 L 22 26 L 24 26 L 17 6 Z M 16 8.84375 L 19.1875 18 L 12.8125 18 Z" />
                                </svg>
                                <span class="lab-only">FSMe</span>
                            </button>
                        </li>
                        <li>
                            <button class="lab-font-tiresias" aria-label="Fuente Tiresias: diseñada para personas con baja visión">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                    <path d="M 15 6 L 8 26 L 10 26 L 12.09375 20 L 19.90625 20 L 22 26 L 24 26 L 17 6 Z M 16 8.84375 L 19.1875 18 L 12.8125 18 Z" />
                                </svg>
                                <span class="lab-only">Tiresias</span>
                            </button>
                        </li>
                        <li>
                            <button class="lab-font-opendyslexic" aria-label="Fuente OpenDyslexic: diseñada específicamente para personas con dislexia">
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
                            <button class="lab-font-smaller" aria-label="Reducir tamaño del texto para ver más contenido en pantalla">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                    <path d="M 16 3 C 8.832031 3 3 8.832031 3 16 C 3 23.167969 8.832031 29 16 29 C 23.167969 29 29 23.167969 29 16 C 29 8.832031 23.167969 3 16 3 Z M 16 5 C 22.085938 5 27 9.914063 27 16 C 27 22.085938 22.085938 27 16 27 C 9.914063 27 5 22.085938 5 16 C 5 9.914063 9.914063 5 16 5 Z M 10 15 L 10 17 L 22 17 L 22 15 Z" />
                                </svg>
                                <span class="lab-only">Reducir</span>
                            </button>
                        </li>
                        <li>
                            <button class="lab-font-larger" aria-label="Aumentar tamaño del texto para mejor lectura">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                    <path d="M 16 3 C 8.832031 3 3 8.832031 3 16 C 3 23.167969 8.832031 29 16 29 C 23.167969 29 29 23.167969 29 16 C 29 8.832031 23.167969 3 16 3 Z M 16 5 C 22.085938 5 27 9.914063 27 16 C 27 22.085938 22.085938 27 16 27 C 9.914063 27 5 22.085938 5 16 C 5 9.914063 9.914063 5 16 5 Z M 15 10 L 15 15 L 10 15 L 10 17 L 15 17 L 15 22 L 17 22 L 17 17 L 22 17 L 22 15 L 17 15 L 17 10 Z" />
                                </svg>
                                <span class="lab-only">Aumentar</span>
                            </button>
                        </li>
                        <li>
                            <button class="lab-link-underline" aria-label="Subrayar todos los enlaces para identificarlos más fácilmente">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                    <path d="M 21.75 4 C 20.078125 4 18.492188 4.660156 17.3125 5.84375 L 15.84375 7.3125 C 14.660156 8.496094 14 10.078125 14 11.75 C 14 12.542969 14.152344 13.316406 14.4375 14.03125 L 16.0625 12.40625 C 15.859375 11.109375 16.253906 9.714844 17.25 8.71875 L 18.71875 7.25 C 19.523438 6.445313 20.613281 6 21.75 6 C 22.886719 6 23.945313 6.445313 24.75 7.25 C 26.410156 8.910156 26.410156 11.621094 24.75 13.28125 L 23.28125 14.75 C 22.476563 15.554688 21.386719 16 20.25 16 C 20.027344 16 19.808594 15.976563 19.59375 15.9375 L 17.96875 17.5625 C 18.683594 17.847656 19.457031 18 20.25 18 C 21.921875 18 23.507813 17.339844 24.6875 16.15625 L 26.15625 14.6875 C 27.339844 13.503906 28 11.921875 28 10.25 C 28 8.578125 27.339844 7.027344 26.15625 5.84375 C 24.976563 4.660156 23.421875 4 21.75 4 Z M 19.28125 11.28125 L 11.28125 19.28125 L 12.71875 20.71875 L 20.71875 12.71875 Z M 11.75 14 C 10.078125 14 8.492188 14.660156 7.3125 15.84375 L 5.84375 17.3125 C 4.660156 18.496094 4 20.078125 4 21.75 C 4 23.421875 4.660156 24.972656 5.84375 26.15625 C 7.023438 27.339844 8.578125 28 10.25 28 C 11.921875 28 13.507813 27.339844 14.6875 26.15625 L 16.15625 24.6875 C 17.339844 23.503906 18 21.921875 18 20.25 C 18 19.457031 17.847656 18.683594 17.5625 17.96875 L 15.9375 19.59375 C 16.140625 20.890625 15.746094 22.285156 14.75 23.28125 L 13.28125 24.75 C 12.476563 25.554688 11.386719 26 10.25 26 C 9.113281 26 8.054688 25.554688 7.25 24.75 C 5.589844 23.089844 5.589844 20.378906 7.25 18.71875 L 8.71875 17.25 C 9.523438 16.445313 10.613281 16 11.75 16 C 11.972656 16 12.191406 16.023438 12.40625 16.0625 L 14.03125 14.4375 C 13.316406 14.152344 12.542969 14 11.75 14 Z" />
                                </svg>
                                <span class="lab-only">Subrayar enlaces</span>
                            </button>
                        </li>
                        <li>
                            <button class="lab-font-readable" aria-label="Modo legible: aumenta el espaciado entre líneas para facilitar la lectura">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                    <path d="M 8 6 L 8 8 L 15 8 L 15 22 L 17 22 L 17 8 L 24 8 L 24 6 Z M 10 21.5 L 5.625 25 L 10 28.5 L 10 26 L 22 26 L 22 28.5 L 26.375 25 L 22 21.5 L 22 24 L 10 24 Z" />
                                </svg>
                                <span class="lab-only">Legible</span>
                            </button>
                        </li>
                        <li>
                            <button class="lab-font-normal" aria-label="Restaurar fuente y tamaño de texto predeterminados">
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
                    <button class="lab-reset" aria-label="Restablecer todo: vuelve a la configuración original del sitio">
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
