# INFORME DE AUDITORÍA DE ACCESIBILIDAD WEB
## Proyecto: Mejora de la Accesibilidad Web - CoordiCanarias

**Sitio web auditado:** https://coordicanarias.com/
**Entidad:** Coordinadora de Personas con Discapacidad Física de Canarias
**Fecha de auditoría:** 2024
**Páginas auditadas:** 81 páginas
**Estado:** Auditoría inicial (diagnóstico previo a mejoras)

---

## 1. RESUMEN EJECUTIVO

La presente auditoría de accesibilidad se realizó sobre el sitio web coordicanarias.com y sus 81 páginas para evaluar el cumplimiento de las **Pautas de Accesibilidad para el Contenido Web (WCAG 2.2)** y determinar las necesidades de mejora.

### Hallazgos Principales

- **100% de las páginas** presentaban barreras de accesibilidad
- **Incumplimiento generalizado** de criterios WCAG nivel A (crítico)
- **Errores de contraste masivos**: Promedio de 118 errores por página
- **Problemas estructurales recurrentes** en navegación y formularios

### Conclusión

El sitio web presentaba **deficiencias críticas** que impedían el acceso a personas con discapacidad, requiriendo una intervención integral para alcanzar conformidad WCAG 2.2 nivel AA.

---

## 2. METODOLOGÍA

### Herramientas Utilizadas

- **WAVE** (Web Accessibility Evaluation Tool) - Análisis automatizado
- **Lighthouse** - Auditoría de rendimiento y accesibilidad
- **AXE DevTools** - Análisis técnico en navegador
- **Evaluación manual** - Pruebas con lectores de pantalla y navegación por teclado

### Alcance

- Dominio principal: https://coordicanarias.com/
- Subpáginas auditadas: 81 páginas
- Secciones evaluadas: 13 (Inicio, Conócenos, Empleo, Transparencia, etc.)
- Estándar de referencia: WCAG 2.2 niveles A, AA y AAA

---

## 3. ESTADÍSTICAS CONSOLIDADAS

### 3.1. Datos Agregados

Basándose en el análisis de las 81 páginas:

| Métrica                      | Total en el sitio |
|------------------------------|-------------------|
| **Errores estructurales**    | ~540              |
| **Errores de contraste**     | ~9,500            |
| **Alertas de accesibilidad** | ~380              |

### 3.2. Distribución por Sección

| Sección                 | Páginas | Errores Promedio | Contraste Promedio | Alertas Promedio |
|-------------------------|---------|------------------|--------------------|------------------|
| Transparencia           | 8       | 10.4             | 116.6              | 14.1             |
| Filmoteca               | 4       | 9.0              | 124.5              | 5.0              |
| Ocio                    | 4       | 7.8              | 117.8              | 4.0              |
| Mujer e Igualdad        | 7       | 7.1              | 119.7              | 4.6              |
| Participación y Cultura | 5       | 7.2              | 118.2              | 4.2              |
| Atención Integral       | 16      | 6.5              | 117.9              | 4.6              |
| Sensibilización         | 2       | 6.5              | 118.0              | 4.0              |
| Empleo                  | 7       | 6.4              | 123.4              | 4.6              |
| Inicio                  | 1       | 6.0              | 9.0                | 5.0              |
| Formación               | 3       | 6.0              | 119.3              | 4.0              |
| Colabora                | 2       | 5.0              | 112.0              | 5.5              |
| Conócenos               | 2       | 7.0              | 115.5              | 4.0              |

### 3.3. Páginas con Mayor Necesidad de Intervención

| Página                                | Errores | Contraste | Alertas |
|---------------------------------------|---------|-----------|---------|
| Transparencia / Información Histórica | 18      | 118       | 5       |
| Transparencia / Información Económica | 14      | 192       | 75      |
| Filmoteca / SOY                       | 12      | 126       | 5       |
| Atención Integral / Tayda             | 12      | 124       | 5       |
| Ocio / Inclusión Senior               | 12      | 121       | 4       |

---

## 4. PRINCIPALES BARRERAS IDENTIFICADAS

### 4.1. Problemas Críticos (presentes en >95% de páginas)

#### 1. Errores de Contraste de Color
- **Incidencia:** 100% de las páginas
- **Problema:** Fondo blanco con texto naranja claro, iconos magenta sobre blanco
- **Criterio WCAG:** 1.4.3 (Nivel AA) - NO CUMPLE
- **Impacto:** Usuarios con baja visión y daltonismo no pueden leer el contenido

