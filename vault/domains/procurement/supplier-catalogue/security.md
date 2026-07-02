---
domain: procurement
module: supplier-catalogue
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Supplier Catalogue — Security

## Access contract

Internal artifacts: `canAccess() = Auth::user()->can('procurement.catalogue.view-any') && BillingService::hasModule('procurement.catalogue')` — [[../../../architecture/filament-patterns]] #1.

The **supplier portal** ([[features/supplier-portal]]) is a public Vue+Inertia surface behind a scoped **invite/token guard**, not the app auth guard — a supplier is not a tenant user.

## Permissions

| Permission | Grants |
|---|---|
| `procurement.catalogue.view-any` | see catalogue + statuses |
| `procurement.catalogue.manage` | create/edit catalogue items |
| `procurement.catalogue.manage-supplier-status` | approve / blacklist suppliers (notes required for blacklist) |

## Authorization rules

- **Blacklist is the security control**: `SupplierGate::isBlocked` is the single chokepoint blocking blacklisted suppliers from requisitions, sourcing, and PO supplier selection. Bypassing it is a spend-control violation.
- Blacklisting requires notes (audit trail for why a supplier was blocked).

## Data ownership

Writes **only** `proc_catalogue_items`, `proc_supplier_status`. References `ops_suppliers` read-only — never writes them ([[../../../security/data-ownership]]). Portal submissions land as **pending** status + **draft** items owned by this module; a staff user promotes them.

## Portal-specific

- Invite-token, rate-limited submission endpoint; uploaded docs scanned + stored via media-library with strict mime/size limits ([[../../../architecture/security]]).
- No supplier can read another supplier's data (token scoped to one supplier record).

## Related

- [[_module]] · [[../../../security/data-ownership]] · [[features/supplier-portal]]
