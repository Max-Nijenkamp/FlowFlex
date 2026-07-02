---
domain: infrastructure
type: infrastructure
build-status: planned
status: unverified
color: "#F97316"
updated: 2026-06-20
---

# Module Catalog

The catalog of activatable modules is **code-defined**, in two places:

| Set | Source | Count | Notes |
|---|---|---|---|
| Free Core Platform | `ModuleCatalog::FREE_CORE` (`app/Models/ModuleCatalog.php`) | **16** | `core.auth, settings, rbac, invitations, billing, marketplace, audit, notifications, files, import, webhooks, api, setup, privacy, i18n, health` — included with every subscription |
| Paid domain modules | `config/flowflex.php` → `modules` | **46** | `hr.*` ×15, `finance.*` ×13, `crm.*` ×18 — per-user monthly price (many `*(assumed)*`) |

`ModuleCatalog` is a Sushi (static Eloquent) model: free-core set + the config array. `ModuleCatalogSeeder`
is intentionally a **no-op** ("nothing to seed — code-defined").

> [!important] The catalog outlived the code on purpose
> The [[../decisions/decision-2026-06-19-strip-to-app-admin-shell|strip]] deleted all HR/Finance/CRM
> **code** but kept these 46 catalog entries — they are **billing + marketing metadata** that the public
> pricing site and the in-app marketplace read. Activating one writes a `company_module_subscription`
> billing row only; no domain code is required. So these 46 are `build-status: planned` as *features*
> but `built` as *catalog data*.

Also in `config/flowflex.php`: `webhook_events` (2 platform events), `notification_types` (2),
`dunning_retry_days` `[3,7,14]`.

## Consumers

- [[../domains/core/module-marketplace/_module]] — activation UI (writes billing rows).
- [[../domains/core/billing-engine/_module]] — per-module pricing → monthly invoice.
- [[../product/pricing-model]] — the commercial packaging this catalog implements.
- Public pricing/product pages ([[../frontend/_index]]).

## Related

- [[../domains/core/module-marketplace/_module]] · [[../product/pricing-model]] · [[_moc|Infrastructure MOC]]
