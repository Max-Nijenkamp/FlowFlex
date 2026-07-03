---
domain: events
module: speakers
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Speakers — Security

## Permissions

| Permission | Grants |
|---|---|
| `events.speakers.view-any` | View the directory + records |
| `events.speakers.manage` | Create/edit speakers |
| `events.speakers.assign` | Assign speakers to sessions |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('events.speakers.view-any')
        && BillingService::hasModule('events.speakers');
}
```

## Public Exposure

- Public landing shows **confirmed** speakers only. `logistics` (travel/accommodation/AV/notes) is internal and never rendered publicly.

## Signed-Token Submit

- The self-submit endpoint resolves the speaker by a signed `submit_token`; invalid/expired tokens → 404. Rate-limited.

## Rich-text + Uploads

- `bio` is sanitized via HTMLPurifier on **both** admin and public-token writes (per [[../../../_archive/build-history/security-audit-2026-06-11]], medium).
- Photo upload: allowed image MIME whitelist, max file size, `companies/{id}/` media path — enforced especially on the public submit endpoint (medium).

## Tenant Isolation

- Both tables carry `company_id` (indexed); `CompanyScope` constrains queries. See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None. Speaker bios/social links are public-facing by design; logistics is internal but not encrypted *(assumed)* — see [[unknowns]].
