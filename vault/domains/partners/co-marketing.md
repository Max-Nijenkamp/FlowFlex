---
type: module
domain: Partner & Channel
panel: partners
module-key: partners.co-marketing
status: planned
color: "#4ADE80"
---

# Co-Marketing

> Shared asset library (logos, banners, case studies, decks) for partner download, MDF budget allocation per partner, fund request workflow with proof-of-performance upload, and co-branded material generation.

**Panel:** `/partners`
**Module key:** `partners.co-marketing`

## What It Does

Co-Marketing enables the company to share branded sales and marketing materials with its partner network and to allocate and manage Market Development Funds (MDF). The asset library provides partners with a curated collection of logos, banners, case studies, one-pagers, pitch decks, and email templates they can download directly from the partner portal — without emailing the marketing team for every asset. MDF allows the company to allocate a quarterly budget to specific partners to fund their marketing activities (events, digital ads, content production). Partners request funds, the company approves, and partners upload proof of performance (invoice or campaign screenshot) to trigger reimbursement. A co-branded material generator lets partners apply the company's template assets with their own logo for approved co-branding purposes.

## Features

### Core
- Asset library: upload and manage assets in Filament. Each asset has a title, type (logo/banner/case-study/one-pager/pitch-deck/email-template/video), file (via spatie/laravel-media-library), description, visibility (public = all partners / tier-specific = bronze and above / private = specific partner list), and a download count.
- Partner asset access: partners browse and download assets from the portal. Downloads tracked in `partner_asset_downloads`. Assets require no login for public tier — only for authenticated portal users.
- MDF budget allocation: company allocates a quarterly MDF budget (amount + currency) to specific partners based on tier or individually. Budget stored per partner per year + quarter.
- MDF fund request: partner submits a request from the portal describing the marketing activity, requested amount, and planned dates. Request goes to company marketing team for approval.
- Fund request approval: marketing reviewer approves (confirming fund availability) or rejects with reason. Partner notified by email.
- Proof of performance: after the marketing activity, partner uploads proof (invoice, screenshot, campaign report) via the portal. Finance reviewer confirms proof and marks funds as disbursed.

### Advanced
- Asset versioning: when an asset file is replaced (new version uploaded), the previous version is archived (not deleted) and the version number is incremented. Partners see the latest version; admins can browse version history.
- Asset expiry: optional expiry date on assets. Expired assets are automatically hidden from the partner portal (but retained in Filament for archive reference).
- MDF budget carry-forward: company configures whether unused MDF budget from Q1 rolls over to Q2 (up to 50% carry-forward, configurable cap per tier).
- MDF utilisation report: Filament report showing total allocated vs used vs remaining MDF across all partners for the current quarter. Per-partner breakdown.
- Co-branded material generator: partners select a template asset (e.g. co-branded banner or email header), upload their logo, and the system composites the files using ImageMagick (via PHP Intervention Image) and returns a downloadable co-branded PNG/PDF. Templates define the logo placement zone and accepted dimensions.
- Asset usage analytics: download count per asset, download count per partner — visible to company admin. Helps identify which assets are most valuable.

### AI-Powered
- Asset recommendation engine: when a partner views the asset library, AI surfaces the assets most relevant to their current deal pipeline (e.g. if they have a deal in the healthcare sector, surface healthcare case studies)
- MDF activity description review: when a partner submits an MDF request, Claude reviews the activity description for completeness and surfaces any missing details before submission (e.g. "No target audience specified — please describe who the campaign targets")

## Data Model

