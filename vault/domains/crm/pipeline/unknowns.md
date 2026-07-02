---
domain: crm
module: pipeline
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Pipeline — Unknowns & Assumptions

All items below are unverified. They function as authoritative defaults at build time but are overridable via ADR. Design-affecting items should be resolved before implementation begins.

---

## Open Questions

1. **Exactly one won + one lost stage: per pipeline or per company?**
   The spec says "exactly one of each per company *(assumed)*" but with multi-pipeline support added 2026-06-12, the constraint may need to be per-pipeline instead. Clarify before writing the stage deletion guard.

2. **Stage migration ownership in BUILD-ORDER**
   The spec notes that the `crm_pipeline_stages` migration is owned by this module but physically created in the deals build step (as FK target). Should the migration file live in the pipeline module migration folder and be referenced, or actually be in the deals build step? Clarify ownership before writing the migration.

3. **Who seeds default pipelines on module activation?**
   The BillingService::activateModule hook is assumed to call a pipeline seeder. Confirm which seeder class and where it's registered.

4. **Pipeline switcher UX**
   How does a user switch between multiple pipelines on the board? Dropdown in the header? Separate tab? Not specified in the spec.

---

## Assumed Items (verbatim from spec, unverified)

- `*(assumed: single migration, owned here)*` — the stage table migration is a single file owned by the pipeline module, even though it ships in the deals build step
- `*(assumed)* exactly one of each per company` — `is_won` and `is_lost` flags: exactly one won + one lost stage allowed per company (constraint scope unverified — see open question #1)
- `*(assumed)*` — default stages seeded on module activation: Lead → Qualified → Proposal → Won / Lost
