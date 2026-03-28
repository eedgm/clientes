---
name: laravel-api-resources
description: Guía para implementar o modificar API Resources en Laravel 10 manteniendo consistencia, serialización clara y evitando lógica o consultas inesperadas.
---

# Laravel API Resources skill

## Cuándo usar

Usa esta skill cuando la tarea implique:

- `JsonResource`
- colecciones de recursos
- serialización de modelos para API
- respuesta JSON estructurada
- exposición de relaciones
- normalización de payloads
- cambios de contrato de respuesta

## Objetivo

Mantener respuestas API claras, consistentes, controladas y seguras mediante Resources bien definidos.

## Flujo recomendado

1. Revisar Resources existentes del dominio.
2. Revisar contrato JSON actual del endpoint.
3. Identificar campos obligatorios, opcionales y relaciones.
4. Implementar o ajustar el Resource respetando el estilo del proyecto.
5. Evitar lógica compleja o queries ocultas dentro del Resource.
6. Revisar consistencia entre item y colección.
7. Agregar o ajustar pruebas de estructura JSON.

## Reglas

- Reutilizar Resources existentes si ya representan el mismo dominio.
- Mantener estructura consistente entre endpoints similares.
- Evitar meter lógica de negocio dentro del Resource.
- Evitar acceso a relaciones no cargadas que puedan causar N+1.
- Exponer solo lo necesario.
- Si hay campos condicionales, que la condición sea clara y consistente.
- Si cambia el contrato, advertirlo explícitamente.
- Respetar naming predominante del proyecto.

## Qué inspeccionar primero

- Resources existentes
- controllers o actions que los usan
- tests de estructura JSON
- relaciones cargadas desde la consulta
- endpoints similares
- contratos consumidos por frontend o terceros si es visible en el repo

## Checklist

- [ ] resource existente revisado
- [ ] contrato JSON consistente
- [ ] relaciones expuestas correctamente
- [ ] sin lógica pesada dentro del resource
- [ ] sin riesgo evidente de N+1
- [ ] pruebas de estructura ajustadas

## Señales de mala implementación

- queries disparadas desde el Resource por relaciones no cargadas
- payload inconsistente entre endpoints similares
- exposición de campos innecesarios o sensibles
- lógica de permisos mezclada en serialización sin claridad
- cambios silenciosos en nombres o estructura

## Pruebas recomendadas

- estructura JSON esperada
- presencia o ausencia de campos clave
- relaciones incluidas cuando corresponde
- consistencia entre recurso individual y colección
- manejo correcto de campos opcionales o condicionales---
name: laravel-api-resources
description: Guía para implementar o modificar API Resources en Laravel 10 manteniendo consistencia, serialización clara y evitando lógica o consultas inesperadas.
---

# Laravel API Resources skill

## Cuándo usar

Usa esta skill cuando la tarea implique:

- `JsonResource`
- colecciones de recursos
- serialización de modelos para API
- respuesta JSON estructurada
- exposición de relaciones
- normalización de payloads
- cambios de contrato de respuesta

## Objetivo

Mantener respuestas API claras, consistentes, controladas y seguras mediante Resources bien definidos.

## Flujo recomendado

1. Revisar Resources existentes del dominio.
2. Revisar contrato JSON actual del endpoint.
3. Identificar campos obligatorios, opcionales y relaciones.
4. Implementar o ajustar el Resource respetando el estilo del proyecto.
5. Evitar lógica compleja o queries ocultas dentro del Resource.
6. Revisar consistencia entre item y colección.
7. Agregar o ajustar pruebas de estructura JSON.

## Reglas

- Reutilizar Resources existentes si ya representan el mismo dominio.
- Mantener estructura consistente entre endpoints similares.
- Evitar meter lógica de negocio dentro del Resource.
- Evitar acceso a relaciones no cargadas que puedan causar N+1.
- Exponer solo lo necesario.
- Si hay campos condicionales, que la condición sea clara y consistente.
- Si cambia el contrato, advertirlo explícitamente.
- Respetar naming predominante del proyecto.

## Qué inspeccionar primero

- Resources existentes
- controllers o actions que los usan
- tests de estructura JSON
- relaciones cargadas desde la consulta
- endpoints similares
- contratos consumidos por frontend o terceros si es visible en el repo

## Checklist

- [ ] resource existente revisado
- [ ] contrato JSON consistente
- [ ] relaciones expuestas correctamente
- [ ] sin lógica pesada dentro del resource
- [ ] sin riesgo evidente de N+1
- [ ] pruebas de estructura ajustadas

## Señales de mala implementación

- queries disparadas desde el Resource por relaciones no cargadas
- payload inconsistente entre endpoints similares
- exposición de campos innecesarios o sensibles
- lógica de permisos mezclada en serialización sin claridad
- cambios silenciosos en nombres o estructura

## Pruebas recomendadas

- estructura JSON esperada
- presencia o ausencia de campos clave
- relaciones incluidas cuando corresponde
- consistencia entre recurso individual y colección
- manejo correcto de campos opcionales o condicionales