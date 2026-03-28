---
name: laravel-10
description: Guía para implementar cambios en Laravel 10 respetando controladores delgados, validación con Form Requests, autorización, services/actions y pruebas. Usar cuando la tarea afecte rutas, controladores, modelos, jobs, listeners, eventos o lógica de negocio.
---

# Laravel 10 skill

## Cuándo usar

Usa esta skill cuando la tarea implique alguno de estos puntos:

- endpoints nuevos o existentes
- controladores
- modelos
- validación HTTP
- autorización
- lógica de negocio
- servicios o actions
- jobs, listeners o eventos
- queries complejas
- persistencia o cambios de flujo backend

## Objetivo

Implementar cambios pequeños, seguros y consistentes con el proyecto actual.

## Flujo recomendado

1. Revisar rutas relacionadas.
2. Revisar controller, model, request, service o action relacionados.
3. Detectar patrón actual del proyecto.
4. Decidir la mínima cantidad de archivos a tocar.
5. Implementar la solución más simple que respete arquitectura existente.
6. Agregar o ajustar pruebas.
7. Resumir impacto técnico.

## Reglas

- Evitar lógica pesada en controllers.
- Preferir `FormRequest` para validación HTTP.
- Preferir `Policies` o `Gates` para autorización.
- Reutilizar servicios, helpers o patterns existentes.
- No romper contratos de respuesta.
- Mantener nombres coherentes con el dominio.
- Considerar manejo de errores y respuestas consistentes.
- Revisar impacto en tests, factories y seeders si aplica.

## Checklist

- [ ] rutas revisadas
- [ ] validación definida
- [ ] autorización revisada
- [ ] lógica ubicada en la capa correcta
- [ ] respuestas consistentes
- [ ] pruebas creadas o ajustadas
- [ ] riesgos documentados

## Señales de mala implementación

- controller demasiado largo
- validación inline innecesaria
- reglas de negocio en Blade o Livewire
- respuestas JSON inconsistentes
- cambios sin pruebas
- duplicación de lógica existente---
name: laravel-10
description: Guía para implementar cambios en Laravel 10 respetando controladores delgados, validación con Form Requests, autorización, services/actions y pruebas. Usar cuando la tarea afecte rutas, controladores, modelos, jobs, listeners, eventos o lógica de negocio.
---

# Laravel 10 skill

## Cuándo usar

Usa esta skill cuando la tarea implique alguno de estos puntos:

- endpoints nuevos o existentes
- controladores
- modelos
- validación HTTP
- autorización
- lógica de negocio
- servicios o actions
- jobs, listeners o eventos
- queries complejas
- persistencia o cambios de flujo backend

## Objetivo

Implementar cambios pequeños, seguros y consistentes con el proyecto actual.

## Flujo recomendado

1. Revisar rutas relacionadas.
2. Revisar controller, model, request, service o action relacionados.
3. Detectar patrón actual del proyecto.
4. Decidir la mínima cantidad de archivos a tocar.
5. Implementar la solución más simple que respete arquitectura existente.
6. Agregar o ajustar pruebas.
7. Resumir impacto técnico.

## Reglas

- Evitar lógica pesada en controllers.
- Preferir `FormRequest` para validación HTTP.
- Preferir `Policies` o `Gates` para autorización.
- Reutilizar servicios, helpers o patterns existentes.
- No romper contratos de respuesta.
- Mantener nombres coherentes con el dominio.
- Considerar manejo de errores y respuestas consistentes.
- Revisar impacto en tests, factories y seeders si aplica.

## Checklist

- [ ] rutas revisadas
- [ ] validación definida
- [ ] autorización revisada
- [ ] lógica ubicada en la capa correcta
- [ ] respuestas consistentes
- [ ] pruebas creadas o ajustadas
- [ ] riesgos documentados

## Señales de mala implementación

- controller demasiado largo
- validación inline innecesaria
- reglas de negocio en Blade o Livewire
- respuestas JSON inconsistentes
- cambios sin pruebas
- duplicación de lógica existente