#### 2. Botones Vacíos o Sin Texto
- **Incidencia:** 100% de las páginas
- **Problema:** Elementos interactivos sin etiqueta textual
- **Criterio WCAG:** 4.1.2, 2.1.1 (Nivel A) - NO CUMPLE
- **Impacto:** Imposible usar con teclado o lectores de pantalla

#### 3. Enlaces Sin Texto Alternativo
- **Incidencia:** 100% de las páginas
- **Problema:** Links no descriptivos o vacíos
- **Criterio WCAG:** 2.4.4 (Nivel A) - NO CUMPLE
- **Impacto:** Usuarios de lectores de pantalla no saben destino del enlace

#### 4. Formularios Sin Etiquetas
- **Incidencia:** 90% de las páginas con formularios
- **Problema:** Campos `<input>` sin elementos `<label>` asociados
- **Criterio WCAG:** 1.3.1, 4.1.2 (Nivel A) - NO CUMPLE
- **Impacto:** Formularios completamente inaccesibles

#### 5. Imágenes Sin Texto Alternativo
- **Incidencia:** 75% de las páginas
- **Problema:** Falta atributo `alt` descriptivo
- **Criterio WCAG:** 1.1.1 (Nivel A) - NO CUMPLE
- **Impacto:** Contenido visual invisible para usuarios ciegos

#### 6. Estructura de Encabezados Deficiente
- **Incidencia:** 95% de las páginas
- **Problema:** Headers vacíos, mal ordenados o ausentes
- **Criterio WCAG:** 1.3.1, 2.4.6 (Nivel AA) - NO CUMPLE
- **Impacto:** Imposible navegar por estructura del documento

### 4.2. Problemas Recurrentes Adicionales

- Enlaces no descriptivos ("leer más", "aquí")
- Menús ARIA mal implementados
- PDFs inaccesibles sin alternativa HTML
- Videos sin subtítulos ni transcripciones
- Texto justificado (dificulta lectura para dislexia)
- Atributos redundantes con texto alternativo

---

## 5. CLASIFICACIÓN POR NIVEL DE PRIORIDAD WCAG 2.2

### 5.1. Nivel A - CRÍTICO (Bloquea el acceso)

**Criterios incumplidos que requieren corrección inmediata:**

| Criterio | Descripción                                        | Páginas Afectadas |
|----------|----------------------------------------------------|-------------------|
| 1.1.1    | Contenido no textual (imágenes sin alt)            | 75%               |
| 1.3.1    | Información y relaciones (estructura semántica)    | 100%              |
| 2.1.1    | Teclado (navegación por teclado imposible)         | 100%              |
| 2.4.4    | Propósito de los enlaces (enlaces no descriptivos) | 100%              |
| 3.2.4    | Identificación consistente                         | 95%               |
| 4.1.2    | Nombre, función, valor (botones y formularios)     | 90%               |

**Impacto:** Sin corrección, usuarios con discapacidad visual, motriz y cognitiva **NO pueden usar el sitio**.

### 5.2. Nivel AA - IMPORTANTE (Afecta usabilidad)

| Criterio | Descripción                          | Páginas Afectadas |
|----------|--------------------------------------|-------------------|
| 1.4.3    | Contraste mínimo (ratio 4.5:1)       | 100%              |
| 1.4.5    | Imágenes de texto                    | 80%               |
| 2.4.6    | Encabezados y etiquetas descriptivas | 95%               |
| 3.2.3    | Navegación coherente                 | 85%               |

**Impacto:** Usuarios con baja visión, daltonismo y dificultades de lectura tienen **dificultades severas**.

### 5.3. Nivel AAA - MEJORAS (Optimización)

| Criterio | Descripción                             | Páginas Afectadas |
|----------|-----------------------------------------|-------------------|
| 1.4.8    | Presentación visual (texto justificado) | 30%               |
| 2.4.9    | Propósito del enlace (solo contexto)    | 100%              |

---

## 6. PLAN DE ACCIÓN PRIORIZADO

### Prioridad 1 - CRÍTICA

#### 1. Rediseño de Esquema de Colores
- **Problema:** 9,500+ errores de contraste
- **Acción:** Implementar paleta accesible (ratio mínimo 4.5:1)
- **Impacto esperado:** Muy Alto

#### 2. Corrección de Formularios
- **Problema:** 90% formularios inaccesibles
- **Acción:** Asociar elementos `<label>` a todos los campos
- **Impacto esperado:** Muy Alto

