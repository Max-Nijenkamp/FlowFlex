---
domain: dms
module: templates
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Document Templates

Reusable document templates (contracts, SOPs, policies, letters) with merge-field substitution. Author a template body in Tiptap, then generate a finished document — filling merge fields from an HR employee, a CRM contact, or manual entry — and land the output in the [[../document-library/_module|Document Library]]. Sits on top of `dms.library`; it never stores documents itself.

## Module-key

`dms.templates`

**Priority:** p2  
**Panel:** dms  
**Permission prefix:** `dms.templates`  
**Tables:** `dms_templates`

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../document-library/_module\|Document Library]] (`dms.library`) | Generated documents land in the library via `DocumentService::upload` |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | Module gating (`hasModule`) |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions, `canAccess()`, generate gate |
| Soft | [[../../hr/employee-profiles/_module\|hr.profiles]] | Merge data source; registers a field provider — manual entry without it |
| Soft | [[../../crm/contacts/_module\|crm.contacts]] | Merge data source; registers a field provider — manual entry without it |

## Core Features

- **Template record** — name, category, body (Tiptap rich text with merge fields), declared `merge_fields`.
- **Merge fields** — `{{company_name}}`, `{{employee_name}}`, `{{date}}`, plus custom fields. Sensitive fields (salary, national ID) are NEVER offered as merge sources *(assumed)*.
- **Generate document** — pick a template, choose a merge source (employee / contact) or fill manually, produce a finished document in the library. PDF or document output.
- **PDF export** — branded PDF of the generated document via `spatie/laravel-pdf`.
- **Template categories** — `hr-contracts` / `legal` / `finance` / `general`.
- **System templates** — built-in starting templates (seeded, `is_system`, read-only, copy-on-edit).
- **Merge source registry** — HR / CRM modules register field providers when active; whitelisted fields only.

## See features/

- [[features/template-editor|Template Editor]] — Tiptap authoring + merge-field insert menu; system templates copy-on-edit.
- [[features/generate-from-template|Generate From Template]] — the template → source/fields → folder → generate wizard.
- [[features/merge-source-registry|Merge Source Registry]] — HR / CRM field providers, whitelist-only.

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

## Test Checklist

- [ ] Tenant isolation: company A cannot view, edit, or generate from company B's templates.
- [ ] Module gating: artifacts hidden when `dms.templates` inactive.
- [ ] All declared fields substituted; missing value blocks generation.
- [ ] Employee / contact merge sources resolve whitelisted fields only (no sensitive fields).
- [ ] PDF output branded + stored in chosen folder via the library service.
- [ ] System template uneditable; copy-on-edit produces a company-owned copy.
- [ ] Body purified; unknown placeholder at save → validation error.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | HR field provider (whitelisted fields) | hr.profiles | Resolves `{{employee_name}}` etc. via the registered provider; soft dep — absent when HR inactive |
| Reads | CRM field provider (whitelisted fields) | crm.contacts | Resolves contact merge fields via the registered provider; soft dep |
| Commands | `DocumentService::upload` | dms.library | Generated document/PDF stored through the library service — never writes `dms_documents` |
| Fires | *(none)* | — | No cross-domain events in v1 *(assumed)* |

**Data ownership:** `dms.templates` writes only `dms_templates`. Generated documents are created **through** `dms.library`'s `DocumentService::upload` (the owning API), never by writing `dms_documents` directly. Employee/contact field data is **read-only** via each domain's registered provider, whitelisted fields only — sensitive fields are never offered ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../document-library/_module|Document Library]] · [[../../hr/employee-profiles/_module|hr.profiles]] · [[../../crm/contacts/_module|crm.contacts]]
- [[../../core/billing-engine/_module|core.billing]] · [[../../core/rbac/_module|core.rbac]] · [[../../../architecture/packages]] (spatie/laravel-pdf)
