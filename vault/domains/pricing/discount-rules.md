---
type: module
domain: Pricing Management
panel: pricing
module-key: pricing.discounts
status: planned
color: "#4ADE80"
---

# Discount Rules

> Discount policy management — volume discounts, promotional codes, customer-specific discounts, and approval thresholds.

**Panel:** `pricing`
**Module key:** `pricing.discounts`

---

## What It Does

Discount Rules governs when and how discounts can be applied to quotes and orders, above and beyond price book prices. Sales managers configure discount policies — volume thresholds, customer loyalty tiers, promotional campaigns, and maximum discretionary discount levels by sales rep seniority. Each discount rule has an eligibility condition and a discount value (percentage or fixed amount). When a quote is built in the CRM, eligible discount rules are surfaced automatically. Discounts above the rep's authorised threshold require manager approval before the quote can be sent.

---

## Features

### Core
- Discount rule creation: name, type (volume/promotional/loyalty/discretionary), discount value, and eligibility conditions
- Discount types: percentage off list price, fixed amount reduction, or free goods
- Eligibility conditions: minimum order quantity, minimum order value, customer segment, date range
- Discount stacking rules: configure whether multiple discount rules can apply to the same order
- Maximum discount thresholds: set the maximum discount a rep can apply without approval
- Approval workflow: discounts exceeding threshold require manager approval before quote is finalised

### Advanced
- Promotional codes: generate alphanumeric promo codes that activate a discount when entered at checkout or in a quote
- Customer-specific discounts: create one-off discount rules scoped to a single customer account
- Product exclusions: exclude certain products or categories from a discount rule
- Discount expiry: set a date after which the discount rule is automatically deactivated
- Discount usage limits: cap the number of times a promotional discount can be used
- Channel-specific rules: apply different discount policies for direct sales vs partner sales

### AI-Powered
- Discount impact analysis: model the margin impact of proposed discount rules before activation
- Overuse detection: flag sales reps consistently applying maximum discretionary discounts, indicating list price may be too high
- Optimal discount recommendation: suggest the minimum discount needed to convert a specific opportunity based on deal history

---

## Data Model

```erDiagram
    discount_rules {
        ulid id PK
        ulid company_id FK
        string name
        string discount_type
        decimal discount_value
        string value_type
        json eligibility_conditions
        date valid_from
        date valid_to
        integer usage_limit
        integer usage_count
        boolean is_active
        timestamps created_at_updated_at
    }

    discount_approval_thresholds {
        ulid id PK
        ulid company_id FK
        string role_name
        decimal max_discount_percent
        timestamps created_at_updated_at
    }

    discount_applications {
        ulid id PK
        ulid company_id FK
        ulid discount_rule_id FK
        string applicable_to_type
        string applicable_to_id
        decimal applied_discount
        string approval_status
        ulid approved_by FK
        timestamps created_at_updated_at
    }

    discount_rules ||--o{ discount_applications : "applied via"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `discount_rules` | Discount policy definitions | `id`, `company_id`, `name`, `discount_type`, `discount_value`, `eligibility_conditions`, `valid_from`, `valid_to`, `is_active` |
| `discount_approval_thresholds` | Per-role max discount | `id`, `company_id`, `role_name`, `max_discount_percent` |
| `discount_applications` | Discount use records | `id`, `discount_rule_id`, `applicable_to_id`, `applied_discount`, `approval_status` |

---

## Permissions

```
pricing.discounts.view
pricing.discounts.create
pricing.discounts.edit
pricing.discounts.approve
pricing.discounts.view-usage
```

---

## Filament

- **Resource:** `App\Filament\Pricing\Resources\DiscountRuleResource`
- **Pages:** `ListDiscountRules`, `CreateDiscountRule`, `EditDiscountRule`, `ViewDiscountRule`
- **Custom pages:** `DiscountApprovalQueuePage`, `DiscountUsageReportPage`
- **Widgets:** `PendingDiscountApprovalsWidget`, `TopDiscountedProductsWidget`
- **Nav group:** Rules

---

## Displaces

| Feature | FlowFlex | Vendavo | PROS | Zilliant |
|---|---|---|---|---|
| Volume discount rules | Yes | Yes | Yes | Yes |
| Approval thresholds | Yes | Yes | Yes | Yes |
| Promotional codes | Yes | Partial | Partial | No |
| AI optimal discount | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[price-books]] — discounts apply on top of price book list prices
- [[product-pricing]] — margin floor checked against discounted effective price
- [[crm/INDEX]] — discount approvals surface in CRM quote workflow
- [[competitive-pricing]] — competitive data informs minimum discount needed to win
