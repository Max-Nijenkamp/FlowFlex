---
domain: ecommerce
module: reviews
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Reviews — Unknowns

## Assumed Items

- Verified-purchase setting defaults on *(assumed)*.
- Review-request mail fires at fulfilment +7d, once per order *(assumed)*.
- `status` is a plain enum, no state-machine class *(assumed)*.

## Open Questions

- Helpful-vote abuse: is IP/session dedup enough, or is a soft cap per token needed?
- Should reviews support photo/media uploads (verified image reviews)?
- Auto-approve trusted reviews (e.g. high-rating verified) vs always-moderate?
- Retention of rejected reviews — purge window or keep for audit?
