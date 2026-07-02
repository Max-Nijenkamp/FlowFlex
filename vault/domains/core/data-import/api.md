---
domain: core
module: data-import
type: api
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Data Import — API (DTO + Contract)

Parent: [[_module]] · See also [[architecture]]

This module fires no events and consumes none. Its cross-module surface is the input DTO and the importer contract that domain modules implement.

## DTOs

### CreateImportData (input)

| Field | Type | Validation |
|---|---|---|
| target | string | required, in registered importer keys, module active |
| file | UploadedFile | required, mimes:csv,xlsx, max per settings |
| column_map | array\<string,string\> | required fields of the target all mapped |

## Contract — `ImporterInterface`

Each domain module ships an importer that implements `ImporterInterface` and registers it with `ImporterRegistry::register($key, $importer)`. An importer supplies:

- the **template** (expected columns) surfaced in the mapping UI,
- the **required fields** that `column_map` must cover,
- per-row validation via the target module's **Create DTO**,
- optionally a **natural key** for upsert-based idempotency ([[architecture]]).

`ImporterRegistry::available()` filters registered importers by `hasModule`, so the import UI only lists targets whose module is active.

> [!warning] UNVERIFIED — needs confirmation: the exact method signatures of `ImporterInterface` (the spec names the contract but not its methods).
