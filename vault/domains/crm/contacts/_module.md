---
domain: crm
module: contacts
type: module
build-status: in-progress
status: wip
color: "#4ADE80"
updated: 2026-07-05
---

# Contacts

Contact and company (account) records with communication history, relationship mapping, and activity timeline. The foundation of all CRM activity — the CRM anchor, build first in `/crm`.

> This module is planned for build. All prior "shipped/built" references reflect the stripped codebase; see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] for context.

---

## Module-key

`crm.contacts`

**Priority:** v1-core  
**Panel:** crm  
**Permission prefix:** `crm.contacts`  
**Tables:** `crm_contacts`, `crm_accounts`, `crm_contact_accounts`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | [[../../core/data-import/_module\|core.import]] | CSV contact import; manual entry without it |
| Soft | [[../../crm/activities/_module\|crm.activities]] | timeline tab; hidden without it |
| Soft | [[../../crm/deals/_module\|crm.deals]] | deals tab; hidden without it |

---

## Core Features

- Contact records: first name, last name, email, phone, job title, company, address
- Company (account) records: name, industry, size, website, address — contacts linked to companies
- Relationship mapping: a contact can belong to multiple companies
- Communication history: all activities (calls, emails, meetings) appear on contact timeline
- Tags: polymorphic tagging via `spatie/laravel-tags`
- Custom fields: company-specific attributes via `spatie/laravel-schemaless-attributes` per [[../../../architecture/patterns/custom-fields]] (when configured)
- Duplicate detection on import and create (same email)
- Import via Core Data Import: CSV contact upload with column mapping
- Export via `pxlrbt/filament-excel`
- Contact source tracking (website, referral, LinkedIn, manual)
- **Lead status field**: `crm_contacts.lifecycle_stage` enum (`lead | marketing_qualified | sales_qualified | opportunity | customer | churned`) — FlowFlex does NOT have a separate Lead model. A "lead" is a contact with `lifecycle_stage = lead`. This eliminates the HubSpot Lead-to-Contact conversion complexity. Reps move contacts through lifecycle stages as they qualify.

See [[./features/lifecycle-stages|Lifecycle Stages feature]] and [[./features/duplicate-detection|Duplicate Detection feature]] for deeper notes.

---

## Build Manifest

```
database/migrations/xxxx_create_crm_accounts_table.php
database/migrations/xxxx_create_crm_contacts_table.php
database/migrations/xxxx_create_crm_contact_accounts_table.php
app/Models/CRM/{Contact,Account,ContactAccount}.php
app/Data/CRM/{CreateContactData,UpdateContactData,ContactData,AccountData}.php
app/Contracts/CRM/ContactServiceInterface.php
app/Services/CRM/ContactService.php
app/Providers/CRM/CRMServiceProvider.php
app/Listeners/CRM/{CreateContactFromFormListener,CreateContactFromRegistrationListener,UpdateAccountLtvListener}.php
app/Filament/CRM/Resources/{ContactResource,AccountResource}.php
database/factories/CRM/{ContactFactory,AccountFactory}.php
tests/Feature/CRM/{ContactTest,ContactDuplicateTest,ContactListenersTest}.php
```

---

## Test Checklist

- [ ] Tenant isolation: company A cannot see/search company B contacts or accounts
- [ ] Module gating: artifacts hidden when `crm.contacts` inactive
- [ ] Duplicate email per company rejected with message; cross-company same email allowed
- [ ] `findOrCreateByEmail` idempotent (two calls = one contact)
- [ ] `InvoicePaid` listener updates account LTV; null account no-op
- [ ] Merge reassigns related records + audited
- [ ] Lifecycle stage enum enforced; any-stage moves allowed
- [ ] Custom fields validate against company definitions; unknown keys stripped
- [ ] Meilisearch returns only current company's contacts (tenant-safe search per [[../../../architecture/search]])

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Consumes | `DealWon` | crm.deals | Advance lifecycle → `customer`; update account on OWN `crm_contacts`/`crm_accounts` |
| Consumes | `InvoicePaid` | finance.invoicing | `UpdateAccountLtvListener` bumps account LTV; null account = no-op |
| Consumes | `FormSubmitted` / `UserRegistered` | forms / auth | `findOrCreateByEmail` creates a contact (idempotent) |
| Reads | `ContactService` API | support, events, marketing | Those domains find-or-create contacts here (shared entity) |

**Data ownership:** `crm.contacts` writes only `crm_contacts`, `crm_accounts`, `crm_contact_accounts`; all cross-domain effects go through events / owning-service APIs ([[../../../security/data-ownership]]).

---

## Related

- [[../deals/_module|crm.deals]]
- [[../activities/_module|crm.activities]]
- [[../../../architecture/patterns/custom-fields]]
- [[../../../architecture/search]]
- [[../../../architecture/event-bus]]
