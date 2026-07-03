---
domain: finance
module: accounts-receivable
feature: write-off
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Write-Off

Removes an uncollectable invoice's remaining balance and records the loss.

- Driven by `WriteOffData`: `invoice_id`, `reason` (required, max:1000). The amount is the invoice's current open balance — never supplied by the caller.
- `ArService::writeOff` posts a balanced GL bad-debt entry through the invoicing/ledger services and voids the remaining balance.
- Recorded on `fin_ar_writeoffs`: `amount_cents`, `reason`, `approved_by`, `written_off_at`.
- Permission-gated: `finance.ar.write-off`; the approving user is captured.
- Busts the aging cache. Amounts via brick/money.

## UI
- **Kind**: custom-page (action/modal)
- **Page**: "Write off" action + modal from an AR invoice list (`/finance/ar`)
- **Layout**: modal launched from an invoice row — read-only amount (invoice's current open balance), required `reason` textarea (max 1000), confirm
- **Key interactions**: select an uncollectable invoice → open write-off modal → enter reason → confirm; amount is never caller-supplied
- **States**: empty (n/a — action requires a selected invoice) · loading (submit spinner) · error (invoice already settled / no open balance) · selected (invoice row picked, modal open with its balance)
- **Gating**: `finance.ar.write-off`

## Data
- Owns / writes: `fin_ar_writeoffs` (`amount_cents`, `reason`, `approved_by`, `written_off_at`; amounts as integer minor units / cents via brick/money)
- Reads: `fin_invoices` open balance (via finance.invoicing)
- Cross-domain writes: balanced bad-debt GL entry via the invoicing/ledger services — never writes `fin_journal_*` directly ([[../../../../security/data-ownership]])

## Relations
- Consumes: none directly
- Feeds: nothing cross-domain; busts the aging cache
- In-domain: `ArService::writeOff` posts the GL entry through the ledger/invoicing services, voids the remaining balance, captures `approved_by`

## Test Checklist

### Unit
- [ ] Write-off amount is derived from the invoice's current open balance and never taken from the caller
- [ ] `reason` is required and capped at 1000 chars

### Feature (Pest)
- [ ] `writeOff` posts a balanced bad-debt GL entry via the ledger/invoicing services (never `fin_journal_*` directly), voids the remaining balance, records `approved_by`, and busts the aging cache — under a pessimistic money lock
- [ ] Writing off an already-settled / zero-balance invoice is rejected; tenant isolation — cannot write off another company's invoice; `write-off` permission enforced

### Livewire
- [ ] The write-off action opens with a read-only balance + required reason and is gated on `finance.ar.write-off`; `canAccess` denied without it

See [[../api]], [[../security]], [[../../general-ledger/_module]].
