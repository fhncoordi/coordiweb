# Plan de Desarrollo de Documentos de Justificación
## Proyecto: Mejora de la Accesibilidad Web - CoordiCanarias

**Fecha de inicio:** 2025-01-01
**Fecha de finalización:** 2025-12-31
**Última actualización:** 2025-12-22

---

## 1. CONTEXTO DEL PROYECTO

### Objetivo General
Mejorar la accesibilidad de la página web de CoordiCanarias para garantizar el acceso universal de todas las personas con discapacidad a la información y servicios que ofrecemos, promoviendo así una inclusión digital completa y efectiva.

### Alcance
- **Dominio:** https://coordicanarias.com/
- **Páginas auditadas:** 81 páginas
- **Estándar objetivo:** WCAG 2.2 nivel AA
- **Beneficiarios directos estimados:** ~2,700 personas
- **Beneficiarios indirectos estimados:** ~6,100 personas

### Fases del Proyecto
1. Evaluación y diagnóstico inicial (Completada)
2. Desarrollo e implementación (Completada)
3. Formación y sensibilización (Completada)
4. Pruebas y validación (Completada)
5. Monitoreo y evaluación continua (En curso)
6. **Documentación de justificación (Pendiente)**

---

## 2. DOCUMENTOS REQUERIDOS PARA JUSTIFICACIÓN

Según el documento "DG - Web Accesible.pdf", se requieren los siguientes documentos:

### 2.1. Informe de Auditoría de Accesibilidad Web
**Estado:** ✅ COMPLETADO
**Prioridad:** ALTA
**Recursos disponibles:**
- ✅ Carpeta: `docs/auditoria_antigua_web/` con informes de 81 páginas
- ✅ Herramientas utilizadas: WAVE, Lighthouse, AXE DevTools
- ✅ Metodología documentada en README.md

**Contenido requerido:**
- [x] Resumen ejecutivo
- [x] Alcance y metodología
- [x] Estadísticas consolidadas (total de errores por tipo)
- [x] Análisis por sección del sitio
- [x] Categorización de problemas (Críticos, Importantes, Menores)
- [x] Plan de acción priorizado según WCAG 2.2
- [x] Anexo con detalle página por página (ZIP)

**Resultado esperado:** Informe completo que identifique barreras de accesibilidad en las 81 páginas, clasificadas por prioridad según criterios WCAG 2.2.

**✅ Entregable creado:**
- `docs/justificación/01_INFORME_AUDITORIA_ACCESIBILIDAD.md`
- `docs/justificación/ANEXO_auditoria_completa_81_paginas.zip` (110 KB)

---

### 2.2. Documentación de Mejoras Técnicas Implementadas
**Estado:** Pendiente
**Prioridad:** ALTA
**Recursos disponibles:**
- Por identificar en el código fuente actual

**Contenido requerido:**
- [ ] Registro de correcciones de texto alternativo
- [ ] Ajustes de contraste de colores (antes/después)
- [ ] Mejoras en estructura HTML semántica
- [ ] Implementación de navegación por teclado
- [ ] Etiquetas ARIA añadidas
- [ ] Optimización de formularios
- [ ] Subtítulos y transcripciones añadidas
- [ ] Evidencia de cumplimiento WCAG 2.2 nivel AA al 100%

**Resultado esperado:** Documento técnico que demuestre las correcciones realizadas para alcanzar conformidad WCAG 2.2 nivel AA.

---

### 2.3. Materiales de Formación
**Estado:** Pendiente de localización/creación
**Prioridad:** MEDIA
**Recursos disponibles:**
- Por localizar o crear

**Contenido requerido:**
- [ ] Guías internas de accesibilidad web
- [ ] Recursos educativos creados para el equipo
- [ ] Checklist de verificación pre-publicación
- [ ] Plantillas de contenido accesible
- [ ] Certificados o constancias de formación del personal (85 personas)

