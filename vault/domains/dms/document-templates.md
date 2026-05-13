---
type: module
domain: Document Management
panel: dms
module-key: dms.templates
status: planned
color: "#4ADE80"
---

# Document Templates

> Branded document template library — variable substitution, version control, and one-click document generation.

**Panel:** `dms`
**Module key:** `dms.templates`

---

## What It Does

Document Templates provides a library of reusable document templates — offer letters, NDA templates, project briefs, meeting agendas, policy documents — that are pre-formatted with the company's branding and contain variable placeholders that are substituted with data at generation time. Users select a template, fill in the variable values (or have them populated automatically from a linked record), and the system generates a new document saved to the appropriate library folder. Templates are versioned so changes do not affect previously generated documents.

---

## Features

### Core
- Template creation: upload a base document (DOCX or an in-app rich text template) with variable placeholders
- Variable substitution: define variables (e.g. {{employee_name}}, {{start_date}}) replaced at generation time
- Template categories: organise by type (HR, Legal, Finance, Marketing)
- Document generation: user selects template, fills in variables, clicks generate — document saved to library
- Template versioning: publish new template versions; historical documents retain the version used to generate them
- Template catalogue: browsable catalogue of all available templates

### Advanced
- Auto-population from records: link template variables to FlowFlex record fields (e.g. employee record → offer letter variables)
- Conditional blocks: sections of the template that appear or hide based on a variable value
- Approval before use: require template manager sign-off before a new template version goes live
- Template usage analytics: track which templates are most frequently used and by which teams
- Bulk generation: generate the same template for multiple records at once

### AI-Powered
- Template drafting: AI drafts an initial template from a brief description of the document purpose
- Variable suggestion: AI identifies likely variable placeholders from an uploaded draft document
- Readability check: AI scores template readability and flags overly complex language

---

## Data Model

```erDiagram
    document_templates {
        ulid id PK
        ulid company_id FK
        string name
        string category
        text description
        string base_file_url
        json variables
        integer version
        boolean is_active
        ulid created_by FK
        timestamps created_at_updated_at
    }

    template_generations {
        ulid id PK
        ulid template_id FK
        ulid company_id FK
        ulid generated_by FK
        ulid resulting_document_id FK
        json variable_values
        timestamp generated_at
    }

    document_templates ||--o{ template_generations : "generates"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `document_templates` | Template definitions | `id`, `company_id`, `name`, `category`, `variables`, `version`, `is_active` |
| `template_generations` | Generation log | `id`, `template_id`, `generated_by`, `resulting_document_id`, `variable_values` |

---

## Permissions

```
dms.templates.view
dms.templates.create
dms.templates.update
dms.templates.delete
dms.templates.generate
```

---

## Filament

- **Resource:** `App\Filament\Dms\Resources\DocumentTemplateResource`
- **Pages:** `ListDocumentTemplates`, `CreateDocumentTemplate`, `EditDocumentTemplate`, `ViewDocumentTemplate`
- **Custom pages:** `TemplateCataloguePage`, `DocumentGeneratorPage`
- **Widgets:** `MostUsedTemplatesWidget`, `GenerationVolumeWidget`
- **Nav group:** Library

---

## Displaces

| Feature | FlowFlex | SharePoint | Templafy | Pandadoc |
|---|---|---|---|---|
| Variable substitution | Yes | Yes | Yes | Yes |
| Auto-population from records | Yes | No | Partial | Partial |
| Template versioning | Yes | Yes | Yes | No |
| AI template drafting | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[document-library]] — generated documents saved to the library
- [[document-workflows]] — generated documents can be submitted to approval workflows
- [[hr/INDEX]] — HR templates auto-populate from employee records
- [[legal/INDEX]] — legal templates for contracts and NDAs
