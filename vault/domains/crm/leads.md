---
type: module
domain: CRM & Sales
domain-key: crm
panel: crm
module-key: crm.leads
status: complete
priority: v1
depends-on: [core.billing, core.rbac]
soft-depends: [crm.contacts, crm.deals, crm.pipeline]
fires-events: []
consumes-events: []
patterns: [actions]
tables: [crm_leads]
permission-prefix: crm.leads
encrypted-fields: []
last-reviewed: 2026-06-14
color: "#4ADE80"
---

# Leads

Top-of-funnel prospect records — captured before they are qualified into pipeline deals. Built 2026-06-14 on founder request ("every CRM has leads that they can make into deals"). **No prior vault spec — this file documents what was built; copy is `*(assumed)*` until a design/spec lands.**

---

## Core Features

- Lead capture: name, company, email, phone, source (manual / website / referral / event / import), estimated value, owner, notes
- Status lifecycle: `new → working → qualified → converted` (or `unqualified`)
- **Convert to deal**: a qualified lead becomes a `crm_deals` row in the default pipeline's first stage; a contact is created/matched from the lead email; the lead is stamped `converted` + linked to the deal (idempotent — a converted lead can't reconvert)

## Data Model

`crm_leads`: company_id, name, company_name, email, phone, source, status, owner_id, estimated_value_cents, notes, converted_deal_id, converted_at, timestamps, softDeletes. Index `(company_id, status)`.

## Services & Actions

- `App\Actions\CRM\ConvertLeadAction` (lorisleiva action) — lead → deal in a DB transaction; resolves/creates contact; throws `ValidationException` when already converted or no pipeline/stage exists.

## Filament

- `LeadResource` (panel `crm`, nav group "Contacts", sort -1 so it sits above Contacts). Real Section form, status/source filters, "Convert to deal" row action (gated `crm.leads.convert`, hidden once converted) + Edit. Empty state teaches the capture→work→convert flow.

## Permissions

`crm.leads.view-any`, `crm.leads.create`, `crm.leads.update`, `crm.leads.delete`, `crm.leads.convert`.

## Test Checklist

- [x] Convert creates a deal in the default pipeline first stage with lead value + stage probability
- [x] Convert creates/links a contact from the lead email
- [x] Already-converted lead refuses reconversion
- [x] Leads are company-scoped
(`tests/Feature/CRM/LeadFlowTest.php`, 4 passing)

## Build Manifest

- `database/migrations/2026_06_14_090000_create_crm_leads_table.php`
- `app/Models/CRM/Lead.php` · `database/factories/CRM/LeadFactory.php`
- `app/Actions/CRM/ConvertLeadAction.php`
- `app/Filament/CRM/Resources/LeadResource.php` (+ `Pages/ListLeads.php`)
- catalog `crm.leads` in `config/flowflex.php`; perms in `PermissionSeeder`; demo rows in `LocalDevSeeder`
- `tests/Feature/CRM/LeadFlowTest.php`

## Related

[[contacts]] · [[deals]] · [[pipeline]] · [[../../build/gaps/gap-switchboard-expansion-spec-missing|design-gap]]
