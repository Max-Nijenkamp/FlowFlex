---
tags: [flowflex, domain/finance, open-banking, bank-feeds, phase/6]
domain: Finance & Accounting
panel: finance
color: "#059669"
status: planned
last_updated: 2026-05-08
---

# Open Banking & Bank Feeds

Connect your bank accounts directly to FlowFlex. Transactions import automatically, AI categorises them, and reconciliation takes minutes instead of hours.

**Who uses it:** Finance teams, business owners
**Filament Panel:** `finance`
**Depends on:** [[Bank Reconciliation]], [[Accounts Payable & Receivable]]
**Phase:** 6
**Build complexity:** High — 3 resources, 4 tables, external API integration

---

## Features

### Bank Connection

- Connect via Open Banking (PSD2/Open Banking standard — EU and UK)
- Supported connection methods:
  - **GoCardless (Nordigen)** — 2,300+ banks across EU/UK (free tier available)
  - **Plaid** — US, Canada, UK, EU coverage
  - **TrueLayer** — UK and EU
  - **Manual CSV import** — any bank, any country (universal fallback)
- Connection flow: select bank → OAuth redirect → consent → connected in 2 minutes
- Re-authentication: most connections valid 90 days, auto-prompt before expiry

### Transaction Import

- Transactions sync automatically (daily, or real-time for supported banks)
- Import history: last 12 months on initial connect, then continuous
- Deduplication: same transaction never imported twice (hash-based)
- Transaction fields: date, amount, currency, description, reference, bank balance
- Unreviewed / Reviewed / Reconciled status per transaction

### AI Auto-Categorisation

- AI suggests GL account for each transaction based on:
  - Description pattern matching
  - Vendor name recognition (Stripe → Revenue, AWS → IT Costs, Zalando → Office Expenses)
  - Historical categorisation patterns (learns from your corrections)
  - Merchant category codes (MCC) from bank
- Confidence percentage shown per suggestion
- One-click accept or edit
- Bulk accept all high-confidence suggestions
- Categorisation rules: create explicit rules ("If description contains 'Stripe' → Revenue")

### Smart Matching

- Match imported transactions against:
  - Open invoices (by amount + date proximity)
  - Recorded expenses
  - Payroll runs
  - Expected bank transfers (AP payments made)
- Exact match: auto-accept
- Close match: show for confirmation (amount within €1, date within 3 days)
- Unmatched: queue for manual action

### Bank Statement Reconciliation

- Statement balance vs book balance comparison
- Uncleared items shown (timing differences — normal)
- Outstanding items: transactions in books not yet in bank, and vice versa
- One-click lock when reconciled (prevents edits to prior period)
- Reconciliation audit report

### Cash Position Dashboard

- Real-time balance across all connected accounts
- Projected balance chart (based on known payables and receivables)
- Flagged: days with projected negative balance
- Drill down to transactions per account

---

## Database Tables (4)

### `bank_connections`
| Column | Type | Notes |
|---|---|---|
| `bank_name` | string | |
| `provider` | enum | `gocardless`, `plaid`, `truelayer`, `manual` |
| `account_identifier` | string nullable | |
| `account_name` | string | |
| `currency` | string | |
| `last_synced_at` | timestamp nullable | |
| `connection_status` | enum | `active`, `expired`, `error`, `disconnected` |
| `access_token` | string encrypted nullable | |
| `refresh_token` | string encrypted nullable | |
| `token_expires_at` | timestamp nullable | |
| `balance` | decimal nullable | last known balance |
| `balance_at` | timestamp nullable | |

### `bank_transactions`
| Column | Type | Notes |
|---|---|---|
| `bank_connection_id` | ulid FK | |
| `external_id` | string | from bank API — dedup key |
| `date` | date | |
| `amount` | decimal | positive = in, negative = out |
| `currency` | string | |
| `description` | string | raw bank description |
| `reference` | string nullable | |
| `gl_account_id` | ulid FK nullable | → chart of accounts |
| `suggested_gl_account_id` | ulid FK nullable | AI suggestion |
| `ai_confidence` | decimal nullable | 0-1 |
| `matched_type` | string nullable | `invoice`, `expense`, `payroll`, etc. |
| `matched_id` | ulid nullable | |
| `status` | enum | `unreviewed`, `categorised`, `matched`, `reconciled` |
| `reviewed_by` | ulid FK nullable | |
| `reviewed_at` | timestamp nullable | |

### `bank_categorisation_rules`
| Column | Type | Notes |
|---|---|---|
| `match_field` | enum | `description`, `amount`, `reference` |
| `match_operator` | enum | `contains`, `equals`, `starts_with` |
| `match_value` | string | |
| `gl_account_id` | ulid FK | → chart of accounts |
| `priority` | integer | rule order |
| `is_active` | boolean | |

### `bank_reconciliations`
| Column | Type | Notes |
|---|---|---|
| `bank_connection_id` | ulid FK | |
| `period_end` | date | |
| `statement_balance` | decimal | |
| `book_balance` | decimal | |
| `difference` | decimal | |
| `status` | enum | `open`, `reconciled`, `locked` |
| `reconciled_by` | ulid FK nullable | |
| `reconciled_at` | timestamp nullable | |

---

## Permissions

```
finance.bank-connections.view
finance.bank-connections.connect
finance.bank-connections.disconnect
finance.bank-transactions.view
finance.bank-transactions.categorise
finance.bank-transactions.reconcile
finance.bank-reconciliations.lock
```

---

## Competitor Comparison

| Feature | FlowFlex | Xero | QuickBooks | Wave |
|---|---|---|---|---|
| Open Banking connection | ✅ | ✅ | ✅ (US-focused) | ✅ |
| AI auto-categorisation | ✅ | ✅ (basic) | ✅ | ✅ (basic) |
| Custom categorisation rules | ✅ | ✅ | ✅ | ✅ |
| EU bank coverage (2,300+) | ✅ | ✅ (via Yodlee) | partial | partial |
| CSV fallback for any bank | ✅ | ✅ | ✅ | ✅ |
| Included in base price | ✅ | ❌ (Starter limited) | ❌ (Essentials+) | ✅ |

---

## Related

- [[Finance Overview]]
- [[Bank Reconciliation]]
- [[Invoicing]]
- [[Expense Management]]
- [[Cash Flow Forecasting & Scenario Planning]]
