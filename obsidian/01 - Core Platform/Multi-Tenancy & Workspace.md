---
tags: [flowflex, core, multi-tenancy, workspace, phase/1]
domain: Core Platform
panel: workspace
color: "#2199C8"
status: built
last_updated: 2026-05-06
---

# Multi-Tenancy & Workspace

Manages the complete isolation and configuration of each tenant workspace. Every other module inherits tenancy from here.

**Who uses it:** Workspace owners, FlowFlex super-admins
**Filament Panel:** `workspace`
**Depends on:** Nothing (foundational alongside Core Auth)
**Build complexity:** High — 2 pages, 2 tables (Spatie)

## Workspace Setup

- Workspace creation wizard (name, industry, size, primary currency, timezone, locale)
- Subdomain assignment (`yourcompany.flowflex.com`)
- Custom domain (CNAME record setup with DNS verification)
- Workspace avatar / logo upload
- Primary owner designation
- Initial module selection

## Branding & White-label

- Logo upload (light and dark versions)
- Primary brand colour (used in emails and client-facing portals)
- Email sender name and from-address (e.g. "Acme Corp via FlowFlex")
- Client portal custom branding (completely white-labelled, no FlowFlex mention)
- Custom login page background image

## Locale & Regionalisation

| Setting | Options |
|---|---|
| Workspace timezone | All timestamps displayed in this timezone |
| Date format | DD/MM/YYYY vs MM/DD/YYYY |
| Primary currency | For invoices, budgets, financial reports |
| Language | en, nl, de, fr, es (supported) |
| Number format | 1,000.00 vs 1.000,00 |
| First day of week | Monday vs Sunday |

## Backup & Data Management

- Scheduled workspace data exports (CSV + JSON per module)
- On-demand full export ("Download everything")
- Data retention policies per module (auto-delete old records after N years)
- Workspace deletion with 30-day recovery window
- GDPR data erasure request (deletes all PII across all modules)

## Implementation

### Workspace Settings Pages (all in `app/Filament/Workspace/Pages/Settings/`)

| Page class | Route slug | What it manages |
|---|---|---|
| `ManageCompany.php` | `settings/company` | Company name, slug, locale, timezone, currency, logo |
| `ManageTeam.php` | `settings/team` | List/add/edit/enable-disable workspace members (Tenant records) |
| `ManageNotificationPreferences.php` | `settings/notifications` | Per-type notification channel toggles |
| `ManageApiKeys.php` | `settings/api-keys` | Create/revoke API keys |

All pages require `workspace.settings.view` permission; write actions require `workspace.settings.edit`.

### Company Model Extensions

`app/Models/Company.php`
- `logo_file_id` on `fillable`; `logo(): BelongsTo` → `File`
- `logoUrl(int $minutes = 60): ?string` — returns signed URL or null

### Navigation

`WorkspacePanelProvider` — Settings navigation group: `NavigationGroup::make('Settings')->icon('heroicon-o-cog-6-tooth')`.

### Team Management (ManageTeam)

- Table queries `Tenant::withoutGlobalScopes()->where('company_id', auth('tenant')->user()->company_id)`
- Add member modal → creates `Tenant` record; no self-registration (admin creates first accounts)
- Edit row action → name/email/role
- Enable/Disable toggle → sets `is_active` flag

## Technical Notes

See [[Multi-Tenancy]] for implementation details (BelongsToTenant, global scope, spatie/laravel-multitenancy).

## Related

- [[Multi-Tenancy]]
- [[Module Billing Engine]]
- [[Authentication & Identity]]
- [[Workspace Panel]]
- [[Security Rules]]
