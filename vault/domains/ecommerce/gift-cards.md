---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.gift-cards
status: planned
color: "#4ADE80"
---

# Gift Cards

> Issue digital gift cards in configurable denominations, track redemptions, and manage balances — with partial redemption and expiry support.

**Panel:** `ecommerce`
**Module key:** `ecommerce.gift-cards`

## What It Does

Gift Cards allows the storefront to sell digital gift cards that recipients redeem at checkout. A gift card is purchased like any other product and delivered to the recipient via email with a unique code and balance. At checkout the recipient enters the code to apply the balance. Partial redemption is supported — the remaining balance stays on the card for a future purchase. Merchants can also issue gift cards manually (for compensation, refunds as store credit, or promotional awards). Balance and redemption history are tracked per card.

## Features

### Core
- Gift card products: list gift cards as purchasable products in fixed denominations (€10, €25, €50, €100) or a custom amount input within a range
- Delivery: after purchase, an email with the unique gift card code and balance is sent to the recipient (email entered at checkout)
- Redemption: enter code at checkout to apply balance; code validated and balance deducted in real time
- Partial redemption: if the cart total is less than the balance, the remaining balance stays on the card; if more, the customer pays the difference via another payment method
- Balance enquiry: customer can check balance via a link in the gift card email without signing in
- Expiry: configurable expiry period (e.g., 2 years from issue date); expired cards cannot be redeemed

### Advanced
- Manual issuance: issue a gift card manually from the FlowFlex admin (compensation, store credit refund, promotional reward) with configurable amount and expiry
- Bulk issuance: generate and export a batch of gift card codes for a marketing campaign or employee benefit programme
- Gift card as refund method: instead of a Stripe refund, offer the customer a gift card for the refund value; useful for managing cash flow
- Redemption history: per-card log of every redemption event with order reference, amount applied, and remaining balance
- Outstanding liability report: total value of all unredeemed gift card balances (for accounting purposes)
- Design customisation: configure the gift card email with brand colours, logo, and a custom message field at checkout

### AI-Powered
- Breakage estimation: predict the percentage of issued gift card value that will never be redeemed, for revenue recognition purposes
- Gift card demand forecasting: predict peak gift card purchase periods to ensure adequate promotional inventory

## Data Model

```erDiagram
    ec_gift_cards {
        ulid id PK
        ulid company_id FK
        string code
        decimal initial_balance
        decimal remaining_balance
        string currency
        date expires_at
        string status
        ulid issued_to_customer_id FK
        string issued_to_email
        ulid purchased_via_order_id FK
        boolean manually_issued
        timestamps timestamps
    }

    ec_gift_card_redemptions {
        ulid id PK
        ulid gift_card_id FK
        ulid order_id FK
        decimal amount_applied
        decimal balance_before
        decimal balance_after
        timestamp redeemed_at
    }

    ec_gift_cards ||--o{ ec_gift_card_redemptions : "redeemed in"
```

| Table | Purpose |
|---|---|
| `ec_gift_cards` | Gift card records with balance and expiry |
| `ec_gift_card_redemptions` | Per-order redemption events with balance tracking |

## Permissions

```
ecommerce.gift-cards.view-any
ecommerce.gift-cards.issue-manual
ecommerce.gift-cards.bulk-issue
ecommerce.gift-cards.void
ecommerce.gift-cards.view-liability
```

## Filament

**Resource class:** `GiftCardResource`
**Pages:** List, Create, View
**Custom pages:** `GiftCardLiabilityPage` (outstanding balance report for accounting)
**Widgets:** `TotalGiftCardLiabilityWidget`, `GiftCardSalesWidget`
**Nav group:** Marketing

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Shopify Gift Cards | Native gift card issuance and redemption |
| Rise.ai | Gift cards and store credit management |
| GiftUp | Digital gift card platform |
| WooCommerce Gift Cards | Gift card plugin for WooCommerce |

## Related

- [[products]] — gift cards sold as products in the catalogue
- [[payments]] — gift card redemptions applied as payment method at checkout
- [[orders]] — gift card purchases create standard orders; redemptions linked to orders
- [[../finance/INDEX]] — outstanding gift card liability tracked as deferred revenue
