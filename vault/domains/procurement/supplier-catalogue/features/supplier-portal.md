---
domain: procurement
module: supplier-catalogue
feature: supplier-portal
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Supplier Self-Onboarding Portal

A public surface where an invited supplier submits their own details and documents (tax ID, bank/ACH, insurance certs), removing the email-back-and-forth that makes onboarding the biggest P2P cost.

> [!warning] UNVERIFIED
> Not in the original v1 module spec — added per the full-map "supplier portal public-vue" mandate and the [[../../_opportunities]] onboarding-cost finding. Scope/priority (and possible Phase-2 deferral with `laravel/socialite`) need product confirmation.

## Behaviour

- Staff invite a supplier by email → tokenised link.
- Supplier fills a form (company details, tax/registration, bank details, category, uploads certs) — no tenant login.
- Submission creates a **pending** `proc_supplier_status` row + **draft** catalogue items owned by this module; staff review and approve.
- Token scoped to one supplier; expires; rate-limited.

## UI

- **Kind**: public-vue
- **Page**: "Supplier onboarding" (`/portal/suppliers/onboard/{token}`) — Vue + Inertia.
- **Layout**: multi-step wizard (details → banking → documents → review) with progress; mobile-friendly.
- **Key interactions**: stepper next/back (pinia wizard state); file uploads with client validation; submit → confirmation screen.
- **States**: empty (fresh form) · loading (submitting) · error (field + upload errors, expired/invalid token page) · success (submitted, "we'll review" screen).
- **Gating**: invite-token guard (not app auth); staff-side review gated by `procurement.catalogue.manage-supplier-status`.

## Data

- Owns / writes: pending `proc_supplier_status` + draft `proc_catalogue_items` — this module's own tables; uploaded docs via media-library.
- Reads: invite token record (own).
- Cross-domain writes: none — an approved supplier can later be linked to `ops_suppliers` by Operations' own flow, not written here ([[../../../../security/data-ownership]]).

## Relations

- Feeds: pending suppliers → [[supplier-status]] review queue.
- Consumes: nothing cross-domain (self-contained submission).

## Test Checklist

### Unit
- [ ] Invite token validation: expiry + supplier binding; submitted docs follow the upload contract (mime/size)

### Feature (Pest)
- [ ] Portal submission creates pending status + draft items -- nothing active until admin review
- [ ] Sensitive supplier fields (tax ID, bank/ACH) stored with `encrypted` cast *(per security spec)*; public endpoint rate-limited on the invite/guest guard *(assumed)*
- [ ] Token never exposes another supplier's data

### Livewire
- (none -- public Vue + Inertia portal)

## Unknowns

- Whether an approved portal supplier auto-creates an `ops_supplier` (via event to Operations) or is linked manually. **UNVERIFIED**.
- SSO/socialite for returning suppliers — Phase 2.

## Related

- [[../_module|Supplier Catalogue]] · [[supplier-status]] · [[../../_opportunities]] · [[../../../../architecture/ui-strategy]]
