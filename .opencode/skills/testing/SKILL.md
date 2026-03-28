---
name: testing
description: Guía para crear o ajustar pruebas en Laravel, Livewire y API siguiendo el estilo existente del proyecto.
---

# Testing skill

## Cuándo usar

Usa esta skill cuando:

- se crea una nueva feature
- cambia una validación
- cambia un endpoint
- cambia un componente Livewire
- se corrige un bug
- se hace refactor con riesgo funcional

## Objetivo

Asegurar que el comportamiento crítico quede cubierto sin generar pruebas redundantes.

## Flujo recomendado

1. Revisar estilo de testing del repo.
2. Revisar pruebas cercanas a la funcionalidad.
3. Identificar qué comportamiento realmente cambió.
4. Cubrir caso feliz.
5. Cubrir validación y permisos si aplica.
6. Cubrir errores razonables.
7. Mantener el set de pruebas pequeño pero útil.

## Reglas

- Seguir estilo existente: PHPUnit o Pest.
- Priorizar pruebas de comportamiento.
- Evitar mocks innecesarios.
- Usar factories existentes cuando sea posible.
- Para API, verificar status y payload.
- Para Livewire, verificar render, acciones, validación y estado final.
- No duplicar pruebas ya existentes.

## Checklist

- [ ] estilo del repo respetado
- [ ] caso feliz cubierto
- [ ] validación cubierta
- [ ] autorización cubierta
- [ ] errores razonables cubiertos
- [ ] pruebas legibles

## Señales de mala implementación

- pruebas frágiles
- exceso de mocks
- cobertura repetida
- pruebas que verifican detalles internos sin necesidad