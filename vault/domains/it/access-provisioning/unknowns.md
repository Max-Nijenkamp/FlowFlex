---
domain: it
module: access-provisioning
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Access Provisioning — Unknowns & Assumptions

All items below are unverified. They function as authoritative defaults at build time but are overridable
via ADR. Design-affecting items should be resolved before implementation begins.

---

## Open Questions

1. **Access-level set** — is `admin / user / read` the right enumeration, or does it vary per system? Some tools have no read-only tier.
2. **Template match key** — matching on job **role name** assumes a stable role taxonomy in hr.profiles. What happens when a role is renamed or an employee has multiple roles?
3. **Third-party provisioning** — v1 is tracking-only. When (if) API provisioning lands, does it belong here or in a separate integration module?
4. **Onboarding mirror** — soft dep on hr.onboarding: should pending grants surface as onboarding-plan tasks, or stay independent?

---

## Assumed Items (verbatim from spec, unverified)

> [!warning] UNVERIFIED — No automated third-party API provisioning v1
> `*(assumed)*` — access provisioning is tracking + checklists only; FlowFlex does not call Google/Slack/GitHub/AWS APIs to create or delete accounts in v1.

> [!warning] UNVERIFIED — Access-level set
> `*(assumed set)*` — `access_level ∈ { admin, user, read }`. The concrete enumeration is not confirmed and may differ per system.

> [!warning] UNVERIFIED — Template match by job role name
> `*(assumed: template name matching)*` — `ProvisionOnHireListener` matches an `it_access_templates.role_name` against the hired employee's job role name. Behaviour on rename / multi-role is unspecified.

> [!warning] UNVERIFIED — Single-approval access request workflow
> `*(assumed)*` — access requests are completed by a single IT grant; no multi-stage approval chain in v1.

> [!warning] UNVERIFIED — Active-grant uniqueness enforcement
> `*(assumed: enforced via partial unique index on non-revoked rows)*` — the unique active `(employee_id, system_id)` constraint is implemented as a filtered/partial unique index excluding revoked grants.

> [!warning] UNVERIFIED — Onboarding independence
> `*(assumed: independent v1)*` — provisioning tasks do not mirror into the hr.onboarding plan in v1.
