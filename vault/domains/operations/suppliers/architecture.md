---
domain: operations
module: suppliers
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Suppliers — Architecture

## Services & Actions

`SupplierService` — a light service; supplier CRUD is a plain resource, the service holds the computed queries.

| Method | Notes |
|---|---|
| `performance(string $supplierId): array{on_time_rate: float, order_count: int}` | joins POs (`expected_delivery`) with GRNs (`received_at`); on-time = received ≤ expected. Read-only over PO/GRN tables. |
| `PreferredSupplierFor::item(string $itemId): ?OpsSupplier` | the `is_preferred` supplier for an item (used by PO line cost default). |

Setting a supplier-item `is_preferred = true` unsets the prior preferred for that item in the same transaction (one preferred per item).

Phone normalised to E.164 via `propaganistas/laravel-phone` on the DTO.

---

## Events

None fired, none consumed. Performance is derived read-only from PO/GRN data.

---

## Filament Artifacts

**Nav group:** Purchasing

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `OpsSupplierResource` | #1 CRUD resource | supplied-items relation manager; performance + order-history panels on view |

**Access contract:** `canAccess() = Auth::user()->can('operations.suppliers.view-any') && BillingService::hasModule('operations.suppliers')` per [[../../../architecture/filament-patterns]] #1.

---

## Search & Realtime

- Meilisearch: suppliers indexed on `name`, `contact_name`, `email` *(assumed)*.
- No realtime.
