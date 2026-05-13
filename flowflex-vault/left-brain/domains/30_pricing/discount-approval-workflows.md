---
type: module
domain: Pricing Management
panel: pricing
module: Discount Approval Workflows
phase: 4
status: complete
cssclasses: domain-pricing
migration_range: 1100500–1100999
last_updated: 2026-05-12
---

# Discount Approval Workflows

Rule-based discount approval gates. When a sales rep applies a discount above their authority level, the quote is held pending manager approval. Prevents margin leakage without slowing down authorised deals.

---

## Key Tables

```sql
CREATE TABLE pricing_discount_rules (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    name            VARCHAR(100),
    priority        INT DEFAULT 0,          -- lower = checked first
    condition_type  ENUM('discount_pct','deal_value','product_category','customer_segment','margin_pct'),
    condition_operator ENUM('gt','gte','lt','lte','eq'),
    condition_value DECIMAL(12,2),
    action          ENUM('block','require_approval','notify'),
    approval_role   VARCHAR(100) NULL,      -- Spatie permission role
    is_active       BOOLEAN DEFAULT TRUE
);

CREATE TABLE pricing_discount_requests (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    quote_id        ULID NULL,              -- CRM quote
    order_id        ULID NULL,              -- E-commerce order
    requested_by    ULID NOT NULL REFERENCES users(id),
    discount_pct    DECIMAL(5,2),
    discount_amount DECIMAL(12,2),
    deal_value      DECIMAL(12,2),
    margin_pct      DECIMAL(5,2) NULL,
    justification   TEXT NOT NULL,
    status          ENUM('pending','approved','rejected','expired'),
    rule_id         ULID NULL REFERENCES pricing_discount_rules(id),
    reviewed_by     ULID NULL REFERENCES users(id),
    reviewed_at     TIMESTAMP NULL,
    review_comment  TEXT NULL,
    expires_at      TIMESTAMP,              -- auto-reject after N hours
    created_at      TIMESTAMP DEFAULT NOW()
);
```

---

## Approval Tiers

Example rule configuration:

| Discount | Authority |
|---|---|
| 0–5% | Rep can apply without approval |
| 5–15% | Sales Manager approval required |
| 15–25% | VP Sales approval required |
| 25%+ | CFO approval required |
| Below min_price | Always blocked (no approval possible) |

Rules are company-configurable — the above is just the default template.

---

## Flow

1. Rep creates quote, applies 22% discount
2. System checks: rule found → 15–25% requires VP Sales approval
3. Quote status → `pending_discount_approval`, rep notified
4. VP Sales receives notification with deal context, discount amount, margin impact
5. VP approves with comment → quote unlocked, `DiscountApproved` event fires
6. Or VP rejects → rep notified, can resubmit with lower discount or new justification

---

## Margin Impact Preview

On discount approval request form:
- Current margin %
- Margin after proposed discount
- Breakeven discount (where margin = 0%)
- Comparison to similar deals (P50 margin for deals of this size + product)

---

## Related

- [[MOC_Pricing]]
- [[price-book-management]]
- [[MOC_CRM]] — quotes held pending approval
