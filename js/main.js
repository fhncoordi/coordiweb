let LaboffcanvaslastScroll;
let cookie_path = { "cookiePath": "/" };
let fonts = {
    '.lab-link-default': 'fontfamily_default',
    '.lab-font-inter': 'fontfamily_inter',
    '.lab-font-andika': 'fontfamily_andika',
    '.lab-font-fsme': 'fontfamily_fsme',
    '.lab-font-tiresias': 'fontfamily_tiresias',
    '.lab-font-opendyslexic': 'fontfamily_opendyslexic'
};

function setFont(fnt) {
    Cookies.set('fontfamily', fonts['.' + fnt], { expires: 7, path: cookie_path.cookiePath });
    jQuery('body').removeClass(Object.values(fonts).join(" ")).addClass(fonts['.' + fnt]);
}

function stickyResize() {
    let container = jQuery('#lab-header');
    if (jQuery('body').hasClass('sticky-bar') && !(container.hasClass('top-bar-over-header'))) {
        let bar = jQuery('#lab-logo-nav');
        if (bar.length) {
            container.css('padding-top', (bar.outerHeight()) + 'px');
        }
    }
}

function scrollToSection(event) {
    event.preventDefault();

    console.log('=== SCROLL DEBUG ===');
    let href = $(this).attr('href');
    console.log('1. href clickeado:', href);

    let $section = $(href);
    console.log('2. Sección encontrada:', $section.length > 0);

    if ($section.length === 0) {
        console.log('ERROR: La sección no existe en el DOM');
        return;
    }

    const offsetMap = {
        '#transparencia': 200,
        '#about': 150,
        '#portfolios': 150,
        '#news': 150,
        '#news-fi': 150,
        '#news-em': 150,
        '#news-ai': 150,
        '#news-ig': 150,
        '#news-oc': 150,
        '#news-pa': 150,
        '#colabora': 150,
        '#contact': 150,
        '#info': 300,
        '#features': 100,
        '#about-ai': 300,'#services-ai': 160,'#portfolios-ai': 150,'#beneficios-ai': 150,'#participa-ai': 100,
        '#about-em': 300,'#services-em': 160,'#portfolios-em': 150, '#beneficios-em': 150,'#participa-em': 100,
        '#about-fi': 300, '#services-fi': 160, '#portfolios-fi': 150, '#beneficios-fi': 150, '#participa-fi': 100,
        '#about-o': 300,  '#services-o': 160, '#portfolios-o': 150,'#beneficios-o': 150, '#participa-o': 100,
        '#about-pca': 300,  '#services-pca': 160, '#portfolios-pca': 150,'#beneficios-pca': 150, '#participa-pca': 100,
        '#about-ipm': 300,  '#services-ipm': 160, '#portfolios-ipm': 150,'#beneficios-ipm': 150, '#participa-ipm': 100,
        '#welcome': 300, '#transparency-info': 150, '#participa-tra': 100,
        '#identificacion-al':300, '#objeto-al': 160, '#condiciones-al': 150, '#propiedadintelectual-al':150,
        '#enlaces-al':150, '#protect-al': 150, '#legisla-al': 150,'#cook-al':150, '#cotacto-al': 50,
        '#compromiso': 180, '#accesi-info': 150, '#contacto-acc': 150,
        '#objeto-pc': 180, '#politica-cookies': 150, 'contacto-pc': 150,
        '#objeto-pp': 180, '#politica-privacidad': 150, 'contacto-pp': 150,
    };

    let offset = offsetMap[href] || 0;
    console.log('3. Offset aplicado:', offset);

    let sectionTop = $section.offset().top;
    console.log('4. Posición de la sección:', sectionTop);
    console.log('5. Scroll final:', sectionTop - offset);

    $(this).closest("li").addClass("current-menu-item").siblings().removeClass('current-menu-item');

    // Cerrar el menú lateral si está abierto
    if ($('body').hasClass('off-canvas-opened')) {
        LaboffcanvasToggleNav();
        $('#lab-offcanvas').css('right', '-330px');
    }

    $('html, body').animate({
        scrollTop: sectionTop - offset
    }, 500, function() {
        // Actualizar URL sin recargar página (limpia el &gsc.tab=0 de Google Search)
        // Solo actualiza el hash si el href es válido
        if (href && href.startsWith('#')) {
            // Limpiar cualquier parámetro extra que Google Custom Search pueda agregar
            const cleanHash = href.split('&')[0]; // Elimina todo después de &
            if (history.pushState) {
                history.pushState(null, null, cleanHash);
            } else {
                // Fallback para navegadores antiguos
                window.location.hash = cleanHash;
            }
        }
    });
}

