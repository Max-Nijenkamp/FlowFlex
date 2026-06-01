---
type: module
domain: Document Management
panel: dms
module-key: dms.templates
status: planned
color: "#4ADE80"
---

# Document Templates

Reusable document templates (contracts, SOPs, policies, letters) with variable substitution. Generate a new document from a template with merge fields filled.

## Core Features

- Template record: name, category, body (rich text with merge fields)
- Merge fields: `{{company_name}}`, `{{employee_name}}`, `{{date}}`, custom fields
- Generate document: select template, fill merge fields, produce a new document in the library
- PDF export of generated document (spatie/laravel-pdf)
- Template categories (HR contracts, legal, finance, general)
- System templates: built-in starting templates
- Pull merge data from other domains (employee data from HR, contact data from CRM)

## Data Model

| Table | Key Columns |
|---|---|
| `dms_templates` | company_id, name, category, body, merge_fields (json), is_system |

## Filament

**Nav group:** Templates

- `DocumentTemplateResource` — create, edit (Tiptap editor with merge field insertion)
- `GenerateFromTemplatePage` (custom page) — select template, fill fields, generate

## Cross-Domain

- Merge data sourced from HR (employees), CRM (contacts) where available

## Related

- [[domains/dms/document-library]]
- [[architecture/packages]] (spatie/laravel-pdf)
