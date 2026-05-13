---
type: module
domain: Finance & Accounting
panel: finance
cssclasses: domain-finance
phase: 3
status: complete
migration_range: 200009–200010
last_updated: 2026-05-12
right_brain_log: "[[builder-log-finance-phase3]]"
---

# Bank Reconciliation

Import bank statements, match transactions against GL entries (invoices paid, bills paid, expenses), and reconcile differences. Phase 3: manual CSV import. Phase 6: live Plaid/PSD2 feeds (see [[open-banking]]).

**Panel:** `finance`  
**Phase:** 3  
**Module key:** `finance.bank`

---

## Data Model

```erDiagram
    bank_accounts {
        ulid id PK
        ulid company_id FK
        string name
        string iban
        string currency
        decimal current_balance
        string bank_name
        boolean is_active
    }

    bank_transactions {
        ulid id PK
        ulid company_id FK
        ulid bank_account_id FK
        date transaction_date
        string description
        decimal amount
        string type
        string reference
        ulid matched_invoice_id FK
        ulid matched_bill_id FK
        ulid matched_expense_id FK
        timestamp reconciled_at
        ulid reconciled_by FK
    }
```

**Transaction type:** `credit` (money in) | `debit` (money out)

---

## Features

### Statement Import (Phase 3)
- Upload bank statement CSV (standard OFX/MT940/CSV formats)
- Auto-map columns on first import, remember mapping per bank
- Duplicate detection: skip transactions already imported (match by date + amount + description)

### Transaction Matching
- Auto-match: find invoice payments, bill payments, expense reimbursements by amount + approximate date
- Manual match: search and link any unmatched transaction to a GL source
- Create new GL entry from transaction (for bank fees, interest, etc.)
- Unmatched transactions report — flag for review

### Reconciliation Summary
- Total debits vs credits per period
- GL balance vs bank statement balance
- Unreconciled difference highlighted

---

## Permissions

```
finance.bank.view
finance.bank.manage-accounts
finance.bank.import
finance.bank.reconcile
```

---

## Related

- [[MOC_Finance]]
- [[general-ledger-chart-of-accounts]] — GL entries to match against
- [[invoicing]] — invoice payments to match
- [[accounts-payable-receivable]] — bill payments to match
- [[open-banking]] — Phase 6 live feeds via Plaid/Tink
