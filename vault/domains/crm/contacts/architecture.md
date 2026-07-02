---
domain: crm
module: contacts
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Contacts — Architecture

## Services & Actions

Interface→Service: `ContactServiceInterface` → `ContactService`.

- `create(CreateContactData $data): ContactData` / `update(...)`
- `findOrCreateByEmail(string $email, array $attributes = []): ContactData` — the listener entry point (form submissions, event registrations); idempotent by email
- `moveLifecycleStage(string $contactId, string $stage): ContactData`
- `merge(string $keepId, string $mergeId): ContactData` — duplicate resolution; reassigns activities/deals, audited *(assumed)*
- `linkAccount(string $contactId, string $accountId, ?string $title, bool $isPrimary): void`

---

## Events

### Consumes (queued + WithCompanyContext, contracts in [[../../../architecture/event-bus]]):

| Event | Source | Handler | Action |
|---|---|---|---|
| `FormSubmissionReceived` | marketing (P3) | `CreateContactFromFormListener` | find-or-create contact, attach submission activity |
| `EventRegistrationReceived` | events (P3) | `CreateContactFromRegistrationListener` | find-or-create contact |
| `InvoicePaid` | finance | `UpdateAccountLtvListener` | update account `lifetime_value_cents` + last-payment activity (no-op when `crm_account_id` null) |

---

## Filament Artifacts

**Nav group:** Contacts

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ContactResource` | #1 CRUD resource | search, filters: owner/account/stage/tag; lifecycle stage quick-move; excel export |
| Contact view page | #2 detail with tabs | Overview, Activities (soft-dep), Deals (soft-dep), Files |
| `AccountResource` | #1 CRUD resource | view shows contacts + deals + LTV |

**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('crm.contacts.view-any') && BillingService::hasModule('crm.contacts')` per [[../../../architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

---

## Search & Realtime

Meilisearch (Scout) — see [[../../../architecture/search]]:

- Contacts indexed: `first_name`, `last_name`, `email`, `job_title`, account name
- Accounts indexed: `name`, `industry`, `website`
- CRM global search surface
- Realtime: none

---

## Security Notes

Per [[build/security-audit-2026-06-11]]:

- **Rate limiter** (medium): a named rate limiter is planned for the import upload and export actions, and throttling/dedupe on contact-creating event listeners.

See [[./security]] for full access contract and permissions.
