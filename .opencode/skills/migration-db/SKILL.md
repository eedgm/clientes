---
name: migrations-db
description: Guía para crear o modificar migraciones, esquema, índices, llaves foráneas y cambios de base de datos en Laravel 10 con bajo riesgo.
---

# Migrations and Database skill

## Cuándo usar

Usa esta skill cuando la tarea implique:

- nuevas tablas
- cambios de columnas
- llaves foráneas
- índices
- constraints
- renombrar columnas o tablas
- datos afectados por cambios estructurales
- soporte a nuevas búsquedas o relaciones

## Objetivo

Hacer cambios de base de datos seguros, coherentes y con riesgo controlado para datos existentes y rendimiento.

## Flujo recomendado

1. Revisar schema actual y migraciones relacionadas.
2. Confirmar impacto del cambio sobre datos existentes.
3. Diseñar la migración más segura posible.
4. Evaluar índices, llaves foráneas y nulabilidad.
5. Revisar impacto en modelos, factories, seeders y tests.
6. Implementar la migración y cambios de código necesarios.
7. Agregar o ajustar pruebas relevantes.
8. Documentar riesgos operativos si existen.

## Reglas

- No hacer cambios destructivos sin advertencia explícita.
- Evitar borrar columnas o tablas sin entender impacto total.
- Considerar compatibilidad con datos ya existentes.
- Agregar índices cuando se introduzcan filtros, búsquedas o joins frecuentes.
- Revisar tamaño y tipo de dato correctos.
- Mantener nombres claros para columnas, índices y llaves.
- Considerar defaults, nulabilidad y migración de datos si aplica.
- Si el cambio es riesgoso en producción, advertirlo.
- Revisar si el proyecto usa soft deletes, timestamps, UUIDs o convenciones propias.

## Qué inspeccionar primero

- migraciones previas relacionadas
- modelo o modelos afectados
- factories y seeders
- queries principales del recurso
- validaciones y forms relacionados
- tests de persistencia
- relaciones Eloquent existentes

## Checklist

- [ ] impacto sobre datos actuales evaluado
- [ ] tipo de dato correcto
- [ ] nulabilidad correcta
- [ ] índices evaluados
- [ ] llaves foráneas evaluadas
- [ ] rollback razonable
- [ ] factories/seeders/tests revisados
- [ ] riesgos documentados

## Señales de mala implementación

- columnas nuevas sin índice cuando la consulta lo necesita
- tipos de datos incorrectos
- cambios destructivos silenciosos
- nullable por conveniencia sin razón real
- migración sin impacto reflejado en modelo/tests
- relaciones inconsistentes entre esquema y Eloquent

## Pruebas recomendadas

Según el caso, cubrir:

- persistencia correcta del nuevo campo o relación
- validación acorde al nuevo esquema
- consulta o filtro usando nueva columna
- integridad básica de relaciones
- comportamiento esperado de defaults o nulabilidad