---
type: builder-log
module: marketing-phase5
domain: Marketing & Content
panel: marketing
phase: 5
started: 2026-05-11
status: in-progress
color: "#F97316"
left_brain_source: "[[MOC_Marketing]]"
last_updated: 2026-05-11
---

# Builder Log: Marketing Phase 5

Left Brain source: [[MOC_Marketing]]

---

## Sessions

### Session 2026-05-11

**Goal:** Build the Marketing panel, core data layer, and Filament resources for CMS, Email Campaigns, Forms & Lead Capture, and Events & Webinars (Phase 5 foundation modules).

**Built:**

Panel provider:
- `app/Providers/Filament/MarketingPanelProvider.php` — id: `marketing`, path: `/marketing`, Color::Pink, navigation groups: Content / Campaigns / Capture / Events / Analytics / Settings, same middleware stack as HrPanelProvider

Theme:
- `resources/css/filament/marketing/theme.css` — @source paths for marketing Filament classes

Migrations (range 400000–449999):
- Note: 400001 was already taken by documents table (Operations domain). Marketing migrations start at 400002.
- `database/migrations/2026_05_11_400002_create_cms_pages_table.php` — unique [company_id, slug], author_id FK to users
- `database/migrations/2026_05_11_400003_create_email_campaigns_table.php` — enum status (draft/scheduled/sending/sent/cancelled), tracking counters
- `database/migrations/2026_05_11_400004_create_marketing_forms_table.php` — fields as JSON array
- `database/migrations/2026_05_11_400005_create_form_submissions_table.php` — index [form_id, submitted_at]
- `database/migrations/2026_05_11_400006_create_marketing_events_table.php` — enum type (webinar/conference/workshop/meetup/other)
- `database/migrations/2026_05_11_400007_create_event_registrations_table.php` — index [event_id]

Models:
- `app/Models/Marketing/CmsPage.php` — BelongsToCompany, HasUlids, SoftDeletes; author()
- `app/Models/Marketing/EmailCampaign.php` — BelongsToCompany, HasUlids, SoftDeletes
- `app/Models/Marketing/MarketingForm.php` — BelongsToCompany, HasUlids, SoftDeletes; submissions(); casts fields as array
- `app/Models/Marketing/FormSubmission.php` — BelongsToCompany, HasUlids; form(); casts data as array
- `app/Models/Marketing/MarketingEvent.php` — BelongsToCompany, HasUlids, SoftDeletes; registrations()
- `app/Models/Marketing/EventRegistration.php` — BelongsToCompany, HasUlids; event()

Filament Resources:
- `app/Filament/Marketing/Resources/CmsPageResource.php` — group: Content, icon: heroicon-o-document-text, canAccess: marketing.cms; auto-slug, SEO section, author_id set in mutateFormDataBeforeCreate
- `app/Filament/Marketing/Resources/EmailCampaignResource.php` — group: Campaigns, icon: heroicon-o-envelope, canAccess: marketing.email
- `app/Filament/Marketing/Resources/MarketingFormResource.php` — group: Capture, icon: heroicon-o-clipboard-document-list, canAccess: marketing.forms; fields JSON stored as textarea (v1 — complex builder deferred)
- `app/Filament/Marketing/Resources/FormSubmissionResource.php` — group: Capture, icon: heroicon-o-inbox, canAccess: marketing.forms; read-only (canCreate returns false, no create/edit pages)
- `app/Filament/Marketing/Resources/MarketingEventResource.php` — group: Events, icon: heroicon-o-calendar-days, canAccess: marketing.events; registration_count/max_attendees formatted as "N / max" in table

All resource page trios (List/Create/Edit) with mutateFormDataBeforeCreate injecting company_id. FormSubmissionResource has only ListFormSubmissions (no create/edit).

MarketingFormResource Create/Edit pages handle JSON decode of fields textarea before save.

Dashboard + Widget:
- `app/Filament/Marketing/Pages/Dashboard.php`
- `app/Filament/Marketing/Widgets/MarketingOverviewWidget.php` — Stats: Published Pages, Active Campaigns, Upcoming Events

**Decisions made:**
- MarketingForm fields column is JSON array; v1 UI is a textarea with raw JSON. Full drag-drop form builder is a Phase 5+ enhancement. Logged as a known limitation rather than a gap.
- FormSubmission is read-only in Filament — submissions come from the public-facing API/webhook, not from staff UI. canCreate() returns false.
- EventRegistration and FormSubmission carry BelongsToCompany for consistent scoping even though they are child records. This allows direct queries without always joining through the parent.
- migration 400001 collision: documents table (Operations/DMS domain) used that number. Marketing starts at 400002. No data-model conflict — different tables.
- module keys: marketing.cms, marketing.email, marketing.forms, marketing.events (one key per resource group).

**Problems hit:**
- Marketing migration range shares 400000–449999 with the Operations domain documents table (400001). Solution: start marketing migrations at 400002. Range still within spec (400000–449999 is the marketing allocation per MOC).

---

## Gaps Discovered

None discovered in this session.

---

## Lessons

- The 19-module Marketing domain will need further sessions for: Social Media, SEO, Ad Campaigns, Affiliates, Attribution, AI Content, SMS/WhatsApp, Push, Influencer, Referrals, Reviews, DAM, Landing Pages, UTM, Contact Scoring.
- module keys marketing.cms / marketing.email / marketing.forms / marketing.events should be added to ModuleCatalogSeeder and LocalCompanySeeder before the panel is used.
- Fields JSON v1 approach (textarea) works for scaffolding but should be replaced with a proper repeater/builder component before customer-facing launch.

---

## Post-Build Checklist

- [ ] Add marketing module keys to ModuleCatalogSeeder
- [ ] Add marketing module keys to LocalCompanySeeder active subscriptions
- [ ] Register MarketingPanelProvider in bootstrap/providers.php when activating
- [ ] Run `php artisan migrate` to verify all 6 migrations execute cleanly
- [ ] Verify panel resolves at `/marketing`
- [ ] Left Brain spec updated ✅
- [ ] STATUS_Dashboard updated ✅

---

## Related

- [[ACTIVATION_GUIDE]]
- [[STATUS_Dashboard]]
- [[MOC_Marketing]]