function count(options) {
    let $this = $(this);
    options = $.extend({}, options || {}, $this.data('countToOptions') || {});
    $this.countTo(options);
}

function scrolledBar() {
    $('body').toggleClass('scrolled', $(window).scrollTop() >= 5);
}

function LaboffcanvasToggleNav() {
    let body = $('body');
    if (body.hasClass('off-canvas-opened')) {
        body.removeClass('off-canvas-opened');
        $('#lab-offcanvas, #lab-canvas').find('a').attr('tabindex', -1);
        setTimeout(function() {
            body.removeClass('overflow');
            body.scrollTop(LaboffcanvaslastScroll);
        }, 300);
    } else {
        LaboffcanvaslastScroll = $(window).scrollTop();
        body.addClass('off-canvas-opened overflow');
        $('#lab-offcanvas, #lab-canvas').find('a').attr('tabindex', 0);
    }
}

function expand() {
    let expandHeight = 0;
    let widget = jQuery('.lab-widget.lab-expand');
    if (widget.length) {
        let child = widget.children().first();
        let container = widget.closest('.lab-container');
        let container_wrapper = container.parent();
        container_wrapper.addClass('expand');
        expandHeight = container.height();
        child.css('min-height', expandHeight);
    }
}

function formatter(value, settings) {
    return value.toFixed(settings.decimals);
}

function onComplete(value) {
    return value;
}

