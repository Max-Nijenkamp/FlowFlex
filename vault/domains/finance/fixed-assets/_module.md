---
domain: finance
module: fixed-assets
type: module
module-key: finance.assets
priority: v1
build-status: planned
status: wip
depends-on: [finance.ledger, core.billing, core.rbac]
soft-depends: [it.assets]
fires-events: []
consumes-events: []
patterns: [money, custom-pages]
tables: [fin_fixed_assets, fin_depreciation_entries]
permission-prefix: finance.assets
encrypted-fields: []
color: "#4ADE80"
updated: 2026-06-20
---

# Fixed Assets

Fixed asset register, depreciation schedules, and disposal tracking. Capitalised assets are intended to be accounted over their useful life, with monthly depreciation and disposal gain/loss both posting to the General Ledger.

> Rebuild blueprint. Code was stripped to the [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell|app/admin shell]]; nothing here is built yet. This spec is the source of truth for the rebuild.

## Purpose

The fixed-assets register tracks each capitalised asset from acquisition to disposal. Depreciation is intended to run monthly as a batch, posting a balanced journal entry to the ledger per asset, and disposal computes gain/loss against net book value (NBV) and posts the resulting GL entry. The module is the financial counterpart to physical asset inventory (IT), but is intended to stand alone without it.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../general-ledger/_module\|finance.ledger]] | depreciation + disposal entries post to GL |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | [[../../it/asset-inventory/_module\|it.assets]] | physical ↔ financial asset link (P3); standalone register without it |

## Core Features

- Asset record: name, category, cost, purchase date, useful life, depreciation method, salvage value.
- Depreciation methods: straight-line, declining balance, units of production *(v1: straight-line + declining; units-of-production deferred)* *(assumed)*.
- Monthly depreciation calculation + auto-post journal entry to GL.
- Net book value: cost − accumulated depreciation.
- Asset disposal: sale/scrap with gain/loss calculation → GL entry.
- Asset categories with default depreciation settings.
- Depreciation schedule view (full life projection).
- Links to IT Asset Inventory (physical asset ↔ financial asset).

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `RunDepreciationCommand` | finance | monthly, 1st 02:30 | unique `(asset, period)` — re-run skips done assets, continues on per-asset failure |

See [[../../../architecture/queue-jobs]].

## Permissions

`finance.assets.view-any` · `finance.assets.create` · `finance.assets.update` · `finance.assets.run-depreciation` · `finance.assets.dispose`

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Straight-line schedule sums exactly to cost − salvage (rounding absorbed in final period)
- [ ] Declining-balance never depreciates below salvage
- [ ] Monthly run idempotent; per-asset failure doesn't stop batch
- [ ] Each depreciation entry has balanced GL posting
- [ ] Disposal gain/loss correct vs NBV; double disposal rejected
- [ ] Fully-depreciated status set at end of life

## Build Manifest

```
database/migrations/xxxx_create_fin_fixed_assets_table.php
database/migrations/xxxx_create_fin_depreciation_entries_table.php
app/Models/Finance/{FixedAsset,DepreciationEntry}.php
app/Data/Finance/{CreateAssetData,DisposeAssetData,AssetData}.php
app/Services/Finance/FixedAssetService.php
app/Exceptions/Finance/AlreadyDisposedException.php
app/Console/Commands/Finance/RunDepreciationCommand.php
app/Filament/Finance/Resources/FixedAssetResource.php
app/Filament/Finance/Pages/DepreciationRunPage.php
database/factories/Finance/FixedAssetFactory.php
tests/Feature/Finance/{DepreciationTest,AssetDisposalTest}.php
```

## Cross-Domain Edges

**Data ownership.** This module writes only its own tables (`fin_fixed_assets`, `fin_depreciation_entries`); all cross-domain effects happen via events or the owning domain's service — never a direct write into another domain's tables ([[../../../security/data-ownership]]).

| Direction | Event / Call | Counterpart |
|---|---|---|
| Calls | `LedgerService::post` for depreciation + disposal gain/loss entries | [[../general-ledger/_module\|finance.ledger]] |
| Reads | physical asset link (soft) *(assumed)* | [[../../it/asset-inventory/_module\|it.assets]] |

## Entity Notes

- [[architecture]] — service methods, depreciation + disposal logic, money handling, scheduling
- [[data-model]] — tables + ERD
- [[api]] — DTOs, service methods, events
- [[security]] — access contract, permissions
- [[decisions]] — method deferral, GL-coupling
- [[unknowns]] — `*(assumed)*` items
- Features: [[features/depreciation]], [[features/disposal]]

## Related

- [[../general-ledger/_module]]
- [[../financial-reporting/_module]]
- [[../../it/asset-inventory/_module]]
- [[../../../architecture/event-bus]]
- [[../../../glossary]]
