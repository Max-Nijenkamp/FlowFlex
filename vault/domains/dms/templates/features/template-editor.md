---
domain: dms
module: templates
feature: template-editor
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Template Editor

Author and edit a document template — name, category, and a rich-text body with merge-field placeholders — through a Filament resource. System templates are read-only and copy-on-edit.

## Behaviour

1. Create / edit a `dms_templates` record: `name`, `category` (`hr-contracts` / `legal` / `finance` / `general`), Tiptap `body`.
2. A **merge-field insert menu** lets the author drop declared fields (`{{company_name}}`, `{{employee_name}}`, `{{date}}`, custom) into the body; each declared field is tracked in `merge_fields` (jsonb) with its source hint.
3. On save, `body` is **purified** (markup sanitised, `{{field}}` placeholders preserved).
4. **Validation:** every `{{field}}` in the body must be a declared merge field — an unknown placeholder blocks save with an error listing it.
5. **System templates** (`is_system`) are read-only. Editing one triggers **copy-on-edit**: a company-owned copy (`is_system = false`) is created and opened for editing; the seeded original is never mutated.

## UI

- **Kind**: simple-resource ([[../../../../architecture/patterns/feature-ui-spec]] #1 CRUD).
- **Page**: `DocumentTemplateResource` — "Templates" nav group (`/dms/templates`).
- **Layout**: table (name, category, system badge) → form with `name`, `category` select, and a Tiptap editor for `body` with a merge-field insert menu.
- **Key interactions**: pick a merge field from the insert menu → placeholder dropped at cursor; save → purify + validate placeholders; open a system template → read-only banner + "Duplicate to edit" copy-on-edit action.
- **States**: empty (no templates → "create your first template" CTA) · loading (table skeleton) · error (unknown-placeholder validation error listing the field) · selected (system template shows read-only banner).
- **Gating**: `dms.templates.view-any` to list; `dms.templates.create` / `dms.templates.update` for write. System templates never editable in place.

## Data

- Owns / writes: `dms_templates` (this module).
- Reads: declared merge-field metadata from the [[merge-source-registry|Merge Source Registry]] to populate the insert menu (which fields are offered).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: templates authored here are the input to [[generate-from-template|Generate From Template]].
- Shared entity: the merge-field whitelist owned by [[merge-source-registry|Merge Source Registry]].

## Unknowns

- Custom merge fields beyond the built-ins — how they're declared and typed is not fully specified.
- Whether copy-on-edit re-seeds on module upgrade — see [[../unknowns]].

## Related

- [[../_module|Document Templates]] · [[generate-from-template]] · [[merge-source-registry]]
