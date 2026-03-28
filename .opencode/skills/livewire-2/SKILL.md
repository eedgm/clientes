---
name: livewire-2
description: Flujo para crear o modificar componentes Livewire 2 con estado claro, validación, eventos, actions y pruebas.
---

# Livewire 2 skill

## Cuándo usar

Usa esta skill para:

- formularios
- tablas
- filtros
- modales
- acciones interactivas
- componentes existentes a corregir o ampliar
- componentes nuevos con persistencia o validación

## Objetivo

Mantener componentes Livewire claros, pequeños y fáciles de probar.

## Flujo recomendado

1. Revisar el componente PHP actual.
2. Revisar el Blade asociado.
3. Revisar pruebas existentes.
4. Identificar propiedades públicas, reglas, listeners y acciones.
5. Mantener la lógica del componente enfocada en orquestación.
6. Extraer lógica compleja a service o action si crece.
7. Ajustar o crear pruebas Livewire.

## Reglas

- No poner lógica de negocio grande dentro del componente.
- Mantener propiedades públicas claras y mínimas.
- Validar entradas antes de persistir.
- Usar acciones explícitas y nombres entendibles.
- Revisar mensajes de error y flujo final de usuario.
- Mantener compatibilidad con el Blade existente si no hace falta cambiarlo.
- Evitar side effects ocultos.

## Checklist

- [ ] componente revisado
- [ ] blade revisado
- [ ] estado claro
- [ ] validación aplicada
- [ ] acciones claras
- [ ] pruebas ajustadas o creadas

## Señales de mala implementación

- demasiadas propiedades públicas
- demasiada lógica en `render()`
- persistencia mezclada con UI sin orden
- reglas de validación confusas
- eventos o listeners difíciles de seguir