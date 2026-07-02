---
domain: dms
module: templates
feature: merge-source-registry
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Merge Source Registry

The registry through which HR and CRM modules expose field providers to templates. Providers map a template's declared merge fields to model data — whitelisted fields only. No UI: it's a service/registry that other modules register into at boot.

## Behaviour

1. `MergeSourceRegistry::register(string $type, class-string $provider)` — HR / CRM register a provider (`employee`, `contact`) when their module is active.
2. Each provider exposes a **fixed whitelist** of fields (`{{employee_name}}`, `{{date}}`, contact fields, etc.). Only whitelisted fields are offered to the template editor and resolvable at generate time.
3. **Sensitive fields — salary, national ID, and similar — are NEVER registered** as merge sources *(assumed)*. There is no path to reach an unwhitelisted column.
4. When a source module is inactive, its provider is simply absent → those fields fall back to **manual entry** in the generate wizard (soft dependency, degrades gracefully).
5. At generate time, `TemplateService` asks the chosen provider to resolve whitelisted fields for a given record id (read-only); the wizard supplies the rest via `manual_values`.

## UI

- **Kind**: background ([[../../../../architecture/patterns/feature-ui-spec]] — no UI).
- **Trigger**: HR / CRM service providers call `MergeSourceRegistry::register` at boot; the registry is read by [[template-editor|Template Editor]] (which fields to offer) and [[generate-from-template|Generate From Template]] (field resolution). No page.

## Data

- Owns / writes: nothing — an in-memory registry plus read-only calls into provider modules.
- Reads: employee data from [[../../../hr/employee-profiles/_module|hr.profiles]] and contact data from [[../../../crm/contacts/_module|crm.contacts]] via their registered providers — **whitelisted fields only**, read-only.
- Cross-domain writes: none — read-only field resolution; sensitive fields never offered ([[../../../../security/data-ownership]]).

## Relations

- Consumes: field-provider registrations from hr.profiles and crm.contacts (soft deps).
- Feeds: the merge-field insert menu ([[template-editor]]) and field resolution ([[generate-from-template]]).
- Shared entity: employee / contact records owned by HR / CRM (read-only).

## Unknowns

- The exact whitelist each provider exposes is not enumerated in the source — see [[../unknowns]].
- How custom (non-built-in) merge fields declare their provider mapping.

## Related

- [[../_module|Document Templates]] · [[template-editor]] · [[generate-from-template]]
- [[../../../hr/employee-profiles/_module|hr.profiles]] · [[../../../crm/contacts/_module|crm.contacts]]
