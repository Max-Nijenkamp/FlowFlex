---
domain: crm
module: leads
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# CRM Leads

Top-of-funnel prospect records — captured before they are qualified into pipeline deals.

> This module is planned for rebuild. Prior "shipped/complete" references reflect the stripped codebase; see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] for context.

## Module-key

| Field | Value |
|---|---|
| key | `crm.leads` |
| priority | v1 |
| panel | crm |
| permission-prefix | `crm.leads` |
| tables | `crm_leads` |

## Dependencies

> Source spec was missing a Dependencies table; the rows below are reconstructed from `depends-on` / `soft-depends` frontmatter *(assumed)*. See [[unknowns]].

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing/_module\|Billing]] | Module gating (`hasModule`) |
| Hard | [[../../core/rbac/_module\|RBAC]] | Permissions, `canAccess()` |
| Soft | [[../contacts/_module\|Contacts]] | Convert creates/matches a contact from the lead email |
| Soft | [[../deals/_module\|Deals]] | Convert produces a `crm_deals` row |
| Soft | [[../pipeline/_module\|Pipeline]] | Convert targets the default pipeline's first stage |

## Core Features

- **Lead capture** — name, company, email, phone, source (manual / website / referral / event / import), estimated value, owner, notes.
- **Status lifecycle** — `new → working → qualified → converted` (or `unqualified`).
- **Convert to deal** — a qualified lead becomes a `crm_deals` row in the default pipeline's first stage; a contact is created/matched from the lead email; the lead is stamped `converted` and linked to the deal. Idempotent — a converted lead cannot reconvert.

## See features/

- [[features/convert-to-deal|Convert to Deal]] — the lead → deal + contact conversion flow.

## Build Manifest

```
database/migrations/2026_06_14_090000_create_crm_leads_table.php
app/Models/CRM/Lead.php · database/factories/CRM/LeadFactory.php
app/Actions/CRM/ConvertLeadAction.php
app/Filament/CRM/Resources/LeadResource.php (+ Pages/ListLeads.php)
catalog crm.leads in config/flowflex.php; perms in PermissionSeeder; demo rows in LocalDevSeeder
tests/Feature/CRM/LeadFlowTest.php
```

## Test Checklist

- [ ] Convert creates a deal in default pipeline first stage with the lead value + stage probability.
- [ ] Convert creates/links a contact from the lead email.
- [ ] Already-converted lead refuses reconversion.
- [ ] Leads are company-scoped.

## Cross-Domain Edges

> [!warning] UNVERIFIED
> Leads is the weakest spec in the vault (see [[unknowns]]). The edges below are reconstructed from the convert flow *(assumed)*; no `LeadConverted` cross-domain event has been evaluated.

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Fires | *(none confirmed)* | — | `LeadConverted` event undecided *(assumed)* — see [[unknowns]] |
| Reads/Commands | `ContactService::findOrCreateByEmail` | crm.contacts | Convert matches/creates a contact from the lead email |
| Reads/Commands | `DealService` | crm.deals | Convert creates the `crm_deals` row via the service, never by direct write |
| Reads | default pipeline + first stage | crm.pipeline | Convert targets the default pipeline's first stage |

**Data ownership:** `crm.leads` writes only `crm_leads`; the deal and contact rows on convert are created through `DealService` / `ContactService` (their owning-service APIs), never by writing another domain's tables ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../contacts/_module|Contacts]] · [[../deals/_module|Deals]] · [[../pipeline/_module|Pipeline]]
- [[../../../glossary]]
