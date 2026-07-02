---
domain: crm
module: deals
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Deals — Unknowns & Assumed Items

## Assumed Items (*(assumed)* markers from spec)

- **`lost_to` field** — `crm_deals.lost_to` (competitor name) is *(assumed)*. Confirm whether this field is required in v1 or deferred to analytics (Phase 3).

- **`stage_entered_at`** — the `stage_entered_at` timestamp column (for days-in-stage calculation) is *(assumed)*. Confirm whether this is a single timestamp (reset on each stage move) or a history log.

- **`crm_deal_products.description`** — the `description` column on `crm_deal_products` is *(assumed)*. Confirm whether free-text description is always required, or only when `product_id` is null.

- **`expected_close_date` validation** — validation rule `after_or_equal:today` on `CreateDealData` is *(assumed)*. Confirm: should historical close dates be allowed (e.g. when importing deals)?

- **Cross-field validation** — "at least one of `account_id` / `contact_id` required" on `CreateDealData` is *(assumed)*. Confirm whether a standalone deal (no contact, no account) is ever valid.

- **Reopen transitions** — `won → open` and `lost → open` reopen transitions are *(assumed)* to exist. See Open Questions below.

- **`duplicate()` first stage** — `DealService::duplicate()` is described as placing the copy in the "first stage" *(assumed)*. Confirm: first stage of the active pipeline, or the same stage as the original deal?

- **Bulk owner reassign** — `DealResource` list bulk action for owner reassignment is *(assumed)*.

- **Meilisearch indexing** — deals Meilisearch index fields (`name`, account name, contact name) are *(assumed)* from the spec.

- **DealLost analytics consumer** — `DealLost` event has no v1 consumers; analytics intended to consume in Phase 3 is *(assumed)*.

---

## Open Questions

- **Reopen + invoice stub**: When a won deal is reopened, the draft invoice stub already created in Finance remains as a draft (the spec says "no — invoice stays as draft in Finance"). Confirm this is acceptable UX and that no void/cancel step is triggered. *(assumed — spec: "currently: no, invoice stays as draft in Finance")*
