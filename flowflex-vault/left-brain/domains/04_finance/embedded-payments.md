---
type: module
domain: Finance & Accounting
panel: finance
module: Embedded Payments
phase: 4
status: planned
cssclasses: domain-finance
migration_range: 259000–259499
last_updated: 2026-05-09
---

# Embedded Payments

Virtual IBANs per customer for clean payment reconciliation, SEPA/BACS batch payment runs, and B2B Buy Now Pay Later (BNPL) via integrated providers. Eliminates manual bank statement matching.

---

## Why This Matters

Current state without embedded payments:
- Payments arrive at company bank account with no reference
- Finance team matches bank statement transactions to invoices manually
- Takes 2–4 hours/day for companies with 200+ invoices/month

With virtual IBANs (Modulr, Stripe Treasury, Swan, or similar):
- Each customer gets a unique IBAN
- All payments to that IBAN automatically reconcile to that customer
- Zero manual matching

---

## Key Tables

```sql
CREATE TABLE fin_virtual_ibans (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    customer_id     ULID NOT NULL REFERENCES contacts(id),
    iban            VARCHAR(34) NOT NULL UNIQUE,
    bic             VARCHAR(11) NOT NULL,
    provider        VARCHAR(50) NOT NULL,    -- 'modulr', 'stripe_treasury', 'swan'
    provider_account_id VARCHAR(255) UNIQUE,
    currency        CHAR(3) DEFAULT 'EUR',
    status          ENUM('active','suspended','closed'),
    created_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE fin_payment_batch_runs (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    type            ENUM('sepa_credit','bacs_credit','faster_payments'),
    status          ENUM('draft','approved','submitted','completed','failed'),
    total_amount    DECIMAL(14,2),
    payment_count   INT,
    submission_ref  VARCHAR(255) NULL,
    approved_by     ULID NULL REFERENCES users(id),
    approved_at     TIMESTAMP NULL,
    submitted_at    TIMESTAMP NULL,
    value_date      DATE NULL,
    created_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE fin_payment_batch_items (
    id              ULID PRIMARY KEY,
    batch_id        ULID NOT NULL REFERENCES fin_payment_batch_runs(id),
    payable_id      ULID NOT NULL,       -- invoice or expense claim
    payable_type    VARCHAR(50),
    payee_name      VARCHAR(255),
    payee_iban      VARCHAR(34),
    payee_bic       VARCHAR(11) NULL,
    amount          DECIMAL(12,2),
    currency        CHAR(3),
    reference       VARCHAR(140),        -- SEPA EndToEndId
    status          ENUM('pending','sent','confirmed','failed','returned'),
    bank_response   JSON NULL
);

CREATE TABLE fin_bnpl_applications (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    customer_id     ULID NOT NULL REFERENCES contacts(id),
    invoice_id      ULID NOT NULL,
    amount          DECIMAL(12,2),
    currency        CHAR(3),
    provider        VARCHAR(50),         -- 'billie', 'two', 'hokodo', 'ratio'
    provider_ref    VARCHAR(255) NULL,
    status          ENUM('pending','approved','declined','funded','overdue','settled'),
    approved_at     TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT NOW()
);
```

---

## Virtual IBAN Flow

1. Customer created in CRM → FlowFlex auto-provisions virtual IBAN via API (Modulr/Swan)
2. IBAN stored in `fin_virtual_ibans`
3. Customer invoices show their unique IBAN as payment destination
4. Incoming payment arrives → provider webhook fires
5. `PaymentReceived` event → auto-match to outstanding invoices by IBAN + amount
6. Invoice marked paid, revenue reconciled — zero manual work

---

## SEPA/BACS Batch Runs

Replaces manual "upload 200 payment rows to bank portal."

1. Finance creates batch run (select outstanding payables)
2. System validates all IBANs, BICs, amounts
3. Approval workflow (dual control for amounts > threshold)
4. Approved → submit to bank via SEPA XML (pain.001) or BACS standard 18
5. Bank returns confirmation → statuses updated, GL entries posted

---

## B2B BNPL Providers

| Provider | Market | Product |
|---|---|---|
| Billie | EU | Invoice financing, 30/60/90 day terms |
| Two | UK/NO | B2B BNPL at checkout |
| Hokodo | UK/EU | Trade credit insurance + payment |
| Ratio | US | SaaS BNPL for annual contracts |

Company offers customer "Pay in 30 days" → Billie pays FlowFlex today → collects from customer.

---

## Related

- [[MOC_Finance]]
- [[general-ledger-chart-of-accounts]]
- [[accounts-receivable-automation]]
- [[entity-invoice]]
