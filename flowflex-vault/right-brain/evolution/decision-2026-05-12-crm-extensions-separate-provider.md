---
type: adr
date: 2026-05-12
status: decided
color: "#F97316"
---

# Decision: CRM Phase 8 Extensions use a Separate Service Provider

## Context

Phase 8 adds 7 new CRM modules (CDP, Client Portal, Loyalty, Deal Room, Sales Sequences, Revenue Intelligence, AI Sales Coach) to the existing CRM panel. The existing `CrmServiceProvider` already binds `CrmDealServiceInterface` and is registered in `bootstrap/providers.php`. The constraint was: do NOT modify `CrmServiceProvider` or `bootstrap/providers.php`.

## Options Considered

1. **Add new bindings to the existing `CrmServiceProvider`** — simple, one file. Ruled out: violates the "do not touch existing CRM files" constraint; also violates open-closed principle for a provider that is already stable.

2. **Create a new `CrmExtensionsServiceProvider`** — a second provider in `app/Providers/Crm/` that binds only the Phase 8 service interfaces. Registered separately in `bootstrap/providers.php`. Clean separation; no risk of breaking Phase 3 bindings.

3. **Inline service resolution (no interface)** — skip the interface layer, resolve concrete classes directly. Ruled out: inconsistent with all other domains; breaks dependency injection testability.

## Decision

Option 2: `CrmExtensionsServiceProvider` in `app/Providers/Crm/`. It binds 7 interfaces:

- `CustomerDataServiceInterface` → `CustomerDataService`
- `ClientPortalServiceInterface` → `ClientPortalService`
- `LoyaltyServiceInterface` → `LoyaltyService`
- `DealRoomServiceInterface` → `DealRoomService`
- `SalesSequenceServiceInterface` → `SalesSequenceService`
- `RevenueIntelligenceServiceInterface` → `RevenueIntelligenceService`
- `SalesCoachServiceInterface` → `SalesCoachService`

## Consequences

- Phase 3 code is untouched — zero regression risk.
- `bootstrap/providers.php` must have `\App\Providers\Crm\CrmExtensionsServiceProvider::class` added alongside `CrmServiceProvider`.
- Pattern is reusable: any future phase extension to an existing domain creates a new `{Domain}ExtensionsServiceProvider` rather than modifying the original.
- Slightly more providers to register — acceptable at this scale.

## Related Left Brain

- `left-brain/domains/05_crm/MOC_CRM.md` — Phase 8 extension architecture
- `right-brain/builder-logs/builder-log-crm-phase8.md`
