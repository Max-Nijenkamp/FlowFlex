---
domain: foundation
module: test-suite
feature: architecture-tests
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Architecture Tests (isolation & layering enforcement)

Pest arch tests that turn the vault's hard rules into build-time gates — the CI teeth behind data-ownership and tenant isolation.

## Behaviour

- `TenancyTest` — `withoutGlobalScope(CompanyScope)` forbidden outside admin/support.
- `ModelsTest` — models declare `HasUlids` + `SoftDeletes`.
- `LayersTest` — no `dd`/`dump`/`var_dump` in `app/`; layer boundaries respected.
- Run as a dedicated `Architecture` suite in `phpunit.xml`; failing a rule fails CI.
- Future arch rule (per [[../../../../security/data-ownership]]): a `Services/{Domain}` class references only `Models/{Domain}` — cross-domain model writes fail the test.

## UI

- **Kind**: background (CI gate — no screen).

## Data

- Owns: no tables. Statically inspects `app/`. Cross-domain writes: none.

## Relations

- Consumes: nothing at runtime. Feeds: CI pass/fail; enforces the constitution ([[../../../../decisions/decision-2026-06-20-full-mapping-conventions]]).

## Test Checklist

### Unit
- [x] `TenancyTest` flags a class calling `withoutGlobalScope(CompanyScope)` outside admin/support

### Feature (Pest)
- [x] `ModelsTest` fails a model missing `HasUlids` / `SoftDeletes`
- [x] `LayersTest` fails on `dd` / `dump` / `var_dump` in `app/`

## Unknowns

> [!warning] UNVERIFIED — the data-ownership cross-domain arch test is proposed for the eventual build, not yet
> present. The three named tests are confirmed. See [[../unknowns]].

## Related

- [[../_module|Test Suite]] · [[tenant-aware-harness]] · [[../../../../security/data-ownership]] · [[../../../../security/tenancy-isolation]]
