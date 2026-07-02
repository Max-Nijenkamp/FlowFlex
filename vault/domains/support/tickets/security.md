---
domain: support
module: tickets
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Tickets — Security

## Permissions

| Permission | Description |
|---|---|
| `support.tickets.view-any` | List all tickets in the company |
| `support.tickets.view` | View a single ticket |
| `support.tickets.create` | Create a ticket manually |
| `support.tickets.reply` | Post a public reply or internal note |
| `support.tickets.assign` | Assign / reassign a ticket |
| `support.tickets.resolve` | Resolve a ticket (fires `TicketResolved`) |
| `support.tickets.merge` | Merge two tickets |
| `support.tickets.manage-categories` | CRUD ticket categories |

Seeded in `PermissionSeeder`.

---

## Access Contract

```php
canAccess() = Auth::user()->can('support.tickets.view-any')
           && BillingService::hasModule('support.tickets')
```

Per [[../../../architecture/filament-patterns]] #1 — custom pages (`TicketInboxPage`) must state this explicitly.

---

## Tenant Isolation

- All tables carry `company_id` with global `CompanyScope` — see [[../../../architecture/multi-tenancy]].
- `ticket_number` uniqueness is scoped per company.
- Meilisearch indices are tenant-scoped; ticket search never leaks cross-company records — [[../../../architecture/search]].

---

## Inbound Webhook & Public Form

- **Webhook signing** (HIGH): inbound-email endpoint signature-verified per [[../../../security/webhooks-signing]]; reject unsigned/invalid; bodies purified before storage.
- **Public/portal guard**: the optional public ticket form runs under an explicit guest/scoped guard (not the panel session), rate-limited.

## Upload Contract

- **Attachments** (medium, per [[build/security-audit-2026-06-11]]): allowed MIME/extension whitelist, max file size, and `companies/{company_id}/` storage path for ticket attachments (Media Library).

## Encrypted Fields

None planned for v1.
