---
type: module
domain: Subscription Billing & RevOps
panel: billing
module-key: billing.invoicing
status: planned
color: "#4ADE80"
---

# Invoicing

> Automated subscription invoice generation, Stripe payment collection, and invoice lifecycle management.

**Panel:** `billing`
**Module key:** `billing.invoicing`

---

## What It Does

Invoicing automates the generation and collection of subscription invoices. At the start of each billing period, the system generates an invoice for each active subscription based on the plan's price and interval, sends it to the customer, and initiates payment collection via Stripe. Invoice status is tracked through paid, unpaid, overdue, and void states. Manual invoices can be raised for one-off charges, and credit notes handle adjustments. All invoices are stored as downloadable PDFs and pushed to the Finance panel's accounts receivable.

---

## Features

### Core
- Automated invoice generation: invoice created at the start of each billing period per active subscription
- Stripe payment collection: charge the customer's payment method on file via Stripe
- Invoice PDF: branded PDF invoice sent to the billing contact by email
- Payment status tracking: draft, sent, paid, overdue, void, uncollectable
- Manual invoices: one-off invoices for professional services, setup fees, or custom charges
- Credit notes: issue a credit note against a previous invoice for refunds or adjustments

### Advanced
- Proration: calculate prorated charges or credits when a customer upgrades or downgrades mid-cycle
- Multi-currency: generate invoices in the customer's preferred currency
- Tax calculation: apply tax rules by customer location (VAT, GST, sales tax) via Stripe Tax
- Invoice customisation: add custom line items, payment terms, and footer notes to invoices
- Bulk invoice run: trigger a manual billing run for a cohort of accounts

### AI-Powered
- Invoice anomaly detection: flag invoices significantly higher or lower than the account's typical billing amount
- Payment timing prediction: estimate the likelihood of payment before the due date based on payment history
- Revenue recognition trigger: automatically queue an invoice for revenue recognition on payment receipt

---

## Data Model

```erDiagram
    subscriptions {
        ulid id PK
        ulid account_id FK
        ulid plan_id FK
        ulid company_id FK
        string status
        date current_period_start
        date current_period_end
        string stripe_subscription_id
        timestamps created_at_updated_at
    }

    invoices {
        ulid id PK
        ulid subscription_id FK
        ulid account_id FK
        ulid company_id FK
        string invoice_number
        date invoice_date
        date due_date
        decimal subtotal
        decimal tax_amount
        decimal total
        string currency
        string status
        string stripe_invoice_id
        string pdf_url
        timestamps created_at_updated_at
    }

    subscriptions ||--o{ invoices : "generates"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `subscriptions` | Active subscriptions | `id`, `account_id`, `plan_id`, `status`, `current_period_start`, `stripe_subscription_id` |
| `invoices` | Invoice records | `id`, `subscription_id`, `invoice_number`, `due_date`, `total`, `status`, `stripe_invoice_id` |

---

## Permissions

```
billing.invoicing.view-any
billing.invoicing.create-manual
billing.invoicing.void
billing.invoicing.issue-credit-note
billing.invoicing.export
```

---

## Filament

- **Resource:** `App\Filament\Billing\Resources\InvoiceResource`
- **Pages:** `ListInvoices`, `CreateManualInvoice`, `ViewInvoice`
- **Custom pages:** `BillingRunPage`, `AccountBillingHistoryPage`
- **Widgets:** `InvoicesDueWidget`, `CollectionRateWidget`, `OverdueAmountWidget`
- **Nav group:** Invoicing

---

## Displaces

| Feature | FlowFlex | Chargebee | Zuora | Stripe Billing |
|---|---|---|---|---|
| Automated invoice generation | Yes | Yes | Yes | Yes |
| PDF invoicing | Yes | Yes | Yes | Yes |
| Credit notes | Yes | Yes | Yes | Yes |
| AI anomaly detection | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[subscription-plans]] — plan price and interval drives invoice generation
- [[dunning]] — failed payment on an invoice triggers dunning
- [[revenue-recognition]] — paid invoices queue revenue recognition
- [[finance/INDEX]] — invoices posted to accounts receivable
