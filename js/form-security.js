/**
 * JavaScript para seguridad de formularios
 * Coordicanarias - 2025
 *
 * Maneja reCAPTCHA v3 y validaciones del lado del cliente
 */

(function() {
    'use strict';

    // Configuración
    const RECAPTCHA_SITE_KEY = window.RECAPTCHA_SITE_KEY || '';

    /**
     * Inicializa la seguridad del formulario
     */
    function initFormSecurity() {
        // Buscar todos los formularios de contacto
        const forms = document.querySelectorAll('form[action*="enviar_correo.php"]');

        forms.forEach(function(form) {
            // Agregar listener al submit
            form.addEventListener('submit', handleFormSubmit);

            // Opcional: Pre-cargar reCAPTCHA cuando el usuario interactúa
            addInteractionListeners(form);
        });
    }

    /**
     * Maneja el envío del formulario
     */
    function handleFormSubmit(event) {
        event.preventDefault();

        const form = event.target;
        const submitButton = form.querySelector('input[type="submit"], button[type="submit"]');

        // Deshabilitar botón para evitar doble envío
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.dataset.originalValue = submitButton.value || submitButton.textContent;
            if (submitButton.tagName === 'INPUT') {
                submitButton.value = 'Verificando...';
            } else {
                submitButton.textContent = 'Verificando...';
            }
        }

        // Validaciones básicas del lado del cliente
        if (!validateForm(form)) {
            resetSubmitButton(submitButton);
            return false;
        }

        // Si reCAPTCHA está configurado, ejecutarlo
        if (RECAPTCHA_SITE_KEY && typeof grecaptcha !== 'undefined') {
            executeRecaptcha(form, submitButton);
        } else {
            // Si no hay reCAPTCHA, enviar directamente
            form.submit();
        }
    }

    /**
     * Ejecuta reCAPTCHA v3
     */
    function executeRecaptcha(form, submitButton) {
        grecaptcha.ready(function() {
            grecaptcha.execute(RECAPTCHA_SITE_KEY, { action: 'submit' })
                .then(function(token) {
                    // Insertar el token en el campo oculto
                    const tokenField = form.querySelector('#recaptchaToken, input[name="recaptcha_token"]');
                    if (tokenField) {
                        tokenField.value = token;
                    }

                    // Enviar el formulario
                    form.submit();
                })
                .catch(function(error) {
                    console.error('Error en reCAPTCHA:', error);
                    alert('Error al verificar seguridad. Por favor, intenta de nuevo.');
                    resetSubmitButton(submitButton);
                });
        });
    }

    /**
     * Validaciones básicas del lado del cliente
     */
    function validateForm(form) {
        // Verificar campos requeridos
        const nombre = form.querySelector('[name="txtName"]');
        const email = form.querySelector('[name="txtEmail"]');
        const mensaje = form.querySelector('[name="txtMsg"]');

        if (!nombre || !nombre.value.trim()) {
            showError(form, 'Por favor, ingresa tu nombre');
            nombre && nombre.focus();
            return false;
        }

        if (!email || !email.value.trim()) {
            showError(form, 'Por favor, ingresa tu email');
            email && email.focus();
            return false;
        }

        // Validar formato de email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email.value)) {
            showError(form, 'Por favor, ingresa un email válido');
            email && email.focus();
            return false;
        }

        if (!mensaje || !mensaje.value.trim()) {
            showError(form, 'Por favor, escribe un mensaje');
            mensaje && mensaje.focus();
            return false;
        }

        // Verificar que el honeypot esté vacío (por si acaso)
        const honeypot = form.querySelector('[name="website"]');
        if (honeypot && honeypot.value !== '') {
            // Bot detectado - no mostrar error, solo prevenir envío
            console.warn('Honeypot activado');
            return false;
        }

        return true;
    }

    /**
     * Muestra un mensaje de error
     */
    function showError(form, message) {
        // Buscar o crear contenedor de mensajes
        let messageContainer = form.querySelector('#contactMessage');

        if (!messageContainer) {
            messageContainer = document.createElement('div');
            messageContainer.id = 'contactMessage';
            messageContainer.style.cssText = 'padding: 15px; margin-bottom: 20px; border-radius: 5px;';
            form.insertBefore(messageContainer, form.firstChild);
        }

        messageContainer.textContent = message;
        messageContainer.style.display = 'block';
        messageContainer.style.backgroundColor = '#f8d7da';
        messageContainer.style.color = '#721c24';
        messageContainer.style.border = '1px solid #f5c6cb';

        // Auto-ocultar después de 5 segundos
        setTimeout(function() {
            messageContainer.style.display = 'none';
        }, 5000);
    }

    /**
     * Restaura el botón de envío a su estado original
     */
    function resetSubmitButton(button) {
        if (!button) return;

        button.disabled = false;
        if (button.dataset.originalValue) {
            if (button.tagName === 'INPUT') {
                button.value = button.dataset.originalValue;
            } else {
                button.textContent = button.dataset.originalValue;
            }
        }
    }

    /**
     * Agrega listeners para detectar interacción del usuario
     * Esto permite pre-cargar reCAPTCHA para mejorar la experiencia
     */
    function addInteractionListeners(form) {
        let interacted = false;

        function markInteracted() {
            if (!interacted) {
                interacted = true;
                // Aquí podrías pre-cargar recursos si fuera necesario
            }
        }

        // Detectar cuando el usuario empieza a escribir
        const inputs = form.querySelectorAll('input, textarea');
        inputs.forEach(function(input) {
            input.addEventListener('focus', markInteracted, { once: true });
        });
    }

    /**
     * Maneja mensajes de error/éxito de la URL
     */
    function handleUrlMessages() {
        const urlParams = new URLSearchParams(window.location.search);
        const success = urlParams.get('success');
        const error = urlParams.get('error');

        const messageContainer = document.getElementById('contactMessage');
        if (!messageContainer) return;

        if (success) {
            messageContainer.textContent = '¡Mensaje enviado correctamente! Te responderemos pronto.';
            messageContainer.style.display = 'block';
            messageContainer.style.backgroundColor = '#d4edda';
            messageContainer.style.color = '#155724';
            messageContainer.style.border = '1px solid #c3e6cb';
            messageContainer.style.padding = '15px';
            messageContainer.style.marginBottom = '20px';
            messageContainer.style.borderRadius = '5px';
        } else if (error) {
            let errorMessage = 'Ocurrió un error al enviar el mensaje.';

            // Personalizar mensaje según el tipo de error
            if (error.includes('seguridad')) {
                errorMessage = decodeURIComponent(error);
            } else if (error === 'error_envio') {
                errorMessage = 'Error al enviar el mensaje. Por favor, intenta de nuevo.';
            } else if (error.includes('obligatorio')) {
                errorMessage = decodeURIComponent(error);
            }

            messageContainer.textContent = errorMessage;
            messageContainer.style.display = 'block';
            messageContainer.style.backgroundColor = '#f8d7da';
            messageContainer.style.color = '#721c24';
            messageContainer.style.border = '1px solid #f5c6cb';
            messageContainer.style.padding = '15px';
            messageContainer.style.marginBottom = '20px';
            messageContainer.style.borderRadius = '5px';
        }

        // Limpiar la URL después de mostrar el mensaje
        if (success || error) {
            setTimeout(function() {
                const url = new URL(window.location);
                url.searchParams.delete('success');
                url.searchParams.delete('error');
                window.history.replaceState({}, '', url.toString());
            }, 100);
        }
    }

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initFormSecurity();
            handleUrlMessages();
        });
    } else {
        initFormSecurity();
        handleUrlMessages();
    }

})();
