---
domain: legal
module: legal-contracts
feature: contract-repository
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Contract Repository

Central CRUD store of every contract: title, counterparty, type, value, dates, renewal terms, status, and signed PDF.

## Behaviour

- Contract types: NDA, MSA, vendor, employment, lease, partnership.
- Counterparty is a free-text string plus optional link to a `crm_account` or `ops_supplier` (read-only reference).
- `end_date` must be after `start_date`; `value_cents` via brick/money.
- Signed PDF stored in a Media Library collection (PDF-only, scoped) — see [[../security]].
- Status is driven by the [[./contract-lifecycle|lifecycle]] machine; repository is the record + form.

## UI

- **Kind**: simple-resource
- **Page**: `LegalContractResource` — list + create/edit at `/legal/contracts`.
- **Layout**: table (title, counterparty, type, status badge, renewal date); form grouped Details / Dates / Counterparty / Document; obligations as a relation-manager tab.
- **Key interactions**: filter by type / status / renewal window; row actions sign / renew / terminate (delegate to lifecycle); upload signed PDF; open obligations tab.
- **States**: empty ("Add your first contract" CTA) · loading (table skeleton) · error (validation: end-before-start, PDF-only) · selected (row → view/edit).
- **Gating**: view `legal.contracts.view-any`; create/edit `legal.contracts.create` / `.update`.

## Data

- Owns / writes: `legal_contracts`, `legal_contract_obligations`.
- Reads: `crm.contacts` (accounts) + `operations.suppliers` for counterparty links; `legal.matters` for `matter_id` (all read-only).
- Cross-domain writes: none — links are references only ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: contract summaries read by [[../../matter-management/_module|legal.matters]] and [[../../legal-spend/_module|legal.spend]].
- Shared entity: `crm_accounts` / `ops_suppliers` as counterparty reference (owned elsewhere).

## Test Checklist

### Unit
- [ ] `end_date` must be after `start_date` (validation rule rejects equal/earlier)
- [ ] `value_cents` round-trips through brick/money (no float drift); currency stored ISO-4217
- [ ] Contract `type` accepts only the allowed set (NDA/MSA/vendor/employment/lease/partnership)

### Feature (Pest)
- [ ] Create contract with counterparty free-text + optional `crm_account_id` link persists as read-only reference (no write to crm tables)
- [ ] Company A cannot read/list company B contracts (CompanyScope)
- [ ] Signed-PDF upload rejects non-PDF and over-cap files; stores under `companies/{id}/`

### Livewire
- [ ] `LegalContractResource` create/edit form validates end-before-start and required fields
- [ ] List filters (type / status / renewal window) return scoped rows
- [ ] `canAccess()` false when `legal.contracts` module inactive or permission missing

## Unknowns

- Obligation types are free-text in v1 (no typed enum) — see [[../unknowns]].

## Related

- [[../_module|Legal Contracts]] · [[./contract-lifecycle]] · [[./obligation-tracking]] · [[./e-signature]]
