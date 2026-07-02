---
domain: crm
module: leads
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Leads — Unknowns

> [!warning] UNVERIFIED
> This is the **weakest spec in the vault** (~2.9KB). It is missing a Dependencies table and a DTOs section, its Data Model was written as prose rather than a table, and the source **self-declares its own copy as *(assumed)*** — it was a retro-doc of a founder-requested build, not a designed spec. It needs a **full v2 spec rebuild** before it can serve as a reliable build blueprint. Concrete gaps:
> - No Dependencies table (reconstructed here from frontmatter only).
> - No DTO definitions (`ConvertLeadAction` input never formalised).
> - Prose data model (normalised into a table here, unverified).
> - No events analysis (no cross-domain contract; `LeadConverted` never evaluated).
> - Convert edge-cases undocumented (blank email, missing owner, no default pipeline behaviour, duplicate-contact resolution).

## Assumed Items

- The whole module copy is *(assumed)* — the source states "No prior vault spec … copy is *(assumed)* until a design/spec lands."
- Status lifecycle uses a plain string field (no state-machine class) *(assumed)*.
- No formal input DTO for `ConvertLeadAction`; it takes a lead id *(assumed)*.
- The lead create/update form-data shape in [[api]] is *(assumed)*.
- Lead PII (email, phone) is stored in plaintext with no encryption requirement *(assumed)*.
- Merge/matching of a contact by email creates one when none matches *(assumed default)*.

## Open Questions

- Should convert emit a `LeadConverted` cross-domain event?
- What happens when a lead has a blank email on convert — skip contact creation, or block?
- How is the "default pipeline" chosen when a company has several pipelines?
- Should `unqualified` leads be archivable / auto-purged (GDPR retention)?
- Are website / import sources wired to real capture channels, or manual-only for v1?
