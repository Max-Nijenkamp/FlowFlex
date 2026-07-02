---
domain: support
module: tickets
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Tickets — DTOs & API

## DTOs

### CreateTicketData (input)

| Field | Type | Validation |
|---|---|---|
| subject | string | required, max:255 |
| description | string | required (purified) |
| requester_email | string | required, email |
| requester_name | ?string | nullable |
| category_id | ?string | ulid in company |
| priority | string | in set, default `normal` |
| source | string | in `email/form/manual/api` |
| attachments | array | mime/size whitelist (see [[./security]]) |

### ReplyData (input)

`ticket_id`, `body` (required, purified), `is_internal_note` (bool). Customer replies arrive via the inbound webhook (signature-verified), not this DTO.

### MergeTicketsData (input)

`keep_id`, `merge_id` (≠, both open-ish).

### TicketData (output)

`id`, `ticket_number`, `subject`, `status`, `priority`, `requester_name`, `requester_email`, `assignee_name`, `category_name`, `first_response_at`, `resolved_at`, `tags[]`.

---

## Inbound Webhook

`POST /webhooks/support/inbound-email` — `InboundEmailController`.

- Signature-verified (per [[../../../security/webhooks-signing]]); bodies purified.
- Routes to `TicketService::handleInboundEmail(array $payload)`: new ticket, or threaded reply matched by ticket number in the subject / `References` header.
- Rate-limited.

---

## Public / Portal Endpoints

- Optional public ticket form (Vue + Inertia `/support/new`) *(assumed: optional embed)* — runs under a guest/scoped guard, rate-limited, posts through `TicketService::create`.
- No authenticated REST API in v1; agent access is via the `/support` Filament panel (`support` guard).
