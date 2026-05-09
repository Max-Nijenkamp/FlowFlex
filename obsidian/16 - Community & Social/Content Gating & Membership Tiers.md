---
tags: [flowflex, domain/community, gating, membership, tiers, phase/7]
domain: Community & Social
panel: community
color: "#E11D48"
status: planned
last_updated: 2026-05-08
---

# Content Gating & Membership Tiers

Control who sees what. Free Spaces, members-only areas, paid tiers, invite-only groups. Monetise your community or keep it exclusive — your rules.

**Who uses it:** Community admins
**Filament Panel:** `community`
**Depends on:** Core (billing), [[Module Billing Engine]], [[Discussion Forums & Channels]]
**Phase:** 7

---

## Features

### Access Levels

| Level | Who Gets In |
|---|---|
| Public | Anyone (no login required) |
| Registered | Any logged-in member |
| Verified | Members who verified email or completed profile |
| Paid | Active paying subscribers to a community tier |
| Invite-only | Explicitly invited by admin |
| Employee | Internal employees only (HR module link) |
| Partner | CRM contacts tagged as Partner |

### Membership Tiers

Create paid membership tiers for your community:
- Tier name, description, price (monthly/annual)
- Stripe integration for payment (uses [[Module Billing Engine]])
- Spaces included in tier
- Benefits listed (markdown)
- Trial period option (7/14/30 days free)
- Coupon/discount codes
- Grandfathering: existing members keep old price on upgrade

### Gating Rules

Per Space and per Post:
- "This Space requires Tier: Pro"
- "This post is for Invite-only members"
- "This recording is visible to attendees only"

Teaser mode: visitors can see post titles + first paragraph → paywall prompt
Full lock mode: title only visible

### Invite System

- Admin generates invite codes (single-use or multi-use)
- Invite link with optional expiry date
- Bulk invite by email (CSV upload)
- Track invite usage: who invited whom

### Community Subscription Revenue Tracking

- MRR from community memberships
- Churn rate (cancellations vs new subs)
- Tier breakdown: how many on each tier
- Lifetime value by tier
- Data flows to [[Finance Overview]] → [[Subscription & MRR Tracking]]

---

## Database Tables (3)

### `community_tiers`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `description` | text nullable | |
| `price_monthly_cents` | integer nullable | |
| `price_annual_cents` | integer nullable | |
| `stripe_product_id` | string nullable | |
| `stripe_price_id_monthly` | string nullable | |
| `stripe_price_id_annual` | string nullable | |
| `trial_days` | integer nullable | |
| `is_default` | boolean | auto-assigned to new members |

### `community_tier_members`
| Column | Type | Notes |
|---|---|---|
| `tier_id` | ulid FK | |
| `member_id` | ulid FK | |
| `status` | enum | `active`, `trialing`, `cancelled`, `expired` |
| `stripe_subscription_id` | string nullable | |
| `started_at` | timestamp | |
| `ends_at` | timestamp nullable | |
| `cancelled_at` | timestamp nullable | |

### `community_invites`
| Column | Type | Notes |
|---|---|---|
| `code` | string unique | |
| `space_id` | ulid FK nullable | specific Space or global |
| `tier_id` | ulid FK nullable | auto-assign tier on join |
| `max_uses` | integer nullable | null = unlimited |
| `used_count` | integer default 0 | |
| `expires_at` | timestamp nullable | |
| `created_by` | ulid FK | |

---

## Permissions

```
community.tiers.view
community.tiers.manage
community.invites.create
community.invites.manage
community.gating.configure
```

---

## Related

- [[Community Overview]]
- [[Discussion Forums & Channels]]
- [[Module Billing Engine]]
- [[Subscription & MRR Tracking]]
