---
type: module
domain: E-commerce & Sales Channels
panel: ecommerce
cssclasses: domain-ecommerce
phase: 5
status: planned
migration_range: 600000–649999
last_updated: 2026-05-09
---

# Gift Cards & Vouchers

Physical and digital gift card issuance, voucher codes, store credit management, and redemption tracking.

---

## Features

### Gift Cards
- Digital gift cards (PDF/email delivery with unique code)
- Physical gift card batch generation (import serial numbers)
- Fixed denominations or custom amounts
- Partial redemption with balance tracking
- Gift card purchase via storefront (product type = gift_card)
- Corporate bulk gift card orders (B2B gifting)
- Expiry date (optional — jurisdiction-dependent)

### Promo & Voucher Codes
- Single-use vs multi-use codes
- % discount or fixed amount
- Free shipping code
- Category or product-specific discount
- Minimum order value requirement
- First-time customer only
- Usage limits (total uses, per-customer uses)
- Stackable or exclusive codes
- Auto-apply at checkout (URL parameter trigger)
- Bulk code generation (CSV export)

### Store Credit
- Issue store credit manually (goodwill, returns, referral reward)
- Auto-issue from returns (if refund preference = store credit)
- Store credit balance on customer account
- Partial payment with store credit at checkout

### Reporting
- Gift card revenue (sold vs redeemed = breakage revenue)
- Voucher usage rate and discount impact
- Store credit liability balance

---

## Data Model

```erDiagram
    gift_cards {
        ulid id PK
        ulid company_id FK
        string code
        decimal initial_value
        decimal current_balance
        string status
        timestamp expires_at
        string issued_to_email
    }

    voucher_codes {
        ulid id PK
        ulid company_id FK
        string code
        string type
        decimal value
        integer max_uses
        integer used_count
        json conditions
        timestamp expires_at
    }

    store_credits {
        ulid id PK
        ulid company_id FK
        ulid contact_id FK
        decimal balance
        text reason
    }
```

---

## Permissions

```
ecommerce.gift-cards.create
ecommerce.gift-cards.view-any
ecommerce.vouchers.create
ecommerce.vouchers.manage
ecommerce.store-credit.manage
```

---

## Related

- [[MOC_Ecommerce]]
- [[entity-contact]]
- [[promotions-discount-engine]]
