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

# Product Bundles

Group multiple products into a single purchasable bundle — at a discount or as a curated set. Different from promotions (which apply rules at checkout). Bundles are a distinct product type customers browse and add to cart. Common in electronics (laptop + bag + mouse), food (hamper), fashion (outfit), software (plan tiers).

**Panel:** `ecommerce`  
**Phase:** 5

---

## Features

### Bundle Types
- **Fixed bundle** — specific products pre-selected, fixed price (e.g. "Starter Kit: Product A + B + C for £49")
- **Mix-and-match** — customer picks N items from a group at bundle price (e.g. "Any 3 wines for £30")
- **Kit** — component products still managed individually in inventory but sold as a unit
- **Virtual bundle** — no physical assembly; each item ships separately (virtual grouping for selling purposes)
- **Build your own** — customer customises (Product A + choose one of B/C/D)

### Pricing
- Fixed bundle price (vs sum of individual prices → show savings)
- % discount on bundle vs individual prices
- Per-component pricing (visible breakdown)
- Bundle-specific price list (B2B: different bundle price for trade customers)

### Inventory Behaviour
- **Component tracking**: kit deducts individual component stock on sale
- **Pre-assembled stock**: bundle tracked as single SKU (e.g. gift boxes pre-packed)
- Bundle available only when all components are in stock
- Low stock alert if any component below threshold

### Display
- Bundle product page: shows component list, individual prices crossed out, bundle savings badge
- "What's in the box" section
- Component images carousel
- Related products / "complete the look"

### Mix-and-Match UX
- Category selection grid (pick N items)
- Running total as items selected
- "X more items to go" counter
- Swap items before checkout

---

## Data Model

```erDiagram
    product_bundles {
        ulid id PK
        ulid company_id FK
        ulid product_id FK
        string bundle_type
        decimal bundle_price
        decimal discount_percent
        integer min_items
        integer max_items
        boolean track_components
    }

    bundle_components {
        ulid id PK
        ulid bundle_id FK
        ulid product_id FK
        integer quantity
        boolean is_required
        string group_key
    }
```

---

## Permissions

```
ecommerce.bundles.create
ecommerce.bundles.manage-pricing
ecommerce.bundles.view
```

---

## Related

- [[MOC_Ecommerce]]
- [[entity-product]]
- [[promotions-discount-engine]] — bundles are products; promotions are checkout rules