jQuery(document).ready(function() {
    // Limpiar URL si Google Custom Search agregó &gsc.tab=0
    // Esto ocurre cuando se hace clic en enlaces externos como index.php#features desde otras páginas
    if (window.location.hash && window.location.hash.indexOf('&gsc.tab=') !== -1) {
        const cleanHash = window.location.hash.split('&')[0];
        if (history.replaceState) {
            // Usar replaceState en lugar de pushState para no crear entrada en historial
            history.replaceState(null, null, cleanHash);
        } else {
            // Fallback para navegadores antiguos
            window.location.hash = cleanHash;
        }

        // Hacer scroll a la sección después de limpiar la URL
        const $section = jQuery(cleanHash);
        if ($section.length > 0) {
            const offsetMap = {
                '#features': 100,
                '#about': 150,
                '#portfolios': 150,
                '#transparencia': 200,
                '#news': 150,
                '#news-fi': 150,
                '#news-em': 150,
                '#news-ai': 150,
                '#news-ig': 150,
                '#news-oc': 150,
                '#news-pa': 150,
                // Agregar otros offsets si es necesario
            };
            const offset = offsetMap[cleanHash] || 0;
            const sectionTop = $section.offset().top;
            setTimeout(function() {
                jQuery('html, body').animate({
                    scrollTop: sectionTop - offset
                }, 500);
            }, 100); // Pequeño delay para asegurar que la página esté cargada
        }
    }

    let ReadableFontButton = jQuery('.lab-font-readable');
    let font_normal = jQuery('.lab-font-normal');
    let font_larger = jQuery('.lab-font-larger');
    let font_smaller = jQuery('.lab-font-smaller');
    let link_underline = jQuery('.lab-link-underline');
    let open_acces = jQuery('#settings_close');
    let reset_all = jQuery('.lab-reset');
    let body = jQuery('body');

    body.toggleClass('font-readable', Cookies.get('readablefont') === 'yes');
    body.toggleClass('link-underline', Cookies.get('underline') === 'yes');

    let fsCount = 100;
    let cookieFont = Cookies.get('lab-font-size');

    if (cookieFont) {
        fsCount = parseInt(cookieFont);
        if (!body.hasClass('fsize' + fsCount)) {
            body.addClass('fsize' + fsCount);
        }
    } else {
        body.removeClass('fsize70 fsize80 fsize90 fsize100 fsize110 fsize120 fsize130');
    }

    font_larger.click(function(event) {
        event.preventDefault();
        jQuery('.lab-font-smaller').removeClass('active');
        if (fsCount < 130) {
            body.removeClass('fsize' + fsCount);
            fsCount = fsCount + 10;
            body.addClass('fsize' + fsCount);
            Cookies.set('lab-font-size', fsCount, { expires: 7, path: cookie_path.cookiePath });
            jQuery(this).addClass('active');
        }
    });

    font_smaller.click(function(event) {
        event.preventDefault();
        jQuery('.lab-font-larger').removeClass('active');
        if (fsCount > 70) {
            body.removeClass('fsize' + fsCount);
            fsCount = fsCount - 10;
            body.addClass('fsize' + fsCount);
            Cookies.set('lab-font-size', fsCount, { expires: 7, path: cookie_path.cookiePath });
            jQuery(this).addClass('active');
        }
    });

    font_normal.click(function(event) {
        event.preventDefault();
        fsCount = 100;
        body.removeClass('fsize70 fsize80 fsize90 fsize100 fsize110 fsize120 fsize130 font-readable link-underline');
        Cookies.remove('lab-font-size', { path: cookie_path.cookiePath });
        Cookies.remove('readablefont', { path: cookie_path.cookiePath });
        Cookies.remove('underline', { path: cookie_path.cookiePath });
    });

    ReadableFontButton.click(function(event) {
        event.preventDefault();
        Cookies.set('readablefont', 'yes', { expires: 7, path: cookie_path.cookiePath });
        if (!body.hasClass('font-readable')) {
            body.addClass('font-readable');
            jQuery(this).addClass('active');
        }

        jQuery(window).trigger('resize');
    });

    link_underline.click(function(event) {
        event.preventDefault();
        Cookies.set('underline', 'yes', { expires: 7, path: cookie_path.cookiePath });
        if (!body.hasClass('link-underline')) {
            body.addClass('link-underline');
            jQuery(this).addClass('active');
        }
    });

    reset_all.click(function() {
        body.removeClass('fsize70 fsize80 fsize90 fsize100 fsize110 fsize120 fsize130 font-readable link-underline fontfamily_inter fontfamily_andika fontfamily_fsme fontfamily_tiresias fontfamily_opendyslexic high-contrast dark-mode');
        fsCount = 100;
        Cookies.remove('lab-font-size', { path: cookie_path.cookiePath });
        Cookies.remove('readablefont', { path: cookie_path.cookiePath });
        Cookies.remove('underline', { path: cookie_path.cookiePath });
        Cookies.remove('fontfamily', { path: cookie_path.cookiePath });
        Cookies.remove('high-contrast', { path: cookie_path.cookiePath });
        Cookies.remove('dark-mode', { path: cookie_path.cookiePath });
        Cookies.remove('screen-reader', { path: cookie_path.cookiePath });

        // Detener síntesis de voz si está activa
        if (typeof window.speechSynthesis !== 'undefined') {
            window.speechSynthesis.cancel();
        }

        // Remover clases active de todos los botones
        jQuery('.lab-wcag-settings li button').removeClass('active');
    });

    jQuery(Object.keys(fonts).join(",") + "").click(function(event) {
        event.preventDefault();
        setFont(this.className);
    });

    body.addClass(Cookies.get('fontfamily'));

    // ========================================
    // NUEVAS FUNCIONALIDADES DE ACCESIBILIDAD
    // ========================================

    // Botones de alto contraste y modo oscuro
    let btn_high_contrast = jQuery('.lab-high-contrast');
    let btn_dark_mode = jQuery('.lab-dark-mode');

    // Cargar estados guardados al inicio
    if (Cookies.get('high-contrast') === 'yes') {
        body.addClass('high-contrast');
        btn_high_contrast.addClass('active');
    }

    if (Cookies.get('dark-mode') === 'yes') {
        body.addClass('dark-mode');
        btn_dark_mode.addClass('active');
    }

    // Toggle Alto Contraste
    btn_high_contrast.click(function(event) {
        event.preventDefault();

        if (body.hasClass('high-contrast')) {
            // Desactivar alto contraste
            body.removeClass('high-contrast');
            Cookies.remove('high-contrast', { path: cookie_path.cookiePath });
            jQuery(this).removeClass('active');
        } else {
            // Activar alto contraste
            body.addClass('high-contrast');
            Cookies.set('high-contrast', 'yes', { expires: 7, path: cookie_path.cookiePath });
            jQuery(this).addClass('active');

            // Desactivar modo oscuro si está activo (son mutuamente excluyentes)
            if (body.hasClass('dark-mode')) {
                body.removeClass('dark-mode');
                Cookies.remove('dark-mode', { path: cookie_path.cookiePath });
                btn_dark_mode.removeClass('active');
            }
        }
    });

    // Toggle Modo Oscuro
    btn_dark_mode.click(function(event) {
        event.preventDefault();

        if (body.hasClass('dark-mode')) {
            // Desactivar modo oscuro
            body.removeClass('dark-mode');
            Cookies.remove('dark-mode', { path: cookie_path.cookiePath });
            jQuery(this).removeClass('active');
        } else {
            // Activar modo oscuro
            body.addClass('dark-mode');
            Cookies.set('dark-mode', 'yes', { expires: 7, path: cookie_path.cookiePath });
            jQuery(this).addClass('active');

            // Desactivar alto contraste si está activo (son mutuamente excluyentes)
            if (body.hasClass('high-contrast')) {
                body.removeClass('high-contrast');
                Cookies.remove('high-contrast', { path: cookie_path.cookiePath });
                btn_high_contrast.removeClass('active');
            }
        }
    });

    // ========================================
    // LECTOR DE VOZ (Speech Synthesis API)
    // ========================================

    let btn_screen_reader = jQuery('.lab-screen-reader');
    let speechSynthesis = window.speechSynthesis;
    let isScreenReaderActive = false;

    // Verificar si el navegador soporta la API de síntesis de voz
    if ('speechSynthesis' in window) {

        // NO cargar estado guardado - siempre empieza desactivado
        // (Comentado: if (Cookies.get('screen-reader') === 'yes'))

        // Función para leer texto
        function speakText(text) {
            if (!isScreenReaderActive || !text || text.trim() === '') return;

            // Cancelar cualquier lectura anterior
            speechSynthesis.cancel();

            // Crear nueva instancia de speech
            let utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'es-ES'; // Español de España
            utterance.rate = 1.0; // Velocidad normal
            utterance.pitch = 1.0; // Tono normal
            utterance.volume = 1.0; // Volumen máximo

            speechSynthesis.speak(utterance);
        }

        // Toggle Lector de Voz
        btn_screen_reader.click(function(event) {
            event.preventDefault();

            if (isScreenReaderActive) {
                // Desactivar lector de voz
                isScreenReaderActive = false;
                speechSynthesis.cancel();
                Cookies.remove('screen-reader', { path: cookie_path.cookiePath });
                jQuery(this).removeClass('active');
                speakText('Lector de voz desactivado');
            } else {
                // Activar lector de voz
                isScreenReaderActive = true;
                Cookies.set('screen-reader', 'yes', { expires: 7, path: cookie_path.cookiePath });
                jQuery(this).addClass('active');
                speakText('Lector de voz activado. Pase el cursor sobre los elementos para escuchar su contenido');
            }
        });

        // Función para extraer texto legible de un elemento
        function getReadableText(element) {
            let text = '';

            // Prioridad 1: aria-label
            if (element.attr('aria-label')) {
                text = element.attr('aria-label');
            }
            // Prioridad 2: title
            else if (element.attr('title')) {
                text = element.attr('title');
            }
            // Prioridad 3: alt (para imágenes)
            else if (element.attr('alt')) {
                text = element.attr('alt');
            }
            // Prioridad 4: texto visible
            else {
                // Obtener texto sin elementos hijos
                text = element.clone()
                    .children()
                    .remove()
                    .end()
                    .text()
                    .trim();

                // Si no hay texto directo, obtener todo el texto
                if (!text) {
                    text = element.text().trim();
                }
            }

            return text;
        }

        // Elementos interactivos a los que aplicar el lector de voz
        let interactiveElements = 'a, button, input, select, textarea, [role="button"], [role="link"], h1, h2, h3, h4, h5, h6, p';

        // Event delegation para hover en elementos interactivos
        jQuery(document).on('mouseenter focusin', interactiveElements, function() {
            if (isScreenReaderActive) {
                let text = getReadableText(jQuery(this));
                if (text) {
                    speakText(text);
                }
            }
        });

        // Detener lectura al salir del elemento
        jQuery(document).on('mouseleave focusout', interactiveElements, function() {
            if (isScreenReaderActive) {
                speechSynthesis.cancel();
            }
        });

    } else {
        // Si el navegador no soporta la API, ocultar el botón
        btn_screen_reader.hide();
        console.warn('Este navegador no soporta la API de síntesis de voz');
    }

    // Indicadores visuales: marcar botones activos según cookies
    function updateActiveIndicators() {
        // Font size
        if (fsCount !== 100) {
            if (fsCount < 100) {
                jQuery('.lab-font-smaller').addClass('active');
            } else if (fsCount > 100) {
                jQuery('.lab-font-larger').addClass('active');
            }
        }

        // Readable font
        if (body.hasClass('font-readable')) {
            jQuery('.lab-font-readable').addClass('active');
        }

        // Link underline
        if (body.hasClass('link-underline')) {
            jQuery('.lab-link-underline').addClass('active');
        }

        // Font family
        jQuery(Object.keys(fonts).join(",")).each(function() {
            if (body.hasClass(fonts['.' + this.className.split(' ')[0]])) {
                jQuery(this).addClass('active');
            }
        });
    }

    // Llamar al inicio para marcar indicadores activos
    updateActiveIndicators();

    open_acces.click(function() {
        jQuery("body").toggleClass("opened-settings");

        setTimeout(function() {
            stickyResize();
        }, 200);
    });
	
	open_acces.keypress(function(event){
		if(event.keyCode == 13){
		jQuery("body").toggleClass("opened-settings");

        setTimeout(function() {
            stickyResize();
        }, 200);
		}
    });  
	 

    jQuery("#lab-main #lab-wcag-mobile a").click(function() {
        jQuery("#lab-main .lab-wcag-settings li:first-child ul li:nth-child(2) button").focus();
    });

    jQuery("#lab-bar-left #lab-logo a").focus(function() {
        if (jQuery(this).hasClass("logo-focused")) {
            jQuery("#lab-offcanvas-button .toggle-nav, #lab-canvas-button .lab-toggle-nav").focus();
        }
        jQuery(this).addClass("logo-focused");
    });

    if (jQuery(window).width() > 991) {
        jQuery("#lab-main #lab-wcag-mobile a, #lab-main .toggle-nav, #lab-offcanvas .toggle-nav.close, #lab-offcanvas .lab-search-box input.s, #lab-offcanvas .lab-search-box .button").attr('tabindex', '-1');
    }

    if ($('#portfolio').length > 0) {
        mixitup('#portfolio');
    }

    $('[data-scroll]').on('click', scrollToSection);

    // Cerrar menú lateral al hacer clic en cualquier enlace del menú
    // (excepto elementos dentro del formulario de búsqueda)
    $('#lab-offcanvas .nav-menu a:not(.menu-search-item a)').on('click', function(e) {
        // Cerrar el menú con un pequeño delay para mejor UX
        setTimeout(function() {
            if ($('body').hasClass('off-canvas-opened')) {
                LaboffcanvasToggleNav();
                $('#lab-offcanvas').css('right', '-330px');
            }
        }, 100);
    });

    // custom formatting example
    $('.count-number').data('countToOptions', {
        formatter: function(value, options) {
            return value.toFixed(options.decimals).replace(/\B(?=(?:\d{3})+(?!\d))/g, ',');
        }
    });

    // start all the timers
    $('.timer').each(count);

    scrolledBar();
    expand();
    $('.lab-section-space').closest('.lab-container').parent().addClass('nospace');
    $('.lab-section-space-top').closest('.lab-container').parent().addClass('nospace-top');
    $('.lab-section-space-bottom').closest('.lab-container').parent().addClass('nospace-bottom');
    let LabboxWidget = $('.row .lab-widget');
    LabboxWidget.first().addClass('first');
    LabboxWidget.last().addClass('last');

    let LabbackTop = $('#lab-back-top');
    LabbackTop.hide();
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            LabbackTop.fadeIn();
        } else {
            LabbackTop.fadeOut();
        }
    });
    LabbackTop.find('a').click(function() {
        $('body,html').animate({ scrollTop: 0 }, 800);
        return false;
    });
	
	LabbackTop.find('a').keypress(function(event){
	  if(event.keyCode == 13){
		$('body,html').animate({ scrollTop: 0 }, 800);
        return false;
	  }
    }); 

    $(document).on('click', function() {
        if ($('body').hasClass('off-canvas-opened')) {
            LaboffcanvasToggleNav();
        }
    });

    let labOffcanvas = $('#lab-offcanvas');
    labOffcanvas.find('a').attr('tabindex', -1);

    $('#lab-offcanvas-button, .toggle-nav .open').click(function(e) {
        e.stopPropagation();
        e.preventDefault();
        $(this).toggleClass('active');
        labOffcanvas.css('right', '0');
        LaboffcanvasToggleNav();
    });
    
    $('.toggle-nav-close').click(function(e) {
        labOffcanvas.css('right', '-330px');
    });

    labOffcanvas.click(function(e) {
        e.stopPropagation();
    });

    $('#lab-canvas').click(function(e) {
        e.stopPropagation();
    });

    $(window).scroll(function() {
        scrolledBar();
    });
});

