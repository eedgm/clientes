---
name: performance-queries
description: Guía para detectar y mejorar consultas, relaciones, carga de datos y puntos de rendimiento en Laravel 10, evitando N+1 y consultas innecesarias.
---

# Performance and Queries skill

## Cuándo usar

Usa esta skill cuando la tarea implique:

- listados grandes
- filtros, búsquedas o reportes
- relaciones Eloquent
- problemas de lentitud
- endpoints o pantallas con mucha carga de datos
- riesgo de N+1
- optimización de consultas
- paginación, eager loading o conteos

## Objetivo

Mantener consultas correctas y eficientes, evitando sobrecarga innecesaria de base de datos y memoria.

## Flujo recomendado

1. Revisar consulta actual y uso del resultado.
2. Identificar relaciones accedidas en loops o serialización.
3. Detectar N+1, selects innecesarios, cargas excesivas o filtros no indexados.
4. Aplicar eager loading, selects específicos, withCount, exists o joins cuando corresponda.
5. Revisar impacto en resources, Blade o Livewire.
6. Validar si se requiere índice o paginación.
7. Ajustar pruebas o validaciones según el cambio.
8. Resumir mejora y supuestos.

## Reglas

- Evitar acceder relaciones dentro de loops sin eager loading.
- No cargar más columnas de las necesarias si el caso es claro.
- Preferir paginación en listados grandes.
- Revisar `with`, `load`, `withCount`, `exists`, `chunk`, `cursor` o estrategias similares si aportan valor.
- No optimizar prematuramente sin señal clara, pero sí evitar patrones evidentemente costosos.
- Revisar serialización JSON y Resources para evitar queries ocultas.
- Revisar componentes Livewire y Blade por acceso repetido a relaciones.
- Considerar índices si el filtro o búsqueda lo justifican.

## Qué inspeccionar primero

- consultas en controllers, services o repositories
- relaciones Eloquent
- API Resources
- componentes Livewire
- Blade con loops
- scopes del modelo
- paginación o ausencia de ella
- migraciones e índices de columnas filtradas

## Checklist

- [ ] riesgo de N+1 revisado
- [ ] eager loading evaluado
- [ ] columnas seleccionadas evaluadas
- [ ] paginación evaluada
- [ ] relaciones pesadas revisadas
- [ ] serialización revisada
- [ ] índices evaluados si aplica

## Señales de mala implementación

- relaciones leídas dentro de loops sin `with`
- payloads gigantes sin necesidad
- uso de `all()` donde debería haber paginación o filtro
- resources que disparan queries inesperadas
- consultas duplicadas para conteos simples
- filtros lentos por falta de índice evidente

## Pruebas o verificaciones recomendadas

- comprobar estructura de respuesta tras optimización
- revisar que no cambie semántica del resultado
- validar paginación o filtros
- revisar cargas de relaciones esperadas
- si hay tests de performance lógica, ajustarlos