**Resultado esperado:** Repositorio de materiales que demuestren la capacitación del 100% del equipo en accesibilidad web.

---

### 2.4. Informe del Taller con Usuarios Voluntarios
**Estado:** Pendiente
**Prioridad:** ALTA
**Recursos disponibles:**
- Según PDF: 1 taller con 9 personas con diversas discapacidades

**Contenido requerido:**
- [ ] Fecha y lugar del taller
- [ ] Lista de participantes (9 personas con diversas discapacidades)
- [ ] Metodología del taller
- [ ] Recopilación de opiniones y experiencias de uso web
- [ ] Necesidades específicas identificadas por discapacidad
- [ ] Feedback sobre mejoras implementadas
- [ ] Validación de satisfacción (objetivo: 100%)
- [ ] Fotografías o evidencias del taller (si están disponibles)

**Resultado esperado:** Informe detallado que demuestre la participación activa de usuarios con discapacidad en el proceso de evaluación y validación.

---

### 2.5. Informe Final de Resultados del Proyecto
**Estado:** Pendiente
**Prioridad:** ALTA
**Recursos disponibles:**
- Documento base: "DG - Web Accesible.pdf" (secciones OBJETIVOS y ACTIVIDADES)

**Contenido requerido:**
- [ ] Comparación objetivos previstos vs. conseguidos
- [ ] Comparación resultados esperados vs. conseguidos
- [ ] Comparación actividades previstas vs. realizadas
- [ ] Población beneficiada (directa e indirecta)
- [ ] Estado final de accesibilidad de la web
- [ ] Beneficios observados en el uso por personas con discapacidad
- [ ] Cumplimiento normativo alcanzado
- [ ] Métricas de usabilidad (antes/después)

**Resultado esperado:** Informe ejecutivo que sintetice el éxito del proyecto y su impacto en la accesibilidad.

---

### 2.6. Protocolo de Monitoreo Continuo
**Estado:** ⏳ Pendiente
**Prioridad:** MEDIA
**Recursos disponibles:**
- Por crear

**Contenido requerido:**
- [ ] Responsables designados
- [ ] Calendario de auditorías periódicas
- [ ] Procedimientos de verificación pre-publicación
- [ ] Canal de reporte de incidencias
- [ ] Proceso de corrección de problemas detectados
- [ ] Frecuencia de revisión y actualización

**Resultado esperado:** Documento que garantice la sostenibilidad de los estándares de accesibilidad a largo plazo.

---

## 3. RECURSOS DISPONIBLES

### Documentación Existente
```
docs/
├── justificación/
│   ├── DG - Web Accesible.pdf (Documento base del proyecto)
│   └── PLAN_DESARROLLO_DOCUMENTOS.md (Este documento)
└── auditoria_antigua_web/
    ├── README.md (Metodología y estructura)
    └── Coordicanarias/
        ├── Inicio/ 
        ├── Conócenos/ 
        ├── Empleo/ 
        ├── Transparencia/ 
        ├── Atención integral/ 
        ├── Bolsa de Empleo/ 
        ├── Colabora/ 
        ├── Filmoteca/ 
        ├── Formación/ 
        ├── Mujer e igualdad/ 
        ├── Ocio/ 
        ├── Participación y cultura/ 
        └── Sensibilización y Formación/ 
        (Total: 81 páginas auditadas)
```

### Código Fuente
- Sitio web actual implementado con mejoras de accesibilidad
- Por revisar para documentar cambios técnicos específicos

---

## 4. PLAN DE ACCIÓN - DESARROLLO DE DOCUMENTOS

### FASE 1: Informe de Auditoría de Accesibilidad 
**Objetivo:** Consolidar los 81 informes individuales en un documento ejecutivo

**Tareas:**
1. [ ] Analizar todos los archivos .md en `docs/auditoria_antigua_web/`
2. [ ] Crear script/proceso para consolidar estadísticas
3. [ ] Contar y categorizar errores por tipo:
   - Errores críticos (texto alternativo, formularios sin etiquetas)
   - Errores de contraste
   - Alertas
