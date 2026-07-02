---
domain: ecommerce
module: storefront
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Storefront — Unknowns

## Assumed Items

- Shipping = flat + free-over-threshold in v1; per-region deferred *(assumed)*.
- `/shop/{company-slug}` path; custom domain later *(assumed)*.
- Session cart; DB capture only for abandoned-cart *(assumed)*.
- Single store currency per storefront *(assumed)*.

## Open Questions

- Multi-language storefront: which release, and does content-page body need per-locale variants?
- Custom-domain onboarding (DNS + TLS) — platform-managed certs vs merchant-supplied?
- Theme system depth: token config only, or full template overrides?
- Guest vs account checkout: is a customer portal (order history/login) in scope for E-commerce, or CRM-owned?
