---
domain: procurement
module: supplier-catalogue
type: unknowns
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Supplier Catalogue — Open Questions

- Preferred-supplier storage: `is_preferred` flag on item vs a category→supplier pivot. `*(assumed: flag)*`
- Supplier portal scope & priority — **UNVERIFIED**, not in v1 spec; may defer to Phase 2.
- External cXML/OCI punch-out to supplier-hosted catalogues vs internal-only. `*(assumed: internal v1)*` — differentiator ([[../_opportunities]]).
- Price-agreement expiry notifications (warn before `valid_until`)? `*(assumed: not v1)*`
- Category taxonomy shared with approvals/spend or per-module free string? `*(assumed: free string)*`

## Related

- [[_module]] · [[decisions]]
