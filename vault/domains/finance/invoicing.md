---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.invoicing
status: planned
color: "#4ADE80"
---

# Invoicing

> Customer invoices — create, customise, send by email, track payment status, set up recurring invoices, and post to the General Ledger automatically.

**Panel:** `finance`
**Module key:** `finance.invoicing`

## What It Does

Invoicing manages the complete outbound invoice lifecycle. Finance creates an invoice, adds line items (products, services, or time entries imported from the Projects Time Tracking module), applies tax rates from the Tax Management module, and sends it to the customer by email as a PDF. The customer can pay via a payment link. Payment status is tracked: outstanding, partially paid, paid, overdue. On send, the invoice automatically posts a journal entry (debit AR, credit Revenue) to the General Ledger. Recurring invoice schedules auto-generate new invoices on the configured cadence.

## Features

### Core
- Invoice creation: customer (from CRM contacts), invoice date, due date, line items (description, quantity, unit price, tax rate), discount, notes
- PDF generation: branded invoice PDF with company logo and branding from Company Settings
- Email delivery: send invoice PDF to customer with a payment link — tracked opens and clicks
- Payment status: outstanding / partially_paid / paid / overdue — updated on payment event
- GL posting: on invoice send, auto-posts journal — debit Accounts Receivable, credit Revenue account (configured per line item)

### Advanced
- Recurring invoices: schedule for weekly / monthly / quarterly — auto-generated on date with same line items; configurable end date
- Import from time tracking: pull billable time entries from Projects Time Tracking — converts to invoice line items with hours × rate
- Credit notes: issue a credit note against a sent invoice — posts a reversal journal entry
- Multi-currency: invoice in customer's currency; base currency equivalent computed at invoice exchange rate
- Custom invoice numbering: configurable prefix and sequence (e.g. INV-2026-0001)

### AI-Powered
- Overdue prediction: AI identifies invoices likely to become overdue based on customer payment history and flags them for proactive follow-up before the due date
- Line item suggestions: based on CRM deal products or prior invoices to the same customer, AI pre-populates likely line items when creating a new invoice

## Data Model

```erDiagram
    invoices {
        ulid id PK
        ulid company_id FK
        ulid contact_id FK
        string invoice_number "unique"
        date invoice_date
        date due_date
        string currency
        decimal subtotal
        decimal tax_total
        decimal discount
        decimal total
        decimal amount_paid
        string status
        string payment_link
        ulid journal_entry_id FK
        timestamps created_at/updated_at
    }

    invoice_line_items {
        ulid id PK
        ulid invoice_id FK
        string description
        decimal quantity
        decimal unit_price
        decimal tax_rate
        decimal line_total
        ulid account_id FK
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `status` | draft / sent / outstanding / partially_paid / paid / overdue / void |
| `payment_link` | Stripe payment link or manual payment URL |
| `account_id` | Revenue account this line item posts to in GL |

## Permissions

- `finance.invoicing.view`
- `finance.invoicing.create`
- `finance.invoicing.send`
- `finance.invoicing.record-payment`
- `finance.invoicing.manage-recurring`

## Filament

- **Resource:** `InvoiceResource`
- **Pages:** `ListInvoices`, `CreateInvoice`, `EditInvoice`, `ViewInvoice` (with payment history and GL entry)
- **Custom pages:** None
- **Widgets:** `OutstandingInvoicesWidget` — total outstanding amount and count on finance dashboard
- **Nav group:** Invoicing (finance panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Xero Invoicing | Customer invoicing |
| FreshBooks | Invoice creation and sending |
| QuickBooks Invoicing | Customer billing |
| Wave | Free invoicing tool |

## Related

## Implementation Notes

**PDF generation:** Invoice PDF uses `barryvdh/laravel-dompdf` (add to `composer.json`). The Blade template is at `resources/views/finance/invoice.blade.php` — it reads company branding (logo URL from `spatie/laravel-media-library`, primary colour from company settings) and renders the invoice table with line items, tax, and total. The PDF is stored in S3 via media library on the invoice record and also attached to the email. Generate PDF in `InvoiceSentJob` dispatched when the invoice status changes to `sent`.

**Email delivery:** Use Laravel Mail (configured mail driver per environment). The email contains: a payment link button, the invoice PDF as an attachment, and a brief summary. Track email opens and clicks via the same pixel/redirect mechanism as the marketing email module — or use Mailgun/SendGrid webhook events if those providers are chosen for the mail driver.

**Payment link — Stripe integration:** `invoices.payment_link` is generated via `stripe->paymentLinks()->create([...])` or via a Stripe Checkout Session. Stripe fires `payment_intent.succeeded` webhook when paid — the `InvoicePaymentController` (webhook handler at `POST /webhooks/stripe`) updates `invoices.amount_paid` and `status`. Partial payments: if `amount_paid < total`, status remains `partially_paid`. When `amount_paid >= total`, status becomes `paid` and a `PaymentReceived` event is fired (which triggers GL journal for bank receipt).

**Custom invoice numbering:** `invoices.invoice_number` is generated by `InvoiceNumberingService::next($companyId)`. Store the current sequence number in a `company_invoice_sequences {ulid company_id, string prefix, integer next_number}` table — not currently defined. Use a PostgreSQL `SELECT ... FOR UPDATE` lock to prevent concurrent duplicate numbering.

**AI features:** Overdue prediction is a PHP scoring function comparing the customer's historical payment delays (days from due date to payment date in past invoices) — no LLM needed. Line item suggestions call `app/Services/AI/InvoiceAiService.php` wrapping OpenAI GPT-4o, which reads the contact's last 3 invoices and suggests recurring line items.

**Missing from data model:** `invoices` has no `ulid company_id FK` visible in the erDiagram — add it explicitly (required for `BelongsToCompany`). Also, `invoice_line_items` needs `ulid company_id FK` for the same reason if it has its own queries. `invoices.payment_link` should store the Stripe Checkout Session or Payment Link URL — add a `string stripe_payment_intent_id nullable` column to link back to Stripe for payment status webhook matching.

- [[general-ledger]]
- [[accounts-receivable]]
- [[tax-management]]
- [[multi-currency]]
- [[time-tracking]]
