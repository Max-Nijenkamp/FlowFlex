---
domain: marketing
module: forms
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Forms — Architecture

Parent: [[_module]]

## Services & Actions

| Class | Signature | Responsibility |
|---|---|---|
| `FormService::submit` | `submit(slug, values, ip): void` | Validate values against the form definition; drop if honeypot filled; store `mkt_form_submissions`; fire `FormSubmissionReceived`; (soft) enrol in sequence; notify users. |
| Embed endpoint | `PublicFormController` | Serves cached form-definition JSON + JS renderer for iframe/snippet embeds; hosted page at `/f/{slug}`. |

## Events

Fires `FormSubmissionReceived` on each successful submit. Consumers: CRM (find-or-create contact), sequences (enrol), UTM (record touch). Consumes none. See [[../../../architecture/event-bus]].

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `FormResource` | Forms | #1 CRUD resource | field repeater, embed-code copy, submissions relation |
| `FormSubmissionResource` | Forms | #1 (read-only) | export |

Hosted page: Vue + Inertia `/f/{slug}` — ui-strategy row #16.

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('marketing.forms.view-any')
        && BillingService::hasModule('marketing.forms');
}
```

## Public route

`POST /f/{slug}` — no auth, resolves company by slug, CSRF-exempt with allowed-origin handling for cross-site embeds, per-IP throttle. See [[security]].

## Related

- [[_module]] · [[data-model]] · [[../../../architecture/event-bus]] · [[../../../architecture/security]]
