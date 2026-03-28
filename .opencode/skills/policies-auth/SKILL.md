---
name: policies-auth
description: Guía para implementar autorización y autenticación en Laravel 10 usando Policies, Gates, middleware, guards y reglas de acceso consistentes.
---

# Policies and Authorization skill

## Cuándo usar

Usa esta skill cuando la tarea implique:

- permisos de usuario
- autorización sobre modelos o acciones
- `Policies`
- `Gates`
- middleware de autenticación o autorización
- guards
- restricciones por rol, ownership o estado
- protección de endpoints API o acciones Livewire

## Objetivo

Asegurar que el acceso a recursos y acciones esté protegido de forma explícita, consistente y fácil de mantener.

## Flujo recomendado

1. Revisar cómo maneja autorización actualmente el proyecto.
2. Identificar si la regla es por rol, permiso, ownership, estado u otra condición.
3. Determinar si corresponde `Policy`, `Gate`, middleware o combinación.
4. Implementar la validación de acceso en la capa adecuada.
5. Evitar duplicación de reglas en múltiples lugares.
6. Agregar o ajustar pruebas de autorización.
7. Documentar supuestos si la lógica de acceso no es obvia.

## Reglas

- Preferir `Policies` para reglas ligadas a modelos o recursos.
- Preferir `Gates` para reglas puntuales no atadas a un modelo específico.
- No esconder autorización solo en frontend o Blade.
- No confiar únicamente en validación visual; aplicar control real en backend.
- Revisar consistencia entre web, API y Livewire.
- Reutilizar permisos, roles o helpers ya existentes en el proyecto.
- Si la app usa paquetes como Spatie Permission, seguir su patrón dominante.
- Considerar ownership, tenant, estado del recurso y permisos explícitos.
- Retornar respuestas coherentes cuando acceso sea denegado.

## Qué inspeccionar primero

- Policies existentes
- Gates definidos
- middleware de auth o permisos
- uso de `can`, `authorize`, `cannot`, `Gate::allows`
- traits o helpers de permisos existentes
- tests relacionados con acceso
- guards configurados
- roles o permisos si el proyecto los usa

## Checklist

- [ ] tipo de restricción identificado
- [ ] capa correcta elegida
- [ ] autorización aplicada en backend
- [ ] consistencia entre flujos web/API/Livewire
- [ ] acceso denegado manejado correctamente
- [ ] pruebas de usuario autorizado
- [ ] pruebas de usuario no autorizado

## Señales de mala implementación

- validación de acceso solo en frontend
- checks duplicados en muchos archivos
- reglas de acceso embebidas en controllers sin reutilización
- lógica de permisos difícil de auditar
- respuestas inconsistentes al denegar acceso
- falta de pruebas de permisos

## Pruebas recomendadas

Cubrir cuando aplique:

- usuario autorizado puede ejecutar acción
- usuario sin permiso recibe denegación correcta
- usuario autenticado vs no autenticado
- ownership válido e inválido
- combinaciones críticas de roles o estados