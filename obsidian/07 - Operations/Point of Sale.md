---
tags: [flowflex, domain/operations, pos, retail, phase/4]
domain: Operations
panel: operations
color: "#D97706"
status: planned
last_updated: 2026-05-07
---

# Point of Sale

Browser-based POS terminals for retail and counter sales. Real-time stock deduction, session float management, and full sales history.

**Who uses it:** Retail staff, cashiers, store managers, operations managers
**Filament Panel:** `operations`
**Depends on:** [[Product Catalogue]], [[Inventory Management]], [[CRM — Contact & Company Management]]
**Phase:** 4
**Build complexity:** High — 4 resources, 3 pages, 5 tables

---

## Features

- **Terminal management** — register named POS terminals per location; each has its own session history and float management
- **Session open/close with float** — staff enter opening float; closing float is reconciled against expected cash; variance is flagged
- **Product lookup** — fast barcode scan or search across the product catalogue; price loaded automatically
- **Customer lookup** — attach a CRM contact to a transaction for loyalty, receipt emailing, and purchase history
- **Multi-line transactions** — add/remove lines, apply per-line or basket-level discounts
- **Multiple payment methods** — cash, card, voucher, split payment across methods in one transaction
- **Real-time stock deduction** — on transaction completion, fires `POSTransactionCompleted` → Inventory deducts stock
- **Refunds and voids** — full or partial transaction refund; creates a negative stock adjustment
- **Receipt printing and email** — print to connected thermal printer or email receipt to attached CRM contact
- **Sales session reports** — per-session Z-report: total sales by payment method, cash variance, item totals
- **Offline mode** — transactions queued locally when connectivity is lost; sync on reconnect
- **Tax handling** — applies tax class from product; splits tax per line for receipt clarity
- **Invoice creation** — `POSTransactionCompleted` event triggers invoice record in [[Invoicing]] for B2B transactions
- **Dashboard widgets** — today's sales, top-selling products, average basket value, hourly sales chart

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `pos_terminals`
| Column | Type | Notes |
|---|---|---|
| `name` | string | e.g. "Counter 1" |
| `location` | string nullable | store or area name |
| `is_active` | boolean | default true |
| `last_synced_at` | timestamp nullable | for offline sync |
| `settings` | json nullable | receipt header, printer config |

### `pos_sessions`
| Column | Type | Notes |
|---|---|---|
| `pos_terminal_id` | ulid FK | → pos_terminals |
| `tenant_id` | ulid FK | opened by → tenants |
| `status` | enum | `open`, `closed` |
| `opened_at` | timestamp | |
| `closed_at` | timestamp nullable | |
| `opening_float` | decimal(10,2) | |
| `closing_float` | decimal(10,2) nullable | entered by staff |
| `expected_float` | decimal(10,2) nullable | computed: opening + cash sales - refunds |
| `float_variance` | decimal(10,2) nullable | closing - expected |
| `total_sales` | decimal(10,2) nullable | sum of completed transactions |
| `total_refunds` | decimal(10,2) nullable | |
| `transaction_count` | integer nullable | |
| `notes` | text nullable | closing notes |

### `pos_transactions`
| Column | Type | Notes |
|---|---|---|
| `pos_session_id` | ulid FK | → pos_sessions |
| `crm_contact_id` | ulid FK nullable | → crm_contacts |
| `reference` | string unique | auto-generated, e.g. "POS-00042" |
| `subtotal` | decimal(10,2) | |
| `tax_total` | decimal(10,2) | |
| `discount_total` | decimal(10,2) default 0 | |
| `total` | decimal(10,2) | |
| `status` | enum | `pending`, `completed`, `voided`, `refunded` |
| `notes` | string nullable | |
| `invoice_id` | ulid FK nullable | → invoices (if B2B) |

### `pos_transaction_lines`
| Column | Type | Notes |
|---|---|---|
| `pos_transaction_id` | ulid FK | → pos_transactions |
| `product_id` | ulid FK | → products |
| `product_variant_id` | ulid FK nullable | → ec_product_variants |
| `description` | string | product name snapshot |
| `quantity` | decimal(10,3) | |
| `unit_price` | decimal(10,2) | |
| `discount_amount` | decimal(10,2) default 0 | |
| `discount_pct` | decimal(5,2) default 0 | |
| `tax_rate` | decimal(5,2) | |
| `tax_amount` | decimal(10,2) | |
| `total` | decimal(10,2) | |

### `pos_payments`
| Column | Type | Notes |
|---|---|---|
| `pos_transaction_id` | ulid FK | → pos_transactions |
| `method` | enum | `cash`, `card`, `voucher`, `account` |
| `amount` | decimal(10,2) | |
| `reference` | string nullable | card auth code, voucher number |
| `tendered` | decimal(10,2) nullable | cash given (for change calc) |
| `change_given` | decimal(10,2) nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `POSTransactionCompleted` | `pos_transaction_id`, `lines[]` | [[Inventory Management]] (deduct stock per line), [[Invoicing]] (record sale as invoice for B2B) |

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `ClockIn` | [[Scheduling & Shifts]] | Starts shift tracking for the till operator tied to the terminal |

---

## Permissions

```
operations.pos-terminals.view
operations.pos-terminals.create
operations.pos-terminals.edit
operations.pos-terminals.delete
operations.pos-sessions.view
operations.pos-sessions.open
operations.pos-sessions.close
operations.pos-transactions.view
operations.pos-transactions.create
operations.pos-transactions.refund
operations.pos-transactions.void
operations.pos-reports.view
```

---

## Related

- [[Operations Overview]]
- [[Product Catalogue]]
- [[Inventory Management]]
- [[Invoicing]]
- [[CRM — Contact & Company Management]]
- [[Scheduling & Shifts]]
