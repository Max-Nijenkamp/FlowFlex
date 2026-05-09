---
tags: [flowflex, domain/finance, revenue-recognition, ifrs15, asc606, phase/6]
domain: Finance
panel: finance
color: "#059669"
status: planned
last_updated: 2026-05-08
---

# Revenue Recognition

Automated IFRS 15 / ASC 606 compliant revenue recognition. For SaaS subscriptions, multi-element contracts, and milestone billing — the system calculates when revenue is earned and posts the entries automatically. Replaces expensive Rev Rec bolt-ons and spreadsheet workbooks.

**Who uses it:** Finance managers, CFOs, auditors
**Filament Panel:** `finance`
**Depends on:** Core, [[Invoicing]], [[Subscription & MRR Tracking]], [[Financial Reporting]], [[Accounts Payable & Receivable]]
**Phase:** 6

---

## Features

### Revenue Recognition Engine

- Identifies performance obligations within each contract
- 5-step IFRS 15 model enforced:
  1. Identify the contract (linked to CRM deal or invoice)
  2. Identify performance obligations (line items flagged as: licence, service, support, setup, etc.)
  3. Determine transaction price
  4. Allocate price to obligations (SSP — standalone selling price rules)
  5. Recognise as obligation satisfied (at point-in-time or over time)
- Over-time recognition: straight-line over service period (SaaS subscription, retainer)
- Point-in-time: recognised on delivery (one-time licence key, implementation go-live)

### Contract Classification

- Per invoice line / contract: set recognition type
  - `over_time` — straight-line from start_date to end_date
  - `point_in_time` — recognise on a specific date
  - `milestone` — recognise on event (e.g. project completion)
  - `percentage_of_completion` — recognise proportionally as work is done
- Multi-element bundles: split revenue across multiple obligations by SSP ratio
- SSP library: define your standalone selling prices per product/service type

### Deferred Revenue Tracking

- Deferred revenue balance per contract: cash received but not yet recognised
- Waterfall schedule: monthly recognition schedule for each contract
- Deferred revenue balance sheet line auto-updated
- Unearned revenue roll-forward report: opening balance + recognised − new deferrals = closing balance

### Automated Journal Entries

- On billing: Dr Accounts Receivable / Cr Deferred Revenue
- On recognition event: Dr Deferred Revenue / Cr Revenue
- Entries auto-posted to ledger (no manual journal required)
- All entries tagged with contract and performance obligation reference for audit

### Revenue Waterfall

- Visual waterfall chart: contracted revenue by month showing when each contract recognises
- Filter by: product type, sales rep, segment
- Committed ARR view: recurring revenue contracted but not yet started

### Contract Modifications

- Handle upgrades, downgrades, extensions (IFRS 15 contract modification rules)
- Prospective vs cumulative catch-up method (selectable per modification type)
- Audit trail: before/after values, who approved modification, effective date

### Reporting

- Revenue recognition journal (all entries for period)
- Deferred revenue ageing by customer
- Revenue by performance obligation type
- Recognised vs billed variance (billing ≠ earned revenue)
- Commission acceleration report: trigger variable comp payout on recognition event
- Export to: CSV for auditors, mapped to trial balance accounts

---

## Database Tables (4)

### `finance_rev_rec_contracts`
| Column | Type | Notes |
|---|---|---|
| `invoice_id` | ulid FK nullable | |
| `crm_deal_id` | ulid FK nullable | |
| `customer_id` | ulid FK | |
| `total_value` | decimal | |
| `currency` | string | |
| `start_date` | date | |
| `end_date` | date nullable | |
| `status` | enum | `active`, `completed`, `modified`, `cancelled` |

### `finance_rev_rec_obligations`
| Column | Type | Notes |
|---|---|---|
| `contract_id` | ulid FK | |
| `description` | string | |
| `obligation_type` | enum | `licence`, `service`, `support`, `setup`, `other` |
| `recognition_method` | enum | `over_time`, `point_in_time`, `milestone`, `pct_completion` |
| `allocated_value` | decimal | SSP-allocated portion |
| `recognised_total` | decimal default 0 | |
| `deferred_balance` | decimal default 0 | |
| `recognition_start` | date nullable | |
| `recognition_end` | date nullable | |

### `finance_rev_rec_schedules`
| Column | Type | Notes |
|---|---|---|
| `obligation_id` | ulid FK | |
| `recognition_date` | date | |
| `amount` | decimal | |
| `status` | enum | `scheduled`, `recognised`, `reversed` |
| `journal_entry_id` | ulid FK nullable | |
| `recognised_at` | timestamp nullable | |

### `finance_rev_rec_ssps`
| Column | Type | Notes |
|---|---|---|
| `product_type` | string | matches obligation_type |
| `product_name` | string nullable | for specific SKU SSPs |
| `ssp_amount` | decimal | |
| `currency` | string | |
| `effective_from` | date | |
| `effective_to` | date nullable | |

---

## Permissions

```
finance.rev-rec.view
finance.rev-rec.configure
finance.rev-rec.override-entries
finance.rev-rec.run-period
finance.rev-rec.export
```

---

## Competitor Comparison

| Feature | FlowFlex | Maxio | Chargebee RevRec | NetSuite ARM |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€€€) | ❌ (€€€) | ❌ (€€€€) |
| IFRS 15 + ASC 606 | ✅ | ✅ | ✅ | ✅ |
| Multi-element allocation | ✅ | ✅ | ✅ | ✅ |
| Auto journal entries | ✅ | ✅ | ✅ | ✅ |
| Contract modifications | ✅ | ✅ | partial | ✅ |
| Native CRM deal integration | ✅ | ❌ | ❌ | ❌ |

---

## Related

- [[Finance Overview]]
- [[Invoicing]]
- [[Subscription & MRR Tracking]]
- [[Financial Reporting]]
- [[Accounts Payable & Receivable]]
- [[Cash Flow Forecasting & Scenario Planning]]
