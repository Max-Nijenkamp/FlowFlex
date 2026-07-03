---
domain: it
module: access-provisioning
feature: access-templates
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Access Templates

Role-based access templates that map a job role to a set of systems and access levels — e.g. "Developer" →
GitHub + AWS + Slack. These drive automatic pending-grant creation on hire.

- Simple Filament resource over `it_access_templates` ([[../../../../architecture/patterns/feature-ui-spec]]).
- Each template: role name + a list of `{ system, access_level }` (stored as jsonb `systems`).

## UI

- **Kind**: simple-resource — CRUD of `it_access_templates`.
- **Page**: `AccessTemplateResource` at `/it/access-templates`.
- **Layout**: table columns role name · # systems; form: role name (required) + repeater of `{ system (select from it_systems), access_level }`.
- **Key interactions**: create / edit / delete a template; each `system_id` must be an existing `it_systems` id in the company.
- **States**: empty (no templates → CTA) · loading (table skeleton) · error (invalid system id → validation toast) · selected (edit form).
- **Gating**: view `it.access.view-any`; create/edit/delete `it.access.manage-templates`.

## Data

- Owns / writes: `it_access_templates` only.
- Reads: `it_systems` (for the systems repeater).
- Cross-domain writes: none — cross-domain effects flow through events only, never another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: templates read by [[provisioning-on-hire]] (`ProvisionOnHireListener` matches by role name).
- Shared entity: systems owned by [[system-catalogue]].

## Test Checklist

### Unit
- [ ] Each `systems[]` entry requires a valid `system_id` + access level

### Feature (Pest)
- [ ] Create a template with role name + systems repeater; each `system_id` must exist in the company
- [ ] Template CRUD tenant-scoped: company A cannot see/edit company B templates

### Livewire
- [ ] Form validates role name required + rejects a `system_id` from another company; denied without `it.access.manage-templates`

## Unknowns

- Template match key by job role name — `*(assumed: template name matching)*`.
- Access-level set (`admin / user / read`) — `*(assumed set)*`.

## Related

- [[../_module|Access Provisioning]] · [[system-catalogue]] · [[provisioning-on-hire]] · [[../data-model|data-model]]
