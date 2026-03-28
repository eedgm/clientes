---
name: queues-jobs
description: Guía para implementar o modificar Jobs, colas, dispatching, reintentos, idempotencia y manejo de fallos en Laravel 10.
---

# Queues and Jobs skill

## Cuándo usar

Usa esta skill cuando la tarea implique:

- crear o modificar `Jobs`
- mover trabajo pesado a cola
- usar `dispatch()`, `dispatchSync()`, `dispatchAfterResponse()`
- procesar correos, notificaciones, integraciones o reportes en background
- reintentos, timeouts o manejo de fallos
- encadenamiento de jobs
- batch processing
- listeners encolados

## Objetivo

Implementar trabajo asíncrono de forma segura, clara, idempotente y consistente con la arquitectura del proyecto.

## Flujo recomendado

1. Revisar si ya existe un patrón de jobs o colas en el proyecto.
2. Identificar si la tarea realmente debe ir a cola o puede ser síncrona.
3. Revisar dependencias, datos de entrada y efectos secundarios.
4. Diseñar el job con responsabilidad única.
5. Validar idempotencia y reintentos.
6. Revisar manejo de errores y logging.
7. Crear o ajustar pruebas.
8. Resumir impacto operativo.

## Reglas

- Mantener el job enfocado en una sola responsabilidad.
- Pasar al job solo los datos necesarios.
- Evitar pasar modelos gigantes si no hace falta; preferir ids cuando sea razonable.
- Considerar qué pasa si el job corre dos veces.
- Definir `tries`, `timeout`, `backoff` o similares si el caso lo amerita.
- No poner lógica de negocio difusa entre controller y job; la responsabilidad debe estar clara.
- Si el job depende de servicios externos, considerar fallos temporales y reintentos.
- Si el trabajo puede bloquear al usuario, evaluar `dispatchAfterResponse()` o cola real.
- Revisar si el proyecto usa colas específicas, nombres de conexión o colas dedicadas.
- Si un listener ya debería ser encolado, considerar `ShouldQueue`.

## Qué inspeccionar primero

- Jobs existentes
- Listeners existentes
- configuración de queue
- servicios usados por jobs similares
- logs o reportes de fallos si existen
- tests de jobs o procesos asíncronos
- eventos relacionados
- notificaciones, mails o integraciones afectadas

## Checklist

- [ ] se confirmó que debe ser asíncrono
- [ ] responsabilidad única del job
- [ ] entradas mínimas y claras
- [ ] idempotencia considerada
- [ ] reintentos y timeout evaluados
- [ ] errores manejados razonablemente
- [ ] logging o trazabilidad considerados
- [ ] pruebas creadas o ajustadas

## Señales de mala implementación

- job demasiado grande
- lógica de negocio repartida sin claridad
- job no idempotente con riesgo de duplicación
- reintentos infinitos o mal definidos
- uso innecesario de cola para tareas triviales
- dependencia fuerte de estado global
- ausencia de manejo de fallos externos

## Pruebas recomendadas

Según el caso, cubrir:

- dispatch correcto
- job ejecuta acción esperada
- comportamiento ante error
- no duplicación de efecto en reintento
- interacción con servicios externos simulada correctamente
- colas o chains esperadas