4. [ ] Generar estadísticas agregadas por sección
5. [ ] Crear gráficos/tablas visuales
6. [ ] Redactar resumen ejecutivo
7. [ ] Clasificar problemas según prioridad WCAG 2.2
8. [ ] Elaborar plan de acción priorizado
9. [ ] Compilar anexos con detalle página por página
10. [ ] Revisar y validar documento completo

**Entregable:** `docs/justificacion/01_INFORME_AUDITORIA_ACCESIBILIDAD.md` (o .pdf)

**Tiempo estimado:** 1-2 sesiones

---

### FASE 2: Documentación de Mejoras Técnicas Implementadas 
**Objetivo:** Documentar todas las correcciones técnicas realizadas

**Tareas:**
1. [ ] Revisar código fuente actual del sitio web
2. [ ] Identificar cambios realizados comparando con auditoría inicial
3. [ ] Documentar:
   - Textos alternativos añadidos
   - Ajustes de contraste (especificar ratios antes/después)
   - Cambios en estructura HTML semántica
   - Implementación de navegación por teclado
   - Etiquetas ARIA implementadas
   - Mejoras en formularios
   - Subtítulos/transcripciones añadidas
4. [ ] Ejecutar validación WCAG 2.2 nivel AA con herramientas
5. [ ] Capturar evidencias (screenshots, reportes de herramientas)
6. [ ] Redactar documento técnico
7. [ ] Incluir comparativas antes/después

**Entregable:** `docs/justificacion/02_MEJORAS_TECNICAS_IMPLEMENTADAS.md`

**Tiempo estimado:** 2-3 sesiones

---

### FASE 3: Materiales de Formación 
**Objetivo:** Recopilar o crear materiales de formación en accesibilidad web

**Tareas:**
1. [ ] Buscar materiales de formación existentes
2. [ ] Si no existen, crear:
   - Guía de accesibilidad web para el equipo
   - Checklist de verificación pre-publicación
   - Plantillas de contenido accesible
   - Ejemplos prácticos
3. [ ] Documentar sesiones de formación realizadas:
   - Fechas
   - Asistentes (85 personas = 100% del equipo)
   - Contenidos impartidos
   - Evaluaciones/certificados
4. [ ] Compilar en un único documento o carpeta

**Entregable:** `docs/justificacion/03_MATERIALES_FORMACION/`

**Tiempo estimado:** 1-2 sesiones

---

### FASE 4: Informe del Taller con Usuarios Voluntarios 
**Objetivo:** Documentar el proceso de validación con usuarios reales

**Tareas:**
1. [ ] Recopilar información del taller realizado:
   - Fecha y lugar
   - Participantes (9 personas)
   - Tipo de discapacidades representadas
2. [ ] Documentar metodología del taller
3. [ ] Sistematizar opiniones recogidas:
   - Experiencias previas con webs
   - Necesidades específicas identificadas
   - Feedback sobre mejoras propuestas
   - Validación post-implementación
4. [ ] Incluir evidencias (fotos, lista de asistencia, formularios)
5. [ ] Redactar informe completo
6. [ ] Destacar nivel de satisfacción alcanzado (100%)

**Entregable:** `docs/justificacion/04_INFORME_TALLER_USUARIOS.md`

**Tiempo estimado:** 1 sesión

---

### FASE 5: Informe Final de Resultados del Proyecto 
**Objetivo:** Sintetizar el éxito del proyecto completo

**Tareas:**
1. [ ] Extraer datos del PDF "DG - Web Accesible.pdf":
   - Objetivos previstos vs. conseguidos (pág. 14-16)
   - Resultados esperados vs. conseguidos (pág. 17-18)
   - Actividades previstas vs. realizadas (pág. 18-20)
2. [ ] Documentar población beneficiada:
   - Directa: ~2,700 personas
   - Indirecta: ~6,100 personas
