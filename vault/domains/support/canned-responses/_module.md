---
domain: support
module: canned-responses
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Canned Responses

Saved reply templates for common questions. Agents insert them into ticket replies and live chat with one click, with variable substitution.

---

## Module-key

`support.canned`

**Priority:** p2  
**Panel:** support  
**Permission prefix:** `support.canned`  
**Tables:** `sup_canned_responses`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../tickets/_module\|support.tickets]] | inserted into the reply composer |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | [[../live-chat/_module\|support.chat]] | chat composer insertion (P3) |

---

## Core Features

- Canned response: title, shortcut code (e.g. `/refund`), body (rich text), category
- Variable placeholders: `{{customer_name}}`, `{{agent_name}}`, `{{ticket_number}}` auto-filled on insert (unknown placeholders left literal *(assumed)*)
- Shortcut insertion: type `/shortcut` in the reply box to insert
- Categories for organisation
- Usage tracking: count how often each response is used
- Shared (team-wide) vs personal canned responses
- Search canned responses by title or content

See [[./features/response-templates|Response Templates]] and [[./features/composer-insertion|Composer Insertion]] features.

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

## Test Checklist

- [ ] Tenant isolation: company A agents never see or insert company B canned responses
- [ ] Module gating: artifacts hidden when `support.canned` inactive
- [ ] Placeholder substitution (customer/agent/ticket number); unknown left literal
- [ ] Personal response invisible to other agents; shared visible
- [ ] Duplicate shortcut rejected
- [ ] Usage count increments on insert

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | ticket context (customer/agent/number) | support.tickets | placeholder substitution at render time |
| Feeds | rendered reply body | support.tickets / support.chat | via `RenderCannedResponseAction` |

**Data ownership:** `support.canned` writes only `sup_canned_responses` (incl. `usage_count`); render reads ticket fields but writes nothing in Tickets ([[../../../security/data-ownership]]).

---

## Related

- [[../tickets/_module|support.tickets]]
- [[../live-chat/_module|support.chat]]
