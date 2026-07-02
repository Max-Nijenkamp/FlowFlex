---
domain: procurement
module: supplier-catalogue
type: api
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Supplier Catalogue — DTOs & Service API

## DTOs

### CreateCatalogueItemData
`supplier_id` (not blacklisted — "This supplier is blacklisted."), `name`, `category`, `agreed_price_cents` (min:0), `valid_from`/`valid_until` (until ≥ from), `lead_time_days`.

### SetSupplierStatusData
`supplier_id`, `status` (in:approved,pending,blacklisted), `notes` (required when blacklisted).

## Service API

| Method | Signature | Notes |
|---|---|---|
| `CatalogueService::search` | `search(string $term, ?string $category): Collection` | active + in-window + approved-supplier items only |
| `SupplierGate::isBlocked` | `isBlocked(string $supplierId): bool` | checked by requisitions / sourcing / PO paths |

## Read API (for other procurement modules)

- `SupplierGate::isBlocked` — the single blacklist check.
- `CatalogueService::search` — the requisition picker source; also feeds spend "maverick" detection (off-catalogue lines).

## Related

- [[_module]] · [[data-model]] · [[architecture]]
