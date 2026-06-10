---
type: module
domain: Support & Help Desk
domain-key: support
panel: support
module-key: support.canned
status: planned
priority: p2
depends-on: [support.tickets, core.billing, core.rbac]
soft-depends: [support.chat]
fires-events: []
consumes-events: []
patterns: []
tables: [sup_canned_responses]
permission-prefix: support.canned
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Canned Responses

Saved reply templates for common questions. Agents insert them into ticket replies and live chat with one click, with variable substitution.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/support/tickets\|support.tickets]] | inserted into reply composer |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/support/live-chat\|support.chat]] | chat composer insertion (P3) |

---

## Core Features

- Canned response: title, shortcut code (e.g. `/refund`), body (rich text), category
- Variable placeholders: `{{customer_name}}`, `{{agent_name}}`, `{{ticket_number}}` auto-filled on insert (unknown placeholders left literal *(assumed)*)
- Shortcut insertion: type `/shortcut` in reply box to insert
- Categories for organisation
- Usage tracking: count how often each response is used
- Shared (team-wide) vs personal canned responses
- Search canned responses by title or content

---

## Data Model

### sup_canned_responses

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| title | string | |
| shortcut | string | unique `(company_id, shortcut)`, slug-like |
| body | text | purified |
| category | string nullable | |
| owner_id | ulid FK users | |
| is_shared | boolean default false | personal visible to owner only |
| usage_count | int default 0 | |
| deleted_at | timestamp nullable | |

---

## DTOs

### CreateCannedResponseData — title (required), shortcut (required, regex `[a-z0-9-]+`, unique per company), body (required), category?, is_shared

## Services & Actions

- `RenderCannedResponseAction::run(string $id, Ticket $ticket): string` — substitutes placeholders, increments usage
- Visibility scope: own + shared

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `CannedResponseResource` | #1 CRUD resource | shared/personal tabs, usage column |
| Composer insert action | within ticket reply (and chat later) | `/shortcut` autocomplete |

---

## Permissions

`support.canned.view-any` · `support.canned.create` · `support.canned.update` · `support.canned.manage-shared`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Placeholder substitution (customer/agent/ticket number); unknown left literal
- [ ] Personal response invisible to other agents; shared visible
- [ ] Duplicate shortcut rejected
- [ ] Usage count increments on insert

---

## Build Manifest

```
database/migrations/xxxx_create_sup_canned_responses_table.php
app/Models/Support/CannedResponse.php
app/Data/Support/CreateCannedResponseData.php
app/Actions/Support/RenderCannedResponseAction.php
app/Filament/Support/Resources/CannedResponseResource.php
database/factories/Support/CannedResponseFactory.php
tests/Feature/Support/CannedResponseTest.php
```

---

## Related

- [[domains/support/tickets]]
- [[domains/support/live-chat]]
