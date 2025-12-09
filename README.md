# Coordicanarias - Sitio Web Accesible

Sitio web institucional de la Coordinadora de Personas con Discapacidad Física de Canarias (COORDICANARIAS), diseñado con enfoque en accesibilidad WCAG 2.2 Nivel AA.

## Características Principales

- **Accesibilidad**: WCAG 2.2 Nivel AA
- **Responsive**: Compatible con todos los dispositivos
- **Tecnologías**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Navegación por teclado**: Compatible con lectores de pantalla
- **Menú de accesibilidad**: Ajustes de fuente, tamaño y contraste

## Estructura del Proyecto

```
coordiweb/
├── index.html                 # Página principal
├── transparencia.html         # Portal de transparencia con sistema de tabs
├── accesibilidad.html        # Declaración de accesibilidad
├── areas/                     # Páginas de áreas específicas
│   ├── alegal.html           # Aviso legal con sistema de tabs
│   ├── aintegral.html        # Atención integral
│   ├── empleo.html           # Empleo
│   ├── forminno.html         # Formación e innovación
│   ├── igualdadpm.html       # Igualdad
│   ├── ocio.html             # Ocio
│   └── participaca.html      # Participación
├── test_pages/               # Páginas de prueba
├── css/                      # Hojas de estilo
├── js/                       # Scripts JavaScript
├── images/                   # Imágenes y recursos
└── audit_web_antigua/        # Documentación de auditoría

```

## Última Actualización

### Sistema de Tabs en Aviso Legal

Se implementó un sistema de tabs interactivo en `areas/alegal.html` siguiendo el patrón de `transparencia.html`:

**Estructura de Tabs:**
1. 📄 Condiciones de uso
2. © Propiedad intelectual
3. 🔗 Enlaces externos
4. 🛡️ Protección de datos
5. 🍪 Cookies
6. ⚖️ Legislación aplicable

**Características:**
- Navegación simplificada a 4 secciones: Inicio, Objeto, Aviso Legal, Contacto
- Iconos Font Awesome para mejor UX
- Navegación por teclado (flechas, Home, End)
- Estilos consistentes con transparencia.html
- Atributos ARIA para accesibilidad
- Responsive para móviles

**Tecnologías Utilizadas:**
- Bootstrap 5 tabs
- Font Awesome 6.4.0
- jQuery 3.7.1
- CSS3 transitions

## Mejoras de Accesibilidad Implementadas

- ✅ Atributo `lang="es"` correcto en todas las páginas
- ✅ Elemento semántico `<main>` en todas las páginas
- ✅ Declaración correcta de nivel de conformidad WCAG 2.2 AA
- ✅ Menú de accesibilidad traducido al español
- ✅ Navegación por landmarks para lectores de pantalla
- ✅ Focus visible en todos los elementos interactivos
- ✅ Respeto a `prefers-reduced-motion`

## Beneficiarios

- **2,700** beneficiarios directos
- **6,000** beneficiarios indirectos
- **8,700** beneficiarios totales

## Contacto

- **Web**: https://coordicanarias.com
- **Email**: info@coordicanarias.com
- **Teléfono**: 922 21 59 09
- **Dirección**: C/ Zurbarán, 7, Local 3, Los Andenes 38108, San Cristóbal de La Laguna, Santa Cruz de Tenerife

## Licencia

© 2024 Coordicanarias. Todos los derechos reservados.
