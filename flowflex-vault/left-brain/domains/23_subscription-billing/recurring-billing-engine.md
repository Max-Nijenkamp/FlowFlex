---
type: module
domain: Subscription Billing & RevOps
panel: subscriptions
phase: 3
status: planned
cssclasses: domain-subscriptions
migration_range: 975300–975499
last_updated: 2026-05-09
---

# Recurring Billing Engine

Automated charge execution on renewal dates. Handles multi-currency, payment method management, invoice generation, and payment gateway integration.

---

## Billing Execution

Scheduled job runs daily (typically 00:00 UTC):
1. Find all subscriptions with `current_period_end = today`
2. For each: calculate charge amount (plan price × quantity + any usage charges)
3. Attempt charge via payment gateway
4. On success: create invoice, advance period dates, emit `PaymentSucceeded`
5. On failure: emit `PaymentFailed` → hands to [[dunning-management]]

---

## Payment Gateways

| Gateway | Card | SEPA | iDEAL | BACS | ACH |
|---|---|---|---|---|---|
| Stripe | ✓ | ✓ | ✓ | ✓ | ✓ |
| Mollie | ✓ | ✓ | ✓ | - | - |
| Adyen | ✓ | ✓ | ✓ | ✓ | ✓ |

Default: Stripe. Configurable per tenant. Webhook listener updates payment status.

---

## Invoice Generation

On successful payment:
- Invoice PDF auto-generated with: subscription line items, usage items, taxes, payment method used, next billing date
- Sent to billing email address
- Stored in customer record
- Finance GL entry: debit AR (or debit Cash if immediate) / credit Revenue (or Deferred Revenue)

---

## Multi-Currency

- Store subscription price in plan's currency
- Charge in customer's preferred currency (stored on subscription)
- Exchange rate locked at invoice generation time (FX rate log maintained)
- Display in both currencies on invoice if different from plan currency

---

## Tax Handling

- EU VAT: reverse charge for B2B (VAT number required), charge VAT for B2C
- UK VAT: 20% on digital services
- US sales tax: per-state rules (integrate Avalara or TaxJar API, or manual exempt certificates)
- Tax exempt: store customer tax exemption certificate

---

## Data Model

### `sub_invoices`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| subscription_id | ulid | FK |
| crm_company_id | ulid | FK |
| status | enum | draft/open/paid/void/uncollectible |
| subtotal | decimal(14,4) | |
| tax_amount | decimal(14,4) | |
| total | decimal(14,4) | |
| currency | char(3) | |
| payment_gateway | varchar(50) | |
| gateway_invoice_id | varchar | nullable |
| paid_at | timestamp | nullable |
| due_date | date | |

---

## Migration

```
975300_create_sub_invoices_table
975301_create_sub_invoice_line_items_table
975302_create_sub_payment_methods_table
975303_create_sub_payment_attempts_table
```

---

## Related

- [[MOC_SubscriptionBilling]]
- [[subscription-lifecycle-management]]
- [[dunning-management]]
- [[revenue-recognition]]
- [[MOC_Finance]] — invoices → AR
