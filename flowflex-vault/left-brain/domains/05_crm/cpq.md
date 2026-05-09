---
type: module
domain: CRM & Sales
panel: crm
cssclasses: domain-crm
phase: 8
status: planned
migration_range: 250000–299999
last_updated: 2026-05-09
---

# CPQ — Configure, Price, Quote

Complex product configuration, dynamic pricing rules, discount approval workflows, and quote generation. Replaces Salesforce CPQ and DealHub for B2B sales.

---

## Features

### Product Configurator
- Rule-based product configuration (if feature A selected, option B required)
- Bundle builder (group products with combined pricing)
- Incompatibility rules (option X cannot coexist with option Y)
- Required upsells / cross-sells during configuration
- Visual configurator for complex products

### Pricing Engine
- Multiple pricing models: flat, per-unit, tiered, volume, usage-based
- Price books (different prices per region, segment, channel)
- Custom pricing per customer / contract
- Dynamic discount rules (% or fixed, conditional)
- Competitor-matching pricing rules
- FX-adjusted pricing per currency

### Discount Approval
- Discount threshold matrix (>10% = manager, >25% = VP, >40% = CFO)
- Approval workflow with deal context (deal size, win probability)
- Counter-offer suggestions
- Discount guard rails (floor pricing)

### Quote Generation
- Professional PDF quote from configured products
- Quote versioning (v1, v2, v3 with diff)
- Quote validity period with auto-expiry
- One-click send to client (email + portal link)
- E-sign integration (Legal E-Signature module)
- Quote acceptance triggers deal won + contract creation

### Guided Selling
- AI-recommended configuration based on deal context
- Win/loss data shows which configurations close faster
- Sales coach suggestions during configuration

---

## Data Model

```erDiagram
    product_configurations {
        ulid id PK
        ulid company_id FK
        string name
        json configuration_rules
        boolean is_active
    }

    price_books {
        ulid id PK
        ulid company_id FK
        string name
        string currency
        string segment
        boolean is_default
    }

    cpq_quotes {
        ulid id PK
        ulid deal_id FK
        ulid price_book_id FK
        integer version
        string status
        decimal subtotal
        decimal discount_amount
        decimal total
        timestamp expires_at
        timestamp accepted_at
    }

    discount_approvals {
        ulid id PK
        ulid quote_id FK
        decimal discount_percent
        string status
        ulid requested_by FK
        ulid approved_by FK
        text approval_notes
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `QuoteAccepted` | Client accepts | CRM (deal won), Finance (create invoice/contract), Legal (create contract) |
| `QuoteExpired` | Expiry date passes | CRM (create follow-up task), Notifications |
| `DiscountApprovalRequired` | Threshold exceeded | Notifications (approver), CRM (pause deal) |

---

## Permissions

```
crm.cpq.view-any
crm.cpq.create
crm.cpq.approve-discount
crm.price-books.manage
```

---

## Competitors Displaced

Salesforce CPQ · DealHub · Conga · Responsive · PandaDoc (basic)

---

## Related

- [[MOC_CRM]]
- [[entity-invoice]]
- [[entity-contact]]