3. [ ] Incluir métricas de impacto:
   - Cumplimiento WCAG 2.2 nivel AA: 100%
   - Satisfacción usuarios: 100%
   - Personal capacitado: 85 personas (100%)
4. [ ] Redactar conclusiones y logros principales
5. [ ] Incluir recomendaciones para el futuro

**Entregable:** `docs/justificacion/05_INFORME_FINAL_RESULTADOS.md`

**Tiempo estimado:** 1 sesión

---

### FASE 6: Protocolo de Monitoreo Continuo ⏳
**Objetivo:** Establecer sistema de mantenimiento de accesibilidad

**Tareas:**
1. [ ] Definir responsables del monitoreo
2. [ ] Establecer calendario de auditorías periódicas:
   - Frecuencia (trimestral, semestral, anual)
   - Herramientas a utilizar
3. [ ] Crear checklist de verificación pre-publicación
4. [ ] Definir canal de reporte de incidencias
5. [ ] Establecer proceso de corrección
6. [ ] Documentar todo en un protocolo formal

**Entregable:** `docs/justificacion/06_PROTOCOLO_MONITOREO_CONTINUO.md`

**Tiempo estimado:** 1 sesión

---

## 5. CHECKLIST DE PROGRESO GENERAL

### Documentos Principales
- [x] 01 - Informe de Auditoría de Accesibilidad Web ✅
- [ ] 02 - Documentación de Mejoras Técnicas Implementadas
- [ ] 03 - Materiales de Formación
- [ ] 04 - Informe del Taller con Usuarios Voluntarios
- [ ] 05 - Informe Final de Resultados del Proyecto
- [ ] 06 - Protocolo de Monitoreo Continuo

**Progreso: 1/6 documentos completados (16.7%)**

### Tareas Transversales
- [ ] Revisión de consistencia entre todos los documentos
- [ ] Validación de datos y cifras
- [ ] Compilación de evidencias (screenshots, certificados, fotos)
- [ ] Formato y presentación profesional
- [ ] Revisión final antes de entrega

---

## 6. PRÓXIMOS PASOS INMEDIATOS

### ✅ Sesión Actual (2025-12-22)
- ✅ Creación de este plan de desarrollo
- ✅ Desarrollo del Informe de Auditoría de Accesibilidad Web
- ✅ Consolidación de informes de las 81 páginas
- ✅ Generación de estadísticas agregadas
- ✅ Creación de archivo ZIP con anexos

### Próxima Sesión
**PRIORIDAD 2:** Documentación de Mejoras Técnicas Implementadas
- Revisar código fuente actual del sitio web
- Identificar cambios realizados vs. auditoría inicial
- Documentar correcciones técnicas
- Generar evidencias de cumplimiento WCAG 2.2 AA

---

## 7. NOTAS Y CONSIDERACIONES

### Formato de Entregables
- **Formato preferido:** Pendiente de definir (Markdown, Word, PDF)
- **Idioma:** Español
- **Estilo:** Profesional, técnico pero accesible

### Recursos Adicionales Necesarios
- Acceso al código fuente del sitio web actual (para Fase 2)
- Fotografías o evidencias del taller de usuarios (para Fase 4)
- Certificados de formación del personal (para Fase 3)
- Datos de métricas de usabilidad antes/después (para Fase 5)

### Contacto para Información Adicional
- **Responsable del proyecto:** Salvador Morales Coello
- **Email:** info@coordicanarias.com
- **Teléfono:** 922215909

---

## 8. HISTORIAL DE CAMBIOS

| Fecha      | Cambio                                                    | Autor  |
|------------|-----------------------------------------------------------|--------|
| 2025-12-22 | Creación inicial del plan                                 | Claude |
| 2025-12-22 | Completado Documento 01: Informe de Auditoría            | Claude |
| 2025-12-22 | Creado archivo ZIP con auditorías completas (110 KB)     | Claude |

---

**Última actualización:** 2025-12-22
**Versión:** 1.1
