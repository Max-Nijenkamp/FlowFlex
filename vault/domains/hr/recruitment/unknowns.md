---
domain: hr
module: recruitment
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Recruitment — Unknowns

Every `*(assumed)*` marker and unverified detail from the source spec. Resolve via ADR before build where design-affecting.

---

## Assumptions

- LinkedIn/Indeed export is P2 and **link-out only** in v1 *(assumed)*.
- Applicant data (public application form) is retained 12 months then purged *(assumed)*.
- `hr_applicants.rejection_reason` column exists *(assumed)*.
- Rejection mail on the `→ rejected` transition is an **optional toggle** *(assumed)*.
- `ApplyData` public form is rate-limited + honeypot *(assumed)* — needs a concrete named RateLimiter (`public-apply`) to remove the marker.

---

## Open questions (from security notes)

- Public/portal guard (HIGH): confirm guest controller boundary + input validation + abuse controls are documented.
- Rate limiter (medium): name the concrete `RateLimiter` and its key (per IP + per requisition).
- Upload contract (medium): confirm CV storage path `companies/{company_id}/recruitment/` on a private disk with type/size rules.

---

## Unverified

- Nothing in this module is built, migrated, or tested — HR code was stripped under [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]. Source spec frontmatter said `status: complete`; that is stale and treated as `build-status: planned`.

---

## Related

- [[_module]] · [[security]]
