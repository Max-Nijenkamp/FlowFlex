---
type: module
domain: Field Service Management
panel: field
module-key: field.invoicing
status: planned
color: "#4ADE80"
---

# Job Invoicing

> Generate and send invoices from completed field work orders — labour, parts, and expenses with customer approval flow.

**Panel:** `field`
**Module key:** `field.invoicing`

---

## What It Does

Job Invoicing converts completed work orders into customer invoices. When a technician marks a job complete, the system pre-populates an invoice draft with labour time at the applicable rate, parts consumed from the work order, and any additional expenses logged. Service managers review the draft, apply any warranty credits or SLA breach credits, and send the invoice to the customer by email. Customers can view and approve invoices on a self-service portal. Invoices sync to the finance module for accounts receivable and general ledger posting.

---

## Features

### Core
- Invoice generation: create invoice from completed work order with pre-populated labour, parts, and expenses
- Labour billing: calculate labour cost from technician time on job and hourly rate for the job type
- Parts billing: line items for parts consumed with quantity and unit price
- Invoice review: service manager reviews draft before sending to customer
- Invoice sending: email invoice as PDF with payment link
- Invoice status: draft → sent → approved → paid → overdue

### Advanced
- Warranty credits: auto-apply credit for labour or parts covered under asset warranty
- SLA breach credits: apply credit note when an SLA breach entitles the customer to a discount
- Maintenance contract billing: suppress labour charges for jobs covered by a flat-rate maintenance contract
- Multi-job invoicing: batch multiple completed work orders into a single consolidated invoice for a customer
- Custom rate cards: configure labour rates by technician skill level, job type, and customer contract
- Partial payment: record partial payments against an invoice with balance tracking

### AI-Powered
- Invoice anomaly detection: flag invoices with unusually high labour or parts costs relative to the job type
- Payment risk scoring: predict likelihood of late payment based on customer payment history
- Optimal billing cycle: recommend when to send consolidated invoices to maximise cash collection rate

---

## Data Model

```erDiagram
    job_invoices {
        ulid id PK
        ulid company_id FK
        ulid customer_id FK
        ulid work_order_id FK
        string invoice_number
        string status
        date invoice_date
        date due_date
        decimal subtotal
        decimal tax_amount
        decimal total
        decimal amount_paid
        timestamps created_at_updated_at
    }

    job_invoice_lines {
        ulid id PK
        ulid company_id FK
        ulid invoice_id FK
        string line_type
        string description
        decimal quantity
        decimal unit_price
        decimal line_total
        timestamps created_at_updated_at
    }

    job_invoice_payments {
        ulid id PK
        ulid company_id FK
        ulid invoice_id FK
        decimal amount
        date payment_date
        string payment_method
        string reference
        timestamps created_at_updated_at
    }

    job_invoices ||--o{ job_invoice_lines : "contains"
    job_invoices ||--o{ job_invoice_payments : "receives"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `job_invoices` | Invoice records | `id`, `company_id`, `customer_id`, `work_order_id`, `invoice_number`, `status`, `due_date`, `total` |
| `job_invoice_lines` | Labour, parts, and expense lines | `id`, `invoice_id`, `line_type`, `description`, `quantity`, `unit_price`, `line_total` |
| `job_invoice_payments` | Payments received | `id`, `invoice_id`, `amount`, `payment_date`, `payment_method` |

---

## Permissions

```
field.invoicing.view-own
field.invoicing.view-all
field.invoicing.create
field.invoicing.send
field.invoicing.record-payment
```

---

## Filament

- **Resource:** `App\Filament\Field\Resources\JobInvoiceResource`
- **Pages:** `ListJobInvoices`, `CreateJobInvoice`, `EditJobInvoice`, `ViewJobInvoice`
- **Custom pages:** `InvoiceAgingReportPage`
- **Widgets:** `OutstandingInvoicesWidget`, `CollectionRateWidget`
- **Nav group:** Invoicing

---

## Displaces

| Feature | FlowFlex | ServiceTitan | FieldAware | Jobber |
|---|---|---|---|---|
| Work-order-to-invoice | Yes | Yes | Yes | Yes |
| Labour and parts billing | Yes | Yes | Yes | Yes |
| Warranty credit application | Yes | Yes | Partial | No |
| AI payment risk scoring | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[work-orders]] — invoice generated from completed work order
- [[part-inventory]] — parts cost pulled from work order consumption record
- [[service-level-agreements]] — SLA breach credits applied to invoice
- [[customer-assets]] — warranty status checked to suppress charges
- [[finance/INDEX]] — invoices posted to accounts receivable ledger
