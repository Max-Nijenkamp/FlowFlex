---
domain: crm
module: leads
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Leads — API / DTOs

> The source spec defined **no DTOs**. `ConvertLeadAction` takes a lead id and no formal input DTO is specified *(assumed)*. The shapes below are reconstructed from the data model and must be confirmed during the v2 spec rebuild. See [[unknowns]].

## Input (assumed form-data shape)

Lead create/update is handled by `LeadResource`'s section form. The implied fields *(assumed)*:

| Field | Type | Rules |
|---|---|---|
| `name` | string | required |
| `company_name` | string | nullable |
| `email` | string | nullable, email |
| `phone` | string | nullable |
| `source` | enum | in: manual, website, referral, event, import |
| `estimated_value_cents` | int | nullable, min:0 |
| `owner_id` | ulid | nullable, exists in company users |
| `notes` | text | nullable |

## ConvertLeadAction input

- Takes a **lead id** (the `Lead` model instance). No formal input DTO exists in the source *(assumed)*.
- Output: the created `crm_deals` row (and, as a side effect, a matched/created `crm_contacts` row and a stamped lead).

## Public / Portal Endpoints

None. Leads is an internal CRM resource with no public or portal-facing routes.
