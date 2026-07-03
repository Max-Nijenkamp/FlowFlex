---
domain: finance
module: bank-accounts
feature: csv-import
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — CSV Statement Import

Bank statements are imported from CSV through a chunked queued job.

- `import(ImportStatementData)` accepts a `bank_account_id`, the CSV `file` (max 10MB, `text/csv`), and a `column_map` of `{date, description, amount}` plus a date format.
- `ImportBankStatementJob` (imports queue) processes the file in chunks per [[../../../../architecture/queue-jobs]].
- Each row is hashed (`date + amount + description`) into `import_hash`; the `(bank_account_id, import_hash)` unique constraint makes re-importing the same statement a no-op (zero duplicates).
- The job never aborts on a bad row — malformed lines land in an error report and processing continues.
- `current_balance_cents` is updated on import.
- A rate limiter is intended on the import action in addition to the queued job (see [[../security]]).

## UI

- **Kind**: custom-page (import wizard)
- **Page**: "Import statement" under `/finance/bank/{account}/import`
- **Layout**: upload CSV → map columns `date / description / amount` + choose date format → confirm; chunked work handed to `ImportBankStatementJob` (imports queue)
- **Key interactions**: file upload (max 10MB, `text/csv`), column mapping, date-format pick, submit → queued job; malformed rows land in an error report, processing continues
- **States**: empty (no import yet) · loading (queued job running) · error (bad file / rows in error report) · selected (mapping step for the uploaded file)
- **Gating**: `finance.bank.import` *(assumed — may be `finance.bank.manage`)*

## Data

- Owns / writes: `fin_bank_transactions`; updates `fin_bank_accounts.current_balance_cents`. Money as integer minor units (cents) via brick/money.
- Reads: own tables (for dedup lookup)
- Cross-domain writes: none. Dedup via `(bank_account_id, import_hash)` unique constraint (re-import = no-op); rate-limited import action ([[../../../../security/data-ownership]])

## Relations

- Consumes: no events
- Feeds: no cross-domain events — imported transactions are surfaced to [[reconciliation]]
- in-domain: `ImportBankStatementJob` (imports queue) does the chunked work

## Test Checklist

### Unit
- [ ] Row hashing of `date + amount + description` produces the `import_hash`; identical rows hash equal
- [ ] Column mapping parses the date per the chosen format and amounts into signed integer minor units (brick/money)

### Feature (Pest)
- [ ] Re-importing the same statement creates zero duplicates via the `(bank_account_id, import_hash)` unique constraint
- [ ] Malformed rows land in the error report and the job continues; `current_balance_cents` updates from imported rows; tenant isolation — import writes only the account's own company rows

### Livewire
- [ ] The import wizard enforces the upload contract (`text/csv`, max 10MB) and the column-mapping step, then queues the job; `canAccess` / action denied without `finance.bank.import`; the named import rate limiter applies

See [[../api]], [[../architecture]].
