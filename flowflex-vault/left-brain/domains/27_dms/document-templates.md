---
type: module
domain: Document Management
panel: dms
phase: 4
status: complete
cssclasses: domain-dms
migration_range: 995000–995499
last_updated: 2026-05-12
---

# Document Templates

Reusable document templates with dynamic variables. Generate NDAs, contracts, proposals, offer letters, invoices from a template in seconds — no copy-paste, no formatting errors.

---

## Template Types

| Category | Examples |
|---|---|
| Legal / Contracts | NDA, MSA, SLA, service agreement |
| HR | Offer letter, employment contract, termination notice |
| Sales | Proposal, quotation, SOW |
| Finance | Invoice (if not using billing module), expense claim |
| Procurement | PO, RFQ, supplier agreement |
| Onboarding | Welcome pack, policy acknowledgement |

---

## Template Engine

Templates authored in a rich-text editor with variable placeholders:
```
Dear {{recipient.first_name}},

On behalf of {{company.name}}, we are pleased to offer you the role of
{{job.title}} at a salary of {{job.salary}} {{job.currency}} per annum.

Your start date will be {{job.start_date}}.
```

Variables sourced from:
- Entity data (employee, contact, supplier)
- Manual input at generation time
- Conditional blocks: `{{#if job.remote}}This is a remote position.{{/if}}`

---

## Template Versioning

Every template edit creates a new version. Documents generated always reference the template version used. Older generated documents remain valid against their version — no retroactive changes.

---

## Approval Before Use

Templates requiring legal sign-off:
- New template → draft → legal review → approved → published
- Only published templates available to end users
- Legal team notified of template change requests

---

## Data Model

### `dms_templates`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(300) | |
| category | varchar(100) | |
| content | longtext | HTML with variable placeholders |
| version | int | |
| status | enum | draft/review/published/archived |
| requires_signature | boolean | |

### `dms_template_variables`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| template_id | ulid | FK |
| key | varchar(100) | e.g. "job.title" |
| label | varchar(200) | |
| source | enum | entity/manual/formula |
| required | boolean | |

---

## Migration

```
995000_create_dms_templates_table
995001_create_dms_template_versions_table
995002_create_dms_template_variables_table
```

---

## Related

- [[MOC_DMS]]
- [[document-workflows]]
- [[e-signature]]
- [[document-automation]]
