---
domain: crm
module: quotes
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Quotes — Security

See also [[../../../security/tenancy-isolation]], [[../../../security/authn-authz]], [[../../../architecture/filament-patterns]].

---

## Permissions

| Permission | Description |
|---|---|
| `crm.quotes.view-any` | View quote list |
| `crm.quotes.view` | View a single quote |
| `crm.quotes.create` | Create a new quote |
| `crm.quotes.update` | Edit a quote (draft only) |
| `crm.quotes.send` | Send a quote to a contact |
| `crm.quotes.accept` | Internally mark a sent quote accepted (rep path; the public path uses the signed token, no permission) |
| `crm.quotes.decline` | Internally mark a sent quote declined |
| `crm.quotes.delete` | Soft-delete a quote |

**Rate limiting:** `crm.quotes.send` queues outbound mail + PDF generation (comms/file action) → runs behind the named `panel-action` rate limiter. The public accept/decline route runs behind a named rate limiter (token-enumeration guard, see Public/Portal Guard below). Per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]].

---

## Access Contract

Every Filament artifact gates on:

```php
canAccess() = Auth::user()->can('crm.quotes.view-any')
           && BillingService::hasModule('crm.quotes')
```

Per [[../../../architecture/filament-patterns]] #1.

---

## Public/Portal Guard (HIGH priority)

The public quote route `/quotes/{token}` runs on a **guest (no app-session) guard**.

Requirements:
- Validate signed `accept_token` — must match a single `crm_quotes` row
- Token is scoped to that quote only — cannot be replayed on a different quote
- Route isolated from authenticated app guards (no session bleed)
- Rate-limited to prevent token enumeration

See [[features/public-acceptance|public-acceptance feature]] for implementation notes. See [[../../../security/authn-authz]] for guard isolation patterns.

---

## Tenant Isolation

- All authenticated queries scoped by `company_id` via `BelongsToCompany` + `CompanyScope`
- `accept_token` is a unique UUID — look up by token alone (no company_id needed, token is globally unique)
- `ExpireQuotesCommand` filters strictly by `status=sent AND valid_until < today` — no cross-tenant mutations
- PDF stored at `pdf_path` — access must go through the controller, not direct storage URL exposure

See [[../../../security/tenancy-isolation]] and [[../../../architecture/multi-tenancy]].
