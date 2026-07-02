---
domain: crm
module: deals
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Deals — Decisions & ADR Notes

## Reopen Transition Behaviour

**Context:** When a deal is won and then reopened, `finance.invoicing` may have already created a draft invoice stub via `DealWon`.

**Decision (from spec):** Reopening a won deal does NOT trigger any action in Finance. The draft invoice stub remains as-is in Finance — it is the Finance user's responsibility to void or cancel it if needed.

**Status:** Assumed default — pending explicit confirmation. See [[./unknowns]] for the open question.

---

## No Separate Lead Model

**Context:** Some CRMs (HubSpot) have a distinct Lead object that must be "converted" to a Contact.

**Decision:** FlowFlex uses a single `crm_contacts` table with a `lifecycle_stage` column. A "lead" is simply a contact with `lifecycle_stage = lead`. No conversion step exists.

**Consequences:** Simpler data model; lifecycle stage moves are unrestricted (no state machine guard); reporting on leads is a filtered view of contacts.

This decision is also recorded in [[../../crm/contacts/architecture|contacts architecture]].

---

## Stage Movement Is Not a State Transition

**Context:** Deals move through pipeline stages (e.g. Qualified → Proposal) and also have a status state machine (open / won / lost).

**Decision:** Stage movement (updating `stage_id`) is a plain update — not a `spatie/laravel-model-states` transition. Only the `status` column uses the state machine. This avoids over-engineering the pipeline board drag-and-drop path.

**Consequences:** `moveToStage()` must manually enforce the `ClosedDealImmutableException` guard (won/lost deals cannot change stage).
