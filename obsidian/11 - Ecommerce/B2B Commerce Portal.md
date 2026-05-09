---
tags: [flowflex, domain/ecommerce, b2b, portal, phase/5]
domain: Ecommerce
panel: ecommerce
color: "#0891B2"
status: planned
last_updated: 2026-05-08
---

# B2B Commerce Portal

A separate password-protected storefront for wholesale buyers and business customers. Custom pricing per account, net payment terms, purchase orders, approval workflows. Everything your B2C store doesn't cover — without needing a second platform.

**Who uses it:** Wholesalers, manufacturers, distributors selling to businesses
**Filament Panel:** `ecommerce`; Vue + Inertia (buyer portal)
**Depends on:** Core, [[Product Catalogue]], [[Order Management]], [[Contact & Company Management]], Finance
**Phase:** 5

---

## Features

### Buyer Portal

- Separate subdomain: `wholesale.yourstore.com`
- Account-gated: apply for access or invite-only
- Application form: company name, VAT number, estimated monthly volume
- Application review: approve/reject in Filament, set account tier on approval
- Custom logo and colours per portal (can match buyer's brand)

### Account-Based Pricing

- Price lists: create named price lists (Reseller, Distributor, VIP Wholesale)
- Discounts: % off RRP, fixed price per SKU, volume tiered pricing
- Assign price list per B2B account (overrides default)
- Minimum order quantity (MOQ) per product
- Minimum order value for checkout
- Hidden B2B prices from public storefront

### Purchase Order Workflow

- Buyer uploads PO number at checkout (or enters PO reference)
- PO number stored on order record
- Option: require PO number before checkout completes
- PDF order confirmation includes PO number
- PO-matched invoice: Finance module invoices reference the PO number

### Payment Terms

- Net 30 / Net 60 / Net 90 payment terms per account
- Invoice sent on dispatch (not payment required at checkout)
- Credit limit: set maximum outstanding balance per account
- Credit hold: auto-block new orders if balance exceeds limit
- Payment tracked in Finance Accounts Receivable

### Approval Workflows (Buyer Side)

- Multi-level purchase approval: order above €X requires buyer manager approval
- Buyer creates order → submits for approval → approver approves/rejects → order confirmed
- Notifications to approver via email or internal FlowFlex messaging

### Quick Order & Reorder

- CSV order upload: buyer uploads spreadsheet with SKU + quantity
- Reorder: one-click reorder last order or favourite order
- Saved order templates: "Monthly standing order" — buyer saves and resubmits monthly
- SKU search: buyer types product code to add directly (no browsing)

### B2B Account Management (Staff)

- Account overview: company details, assigned rep, price list, credit limit, order history
- Statement: running balance view per account
- Set rep: assign an internal sales rep to each B2B account
- Notes: internal notes per account (CRM-style)
- Integration with CRM: B2B account = Company in CRM

---

## Database Tables (4)

### `ecommerce_b2b_accounts`
| Column | Type | Notes |
|---|---|---|
| `company_id` | ulid FK | → crm_companies |
| `status` | enum | `pending`, `approved`, `suspended` |
| `price_list_id` | ulid FK nullable | |
| `payment_terms_days` | integer default 0 | |
| `credit_limit` | decimal default 0 | |
| `outstanding_balance` | decimal default 0 | |
| `min_order_value` | decimal default 0 | |
| `assigned_rep_id` | ulid FK nullable | |

### `ecommerce_b2b_price_lists`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `description` | text nullable | |
| `discount_pct` | decimal default 0 | global discount |

### `ecommerce_b2b_price_list_items`
| Column | Type | Notes |
|---|---|---|
| `price_list_id` | ulid FK | |
| `product_id` | ulid FK | |
| `custom_price` | decimal nullable | fixed price (overrides pct) |
| `discount_pct` | decimal nullable | product-specific % |
| `moq` | integer default 1 | min order qty |

### `ecommerce_b2b_order_approvals`
| Column | Type | Notes |
|---|---|---|
| `order_id` | ulid FK | |
| `approver_id` | ulid FK | buyer company approver |
| `status` | enum | `pending`, `approved`, `rejected` |
| `notes` | text nullable | |
| `decided_at` | timestamp nullable | |

---

## Permissions

```
ecommerce.b2b.view
ecommerce.b2b.manage-accounts
ecommerce.b2b.manage-price-lists
ecommerce.b2b.manage-credit
ecommerce.b2b.view-analytics
```

---

## Competitor Comparison

| Feature | FlowFlex | Shopify B2B | WooCommerce Wholesale | Magento B2B |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (Shopify Plus only) | ❌ (plugin €€) | ❌ (expensive) |
| Account-based price lists | ✅ | ✅ | ✅ | ✅ |
| Net payment terms | ✅ | ✅ | ✅ (plugin) | ✅ |
| Buyer-side approval flow | ✅ | ❌ | ❌ | ✅ |
| CRM company integration | ✅ | ❌ | ❌ | ❌ |
| CSV order upload | ✅ | ✅ | ✅ (plugin) | ✅ |

---

## Related

- [[Ecommerce Overview]]
- [[Product Catalogue]]
- [[Order Management]]
- [[Contact & Company Management]]
- [[Finance Overview]]
