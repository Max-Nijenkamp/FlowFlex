---
domain: dms
module: templates
feature: generate-from-template
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Generate From Template

A wizard that turns a template into a finished document: pick the template, choose a merge source (or fill fields manually), pick the destination folder, and generate — landing a document or branded PDF in the Document Library.

## Behaviour

1. **Step — template:** select a `dms_templates` record.
2. **Step — source / fields:** choose `merge_source` (`employee` / `contact` / `manual`). Employee/contact resolve whitelisted fields via the registered provider; `manual_values` fills the rest. All declared fields must have a value after resolution — otherwise "All merge fields must have a value." blocks generation.
3. **Step — folder:** pick a `target_folder_id`; it must be **accessible** via `dms.library` `accessibleFoldersFor`.
4. **Step — output:** `output` is `document` or `pdf` (branded PDF via `spatie/laravel-pdf`).
5. **Generate:** `TemplateService::generate(GenerateDocumentData)` substitutes fields into the purified body, renders, and stores the result **through** `DocumentService::upload` into the chosen folder. The generate action is rate-limited per company/user.

## UI

- **Kind**: custom-page ([[../../../../architecture/patterns/feature-ui-spec]] #7 wizard).
- **Page**: `GenerateFromTemplatePage` — "Templates" nav group (`/dms/templates/generate`).
- **Layout**: a stepper — template → source/fields → folder → output/confirm; a live field-completeness summary on the fields step.
- **Key interactions**: choose source → whitelisted fields auto-fill, remaining fields shown as manual inputs; incomplete fields → step blocked; generate → progress → success links to the new library document.
- **States**: empty (no templates → CTA to create one) · loading (generate spinner / PDF render) · error (missing field value, inaccessible folder, or rate-limit → toast + retry) · selected (chosen template / source highlighted per step).
- **Gating**: `dms.templates.view-any` to open; `dms.templates.generate` to run generate; target folder must pass `dms.library` folder access.

## Data

- Owns / writes: nothing — templates owns no document tables.
- Reads: `dms_templates` (this module); employee/contact whitelisted fields via the [[merge-source-registry|Merge Source Registry]] providers (read-only); accessible folders via `dms.library` `accessibleFoldersFor`.
- Cross-domain writes: **none directly** — the generated document is created **through** `dms.library`'s `DocumentService::upload`, never by writing `dms_documents` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: templates from [[template-editor|Template Editor]]; field values from [[merge-source-registry|Merge Source Registry]] providers.
- Feeds: a new document into [[../../document-library/_module|Document Library]] via its service.
- Shared entity: the `DocumentData` output + folder tree owned by `dms.library`.

## Test Checklist

### Unit
- [ ] Field-completeness rule: generation is blocked until every declared merge field has a resolved or manual value.

### Feature (Pest)
- [ ] `generate()` substitutes fields into the purified body, renders the requested `document`/`pdf`, and stores it via `DocumentService::upload` into the chosen folder.
- [ ] Generate into a folder the user cannot access is rejected (`accessibleFoldersFor` second gate); a company A user cannot generate into a company B folder (tenant isolation).
- [ ] Generate is throttled by the named `panel-action` limiter per company/user.

### Livewire
- [ ] The wizard blocks a step with incomplete fields; on generate, success links to the new library document.
- [ ] Page open gated on `dms.templates.view-any`; generate gated on `dms.templates.generate`.

## Unknowns

- Whether generation records provenance (template + source) on the library document — see [[../unknowns]].
- Whether heavy PDF renders move to a queue with a "generating…" state — see [[../unknowns]].

## Related

- [[../_module|Document Templates]] · [[template-editor]] · [[merge-source-registry]] · [[../../document-library/_module|Document Library]]
