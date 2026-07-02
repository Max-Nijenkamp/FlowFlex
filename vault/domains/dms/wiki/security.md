---
domain: dms
module: wiki
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Wiki — Security

## Permissions

| Permission | Grants |
|---|---|
| `dms.wiki.view-any` | View the wiki + page records |
| `dms.wiki.create` | Create wiki pages |
| `dms.wiki.update` | Edit wiki pages (+ restore versions) |
| `dms.wiki.delete` | Delete wiki pages |
| `dms.wiki.manage-access` | Configure per-page access restrictions |

The per-page access list is a **second gate** on top of these permissions. See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('dms.wiki.view-any')
        && BillingService::hasModule('dms.wiki');
}
```

## Page Access Control

`accessiblePagesFor(User)` is the single source of truth for what a user may see. A `restricted` page (`access_level = restricted`, `access_list` = role/user ids) is invisible in the **tree**, in **Meilisearch results**, AND on the **direct viewer URL** for non-permitted users. Every list / tree / search / viewer path composes on this one scope so no path can leak a restricted page in isolation.

> [!warning] UNVERIFIED
> Unlike the document library's folder inheritance, wiki access is stated **per page** — the source does not say restriction cascades down the nested `parent_page_id` tree. Whether a restricted parent hides its children is an open question ([[unknowns]]); v1 is assumed **per-page, non-inherited** *(assumed)*.

## Body Sanitisation (explicit)

Rich text body is **purified with `ezyang/htmlpurifier` before storage** — the stored `body` is already safe HTML, so an XSS payload never reaches another viewer. This is a build-time test requirement (XSS fixture in the checklist).

## Search Rate Limiting

`RateLimiter::for` on the wiki **search** endpoint, scoped per company/user, to prevent abuse of the Meilisearch instance ([[architecture]]).

## Tenant Isolation

Both tables carry `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains every query. See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None. `encrypted-fields` is empty in the source spec; wiki bodies are handbook/SOP content, protected by page access rather than column encryption *(assumed)*.