```erDiagram
    partner_assets {
        ulid id PK
        ulid company_id FK
        string title
        string type
        string description
        string visibility
        json tier_visibility
        integer version
        integer download_count
        date expires_at
        boolean is_active
        timestamps created_at/updated_at
    }

    partner_asset_downloads {
        ulid id PK
        ulid asset_id FK
        ulid partner_id FK
        ulid partner_user_id FK
        timestamp downloaded_at
    }

    partner_mdf_budgets {
        ulid id PK
        ulid company_id FK
        ulid partner_id FK
        integer year
        integer quarter
        decimal amount
        decimal carry_forward_amount
        string currency
        decimal used_amount
        timestamps created_at/updated_at
    }

    partner_mdf_requests {
        ulid id PK
        ulid budget_id FK
        ulid partner_id FK
        string activity_description
        decimal requested_amount
        date planned_start_date
        date planned_end_date
        string status
        ulid reviewed_by FK
        timestamp reviewed_at
        string rejection_reason
        timestamp proof_submitted_at
        ulid proof_file_id FK
        boolean is_disbursed
        timestamp disbursed_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `type` on assets | logo / banner / case-study / one-pager / pitch-deck / email-template / video / other |
| `visibility` | public / tier / partner-specific |
| `tier_visibility` | JSON array of tier names when `visibility = tier` (e.g. `["silver","gold","platinum"]`) |
| `status` on requests | pending / approved / rejected / proof_submitted / disbursed |
| `carry_forward_amount` | Amount rolled over from previous quarter — added to `amount` for available total |
| `proof_file_id` | FK to a `media` table record (spatie/laravel-media-library) |

## Permissions

```
partners.co-marketing.view
partners.co-marketing.manage-assets
partners.co-marketing.manage-mdf
partners.co-marketing.approve-mdf
partners.co-marketing.disburse
```

## Filament

- **Resource:** `PartnerAssetResource` — list/create/edit assets with file upload (spatie), visibility selector, type filter, expiry date, download count display. `MdfRequestResource` — list of all MDF requests with status filter, partner filter, approve/reject actions, and proof upload review.
- **Pages:** `ListPartnerAssets`, `CreatePartnerAsset`, `EditPartnerAsset`, `ListMdfRequests`, `ViewMdfRequest`
- **Custom pages:** `MdfBudgetAllocationPage` — per-quarter view of MDF budgets across all partners with inline editing of budget amounts. "Allocate budgets" button creates budget records for all active partners based on tier rules. Class: `App\Filament\Partners\Pages\MdfBudgetAllocationPage`.
- **Widgets:** `MdfUtilisationWidget` (current quarter: total allocated, total used, total remaining), `AssetDownloadsWidget` (top 5 most downloaded assets this month) — on Partners panel dashboard
- **Nav group:** Resources (partners panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Impartner | MDF management, asset library |
| PartnerStack | Co-marketing resources |
| Manual Dropbox/Drive | Shared asset library for partners |
| Kiflo | MDF requests and approval |
| Allocadia | Marketing budget management (partially) |

## Related

- [[partner-portal]]
- [[partner-onboarding]]
- [[domains/marketing/INDEX]]
- [[domains/finance/INDEX]]

## Implementation Notes

- **File storage:** All assets stored via spatie/laravel-media-library on the `partner_assets` model using a dedicated `partner-assets` collection. S3 bucket with private access. Downloads are served via signed temporary URLs (1 hour expiry) generated on the portal download button click. `partner_asset_downloads` record created before the signed URL is returned.
- **Asset visibility query:** `PartnerAssetPolicy::view()` checks: if `visibility = public` → allowed. If `visibility = tier` → check partner tier is in `tier_visibility` JSON array. If `visibility = partner-specific` → check a separate `partner_asset_access` pivot table. Policy applied via Eloquent global scope on portal queries.
- **MDF budget allocation by tier:** `AllocateMdfBudgets` command (or manual Filament action) iterates active partners, reads the tier-based MDF budget amount from `partner_portal_configs.tier_benefits` JSON, and creates `partner_mdf_budgets` records for the specified year/quarter. Existing records are not overwritten (command is idempotent — skips if a record already exists for that partner/year/quarter).
- **Co-branded material generator:** Template assets have a metadata JSON field defining the logo placement zone (`x`, `y`, `width`, `height` as percentages of template dimensions). The `CoBrandingGenerator` service: downloads the template from S3, downloads the partner's uploaded logo, uses `intervention/image` to resize the logo to fit the zone, composites it onto the template at the defined position, and saves the result as a new temporary file served via a signed URL. Supports PNG and PDF output (PDF via ImageMagick conversion).
- **MDF balance calculation:** `partner_mdf_budgets.used_amount` is incremented when an MDF request is marked disbursed. The available balance for a new request is: `amount + carry_forward_amount - used_amount`. The `MdfRequestValidator` checks this before allowing a new request to be submitted.
