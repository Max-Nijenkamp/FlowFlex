---
domain: crm
module: contacts
type: feature
feature: lifecycle-stages
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Lifecycle Stages

## Purpose

FlowFlex does NOT have a separate Lead model. A "lead" is a contact with `lifecycle_stage = lead`. This eliminates the HubSpot Lead-to-Contact conversion complexity.

Reps move contacts through lifecycle stages as they qualify.

---

## Lifecycle Stage Enum

`crm_contacts.lifecycle_stage` — plain string column (no state machine; any stage move is allowed):

| Value | Meaning |
|---|---|
| `lead` | Default; unqualified inbound |
| `marketing_qualified` | MQL — marketing has scored/engaged |
| `sales_qualified` | SQL — sales team accepted |
| `opportunity` | Active deal in pipeline |
| `customer` | Won deal / active paying customer |
| `churned` | Former customer |

---

## Service Method

`ContactService::moveLifecycleStage(string $contactId, string $stage): ContactData`

Any transition is valid — no guard on direction. Stage value must be in the enum set.

---

## Filament UX

`ContactResource` list intended to expose lifecycle stage quick-move (inline action or select) and filter tabs: **All / Leads / Opportunities / Customers**.

---

## UI

- **Kind**: simple-resource — lifecycle stage is a select field + filter tabs on `ContactResource`.
- **Page**: `ContactResource` list/edit at `/crm/contacts`.
- **Layout**: `lifecycle_stage` select in the form; list has filter tabs (All / Leads / Opportunities / Customers) and an inline quick-move select/action per row.
- **Key interactions**: inline stage change (optimistic select) → `ContactService::moveLifecycleStage`; tab switch re-filters the table.
- **States**: empty (no contacts in this stage) · loading (table refresh on tab switch) · error (invalid enum value rejected) · selected (active tab highlighted).
- **Gating**: `crm.contacts.update`.

## Data

- Owns / writes: `crm_contacts` (`lifecycle_stage` column) — plain string, any-direction move allowed.
- Reads: none hard.
- Cross-domain writes: none — stage changes touch only `crm_contacts` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `DealWon` (crm.deals) → may auto-advance to `customer`; `InvoicePaid` (finance.invoicing) → confirm `customer`. Both update this module's OWN `crm_contacts` via a listener.
- Feeds: nothing fired specifically for stage change.
- Shared entity: `crm_contacts` — FlowFlex has no separate Lead model; a "lead" is `lifecycle_stage = lead`, so this field is the funnel other domains read.

## Test Checklist

### Unit
- [ ] `moveLifecycleStage` accepts any enum value in the set and rejects a value outside it
- [ ] Any-direction move is allowed (no guard) — e.g. `customer` → `lead` succeeds

### Feature (Pest)
- [ ] `DealWon` / `InvoicePaid` listeners advance the OWN contact to `customer`; a null account is a no-op
- [ ] Stage change writes only `crm_contacts` and is tenant-scoped — never touches another company's rows
- [ ] Concurrent stage edit resolves via the `updated_at` stale-check (optimistic), surfacing a conflict on the losing write

### Livewire
- [ ] Inline quick-move select changes stage and requires `crm.contacts.update` / `crm.contacts.change-lifecycle`
- [ ] Filter tabs (All / Leads / Opportunities / Customers) re-filter the table correctly