(function($) {
    "use strict";

    $.fn.countTo = function(options) {
        options = options || {};

        return $(this).each(function() {
            // set options for current element
            let settings = $.extend({}, $.fn.countTo.defaults, {
                from: $(this).data('from'),
                to: $(this).data('to'),
                speed: $(this).data('speed'),
                refreshInterval: $(this).data('refresh-interval'),
                decimals: $(this).data('decimals')
            }, options);

            // how many times to update the value, and how much to increment the value on each update
            let loops = Math.ceil(settings.speed / settings.refreshInterval),
                increment = (settings.to - settings.from) / loops;

            // references & variables that will change with each update
            let self = this,
                $self = $(this),
                loopCount = 0,
                value = settings.from,
                data = $self.data('countTo') || {};

            $self.data('countTo', data);

            // if an existing interval can be found, clear it first
            if (data.interval) {
                clearInterval(data.interval);
            }

            data.interval = setInterval(updateTimer, settings.refreshInterval);

            // initialize the element with the starting value
            render(value);

            function updateTimer() {
                value += increment;
                loopCount++;

                render(value);

                if (typeof(settings.onUpdate) == 'function') {
                    settings.onUpdate.call(self, value);
                }

                if (loopCount >= loops) {
                    // remove the interval
                    $self.removeData('countTo');
                    clearInterval(data.interval);
                    value = settings.to;

                    if (typeof(settings.onComplete) == 'function') {
                        settings.onComplete.call(self, value);
                    }
                }
            }

            function render(value) {
                let formattedValue = settings.formatter.call(self, value, settings);
                $self.html(formattedValue + "+");
            }
        });
    };

    $.fn.countTo.defaults = {
        from: 0, // the number the element should start at
        to: 0, // the number the element should end at
        speed: 1000, // how long it should take to count between the target numbers
        refreshInterval: 100, // how often the element should be updated
        decimals: 0, // the number of decimal places to show
        formatter: formatter, // handler for formatting the value before rendering
        onUpdate: null, // callback method for every time the element is updated
        onComplete: onComplete // callback method for when the element finishes updating
    };

})(jQuery);


