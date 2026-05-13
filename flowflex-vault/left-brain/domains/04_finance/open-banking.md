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

# Open Banking & Bank Feeds

Import bank transactions into FlowFlex for reconciliation. Phase 3: CSV statement upload. Phase 6: live bank feeds via Plaid, Tink (PSD2), or Nordigen for automatic daily sync.

**Panel:** `finance`  
**Phase:** 3 (CSV import) · 6 (live feeds)  
**Module key:** `finance.open-banking`

---

## Phase 3 — CSV Import

Uses `bank_accounts` and `bank_transactions` tables (see [[bank-reconciliation]]).

### Supported Formats
- Generic CSV (configurable column mapping)
- MT940 (SWIFT bank statement format — common in Netherlands/EU)
- OFX/QFX (Open Financial Exchange — common in UK/US)
- CAMT.053 (ISO 20022 — modern EU bank format)

### Import Flow
1. Select bank account in FlowFlex
2. Upload statement file
3. Map columns on first import (remembered per bank per company)
4. Preview transactions before import
5. Duplicate check: skip rows already in `bank_transactions` by date + amount + description hash
6. Import — transactions appear in Bank Reconciliation module

---

## Phase 6 — Live Bank Feeds (Deferred)

Future integration with:
- **Plaid** (UK/US) — OAuth bank connection, daily transaction sync
- **Tink / Nordigen** (EU / PSD2) — open banking API, EU bank coverage
- **GoCardless Bank Data** (formerly Nordigen) — strong EU coverage

Phase 6 will add:
- OAuth bank connection flow (one-time consent per bank account)
- Automatic daily import of new transactions
- Webhook on new transaction → trigger reconciliation suggestions

---

## Permissions

```
finance.bank.import
finance.bank.view
finance.bank.manage-connections
```

---

## Related

- [[MOC_Finance]]
- [[bank-reconciliation]] — imported transactions feed directly into reconciliation
- [[general-ledger-chart-of-accounts]] — reconciliation posts to GL
