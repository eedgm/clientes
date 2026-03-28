---
name: api-design
description: Guía para diseñar e implementar endpoints REST en Laravel con requests, resources, autenticación, autorización, filtros y pruebas.
---

# API design skill

## Cuándo usar

Usa esta skill cuando la tarea afecte:

- `routes/api.php`
- endpoints REST
- autenticación de API
- autorización
- resources o transformers
- filtros o búsquedas
- paginación
- contratos de respuesta JSON

## Objetivo

Diseñar e implementar endpoints API consistentes y seguros.

## Flujo recomendado

1. Revisar la ruta o grupo API existente.
2. Revisar controllers, requests, resources y middleware relacionados.
3. Detectar la convención de respuestas del proyecto.
4. Definir request y response esperados.
5. Implementar validación y autorización.
6. Implementar la lógica mínima necesaria.
7. Agregar o ajustar pruebas.

## Reglas

- Usar códigos HTTP correctos.
- Usar `FormRequest` si aplica.
- Reutilizar `Resources` o respuestas estándar del proyecto.
- No mezclar demasiada lógica en el controller.
- Revisar compatibilidad hacia atrás si el endpoint ya existe.
- Cubrir errores razonables además del caso feliz.
- Considerar paginación, filtros y ordenamiento si corresponde.

## Checklist

- [ ] contrato revisado
- [ ] status codes correctos
- [ ] validación
- [ ] autorización
- [ ] estructura JSON consistente
- [ ] pruebas de éxito
- [ ] pruebas de error
- [ ] pruebas de permisos

## Señales de mala implementación

- respuestas inconsistentes
- validación insuficiente
- controller con demasiada lógica
- cambios silenciosos en el payload
- ausencia de cobertura de errores