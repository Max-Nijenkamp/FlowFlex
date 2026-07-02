---
domain: finance
module: general-ledger
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# General Ledger — DTOs, Services & Events

## DTOs

### CreateJournalEntryData (manual entry)
| Field | Type | Validation |
|---|---|---|
| reference | string | required, max:100 |
| description | string | required, max:255 |
| entry_date | CarbonImmutable | required; period open |
| lines | array<{account_id, debit_cents?, credit_cents?, description?}> | min:2; per line exactly one of debit/credit > 0; account active |

Cross-field: `sum(debits) === sum(credits)` — "Journal entry must balance: debits must equal credits."

### TrialBalanceData (output)
Rows[] (account_code, account_name, type, debit_cents, credit_cents), totals, period.

DTOs use `spatie/laravel-data` per [[../../../architecture/patterns/dto-pattern]].

## Services & Actions

Interface→Service: `LedgerServiceInterface` → `LedgerService` — the only write path.

- `post(CreateJournalEntryData $data, ?Model $source = null): JournalEntryData` — validates balance + open period inside `DB::transaction`; throws `UnbalancedEntryException`, `ClosedPeriodException`.
- `reverse(string $journalEntryId, string $reason): JournalEntryData` — mirrored entry; original untouched.
- `trialBalance(CarbonImmutable $from, CarbonImmutable $to): TrialBalanceData`.
- `accountBalance(string $accountId, ?CarbonImmutable $asOf = null): Money`.
- `closePeriod(string $period): void` / `reopenPeriod(string $period): void` — owner-level permission, audited.

## Events

### Consumes: `PayrollRunApproved` (from hr.payroll)
Listener `PostPayrollJournalEntryListener` — queued, `WithCompanyContext`. Posts a balanced entry (gross wages expense / withholdings liability / net wages payable); throws + retries if the period is closed, per the [[../../../architecture/event-bus]] contract.

Invoice/payment/expense postings are direct in-domain service calls — no events.

See [[security]], [[../financial-reporting/_module]].
