---
type: module
domain: Field Service Management
panel: fsm
module: Field Invoicing & Payment
phase: 5
status: planned
cssclasses: domain-fsm
migration_range: 1052000–1052499
last_updated: 2026-05-09
---

# Field Invoicing & Payment

Generate and collect invoices on-site at job completion. Supports card payment via Stripe Terminal (physical reader), BACS/SEPA bank transfer with QR code, and post-job invoice via email. Integrates with Finance domain.

---

## Key Tables

```sql
CREATE TABLE fsm_field_invoices (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    job_id          ULID NOT NULL REFERENCES fsm_jobs(id),
    invoice_id      ULID NULL REFERENCES fin_invoices(id),  -- Finance domain invoice
    invoice_number  VARCHAR(20) UNIQUE,
    customer_id     ULID NOT NULL REFERENCES contacts(id),
    status          ENUM('draft','sent','paid','partial','overdue','cancelled'),
    subtotal        DECIMAL(12,2),
    tax_amount      DECIMAL(12,2),
    total           DECIMAL(12,2),
    currency        CHAR(3) DEFAULT 'EUR',
    due_date        DATE NULL,
    paid_at         TIMESTAMP NULL,
    payment_method  ENUM('card_terminal','bank_transfer','cash','account') NULL,
    stripe_payment_intent_id VARCHAR(100) NULL,
    created_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE fsm_invoice_line_items (
    id              ULID PRIMARY KEY,
    invoice_id      ULID NOT NULL REFERENCES fsm_field_invoices(id),
    type            ENUM('labour','part','callout_fee','disposal','other'),
    description     VARCHAR(500),
    quantity        DECIMAL(8,2),
    unit            VARCHAR(20) DEFAULT 'each',  -- 'hour', 'each', 'm2', etc.
    unit_price      DECIMAL(12,2),
    discount_pct    DECIMAL(5,2) DEFAULT 0,
    tax_rate        DECIMAL(5,2) DEFAULT 21,
    line_total      DECIMAL(12,2),
    part_id         ULID NULL
);
```

---

## Auto-Build from Job

On sign-off, invoice pre-populated from:
- Labour: technician time (actual_start → actual_end) × hourly rate from service contract
- Parts: `fsm_job_parts_used` → line items at part sale price
- Callout fee: fixed fee from service contract (if applicable)

Technician can add/remove line items before presenting to customer.

---

## Payment Methods

**Card Terminal (Stripe Terminal)**
- Bluetooth reader (Stripe Reader M2 or BBPOS WisePOS E)
- Technician taps reader on phone → present to customer
- Funds captured via Stripe payment intent

**QR Code (Bank Transfer)**
- QR encodes IBAN + amount + payment reference
- Customer scans, pre-fills their banking app
- Invoice marks as `pending_bank_transfer`

**Cash**
- Technician marks as cash paid, amount entered
- Cash receipt generated

**On Account**
- Invoice emailed to customer, payment due net 30
- Syncs to Finance [[entity-invoice]] for AR follow-up

---

## Finance Integration

On payment:
- `FieldJobInvoicePaid` event → Finance domain records revenue
- Creates/updates `fin_invoices` record
- Updates `fin_receivables` (if on account)

---

## Related

- [[MOC_FieldService]]
- [[customer-sign-off]]
- [[parts-inventory-fsm]]
- [[MOC_Finance]]
- [[entity-invoice]]
