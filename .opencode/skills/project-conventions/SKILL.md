---
name: project-conventions
description: Skill para obligar al agente a inspeccionar y respetar convenciones ya existentes en el proyecto antes de implementar cambios.
---

# Project conventions skill

## Cuándo usar

Usa esta skill en casi cualquier tarea de implementación dentro del proyecto.

## Objetivo

Evitar que el agente introduzca estilos, estructuras o patrones que no encajen con la base actual del repositorio.

## Flujo recomendado

1. Revisar archivos similares a la tarea pedida.
2. Identificar patrones de nombres, estructura y estilo.
3. Reutilizar el patrón predominante.
4. Solo introducir un patrón nuevo si es claramente necesario.
5. Mantener cambios mínimos y consistentes.

## Qué inspeccionar

Según la tarea, revisar:

- estructura de carpetas
- nombres de clases
- naming de métodos
- estilo de controllers
- uso de requests
- uso de services o actions
- respuesta de API
- componentes Livewire similares
- estilo de pruebas

## Reglas

- Preferir coherencia interna sobre preferencia personal del agente.
- No mezclar varios estilos en una misma implementación.
- No crear abstracciones nuevas sin necesidad.
- No duplicar helpers o servicios ya existentes.
- Respetar idioma y naming dominante del proyecto.

## Checklist

- [ ] patrón existente identificado
- [ ] solución alineada con el repo
- [ ] sin abstracciones innecesarias
- [ ] sin duplicación evidente

## Señales de mala implementación

- clases nuevas que no siguen estructura del proyecto
- naming inconsistente
- cambio de estilo entre archivos
- introducir layers innecesarios para tareas pequeñas