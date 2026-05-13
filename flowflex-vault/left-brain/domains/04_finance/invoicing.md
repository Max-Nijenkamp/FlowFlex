---
type: module
domain: Finance & Accounting
panel: finance
cssclasses: domain-finance
phase: 3
status: complete
migration_range: 200005–200006
last_updated: 2026-05-12
right_brain_log: "[[builder-log-finance-phase3]]"
---

# Invoicing

Create, send, and track invoices. Supports one-off and recurring invoices, payment links, PDF export, and automatic journal posting to the GL. Replaces Xero Invoicing, QuickBooks Invoices, FreeAgent.

**Panel:** `finance`  
**Phase:** 3 — core revenue recording, depends on GL & Chart of Accounts  
**Module key:** `finance.invoicing`

---

## Data Model

```erDiagram
    invoices {
        ulid id PK
        ulid company_id FK
        ulid contact_id FK
        string number
        date issue_date
        date due_date
        string status
        decimal subtotal
        decimal tax_amount
        decimal total
        string currency
        text notes
        string payment_terms
        ulid created_by FK
        ulid approved_by FK
        timestamp sent_at
        timestamp paid_at
    }

    invoice_items {
        ulid id PK
        ulid invoice_id FK
        string description
        decimal quantity
        decimal unit_price
        decimal tax_rate
        decimal total
        ulid account_id FK
    }

    invoice_payments {
        ulid id PK
        ulid invoice_id FK
        ulid bank_account_id FK
        decimal amount
        date payment_date
        string method
        string reference
    }
```

**Invoice status flow:** `draft` → `sent` → `paid` | `overdue` | `cancelled`

---

## Service: InvoiceService

```php
createInvoice(CreateInvoiceData $data): Invoice
updateInvoice(Invoice $invoice, UpdateInvoiceData $data): Invoice
markAsSent(Invoice $invoice): void          // sets sent_at, triggers InvoiceSent event
markAsPaid(Invoice $invoice, PaymentData $payment): void  // posts GL payment entry
generateInvoiceNumber(string $companyId): string  // sequential per company: INV-2026-0001
calculateTotals(Invoice $invoice): void
duplicateInvoice(Invoice $invoice): Invoice
sendPaymentReminder(Invoice $invoice): void
```

---

## Events

| Event | Trigger | Consumed By |
|---|---|---|
| `InvoiceSent` | markAsSent() | Notifications, CRM (log activity), GL (debit AR) |
| `InvoicePaid` | markAsPaid() | CRM (update deal), Finance (update AR), GL (debit Bank, credit AR) |
| `InvoiceOverdue` | Scheduled job (daily check) | Notifications, Credit Control dunning |

---

## GL Integration

On invoice sent → post journal:
- Debit `1100 Accounts Receivable` for total amount
- Credit `4000 Sales Revenue` for subtotal per line account
- Credit `2200 VAT Payable` for tax amount

On payment received → post journal:
- Debit `1000 Cash & Bank`
- Credit `1100 Accounts Receivable`

---

## Permissions

```
finance.invoices.view
finance.invoices.create
finance.invoices.approve
finance.invoices.send
finance.invoices.delete
finance.invoices.record-payment
```

---

## Related

- [[MOC_Finance]]
- [[general-ledger-chart-of-accounts]] — GL posting
- [[credit-control]] — overdue invoice handling
- [[accounts-payable-receivable]] — AR aging reports
- [[client-billing-retainers]] — retainer → invoice auto-creation
