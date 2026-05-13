---
type: module
domain: Field Service Management
panel: fsm
module: Customer Sign-Off & POD
phase: 5
status: complete
cssclasses: domain-fsm
migration_range: 1051500–1051999
last_updated: 2026-05-12
---

# Customer Sign-Off & Proof of Delivery

Digital customer signature capture on technician's device at job completion. Generates a PDF proof-of-work document stored in DMS and triggers invoice generation.

---

## Key Tables

```sql
CREATE TABLE fsm_job_signoffs (
    id              ULID PRIMARY KEY,
    job_id          ULID UNIQUE NOT NULL REFERENCES fsm_jobs(id),
    signed_by_name  VARCHAR(255) NOT NULL,   -- customer rep name
    signed_by_email VARCHAR(255) NULL,
    signature_data  TEXT NOT NULL,           -- base64 SVG path data
    location_lat    DECIMAL(10,7) NULL,
    location_lng    DECIMAL(10,7) NULL,
    device_info     JSON NULL,               -- browser UA, screen size
    signed_at       TIMESTAMP NOT NULL,
    pdf_path        VARCHAR(500) NULL,       -- generated PDF in DMS
    invoice_id      ULID NULL,              -- links to created invoice
    created_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE fsm_signoff_ratings (
    id              ULID PRIMARY KEY,
    signoff_id      ULID NOT NULL REFERENCES fsm_job_signoffs(id),
    rating          TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment         TEXT NULL,
    created_at      TIMESTAMP DEFAULT NOW()
);
```

---

## Sign-Off Screen Flow (Mobile App)

1. Technician marks job `completed` → Sign-Off screen opens
2. Work summary shown (duration, parts used, checklist results, photos)
3. Customer enters their name + email (optional)
4. Customer draws signature on touchscreen
5. Customer rates the visit (1–5 stars, optional comment)
6. "Submit" → triggers:
   - `CustomerSignatureReceived` event
   - PDF generation (job report + signature + photos)
   - PDF stored in DMS (`dms_documents`)
   - Invoice generation if billing configured
   - Email confirmation to customer (if email provided)

---

## Generated PDF Contents

- Company logo + letterhead
- Job number, date, technician name
- Customer name, address
- Work performed (description + checklist results)
- Photos (up to 12, 2-column layout)
- Parts used with quantities
- Customer signature + timestamp
- GPS coordinates stamp

---

## Dispute Protection

Signature `signed_at` timestamp is server-set (not client-side) upon receipt.  
PDF includes HMAC fingerprint of signature data for tamper evidence.  
Stored immutably in DMS version control.

---

## Related

- [[MOC_FieldService]]
- [[mobile-field-app]]
- [[field-invoicing]]
- [[MOC_DMS]] — POD documents stored here
