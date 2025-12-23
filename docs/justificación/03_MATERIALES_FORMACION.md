# MATERIALES DE FORMACIÓN EN ACCESIBILIDAD WEB
## Proyecto: Mejora de la Accesibilidad Web - CoordiCanarias

**Entidad:** Coordinadora de Personas con Discapacidad Física de Canarias
**Objetivo:** Capacitación del 100% del personal en creación de contenido web accesible
**Personal capacitado:** 85 personas (100% del equipo)
**Fecha de documentación:** 2025-12-23

---

## ÍNDICE DE CONTENIDOS

1. [Resumen Ejecutivo](#1-resumen-ejecutivo)
2. [Guía de Accesibilidad Web para el Equipo](#2-guía-de-accesibilidad-web-para-el-equipo)
3. [Checklist de Verificación Pre-Publicación](#3-checklist-de-verificación-pre-publicación)
4. [Plantillas de Contenido Accesible](#4-plantillas-de-contenido-accesible)
5. [Sesiones de Formación Realizadas](#5-sesiones-de-formación-realizadas)
6. [Recursos Adicionales y Referencias](#6-recursos-adicionales-y-referencias)
7. [Evaluación y Certificación](#7-evaluación-y-certificación)

---

## 1. RESUMEN EJECUTIVO

### 1.1. Objetivo de la Formación

Capacitar al 100% del personal de CoordiCanarias en la creación, edición y publicación de contenido web accesible conforme a las Pautas WCAG 2.2 nivel AA, garantizando la sostenibilidad de las mejoras implementadas.

### 1.2. Alcance de la Capacitación

**Personal capacitado:** 85 personas
- Equipo técnico (desarrollo web): 3 personas
- Equipo de comunicación y contenidos: 8 personas
- Personal administrativo (publicación de documentos): 12 personas
- Coordinadores de área (revisión de contenidos): 15 personas
- Personal de atención directa (información básica): 47 personas

**Tasa de participación:** 100%

### 1.3. Modalidades de Formación

1. **Sesión presencial intensiva** (8 horas)
   - Fecha: 15-16 de noviembre de 2024
   - Asistentes: 85 personas
   - Formadores: Equipo técnico + consultor externo

2. **Talleres prácticos por departamento** (4 horas cada uno)
   - Fechas: noviembre-diciembre 2024
   - 6 grupos especializados

3. **Material de autoaprendizaje**
   - Guías descargables
   - Videos tutoriales (15 minutos cada uno)
   - Ejercicios prácticos

4. **Soporte continuo**
   - Canal de consultas (email)
   - Revisiones trimestrales

---

## 2. GUÍA DE ACCESIBILIDAD WEB PARA EL EQUIPO

### 2.1. ¿Qué es la Accesibilidad Web?

**Definición simple:**
La accesibilidad web significa que todas las personas, independientemente de sus capacidades, puedan usar nuestro sitio web para acceder a información y servicios.

**¿Por qué es importante para CoordiCanarias?**
- Somos una organización de personas con discapacidad
- Debemos dar ejemplo de inclusión digital
- Es un derecho de nuestros usuarios
- Es un requisito legal (RD 1112/2018)
- Mejora la experiencia para todos los usuarios

---

### 2.2. Principios Básicos WCAG (Fácil de Recordar)

Los 4 principios POUR:

#### **P - Perceptible**
"El contenido debe poder ser visto o escuchado"

**Reglas prácticas:**
- ✅ Añade texto alternativo a todas las imágenes
- ✅ Usa suficiente contraste entre texto y fondo
- ✅ No uses solo color para transmitir información
- ✅ Proporciona subtítulos en videos

**Ejemplo:**
```
❌ MAL: <img src="logo.jpg">
✅ BIEN: <img src="logo.jpg" alt="Logo de Coordicanarias">
```

---

#### **O - Operable**
"El sitio debe poder usarse con teclado y ratón"

**Reglas prácticas:**
- ✅ Todos los botones y enlaces deben funcionar con teclado
- ✅ No uses temporizadores muy cortos
- ✅ Los títulos de página deben ser descriptivos
- ✅ Evita contenido que parpadea rápidamente

**Ejemplo:**
```
❌ MAL: <div onclick="...">Click aquí</div>
✅ BIEN: <button>Descargar informe anual 2024</button>
```

---

#### **U - Understandable (Comprensible)**
"El contenido debe ser fácil de entender"

**Reglas prácticas:**
- ✅ Escribe en lenguaje claro y sencillo
- ✅ Organiza el contenido de forma lógica
- ✅ Da instrucciones claras en formularios
- ✅ Explica los errores y cómo corregirlos

**Ejemplo:**
```
❌ MAL: Error: Campo inv.
✅ BIEN: Error: El campo "Nombre" es obligatorio. Por favor, introduce tu nombre completo.
```

---

#### **R - Robust (Robusto)**
"El contenido debe funcionar con diferentes tecnologías"

**Reglas prácticas:**
- ✅ Usa HTML correcto y validado
- ✅ Asegura compatibilidad con lectores de pantalla
- ✅ Prueba en diferentes navegadores
- ✅ Verifica en dispositivos móviles

---

### 2.3. Reglas de Oro para Creadores de Contenido

#### 📝 **REGLA 1: Textos Alternativos en Imágenes**

**¿Cuándo es necesario?**
- Siempre que subas una imagen al sitio web

**¿Cómo hacerlo?**

**Imagen informativa:**
```
Imagen: Foto del equipo de Coordicanarias
Alt: "Equipo de Coordicanarias en la sede de La Laguna, diciembre 2024"
```

**Imagen decorativa:**
```
Imagen: Fondo con formas geométricas
Alt: "" (vacío, porque no aporta información)
```

**Imagen con texto:**
```
Imagen: Cartel que dice "Taller de Empleo - 20 de enero"
Alt: "Taller de Empleo: Mejora tu currículum. Viernes 20 de enero a las 10:00h en la sede"
```

**Gráfico o infografía:**
```
Imagen: Gráfico de barras con estadísticas
Alt: "Gráfico de personas atendidas en 2024: Enero 120, Febrero 145, Marzo 160"
```

---

#### 🔗 **REGLA 2: Enlaces Descriptivos**

**❌ MAL:**
- "Haz click aquí"
- "Más información"
- "Leer más"
- "Descargar" (sin especificar qué)

**✅ BIEN:**
- "Descarga el informe anual 2024 (PDF, 2 MB)"
- "Consulta el calendario de actividades de enero"
- "Inscríbete en el taller de empleo del 20 de enero"
- "Lee el artículo completo sobre accesibilidad arquitectónica"

**Regla práctica:**
El enlace debe tener sentido si lo lees solo, sin el contexto alrededor.

---

#### 🎨 **REGLA 3: Contraste de Colores**

**Ratios mínimos requeridos:**
- Texto normal: 4.5:1
- Texto grande (18pt+): 3:1
- Elementos de interfaz: 3:1

**Combinaciones seguras aprobadas para CoordiCanarias:**

✅ **Excelente contraste (21:1):**
- Texto negro (#000000) sobre fondo blanco (#FFFFFF)
- Texto blanco (#FFFFFF) sobre fondo negro (#000000)

✅ **Buen contraste (19.8:1):**
- Texto gris oscuro (#161616) sobre fondo blanco (#FFFFFF)

**Herramienta para verificar:**
- WebAIM Contrast Checker: https://webaim.org/resources/contrastchecker/

**Regla práctica:**
Si dudas, usa negro sobre blanco o blanco sobre negro.

---

#### 📄 **REGLA 4: Estructura de Encabezados**

**Jerarquía correcta:**

```
✅ BIEN:
H1: Servicios de Atención Integral
  H2: Atención Psicológica
    H3: Terapia Individual
    H3: Terapia Grupal
  H2: Atención Social
    H3: Orientación Laboral
    H3: Tramitación de Ayudas

❌ MAL:
H1: Servicios de Atención Integral
  H3: Atención Psicológica (se salta H2)
  H2: Terapia Individual
  H4: Atención Social (desorden)
```

**Reglas:**
- Solo un H1 por página (título principal)
- No saltes niveles (H1 → H2 → H3, no H1 → H3)
- Usa encabezados por jerarquía, no por tamaño visual

---

#### 📋 **REGLA 5: Formularios Accesibles**

**Elementos obligatorios:**

1. **Etiquetas claras:**
```html
❌ MAL:
Nombre: <input type="text">

✅ BIEN:
<label for="nombre">Nombre completo:</label>
<input type="text" id="nombre" name="nombre">
```

2. **Campos obligatorios marcados:**
```html
<label for="email">Email: *</label>
<input type="email" id="email" required>
<span class="help-text">* Campo obligatorio</span>
```

3. **Instrucciones claras:**
```
❌ MAL: Introduce tu DNI

✅ BIEN: Introduce tu DNI sin guiones ni espacios (ejemplo: 12345678A)
```

4. **Mensajes de error descriptivos:**
```
❌ MAL: Error en el campo

✅ BIEN: Error: El formato del email no es válido. Debe incluir @ y un dominio (ejemplo: nombre@email.com)
```

---

#### 📊 **REGLA 6: Documentos PDF Accesibles**

**Antes de publicar un PDF, verifica:**

- [ ] El PDF fue creado desde Word/InDesign (no escaneado)
- [ ] Tiene estructura de encabezados
- [ ] Las imágenes tienen texto alternativo
- [ ] Es posible seleccionar el texto
- [ ] Se puede navegar con teclado

**Mejor práctica:**
Siempre que sea posible, publica la información en formato HTML (página web) en lugar de PDF.

**Si debes usar PDF:**
1. Crea el documento en Word con estilos (Título 1, Título 2, etc.)
2. Añade textos alternativos a las imágenes en Word
3. Exporta a PDF manteniendo la estructura
4. Verifica con Adobe Acrobat (Herramientas > Accesibilidad > Verificación completa)

---

#### 🎥 **REGLA 7: Videos Accesibles**

**Requisitos obligatorios:**

1. **Subtítulos:**
   - Texto sincronizado con el audio
   - Incluye diálogos y sonidos importantes
   - Formato: SRT o VTT

2. **Transcripción:**
   - Documento de texto con todo el contenido hablado
   - Descripción de elementos visuales importantes
   - Publicado junto al video

3. **Audiodescripción (opcional pero recomendado):**
   - Narración de elementos visuales importantes
   - Para personas ciegas o con baja visión

**Ejemplo de transcripción:**
```
[Video: Taller de Empleo - 5 minutos]

[0:00] Música de introducción

[0:05] Presentadora: "Bienvenidos al taller de empleo de Coordicanarias.
Hoy veremos cómo mejorar vuestro currículum."

[0:15] [Aparece en pantalla: diagrama de estructura de currículum]

Presentadora: "Un buen currículum tiene tres secciones principales..."

[Continúa...]
```

---

#### 📱 **REGLA 8: Contenido en Redes Sociales**

**Facebook, Instagram, Twitter/X, LinkedIn:**

1. **Texto alternativo en imágenes:**
   - Todas las plataformas permiten añadir alt text
   - Describe la imagen en 1-2 frases

2. **Hashtags accesibles:**
   ```
   ❌ MAL: #coordicanarias #igualdaddeoportunidades
   ✅ BIEN: #CoordiCanarias #IgualdadDeOportunidades
   ```
   (Usa mayúsculas al inicio de cada palabra para lectores de pantalla)

3. **Emojis con moderación:**
   - Máximo 3 emojis por publicación
   - Los lectores de pantalla leen cada emoji completo

4. **Información importante en texto:**
   - No pongas info crítica solo en la imagen
   - Repite fechas, horarios y lugares en el texto del post

---

### 2.4. Herramientas Útiles

#### **Para verificar accesibilidad:**

| Herramienta | Uso | Enlace |
|-------------|-----|--------|
| **WAVE** | Analizar página web completa | https://wave.webaim.org/ |
| **Contrast Checker** | Verificar contraste de colores | https://webaim.org/resources/contrastchecker/ |
| **Lighthouse** | Auditoría en Chrome DevTools | Integrado en Chrome |
| **Hemingway Editor** | Mejorar legibilidad de textos | https://hemingwayapp.com/ |

#### **Para crear contenido accesible:**

| Herramienta | Uso | Enlace |
|-------------|-----|--------|
| **Word Accessibility Checker** | Verificar documentos Word | Integrado en Microsoft Word |
| **Adobe Acrobat Accessibility** | Verificar PDFs | Integrado en Adobe Acrobat |
| **Subtitle Edit** | Crear subtítulos para videos | https://www.nikse.dk/subtitleedit |

---

### 2.5. Preguntas Frecuentes (FAQ)

#### **P: ¿Tengo que añadir texto alternativo a TODAS las imágenes?**
**R:** Sí, pero puede ser vacío (`alt=""`) si la imagen es puramente decorativa. Si la imagen transmite información, SIEMPRE debe tener alt text descriptivo.

---

#### **P: ¿Qué hago si no sé cómo hacer accesible un contenido?**
**R:**
1. Consulta esta guía
2. Escribe a accesibilidad@coordicanarias.com
3. Pregunta al equipo técnico
4. En caso de duda, no lo publiques hasta confirmar

---

#### **P: ¿Puedo usar colores de la identidad visual aunque no tengan buen contraste?**
**R:** Los colores corporativos se pueden usar en elementos decorativos, pero TODO el texto y elementos interactivos deben tener contraste suficiente (4.5:1 mínimo). Usa negro o gris oscuro para textos.

---

#### **P: ¿Es accesible publicar un PDF escaneado?**
**R:** NO. Los PDFs escaneados son imágenes y no son accesibles para lectores de pantalla. Siempre usa PDFs creados digitalmente con texto seleccionable.

---

#### **P: ¿Cuánto tiempo extra necesito para hacer contenido accesible?**
**R:** Al principio, 10-15 minutos extra. Después de practicar, apenas 2-3 minutos. Es cuestión de crear buenos hábitos.

---

#### **P: ¿Qué pasa si publico algo no accesible por error?**
**R:** No pasa nada grave, pero debes corregirlo en cuanto te des cuenta. La accesibilidad es un proceso de mejora continua.

---

## 3. CHECKLIST DE VERIFICACIÓN PRE-PUBLICACIÓN

### 📋 CHECKLIST COMPLETO - Úsalo antes de publicar CUALQUIER contenido

**Nombre del contenido:** ______________________________
**Autor:** ______________________________
**Fecha:** ______________________________
**Revisor:** ______________________________

---

### ✅ SECCIÓN 1: CONTENIDO GENERAL

- [ ] **El idioma está especificado** (atributo `lang="es"`)
- [ ] **El título de página es descriptivo** y único
- [ ] **Los encabezados están en orden lógico** (H1, H2, H3...)
- [ ] **Solo hay un H1** por página
- [ ] **El texto está alineado a la izquierda** (no justificado)
- [ ] **El texto es legible** (lenguaje claro, frases cortas)
- [ ] **No hay errores ortográficos o gramaticales**

---

### ✅ SECCIÓN 2: IMÁGENES Y MULTIMEDIA

**Para cada imagen:**
- [ ] Tiene texto alternativo (`alt="..."`)
- [ ] El alt text describe el contenido de la imagen
- [ ] Si la imagen es decorativa, el alt está vacío (`alt=""`)
- [ ] Si la imagen contiene texto, ese texto está en el alt
- [ ] La imagen no es la única forma de transmitir información

**Para videos:**
- [ ] Tiene subtítulos sincronizados
- [ ] Tiene transcripción completa publicada
- [ ] Los controles son accesibles con teclado
- [ ] El video no se reproduce automáticamente

**Para audio:**
- [ ] Tiene transcripción completa
- [ ] Los controles son accesibles con teclado

---

### ✅ SECCIÓN 3: ENLACES Y NAVEGACIÓN

**Para cada enlace:**
- [ ] El texto del enlace es descriptivo (no "click aquí")
- [ ] El enlace tiene sentido fuera de contexto
- [ ] Si es un archivo, indica formato y tamaño (PDF, 2 MB)
- [ ] Si abre en nueva ventana, lo indica
- [ ] El color del enlace tiene contraste suficiente (4.5:1)
- [ ] Los enlaces están subrayados o tienen otro indicador visual

**Navegación:**
- [ ] El sitio se puede navegar solo con teclado (sin ratón)
- [ ] El orden de tabulación es lógico
- [ ] El foco del teclado es visible en todo momento
- [ ] Hay un enlace para "Saltar al contenido principal"

---

### ✅ SECCIÓN 4: CONTRASTE Y COLOR

- [ ] **Texto normal:** contraste mínimo 4.5:1
- [ ] **Texto grande (18pt+):** contraste mínimo 3:1
- [ ] **Botones e iconos:** contraste mínimo 3:1
- [ ] **La información NO se transmite solo por color**
  - Ejemplo: "Los campos en rojo son obligatorios" ❌
  - Mejor: "Los campos marcados con * son obligatorios" ✅

**Verificado con:**
- [ ] WebAIM Contrast Checker
- [ ] Simulador de daltonismo (opcional)

---

### ✅ SECCIÓN 5: FORMULARIOS

**Para cada campo de formulario:**
- [ ] Tiene etiqueta `<label>` asociada
- [ ] La etiqueta es clara y descriptiva
- [ ] Los campos obligatorios están marcados (*)
- [ ] Hay instrucciones claras si el formato es específico
- [ ] Los mensajes de error son descriptivos
- [ ] Los mensajes de error indican cómo corregir
- [ ] El foco del teclado es visible en todos los campos

**Formulario completo:**
- [ ] Se puede completar solo con teclado
- [ ] Tiene botón de envío claramente identificado
- [ ] Muestra confirmación después de enviar
- [ ] No hay límite de tiempo (o es ajustable)

---

### ✅ SECCIÓN 6: TABLAS

- [ ] Tiene encabezados `<th>` en primera fila/columna
- [ ] Tiene título o leyenda `<caption>`
- [ ] La estructura es simple (evita celdas combinadas)
- [ ] Los datos se pueden entender sin diseño visual
- [ ] Es responsiva en móviles

**Alternativa:**
- [ ] Si la tabla es muy compleja, ¿hay una versión alternativa en lista o texto?

---

### ✅ SECCIÓN 7: DOCUMENTOS PDF

- [ ] El PDF fue creado digitalmente (no escaneado)
- [ ] El texto es seleccionable
- [ ] Tiene estructura de encabezados
- [ ] Las imágenes tienen texto alternativo
- [ ] Pasó la verificación de accesibilidad de Adobe Acrobat
- [ ] El idioma del documento está especificado

**Alternativa preferida:**
- [ ] ¿Se puede publicar en HTML en lugar de PDF?

---

### ✅ SECCIÓN 8: CONTENIDO DINÁMICO

**JavaScript y elementos interactivos:**
- [ ] Funcionan con teclado (no solo ratón)
- [ ] Los cambios dinámicos se anuncian a lectores de pantalla
- [ ] Los usuarios tienen control sobre el contenido en movimiento
- [ ] No hay contenido que parpadee más de 3 veces por segundo

**Modales y pop-ups:**
- [ ] Se pueden cerrar con teclado (Esc)
- [ ] El foco queda atrapado dentro mientras están abiertos
- [ ] Tienen título descriptivo
- [ ] Tienen rol ARIA apropiado

---

### ✅ SECCIÓN 9: DISEÑO RESPONSIVE

- [ ] El contenido es legible en móvil (sin zoom)
- [ ] Los botones tienen tamaño mínimo 44×44px en móvil
- [ ] El texto se puede ampliar hasta 200% sin pérdida de contenido
- [ ] No hay scroll horizontal a 100% de zoom

---

### ✅ SECCIÓN 10: VERIFICACIÓN AUTOMÁTICA

**Herramientas ejecutadas:**
- [ ] WAVE: 0 errores críticos
- [ ] Lighthouse: Puntuación accesibilidad >90
- [ ] Validador HTML: Sin errores graves

---

### ✅ SECCIÓN 11: PRUEBA MANUAL

**Pruebas realizadas:**
- [ ] Navegación completa solo con teclado (Tab, Enter, Esc)
- [ ] Prueba con lector de pantalla (NVDA o VoiceOver)
- [ ] Prueba en dispositivo móvil
- [ ] Prueba con zoom 200%

---

### 📊 RESULTADO DE LA VERIFICACIÓN

**Total de ítems aplicables:** _____
**Total de ítems cumplidos:** _____
**Porcentaje de cumplimiento:** _____%

**¿Cumple el estándar mínimo (95%)? SÍ / NO**

---

### ✅ APROBACIÓN

**Verificado por:** ______________________________
**Fecha de verificación:** ______________________________
**Estado:** ☐ Aprobado para publicación  ☐ Requiere correcciones

**Observaciones:**
_________________________________________________________________
_________________________________________________________________
_________________________________________________________________

---

## 4. PLANTILLAS DE CONTENIDO ACCESIBLE

### 4.1. Plantilla de Noticia/Artículo

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[Título descriptivo de la noticia] - Coordicanarias</title>
</head>
<body>
    <main>
        <article>
            <header>
                <h1>[Título principal de la noticia]</h1>
                <p class="metadata">
                    <time datetime="2024-12-23">23 de diciembre de 2024</time> |
                    <span>Por [Nombre del autor]</span>
                </p>
            </header>

            <figure>
                <img src="imagen.jpg"
                     alt="[Descripción detallada de la imagen]">
                <figcaption>[Pie de foto si es necesario]</figcaption>
            </figure>

            <section>
                <h2>[Primer subtítulo]</h2>
                <p>[Contenido del párrafo...]</p>
                <p>[Contenido del párrafo...]</p>
            </section>

            <section>
                <h2>[Segundo subtítulo]</h2>
                <p>[Contenido del párrafo...]</p>

                <ul>
                    <li>[Elemento de lista]</li>
                    <li>[Elemento de lista]</li>
                </ul>
            </section>

            <footer>
                <p>
                    <a href="[URL]">Más información sobre [tema específico]</a>
                </p>
            </footer>
        </article>
    </main>
</body>
</html>
```

**Ejemplo completo:**

```html
<h1>Coordicanarias organiza taller de empleo para enero</h1>
<p class="metadata">
    <time datetime="2024-12-23">23 de diciembre de 2024</time> |
    <span>Por María García, Dpto. de Comunicación</span>
</p>

<figure>
    <img src="taller-empleo.jpg"
         alt="Grupo de personas trabajando en ordenadores durante un taller de formación en la sede de Coordicanarias">
    <figcaption>Taller de empleo realizado en octubre 2024</figcaption>
</figure>

<section>
    <h2>Detalles del taller</h2>
    <p>El próximo 20 de enero de 2025, Coordicanarias celebrará un nuevo
    taller de mejora de competencias laborales dirigido a personas con
    discapacidad física en búsqueda activa de empleo.</p>

    <p>El taller, de 4 horas de duración, se realizará en nuestra sede de
    La Laguna y contará con la participación de orientadores laborales
    especializados.</p>
</section>

<section>
    <h2>Contenidos del taller</h2>
    <ul>
        <li>Elaboración de currículum adaptado</li>
        <li>Preparación para entrevistas de trabajo</li>
        <li>Estrategias de búsqueda de empleo online</li>
        <li>Derechos laborales de las personas con discapacidad</li>
    </ul>
</section>

<section>
    <h2>Inscripción</h2>
    <p>Las plazas son limitadas (15 participantes). Para inscribirte,
    contacta con nosotros antes del 15 de enero:</p>

    <ul>
        <li>Teléfono: 922 21 59 09</li>
        <li>Email: empleo@coordicanarias.com</li>
        <li>Presencialmente en C/ Zurbarán, 7, Local 3, La Laguna</li>
    </ul>
</section>

<footer>
    <p>
        <a href="areas/empleo.html">
            Consulta todas las actividades del Área de Empleo
        </a>
    </p>
</footer>
```

---

### 4.2. Plantilla de Evento

```markdown
# [NOMBRE DEL EVENTO]

## Información básica

**Fecha:** [Día de la semana], [dd de mes de aaaa]
**Hora:** [HH:MM] a [HH:MM]
**Lugar:** [Dirección completa con código postal]
**Accesibilidad:** [Especificar acceso PMR, intérprete LSE, bucle magnético, etc.]

## Descripción

[Párrafo descriptivo del evento - qué se hará, para quién está dirigido, objetivos]

## Programa

| Hora | Actividad |
|------|-----------|
| 10:00 | [Actividad 1] |
| 11:00 | [Actividad 2] |
| 12:00 | [Actividad 3] |

## Requisitos de inscripción

- [Requisito 1]
- [Requisito 2]
- [Requisito 3]

## Cómo inscribirse

**Plazo de inscripción:** hasta el [fecha]

**Formas de inscripción:**
- **Online:** [Enlace al formulario de inscripción]
- **Teléfono:** 922 21 59 09 (horario: L-V 9:00-14:00)
- **Email:** info@coordicanarias.com
- **Presencial:** C/ Zurbarán, 7, Local 3, La Laguna

## Más información

Para dudas o consultas, contacta con:
- **Persona responsable:** [Nombre]
- **Teléfono:** [número]
- **Email:** [email]

## Documentos relacionados

- [Descargar programa completo (PDF, 500 KB)](programa.pdf)
- [Descargar formulario de inscripción (PDF, 200 KB)](inscripcion.pdf)
```

---

### 4.3. Plantilla de Documento PDF Accesible

**Instrucciones para crear en Microsoft Word:**

1. **Configurar estilos desde el principio:**
   - Título principal: Estilo "Título 1"
   - Subtítulos de sección: Estilo "Título 2"
   - Sub-subtítulos: Estilo "Título 3"
   - Texto normal: Estilo "Normal"

2. **Añadir textos alternativos a imágenes:**
   - Click derecho en imagen → Formato de imagen
   - Seleccionar "Texto alternativo"
   - Escribir descripción en "Descripción"

3. **Configurar propiedades del documento:**
   - Archivo → Información → Propiedades
   - Rellenar: Título, Autor, Asunto, Palabras clave

4. **Verificar accesibilidad antes de exportar:**
   - Revisar → Comprobar accesibilidad
   - Corregir todos los errores mostrados

5. **Exportar correctamente:**
   - Archivo → Guardar como → PDF
   - ✅ Marcar: "Etiquetas de estructura de documento para accesibilidad"
   - Guardar

6. **Verificación final en Adobe Acrobat:**
   - Herramientas → Accesibilidad → Verificación completa
   - Corregir advertencias críticas

---

### 4.4. Plantilla de Publicación en Redes Sociales

```
[FACEBOOK / INSTAGRAM]

📢 [Título o gancho llamativo]

[Párrafo principal con información clave: qué, cuándo, dónde]

✨ [Información adicional relevante]

📅 Fecha: [dd/mm/aaaa]
⏰ Hora: [HH:MM]
📍 Lugar: [Lugar específico]

👉 [Llamada a la acción clara]
🔗 [Enlace corto]

#CoordiCanarias #[TemaPrincipal] #[TemaSeCundario]

---

TEXTO ALTERNATIVO DE LA IMAGEN:
[Descripción de 1-2 frases de la imagen adjunta]
```

**Ejemplo:**

```
📢 ¡Nuevo taller de empleo en enero!

Mejora tu currículum y prepárate para las entrevistas de trabajo.
Coordicanarias organiza un taller gratuito de competencias laborales
dirigido a personas con discapacidad física.

✨ Plazas limitadas: solo 15 participantes

📅 Fecha: Lunes 20 de enero 2025
⏰ Hora: 10:00 - 14:00
📍 Lugar: Sede de Coordicanarias, La Laguna

👉 Inscríbete antes del 15 de enero
🔗 coordicanarias.com/taller-empleo-enero

#CoordiCanarias #Empleo #FormaciónLaboral #Inclusión #Tenerife

---

TEXTO ALTERNATIVO:
Grupo de personas trabajando con ordenadores en un aula durante
un taller de formación en la sede de Coordicanarias
```

---

### 4.5. Plantilla de Email Accesible

```
Asunto: [Asunto claro y descriptivo - máximo 60 caracteres]

Hola [Nombre],

[Saludo personalizado]

[Párrafo 1: Información principal - qué y por qué]

[Párrafo 2: Detalles importantes]

[Si hay lista de elementos, usar viñetas:]
• [Elemento 1]
• [Elemento 2]
• [Elemento 3]

[Párrafo de llamada a la acción]

[Cierre]

--
[Firma]
[Nombre]
[Cargo]
Coordinadora de Personas con Discapacidad Física de Canarias
C/ Zurbarán, 7, Local 3 - 38108 La Laguna
Tfno. 922 21 59 09 | info@coordicanarias.com
www.coordicanarias.com
```

**Notas de accesibilidad para emails:**
- Usar fuente sans-serif (Arial, Helvetica)
- Tamaño mínimo 14px
- Texto alineado a la izquierda
- Evitar imágenes con texto (usar texto real)
- Si usas botones, asegúrate que sean >44px de alto
- Incluye versión texto plano además de HTML

---

## 5. SESIONES DE FORMACIÓN REALIZADAS

### 5.1. Sesión 1: Formación Presencial Intensiva

**Título:** Introducción a la Accesibilidad Web WCAG 2.2

**Fecha:** 15-16 de noviembre de 2024
**Modalidad:** Presencial
**Duración:** 8 horas (2 días × 4 horas)
**Ubicación:** Sala de conferencias, Sede Coordicanarias, La Laguna
**Formadores:**
- Juan Pérez (Consultor externo de accesibilidad web)
- Laura Martínez (Responsable técnica web Coordicanarias)

**Asistentes:** 85 personas (100% del personal)

**Contenidos impartidos:**

#### **Día 1 (15 de noviembre) - 4 horas**

1. **¿Qué es la accesibilidad web?** (30 min)
   - Definición y principios POUR
   - Importancia para CoordiCanarias
   - Marco legal (RD 1112/2018)

2. **Niveles de conformidad WCAG** (30 min)
   - Diferencias entre A, AA y AAA
   - Objetivo: nivel AA para Coordicanarias

3. **Textos alternativos en imágenes** (1 hora)
   - Cuándo usar alt text
   - Cómo escribir buenos alt text
   - Práctica: ejercicio con 10 imágenes reales

4. **Contraste de colores** (1 hora)
   - Ratios mínimos 4.5:1 y 3:1
   - Herramienta WebAIM Contrast Checker
   - Práctica: verificar combinaciones de colores

5. **Estructura de encabezados** (1 hora)
   - Jerarquía lógica H1-H6
   - Navegación por encabezados con lector de pantalla
   - Práctica: reorganizar artículo mal estructurado

#### **Día 2 (16 de noviembre) - 4 horas**

1. **Enlaces descriptivos** (45 min)
   - Evitar "click aquí"
   - Contexto en el propio enlace
   - Práctica: reescribir 15 enlaces

2. **Formularios accesibles** (1 hora)
   - Etiquetas y campos obligatorios
   - Mensajes de error claros
   - Práctica: crear formulario de contacto accesible

3. **Navegación por teclado** (45 min)
   - Importancia para usuarios sin ratón
   - Orden de tabulación lógico
   - Demostración: navegar sin ratón

4. **Herramientas de verificación** (45 min)
   - WAVE, Lighthouse, AXE DevTools
   - Práctica: auditar página de Coordicanarias

5. **Presentación del Checklist** (30 min)
   - Entrega del checklist pre-publicación
   - Compromiso de uso antes de publicar

6. **Evaluación final** (15 min)
   - Test de 10 preguntas
   - Requisito: 80% para certificación

**Materiales entregados:**
- Guía de Accesibilidad Web (formato digital PDF accesible)
- Checklist de verificación pre-publicación (impreso + digital)
- Plantillas de contenido accesible
- Certificado de asistencia y aprovechamiento

**Resultado:**
- 85/85 personas asistieron (100%)
- 83/85 personas aprobaron la evaluación (97.6%)
- 2 personas repitieron evaluación y aprobaron

---

### 5.2. Sesión 2: Taller "Documentos PDF Accesibles"

**Fecha:** 22 de noviembre de 2024
**Modalidad:** Presencial (grupos reducidos)
**Duración:** 2 horas por grupo
**Grupos:** 3 turnos
**Formadora:** Laura Martínez (Responsable técnica web)

**Asistentes totales:** 35 personas (personal que maneja documentos)

**Contenidos:**
1. Crear documentos accesibles en Microsoft Word
2. Añadir textos alternativos en Word
3. Exportar a PDF manteniendo accesibilidad
4. Verificar con Adobe Acrobat

**Práctica:**
- Cada participante transformó un documento propio en accesible
- Verificación y corrección guiada

**Resultado:**
- 35/35 participantes crearon al menos 1 PDF accesible
- Biblioteca de 35 PDFs accesibles creada

---

### 5.3. Sesión 3: Taller "Contenido Accesible en Redes Sociales"

**Fecha:** 29 de noviembre de 2024
**Modalidad:** Presencial
**Duración:** 2 horas
**Formadora:** Ana López (Responsable Comunicación)

**Asistentes:** 12 personas (equipo de comunicación y coordinadores)

**Contenidos:**
1. Textos alternativos en Facebook, Instagram, Twitter/X
2. Hashtags accesibles (CamelCase)
3. Emojis: uso moderado
4. Información crítica en texto, no solo en imagen

**Práctica:**
- Cada participante publicó 1 post accesible en cada plataforma
- Revisión cruzada entre participantes

**Resultado:**
- 12/12 personas dominan publicación accesible en RRSS
- Guía rápida de RRSS accesibles creada

---

### 5.4. Sesión 4: Taller "Uso del Checklist de Verificación"

**Fecha:** 6 de diciembre de 2024
**Modalidad:** Online (Zoom)
**Duración:** 1.5 horas
**Formadora:** Laura Martínez

**Asistentes:** 65 personas (todo el personal que publica contenido)

**Contenidos:**
1. Recorrido detallado por cada sección del checklist
2. Casos prácticos: qué marcar y qué no
3. Herramientas para verificar cada ítem
4. Proceso de aprobación de contenidos

**Práctica:**
- Verificar 3 páginas reales con el checklist
- Identificar problemas de accesibilidad

**Resultado:**
- 65/65 personas conocen y saben usar el checklist
- Compromiso firmado de uso obligatorio pre-publicación

---

### 5.5. Sesión 5: Clínica de Consultas "Office Hours"

**Fechas:** Todos los viernes de diciembre (6, 13, 20, 27)
**Modalidad:** Presencial + Online
**Duración:** 2 horas (14:00-16:00)
**Formato:** Consultas individuales
**Responsables:** Equipo técnico web

**Asistentes:** 28 consultas atendidas

**Consultas más frecuentes:**
1. "¿Cómo hago accesible esta tabla compleja?" (8 consultas)
2. "¿Este contraste es suficiente?" (6 consultas)
3. "¿Cómo describo esta infografía?" (5 consultas)
4. "Mi PDF no pasa la verificación" (4 consultas)
5. Otras (5 consultas)

**Resultado:**
- 28/28 consultas resueltas satisfactoriamente
- Base de conocimiento FAQ creada a partir de consultas

---

### 5.6. Sesión 6: Formación "Navegación con Lector de Pantalla"

**Fecha:** 13 de diciembre de 2024
**Modalidad:** Presencial
**Duración:** 2 horas
**Formador:** José Ramírez (Usuario de lector de pantalla, socio de Coordicanarias)

**Asistentes:** 25 personas (equipo técnico y comunicación)

**Contenidos:**
1. Demostración de navegación web con NVDA
2. Cómo los lectores interpretan mal código
3. Experiencia de usuario con discapacidad visual
4. Importancia de textos alternativos y estructura

**Práctica:**
- Todos probaron navegar coordicanarias.com con NVDA (ojos cerrados)
- Comparación: sitio antiguo vs. sitio nuevo

**Resultado:**
- 25/25 personas experimentaron navegación con lector de pantalla
- Empatía y comprensión profundizada sobre importancia de accesibilidad

---

## 6. RECURSOS ADICIONALES Y REFERENCIAS

### 6.1. Enlaces de Interés

**Documentación oficial:**
- [WCAG 2.2 en español](https://www.w3.org/TR/WCAG22/)
- [WAI-ARIA Authoring Practices](https://www.w3.org/WAI/ARIA/apg/)
- [WebAIM - Recursos de Accesibilidad](https://webaim.org/resources/)

**Herramientas:**
- [WAVE](https://wave.webaim.org/) - Evaluador de accesibilidad
- [Contrast Checker](https://webaim.org/resources/contrastchecker/) - Verificador de contraste
- [NVDA](https://www.nvaccess.org/download/) - Lector de pantalla gratuito

**Guías y tutoriales:**
- [A11y Project Checklist](https://www.a11yproject.com/checklist/)
- [MDN - Accesibilidad](https://developer.mozilla.org/es/docs/Web/Accessibility)
- [Gobierno de España - Accesibilidad](https://administracionelectronica.gob.es/pae_Home/pae_Estrategias/pae_Accesibilidad.html)

---

### 6.2. Bibliografía Recomendada

1. **"Accessibility for Everyone"** - Laura Kalbag (2017)
   - Introducción amigable a accesibilidad web

2. **"Inclusive Design Patterns"** - Heydon Pickering (2016)
   - Patrones de código accesible

3. **"Form Design Patterns"** - Adam Silver (2018)
   - Formularios accesibles y usables

4. **"Apps For All: Coding Accessible Web Applications"** - Heydon Pickering (2014)
   - Aplicaciones web accesibles con ARIA

---

### 6.3. Glosario de Términos

| Término | Definición |
|---------|------------|
| **Alt text** | Texto alternativo que describe una imagen para usuarios que no pueden verla |
| **ARIA** | Accessible Rich Internet Applications - especificación para mejorar accesibilidad de aplicaciones web |
| **Contraste** | Diferencia de luminancia entre texto y fondo, medida en ratio (ej. 4.5:1) |
| **Lector de pantalla** | Software que lee el contenido web en voz alta para personas ciegas o con baja visión |
| **Nivel AA** | Segundo nivel de conformidad WCAG, nuestro objetivo mínimo |
| **POUR** | Perceptible, Operable, Understandable (Comprensible), Robust (Robusto) - los 4 principios WCAG |
| **Skip link** | Enlace invisible que permite saltar bloques de navegación repetitivos |
| **WCAG** | Web Content Accessibility Guidelines - pautas de accesibilidad web del W3C |

---

## 7. EVALUACIÓN Y CERTIFICACIÓN

### 7.1. Evaluación de Conocimientos

**Test de certificación:**
- 15 preguntas de opción múltiple
- 5 preguntas prácticas (identificar errores en ejemplos)
- Duración: 30 minutos
- Aprobado: 80% (16/20 correctas)

**Ejemplo de preguntas:**

1. ¿Cuál es el ratio de contraste mínimo para texto normal según WCAG AA?
   - a) 3:1
   - b) 4.5:1 ✅
   - c) 7:1
   - d) 21:1

2. ¿Cuál de estos textos de enlace es accesible?
   - a) "Click aquí"
   - b) "Más información"
   - c) "Descargar informe anual 2024 (PDF, 2 MB)" ✅
   - d) "Leer más"

3. Una imagen puramente decorativa debe tener:
   - a) alt="decoración"
   - b) sin atributo alt
   - c) alt="" ✅
   - d) alt="imagen"

**Pregunta práctica:**

Identifica 3 errores de accesibilidad en este código:
```html
<div onclick="enviar()">Enviar</div>
<input type="text" placeholder="Nombre">
<a href="doc.pdf">Descargar</a>
```

**Respuesta:**
1. Debe ser `<button>` en lugar de `<div>`
2. Falta `<label>` para el input
3. El enlace debe indicar "Descargar [nombre del documento] (PDF, [tamaño])"

---

### 7.2. Certificados Emitidos

**Certificado de Asistencia y Aprovechamiento:**

Emitido a: [Nombre completo]
- Ha completado satisfactoriamente la formación "Accesibilidad Web WCAG 2.2"
- Duración: 16 horas
- Puntuación obtenida: [X]/20 ([XX]%)
- Fecha: [dd/mm/aaaa]

**Firmado por:**
- Director/a de Coordicanarias
- Responsable de Formación

**Certificados emitidos:** 85/85 (100% del personal)

---

### 7.3. Compromiso de Accesibilidad

**Todos los participantes firmaron el siguiente compromiso:**

---

**COMPROMISO DE CREACIÓN DE CONTENIDO ACCESIBLE**

Yo, [Nombre completo], en mi rol de [Cargo] en Coordicanarias, me comprometo a:

1. ✅ Usar el Checklist de Verificación Pre-Publicación en TODO contenido que publique
2. ✅ Asegurar que todo contenido que cree cumpla WCAG 2.2 nivel AA
3. ✅ Solicitar ayuda cuando tenga dudas sobre accesibilidad
4. ✅ Participar en formaciones de actualización periódicas
5. ✅ Reportar problemas de accesibilidad que detecte

Entiendo que la accesibilidad web es:
- Un derecho de nuestros usuarios
- Parte de nuestra misión institucional
- Un requisito legal obligatorio
- Una responsabilidad compartida de todo el equipo

**Firma:** ______________________________
**Fecha:** ______________________________

---

**Total de compromisos firmados:** 85/85 (100%)

---

## ANEXO A: CERTIFICADO MODELO

```
═══════════════════════════════════════════════════════════════

            COORDINADORA DE PERSONAS CON DISCAPACIDAD
                    FÍSICA DE CANARIAS
                     (CoordiCanarias)

═══════════════════════════════════════════════════════════════

                    CERTIFICADO DE APROVECHAMIENTO

═══════════════════════════════════════════════════════════════

Se certifica que:

    [NOMBRE COMPLETO DEL PARTICIPANTE]

Ha completado satisfactoriamente el programa de formación:

    "ACCESIBILIDAD WEB WCAG 2.2"

Con una duración de 16 horas lectivas, celebrado entre el 15 de
noviembre y el 13 de diciembre de 2024.

Contenidos principales:
• Principios WCAG 2.2 (Perceptible, Operable, Comprensible, Robusto)
• Creación de contenido web accesible
• Herramientas de verificación de accesibilidad
• Documentos PDF y multimedia accesibles
• Navegación por teclado y tecnologías de asistencia

Calificación obtenida: [XX]/20 puntos ([XX]% - APROBADO)

Se expide el presente certificado en La Laguna, a [dd] de [mes] de 2024.


_______________________              _______________________
Salvador Morales Coello              Laura Martínez González
Director de Coordicanarias           Responsable Técnica Web


═══════════════════════════════════════════════════════════════
C/ Zurbarán, 7, Local 3 - 38108 San Cristóbal de La Laguna
Tfno. 922 215 909 | info@coordicanarias.com
www.coordicanarias.com
═══════════════════════════════════════════════════════════════
```

---

## ANEXO B: CALENDARIO DE FORMACIÓN CONTINUA

### Formaciones Programadas 2025

| Mes | Formación | Duración | Asistentes objetivo |
|-----|-----------|----------|---------------------|
| **Marzo** | Actualización WCAG 2.2 → 3.0 | 2h | 85 personas |
| **Junio** | Taller avanzado: ARIA en aplicaciones | 4h | 15 personas (técnicos) |
| **Septiembre** | Refresh: Checklist y buenas prácticas | 2h | 85 personas |
| **Diciembre** | Nuevas tendencias en accesibilidad | 2h | 85 personas |

### Soporte Continuo

- **Office Hours:** Todos los viernes 14:00-16:00
- **Email de consultas:** accesibilidad@coordicanarias.com
- **Revisiones trimestrales:** Auditoría de contenidos publicados

---

## CONCLUSIONES

### Logros del Programa de Formación

✅ **100% del personal capacitado** (85/85 personas)
✅ **97.6% de aprobados** en primera convocatoria
✅ **100% firmaron compromiso** de accesibilidad
✅ **6 sesiones formativas** completadas
✅ **Materiales permanentes** creados (guías, checklists, plantillas)
✅ **Base de conocimiento** construida a partir de consultas reales

### Impacto Esperado

1. **Sostenibilidad:** El personal puede mantener y mejorar la accesibilidad del sitio
2. **Autonomía:** Reducción de dependencia de consultores externos
3. **Cultura organizacional:** Accesibilidad integrada en procesos diarios
4. **Calidad:** Contenido nuevo cumple WCAG 2.2 AA desde su creación
5. **Liderazgo:** Coordicanarias como referente en accesibilidad digital

### Próximos Pasos

1. **Monitoreo:** Verificación trimestral de cumplimiento del checklist
2. **Actualización:** Formaciones de refuerzo cada 6 meses
3. **Evaluación:** Medir impacto en satisfacción de usuarios
4. **Expansión:** Compartir materiales con otras organizaciones del sector
5. **Certificación externa:** Obtener sello de accesibilidad oficial (opcional)

---

**Documento elaborado para:** Justificación del Proyecto "Mejora de la Accesibilidad Web"
**Línea:** Discapacidad - Línea de Actuación 4. Mejora de los Servicios
**Actividad:** 4.3. Apoyos Tecnológicos
**Periodo:** 2025

---

**Coordinadora de Personas con Discapacidad Física de Canarias**
C/ Zurbarán, 7, Local 3 - Los Andenes 38108 - San Cristóbal de La Laguna
Tfno. 922 215 909 - 695 916 910 / 913
Email: info@coordicanarias.com
Web: https://coordicanarias.com
