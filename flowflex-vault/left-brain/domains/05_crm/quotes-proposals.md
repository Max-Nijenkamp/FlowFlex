---
type: module
domain: CRM & Sales
panel: crm
cssclasses: domain-crm
phase: 3
status: in-progress
migration_range: 250009–250010
last_updated: 2026-05-11
right_brain_log: "[[builder-log-crm-phase3]]"
---

# Quotes & Proposals

Build and send branded quotes to prospects. Track acceptance, rejection, and expiry. Convert accepted quotes to invoices. Replaces PandaDoc (quotes), HubSpot Quotes, QuoteWerks.

**Panel:** `crm`  
**Phase:** 3  
**Module key:** `crm.quotes`

---

## Data Model

```erDiagram
    crm_quotes {
        ulid id PK
        ulid company_id FK
        ulid deal_id FK
        ulid contact_id FK
        string number
        string title
        date issue_date
        date expiry_date
        string status
        decimal subtotal
        decimal tax_amount
        decimal total
        string currency
        text notes
        string payment_terms
        timestamp sent_at
        timestamp accepted_at
        timestamp rejected_at
    }

    crm_quote_items {
        ulid id PK
        ulid quote_id FK
        string description
        decimal quantity
        decimal unit_price
        decimal tax_rate
        decimal total
    }
```

**Quote status:** `draft` → `sent` → `accepted` | `rejected` | `expired`

---

## Features

- Quote builder: add line items with description, quantity, unit price, and tax rate
- Quote number: auto-generated (QTE-2026-0001)
- PDF export: branded quote PDF with company logo, terms, and signature area
- Send by email: attach PDF or send shareable link
- Acceptance: recipient clicks "Accept" on a public page → status updated, timestamp recorded
- Rejection: recipient clicks "Reject" (optional reason)
- Expiry: automated job checks expiry_date daily, marks expired quotes
- Convert to invoice: accepted quote → pre-fill invoice with same line items (one-click)

---

## Permissions

```
crm.quotes.view
crm.quotes.create
crm.quotes.edit
crm.quotes.send
crm.quotes.approve
crm.quotes.delete
crm.quotes.convert-to-invoice
```

---

## Related

- [[MOC_CRM]]
- [[sales-pipeline]] — quotes linked to deals
- [[contact-company-management]] — quotes sent to contacts
- [[invoicing]] — accepted quote converts to invoice (Finance module)
