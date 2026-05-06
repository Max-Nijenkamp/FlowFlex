---
tags: [flowflex, domain/finance, bank-reconciliation, phase/5]
domain: Finance & Accounting
panel: finance
color: "#059669"
status: planned
last_updated: 2026-05-06
---

# Bank Reconciliation

Connect bank accounts and match transactions automatically to invoices and bills.

**Who uses it:** Finance team, bookkeepers
**Filament Panel:** `finance`
**Depends on:** [[Invoicing]], [[Accounts Payable & Receivable]]
**Phase:** 5
**Build complexity:** Very High — 2 resources, 2 pages, 4 tables

## Events Consumed

- `InvoicePaid` (from [[Invoicing]]) → triggers auto-match attempt against bank transactions

## Features

- **Open Banking connection** — Plaid (US/Canada), TrueLayer (UK/EU)
- **Manual bank statement import** — CSV / OFX / QIF
- **Auto-matching rules** — match bank transaction to invoice by amount + reference
- **Confidence scoring on auto-matches** — 100% = exact match, lower = review needed
- **Unmatched transaction queue** — manual classification: categorise, split, or create new bill/invoice
- **Multi-account support** — current accounts, savings, credit cards, PayPal
- **Reconciliation history** — all matched pairs, who matched, when
- **Bank balance vs book balance comparison**
- **Monthly reconciliation sign-off** — finance manager confirms period is reconciled

## Database Tables (4)

1. `bank_accounts` — connected bank account records
2. `bank_transactions` — imported transaction feed
3. `reconciliation_matches` — matched pairs (transaction ↔ invoice/bill)
4. `reconciliation_periods` — sign-off records per period per account

## Related

- [[Finance Overview]]
- [[Invoicing]]
- [[Accounts Payable & Receivable]]