// ============================================
// MODAL DE BÚSQUEDA
// ============================================

jQuery(document).ready(function($) {
    const searchModal = $('#search-modal');
    const searchTrigger = $('#search-icon-trigger');
    const searchClose = $('#search-modal-close');
    const searchOverlay = $('.search-modal-overlay');

    // Abrir modal
    searchTrigger.on('click keypress', function(e) {
        if (e.type === 'click' || (e.type === 'keypress' && e.keyCode === 13)) {
            e.preventDefault();
            openSearchModal();
        }
    });

    // Manejar búsqueda desde formulario móvil
    $('#mobile-search-form').on('submit', function(e) {
        e.preventDefault();

        const searchQuery = $('#mobile-search-input').val().trim();

        if (searchQuery) {
            // Cerrar el menú lateral si está abierto
            if ($('body').hasClass('off-canvas-opened')) {
                LaboffcanvasToggleNav();
                $('#lab-offcanvas').css('right', '-330px');
            }

            // Redirigir a Google Custom Search con el query
            // Usamos el ID de CSE de Coordicanarias: 406aa1d8b8d294efe
            const cseId = '406aa1d8b8d294efe';
            const searchUrl = 'https://www.google.com/search?q=' + encodeURIComponent(searchQuery) + '&cx=' + cseId;
            window.location.href = searchUrl;
        }
    });

    // Cerrar modal con botón X
    searchClose.on('click keypress', function(e) {
        if (e.type === 'click' || (e.type === 'keypress' && e.keyCode === 13)) {
            e.preventDefault();
            closeSearchModal();
        }
    });

    // Cerrar modal al hacer clic en el overlay
    searchOverlay.on('click', function() {
        closeSearchModal();
    });

    // Cerrar modal con tecla ESC
    $(document).on('keydown', function(e) {
        if (e.keyCode === 27 && searchModal.hasClass('active')) {
            closeSearchModal();
        }
    });

    function openSearchModal() {
        searchModal.addClass('active');
        searchTrigger.attr('aria-expanded', 'true');

        // Enfocar el campo de búsqueda después de un pequeño delay
        setTimeout(function() {
            const searchInput = searchModal.find('.gsc-input input');
            if (searchInput.length) {
                searchInput.focus();
            }
        }, 300);

        // Prevenir scroll del body
        $('body').css('overflow', 'hidden');
    }

    function closeSearchModal() {
        searchModal.removeClass('active');
        searchTrigger.attr('aria-expanded', 'false');

        // Restaurar scroll del body
        $('body').css('overflow', '');

        // Devolver el foco al icono de búsqueda
        searchTrigger.focus();
    }
});


