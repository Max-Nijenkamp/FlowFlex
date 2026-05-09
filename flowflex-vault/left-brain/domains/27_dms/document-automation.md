---
type: module
domain: Document Management
panel: dms
phase: 4
status: planned
cssclasses: domain-dms
migration_range: 997500–997999
last_updated: 2026-05-09
---

# Document Automation

Trigger document generation automatically from events in other modules. NDA auto-generated when CRM opportunity reaches "Negotiation" stage. Offer letter auto-drafted when HR hire approved.

---

## Automation Triggers

| Trigger Source | Example Trigger | Document Action |
|---|---|---|
| CRM | Opportunity stage = Negotiation | Generate NDA from template |
| CRM | Deal closed-won | Generate MSA + SOW |
| HR | Job offer approved | Generate offer letter |
| HR | Employee start date set | Generate employment contract |
| Procurement | Supplier onboarded | Generate supplier agreement |
| Finance | Invoice approved | Generate payment remittance |
| Subscriptions | New subscription | Generate subscription agreement |
| Renewals | Contract expiring in 30 days | Generate renewal agreement draft |

---

## Automation Rules

Configure per automation:
1. **Trigger**: event type + conditions (e.g., only for deals > €50k)
2. **Template**: which document template to use
3. **Variable mapping**: where each template variable comes from
4. **Action after generation**:
   - Auto-send for signature immediately
   - Draft + notify owner to review first
   - Draft + start approval workflow
5. **Owner assignment**: who owns the generated document

---

## Variable Mapping

Template variables pulled automatically from trigger context:
```
{{contact.first_name}} ← CRM contact first_name
{{deal.value}}         ← CRM opportunity value
{{company.name}}       ← tenant company name
{{sla.response_time}}  ← deal custom field: SLA tier
```

Manual fallback: if auto-fill fails, document flagged for manual completion before sending.

---

## Bulk Automation

Mass document generation:
- "Generate offer letters for all 12 hires starting next month"
- "Send annual contract renewal to all customers expiring in Q3"

Preview before sending. Override per-document before bulk dispatch.

---

## AI Document Drafting

For complex documents without a fixed template:
- AI drafts initial document based on context (deal size, territory, type)
- Human reviews and edits before approval/signature
- AI learns from accepted/rejected drafts over time

---

## Data Model

### `dms_automation_rules`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(200) | |
| trigger_source | varchar(100) | "crm.opportunity" |
| trigger_event | varchar(100) | "stage_changed" |
| trigger_conditions | json | |
| template_id | ulid | FK |
| variable_mapping | json | |
| post_action | enum | send_immediately/draft_notify/start_workflow |
| is_active | boolean | |

### `dms_automation_runs`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| rule_id | ulid | FK |
| triggered_at | timestamp | |
| trigger_entity_id | ulid | |
| document_id | ulid | nullable FK |
| status | enum | success/failed/skipped |
| error_message | text | nullable |

---

## Migration

```
997500_create_dms_automation_rules_table
997501_create_dms_automation_runs_table
```

---

## Related

- [[MOC_DMS]]
- [[document-templates]]
- [[document-workflows]]
- [[e-signature]]
- [[MOC_CRM]] — deal triggers
- [[MOC_HR]] — hire triggers
