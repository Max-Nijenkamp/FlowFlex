---
type: module
domain: Document Management
domain-key: dms
panel: dms
module-key: dms.templates
status: planned
priority: p2
depends-on: [dms.library, core.billing, core.rbac]
soft-depends: [hr.profiles, crm.contacts]
fires-events: []
consumes-events: []
patterns: [pdf, custom-pages]
tables: [dms_templates]
permission-prefix: dms.templates
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Document Templates

Reusable document templates (contracts, SOPs, policies, letters) with variable substitution. Generate a new document from a template with merge fields filled.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/dms/document-library\|dms.library]] | generated documents land in the library |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/hr/employee-profiles\|hr.profiles]], [[domains/crm/contacts\|crm.contacts]] | merge data sources; manual field entry without them |

---

## Core Features

- Template record: name, category, body (rich text with merge fields)
- Merge fields: `{{company_name}}`, `{{employee_name}}`, `{{date}}`, custom fields — sensitive fields (salary, national ID) NEVER offered as merge sources *(assumed)*
- Generate document: select template, pick merge source (employee/contact) or fill manually, produce a new document in the library
- PDF export of generated document (spatie/laravel-pdf, branded)
- Template categories (HR contracts, legal, finance, general)
- System templates: built-in starting templates (seeded, read-only, copy-on-edit)
- Merge source registry: HR/CRM modules register field providers when active

---

## Data Model

### dms_templates

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| category | string | hr-contracts / legal / finance / general |
| body | text | purified, `{{field}}` placeholders |
| merge_fields | jsonb | declared fields + source hints |
| is_system | boolean | seeded, read-only |
| deleted_at | timestamp nullable | |

---

## DTOs

### GenerateDocumentData — template_id, target_folder_id (accessible), merge_source {type: employee/contact/manual, id?}, manual_values{} (covers all declared fields after source resolution — "All merge fields must have a value."), output (in:document,pdf)

## Services & Actions

- `MergeSourceRegistry::register(string $type, class-string $provider)` — HR/CRM providers map declared fields to model data (whitelisted fields only)
- `TemplateService::generate(GenerateDocumentData $data): DocumentData` — substitute, render (PDF when requested), store via `DocumentService::upload`
- Unknown placeholder in body at save → validation error listing it

---

## Filament

**Nav group:** Templates

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `DocumentTemplateResource` | #1 CRUD resource | Tiptap + merge-field insert menu; system templates read-only |
| `GenerateFromTemplatePage` | #7 wizard custom page | template → source/fields → folder → generate |

---

## Permissions

`dms.templates.view-any` · `dms.templates.create` · `dms.templates.update` · `dms.templates.generate`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] All declared fields substituted; missing value blocks generation
- [ ] Employee/contact merge sources resolve whitelisted fields only (no sensitive fields)
- [ ] PDF output branded + stored in chosen folder via library service
- [ ] System template uneditable; copy-on-edit
- [ ] Body purified

---

## Build Manifest

```
database/migrations/xxxx_create_dms_templates_table.php
app/Models/DMS/DocumentTemplate.php
app/Data/DMS/GenerateDocumentData.php
app/Support/DMS/MergeSourceRegistry.php
app/Services/DMS/TemplateService.php
database/seeders/SystemDocumentTemplatesSeeder.php
app/Filament/DMS/Resources/DocumentTemplateResource.php
app/Filament/DMS/Pages/GenerateFromTemplatePage.php
database/factories/DMS/DocumentTemplateFactory.php
tests/Feature/DMS/TemplateGenerationTest.php
```

---

## Related

- [[domains/dms/document-library]]
- [[architecture/packages]] (spatie/laravel-pdf)
