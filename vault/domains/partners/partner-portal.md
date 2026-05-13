---
type: module
domain: Partner & Channel
panel: partners
module-key: partners.portal
status: planned
color: "#4ADE80"
---

# Partner Portal

> Partner-facing microsite at `/partner-portal` (Vue 3 + Inertia, separate auth guard) where partners see their deal pipeline, commission balance, training resources, co-marketing assets, performance metrics, and tier status.

**Panel:** `/partners`
**Module key:** `partners.portal`

## What It Does

Partner Portal is the external-facing layer of the Partner & Channel domain. It provides partner organisations and their users with a dedicated web portal at `/partner-portal` where they can manage their relationship with the company without needing access to the main FlowFlex application. Partners log in with credentials from the `partner_users` table, which is entirely separate from the tenant user system. The portal is built with Vue 3 + Inertia (sharing the same public frontend stack) and is NOT a Filament panel. Company admins control which sections of the portal are visible via a portal builder in Filament. Partners can see their tier (Bronze/Silver/Gold/Platinum), the benefits associated with their tier, their deal pipeline, commission balance, and access training and co-marketing resources.

## Features

### Core
- Partner login at `/partner-portal/login` — separate auth guard (`partner` guard) using `partner_users` table credentials. Password reset via email magic link.
- Partner dashboard: displays tier badge (Bronze/Silver/Gold/Platinum), key metrics (deals submitted this quarter, commissions earned, deal acceptance rate), and quick actions
- Deal pipeline view: list of all deals registered by this partner with current status (pending/approved/rejected/expired) and CRM deal stage for approved deals
- Commission balance: total approved commissions, total paid, pending balance. Transaction history table with individual commission line items.
- Training section: links to learning paths assigned to this partner in the LMS domain (if LMS is active)
- Co-marketing assets: downloadable asset library filtered to assets marked `is_public` or explicitly shared with the partner's tier
- Performance metrics: this quarter vs last quarter comparison of deals registered, deals won, revenue attributed, commissions earned

### Advanced
- Portal builder in Filament admin: company admin configures which sections are visible (toggle on/off per section: Deals, Commissions, Training, Assets, MDF, Leaderboard). Settings stored in `partner_portal_configs`.
- Tier display with benefits: partner sees their current tier and a comparison table of all tiers and their benefits (commission rates, MDF budget allocation, deal protection period, support priority). Benefits table is configured by the company admin.
- Partner team management: partner admins can invite additional users from their organisation to the portal (up to the limit set by their tier). Partner users have their own permission level (partner_admin / partner_user).
- Notifications in portal: in-app notifications for deal status changes, commission approvals, new asset available, MDF request status
- Leaderboard: optional partner leaderboard showing anonymised or named rankings by deals won or revenue attributed (company admin chooses visibility)
- Multilingual portal: portal locale set per partner (company admin assigns locale to partner record). Translations via Laravel's standard `lang/` files.

### AI-Powered
- Partner success suggestions: AI analyses a partner's deal submission and win rate history and surfaces recommendations in the portal dashboard ("Your win rate is 20% below similar partners — consider taking the Advanced Selling certification")
- Personalised asset recommendations: AI recommends the most relevant co-marketing assets based on the partner's industry focus and recent deal types

## Data Model

```erDiagram
    partners {
        ulid id PK
        ulid company_id FK
        string name
        string type
        string tier
        string status
        ulid contact_id FK
        string portal_slug
        timestamps created_at/updated_at
        timestamp deleted_at
    }

    partner_users {
        ulid id PK
        ulid partner_id FK
        string name
        string email
        string password_hash
        string role
        timestamp last_login_at
        string locale
        timestamp email_verified_at
        timestamps created_at/updated_at
    }

    partner_portal_configs {
        ulid id PK
        ulid company_id FK
        boolean show_deals
        boolean show_commissions
        boolean show_training
        boolean show_assets
        boolean show_mdf
        boolean show_leaderboard
        json tier_benefits
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `type` | reseller / affiliate / referral / integration |
| `tier` | bronze / silver / gold / platinum |
| `status` | applicant / active / suspended / churned |
| `contact_id` | FK to `crm_contacts` — links partner organisation to their CRM record |
| `portal_slug` | Used in partner-specific URLs; globally unique |
| `role` on `partner_users` | partner_admin / partner_user — controls what the user can do within their own partner context |
| `password_hash` | Separate from the main users table — hashed with `bcrypt` like standard Laravel auth |

## Permissions

```
partners.portal.view
partners.portal.configure
partners.portal.manage-users
partners.portal.impersonate
partners.portal.deactivate
```

## Filament

- **Resource:** `PartnerResource` — full CRUD for managing partner records. Shows partner name, type badge, tier badge, status, deal count, commission balance. Actions: edit, suspend, view portal as partner (impersonate), send portal invite email.
- **Pages:** `ListPartners`, `CreatePartner`, `EditPartner`, `ViewPartner` (partner overview with tabs: Details, Team, Deals, Commissions, Onboarding progress)
- **Custom pages:** `PortalConfigPage` — admin-facing page for configuring the portal builder (section toggles, tier benefits table editor, portal branding). Not a Resource — a single-instance config page. Class: `App\Filament\Partners\Pages\PortalConfigPage`.
- **Widgets:** `ActivePartnersWidget`, `NewPartnerApplicationsWidget`, `TopPartnersWidget` on the Partners panel dashboard
- **Nav group:** Partners (partners panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| PartnerStack | Partner portal, tier management |
| Kiflo PRM | Partner portal, deal and commission visibility |
| Impartner | PRM partner portal |
| Salesforce PRM | Partner community and portal |
| Alliances.io | Partner management and portal |

## Related

- [[deal-registration]]
- [[partner-commissions]]
- [[partner-onboarding]]
- [[co-marketing]]
- [[affiliate-management]]
- [[domains/crm/contacts]]
- [[domains/lms/INDEX]]

## Implementation Notes

- **Separate auth guard:** The `partner` guard is defined in `config/auth.php` with `provider = partner_users` (Eloquent provider using `PartnerUser` model) and `driver = session`. Portal routes are grouped under `Route::middleware(['partner.auth'])` in `routes/web.php`. The `PartnerAuthMiddleware` checks the `partner` guard session, not the default `web` guard.
- **Portal routes:** All partner portal routes are prefixed `/partner-portal` and handled by Inertia controllers in `App\Http\Controllers\PartnerPortal\`. Vue components live in `resources/js/partner-portal/`. The portal shares the Vite build pipeline but is a separate Inertia root layout.
- **Impersonation:** Filament admin users with `partners.portal.impersonate` permission can click "View as Partner" on any partner record. This stores the partner user ULID in the admin session and redirects to the portal with an impersonation flag. A persistent banner in the portal shows "Impersonating [partner name]" with an exit link. Impersonation uses `PartnerImpersonation` middleware, not a real auth guard switch.
- **Password-less portal invite:** When a new partner user is created (by the partner admin in the portal or by the company admin in Filament), an invite email is sent with a signed URL (24-hour expiry) that sets the password on first click. No initial password is stored.
- **Tier benefits JSON:** `tier_benefits` in `partner_portal_configs` stores a structured JSON: `{ "bronze": { "commission_rate": 10, "mdf_budget": 0, "deal_protection_days": 60 }, "silver": {...}, ... }`. This is used by the portal frontend to render the benefits comparison table and by the commission and MDF modules to apply tier-specific rules.
