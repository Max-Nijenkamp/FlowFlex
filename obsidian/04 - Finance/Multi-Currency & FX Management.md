---
tags: [flowflex, domain/finance, multi-currency, fx, phase/6]
domain: Finance & Accounting
panel: finance
color: "#059669"
status: planned
last_updated: 2026-05-08
---

# Multi-Currency & FX Management

Invoice, pay, and report in any currency. Live exchange rates, automatic currency conversion, and reporting in your base currency — without a spreadsheet in sight.

**Who uses it:** Finance teams, businesses with international customers/suppliers
**Filament Panel:** `finance`
**Depends on:** [[Invoicing]], [[Accounts Payable & Receivable]], [[Bank Reconciliation]]
**Phase:** 6
**Build complexity:** High — extends all finance modules, 3 new tables

---

## Features

### Base & Transaction Currencies

- Set company base currency (e.g. EUR, GBP, USD)
- Create invoices / expenses in any ISO 4217 currency
- All amounts stored in both transaction currency and base currency equivalent
- Historical rate locked at transaction date (no restatement surprises)

### Live Exchange Rates

- Auto-fetch rates daily from ECB (European Central Bank) — free, reliable
- Optional: Open Exchange Rates or Fixer.io API for more currencies or higher frequency
- Manual rate override: enter your own rate for a specific transaction (e.g. bank's actual rate)
- Rate history stored — always know what rate was used for any historical transaction

### Currency Revaluation

- Month-end revaluation: recalculate open AR/AP balances at current rates
- Realised vs unrealised FX gains/losses tracked separately
- Revaluation journal automatically created and posted
- FX P&L report: gain/loss by currency by period

### Multi-Currency Invoicing

- Customer's preferred currency stored on contact record
- Invoice auto-populates in customer's currency
- Show base currency equivalent on invoice (optional toggle)
- Payment in any currency — FX difference auto-posted to FX gain/loss account

### Multi-Currency Expenses

- Employee submits expense in any currency
- Auto-converts to base currency at daily rate on receipt date
- Manual override if rate unclear

### Multi-Currency Bank Accounts

- Record multiple bank accounts, each with own currency
- Bank feed (when [[Open Banking & Bank Feeds]] active) reconciles in account currency
- Cross-currency transfers recorded with both sides and FX rate

### Reporting in Base Currency

- All financial reports normalised to base currency
- P&L and Balance Sheet: base currency only
- Currency breakdown report: revenue/costs by currency
- FX exposure report: open receivables/payables by currency

---

## Database Tables (3)

### `currencies`
| Column | Type | Notes |
|---|---|---|
| `code` | string unique | ISO 4217 (EUR, USD, GBP, etc.) |
| `name` | string | |
| `symbol` | string | |
| `is_active` | boolean | company has enabled this currency |
| `is_base` | boolean | only one per company |

### `fx_rates`
| Column | Type | Notes |
|---|---|---|
| `from_currency` | string | |
| `to_currency` | string | |
| `rate` | decimal(18, 8) | |
| `rate_date` | date | |
| `source` | enum | `ecb`, `openexchangerates`, `manual` |

### `fx_revaluations`
| Column | Type | Notes |
|---|---|---|
| `revaluation_date` | date | |
| `currency` | string | |
| `gain_loss_amount` | decimal | in base currency |
| `journal_id` | ulid FK | → finance_journal_entries |
| `account_id` | ulid FK | FX gain/loss GL account |
| `run_by` | ulid FK | |

---

## Permissions

```
finance.currencies.view
finance.currencies.manage
finance.fx-rates.view
finance.fx-rates.override
finance.fx-revaluation.run
finance.fx-reports.view
```

---

## Competitor Comparison

| Feature | FlowFlex | Xero | QuickBooks | Sage |
|---|---|---|---|---|
| Multi-currency invoicing | ✅ | ✅ (premium) | ✅ (plus) | ✅ |
| Live ECB rates (free) | ✅ | ❌ (paid add-on) | ❌ (paid) | ❌ (paid) |
| FX revaluation | ✅ | ✅ | ✅ | ✅ |
| FX gains/losses P&L | ✅ | ✅ | ✅ | ✅ |
| Included in base price | ✅ | ❌ (Premium tier) | ❌ (Plus tier) | varies |

---

## Related

- [[Finance Overview]]
- [[Invoicing]]
- [[Accounts Payable & Receivable]]
- [[Financial Reporting]]
- [[Bank Reconciliation]]