// ============================================
// BANNER DE COOKIES
// ============================================
jQuery(document).ready(function($) {
    const cookieBanner = $('#cookie-banner');
    const cookieAcceptBtn = $('#cookie-accept');
    const COOKIE_NAME = 'cookies_accepted';
    const COOKIE_EXPIRY_DAYS = 365;

    // Verificar si el usuario ya aceptó las cookies
    function checkCookieConsent() {
        const consent = Cookies.get(COOKIE_NAME);

        if (!consent) {
            // Si no hay consentimiento previo, mostrar el banner después de un pequeño delay
            setTimeout(function() {
                cookieBanner.addClass('show');
            }, 1000);
        }
    }

    // Aceptar cookies
    cookieAcceptBtn.on('click', function() {
        // Guardar el consentimiento en una cookie
        Cookies.set(COOKIE_NAME, 'true', {
            expires: COOKIE_EXPIRY_DAYS,
            path: '/',
            sameSite: 'Lax'
        });

        // Ocultar el banner con animación
        cookieBanner.removeClass('show');

        // Opcional: Remover del DOM después de la animación
        setTimeout(function() {
            cookieBanner.remove();
        }, 400);
    });

    // Inicializar al cargar la página
    checkCookieConsent();
});


// ============================================
// MENSAJES DE FORMULARIO DE CONTACTO
// ============================================
jQuery(document).ready(function($) {
    // Obtener parámetros de la URL
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Verificar si hay mensaje de éxito o error
    var success = getUrlParameter('success');
    var error = getUrlParameter('error');
    var formMessage = $('#form-message');

    if (success === '1') {
        // Mostrar mensaje de éxito
        formMessage.html('<strong>¡Mensaje enviado!</strong> Gracias por contactarnos. Te responderemos lo antes posible.');
        formMessage.addClass('success').show();

        // Limpiar el formulario
        $('#contact-form')[0].reset();

        // Scroll suave al mensaje
        $('html, body').animate({
            scrollTop: formMessage.offset().top - 100
        }, 500);

        // Limpiar la URL después de mostrar el mensaje
        setTimeout(function() {
            window.history.replaceState({}, document.title, window.location.pathname);
        }, 100);

    } else if (error) {
        // Mostrar mensaje de error
        var errorMessage = '';

        switch(error) {
            case 'metodo_invalido':
                errorMessage = 'Método de envío inválido. Por favor, usa el formulario.';
                break;
            case 'origen_invalido':
                errorMessage = 'Petición no autorizada. Por favor, recarga la página e inténtalo de nuevo.';
                break;
            case 'error_envio':
                errorMessage = 'Hubo un problema al enviar el mensaje. Por favor, inténtalo más tarde o contáctanos directamente.';
                break;
            default:
                errorMessage = error;
        }

        formMessage.html('<strong>Error:</strong> ' + errorMessage);
        formMessage.addClass('error').show();

        // Scroll suave al mensaje
        $('html, body').animate({
            scrollTop: formMessage.offset().top - 100
        }, 500);

        // Limpiar la URL después de mostrar el mensaje
        setTimeout(function() {
            window.history.replaceState({}, document.title, window.location.pathname);
        }, 100);
    }
});