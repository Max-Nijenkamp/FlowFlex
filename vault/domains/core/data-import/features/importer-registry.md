---
domain: core
module: data-import
feature: importer-registry
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Importer Registry

Parent: [[../_module]] · See [[../architecture]] · [[../api]]

Import targets are pluggable. Each domain module registers its importer class + template with the registry — the import module owns the mechanism, not the target-specific logic.

- `ImporterRegistry::register(string $key, class-string $importer)` — a domain registers e.g. `hr.employees`, `crm.contacts`, expense items, products.
- `ImporterRegistry::available(): array` — filters registered importers by `hasModule`, so the UI lists only targets whose module is active.
- Each importer implements `ImporterInterface` ([[../api]]) supplying template, required fields, per-row validation (target Create DTO), and optionally a natural key for upsert idempotency.

## UI

- **Kind**: background
- **Page**: background (no page) — the registry is a service (`ImporterRegistry`) populated at boot; its only user-facing surface is the target dropdown in the [[column-mapping]] wizard, sourced from `ImporterRegistry::available()`.
- **Layout**: n/a (no dedicated screen). Trigger: domain modules call `ImporterRegistry::register($key, $importer)` during service-provider boot; the wizard queries `available()` at render time.
- **Key interactions**: none directly — indirectly, choosing a target in the import wizard reads the registry.
- **States**: empty = no active modules registered an importer → target list empty · loading = n/a · error = registered key references a missing importer class *(assumed guard)* · selected = a target key resolved for the wizard.
- **Gating**: no independent gate; downstream use is gated by `core.import.create`. `available()` additionally filters by `BillingService::hasModule` per target module.

## Data

- Owns / writes: nothing persisted by the registry itself — it is an in-memory service. When used, only `data_imports` (this module's table) is written by the surrounding import flow.
- Reads: each target module's importer class metadata (template, required fields, natural key) read-only; `hasModule` state read-only via `BillingService`.
- Cross-domain writes: none — the registry never writes any table ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none (no domain events).
- Feeds: none.
- Shared entity: the importer classes are owned by their respective target domain modules (`hr.employees`, `crm.contacts`, expense items, products); the registry only holds references to them.