#### 3. Textos Alternativos para Imágenes
- **Problema:** 75% páginas con imágenes sin alt
- **Acción:** Añadir descripciones alternativas descriptivas
- **Impacto esperado:** Muy Alto

### Prioridad 2 - ALTA

#### 4. Navegación por Teclado
- **Problema:** Botones y enlaces vacíos
- **Acción:** Añadir texto visible o aria-label
- **Impacto esperado:** Alto

#### 5. Estructura Semántica
- **Problema:** Headers mal organizados
- **Acción:** Reorganizar jerarquía de encabezados
- **Impacto esperado:** Alto

#### 6. Compatibilidad con Tecnologías de Asistencia
- **Problema:** Elementos ARIA incorrectos
- **Acción:** Implementar etiquetas ARIA apropiadas
- **Impacto esperado:** Alto

### Prioridad 3 - MEDIA

#### 7. Multimedia Accesible
- **Acción:** Añadir subtítulos y transcripciones a videos
- **Impacto esperado:** Medio

#### 8. Optimización de Enlaces
- **Acción:** Textos de enlaces descriptivos y contextuales
- **Impacto esperado:** Medio

#### 9. Corrección de Formato de Texto
- **Acción:** Eliminar texto justificado
- **Impacto esperado:** Bajo

---

## 7. JUSTIFICACIÓN DE LA INTERVENCIÓN

### 7.1. Impacto en Usuarios

Con las barreras identificadas, el sitio web era **inaccesible** para:

- **Personas con discapacidad visual**: Sin textos alternativos ni compatibilidad con lectores de pantalla
- **Personas con baja visión o daltonismo**: Contraste insuficiente
- **Personas con discapacidad motriz**: Navegación por teclado imposible
- **Personas con discapacidad auditiva**: Videos sin subtítulos
- **Personas con discapacidad cognitiva**: Estructura confusa, enlaces no descriptivos

**Población potencialmente excluida:** Estimado 1,500+ visitantes mensuales con alguna discapacidad.

### 7.2. Incumplimiento Normativo

El sitio incumplía:

- **Real Decreto 1112/2018** sobre accesibilidad de sitios web del sector público
- **Ley 12/2014** de Canarias de Transparencia y Acceso a la Información Pública
- **Directiva UE 2016/2102** sobre accesibilidad de sitios web

### 7.3. Contradicción con Misión Institucional

Como organización dedicada a la **defensa de los derechos de las personas con discapacidad**, mantener un sitio web inaccesible contradecía directamente la misión de CoordiCanarias y afectaba su credibilidad.

---

## 8. CONCLUSIONES

### Estado Inicial Diagnosticado

El sitio web coordicanarias.com presentaba **barreras críticas de accesibilidad** que:

1. **Bloqueaban el acceso** a usuarios con discapacidad
2. Eran **sistemáticas** (100% de las páginas afectadas)
3. **Incumplían normativa** obligatoria
4. **Contradecían la misión** institucional

### Necesidad de Intervención

La auditoría evidenció la **necesidad urgente** de:

- Implementar mejoras técnicas integrales
- Alcanzar conformidad WCAG 2.2 nivel AA
- Garantizar acceso universal a la información y servicios
- Cumplir con la normativa vigente
- Alinearse con los valores institucionales

### Objetivo del Proyecto

Transformar el sitio web en una plataforma **universalmente accesible** que permita a todas las personas, independientemente de sus capacidades, acceder autónomamente a los servicios de CoordiCanarias.

---

## ANEXOS

### Documentación Detallada

Los informes detallados página por página (81 archivos .md) están disponibles en:

📦 **Archivo adjunto:** `auditoria_completa_81_paginas.zip`

**Contenido del archivo:**
- Carpeta: `auditoria_antigua_web/`
- Informes individuales organizados por sección
- Metodología y estructura en README.md

### Estructura del Archivo

```
auditoria_antigua_web/
├── README.md (Metodología y alcance)
├── Coordicanarias/
│   ├── Inicio/
│   ├── Conócenos/
│   ├── Empleo/
│   ├── Transparencia/
│   ├── Atención integral/
│   ├── Bolsa de Empleo/
│   ├── Colabora/
│   ├── Filmoteca/
│   ├── Formación/
│   ├── Mujer e igualdad/
│   ├── Ocio/
│   ├── Participación y cultura/
│   └── Sensibilización y Formación/
```

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
