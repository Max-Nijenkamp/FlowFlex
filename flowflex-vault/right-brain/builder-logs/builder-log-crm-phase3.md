---
type: builder-log
module: crm-phase3
domain: CRM & Sales
panel: crm
phase: 3
started: 2026-05-11
status: in-progress
color: "#F97316"
left_brain_source: "[[MOC_CRM]]"
last_updated: 2026-05-12
---

# Builder Log — CRM & Sales Phase 3

## Summary

Phase 3 CRM & Sales domain initial build. Covers the full data layer and Filament panel for 5 core modules: Contact & Company Management, Sales Pipeline, Activities, Customer Support & Helpdesk, and Quotes & Proposals.

---

## Sessions

### 2026-05-11 — Initial Phase 3 CRM Build

**What was built:**

Panel:
- `app/Providers/Filament/CrmPanelProvider.php` — id: `crm`, path: `/crm`, Color::Rose, middleware mirrors HrPanelProvider exactly
- `resources/css/filament/crm/theme.css` — copied from HR theme, sources pointing to Crm

Migrations (range 250001–250010):
- `2026_05_11_250001_create_crm_contacts_table.php` — crm_contacts (ulid pk, company_id FK, first/last name, email, phone, company_name, job_title, source, status enum, notes, composite index [company_id, status])
- `2026_05_11_250002_create_crm_companies_table.php` — crm_companies (ulid pk, company_id FK, name, domain, industry, size, website, phone, address, notes, index company_id)
- `2026_05_11_250003_create_crm_contact_company_table.php` — pivot with composite PK [crm_contact_id, crm_company_id], role nullable
- `2026_05_11_250004_create_deal_stages_table.php` — deal_stages (ulid pk, company_id FK, name, sort_order, probability, is_won, is_lost)
- `2026_05_11_250005_create_crm_deals_table.php` — crm_deals (ulid pk, company_id FK, title, crm_contact_id, crm_company_id, deal_stage_id, owner_id FK users, value decimal 15,2, currency, expected_close_date, status enum, lost_reason, index [company_id, status])
- `2026_05_11_250006_create_crm_activities_table.php` — crm_activities (ulid pk, company_id, user_id, contact_id, deal_id, type enum, subject, description, due_at, completed_at)
- `2026_05_11_250007_create_crm_tickets_table.php` — crm_tickets (ulid pk, company_id, contact_id, assigned_to, title, description, status enum, priority enum, source enum, resolved_at, index [company_id, status, priority])
- `2026_05_11_250008_create_crm_ticket_comments_table.php` — crm_ticket_comments (ulid pk, company_id, ticket_id, user_id, body, is_internal)
- `2026_05_11_250009_create_crm_quotes_table.php` — crm_quotes (ulid pk, company_id, deal_id, contact_id, number, title, issue_date, expiry_date, status enum, subtotal, tax_amount, total, notes)
- `2026_05_11_250010_create_crm_quote_items_table.php` — crm_quote_items (ulid pk, quote_id, description, quantity, unit_price, total)

Models (`app/Models/Crm/`):
- `CrmContact.php` — BelongsToCompany, HasUlids, SoftDeletes; relationships: companies (M2M), deals, tickets, activities, quotes; `getFullNameAttribute()`
- `CrmCompany.php` — BelongsToCompany, HasUlids, SoftDeletes; relationships: contacts (M2M), deals
- `DealStage.php` — BelongsToCompany, HasUlids; relationships: deals
- `CrmDeal.php` — BelongsToCompany, HasUlids, SoftDeletes; relationships: contact, company, stage, owner, activities
- `CrmActivity.php` — BelongsToCompany, HasUlids; relationships: contact, deal, user
- `CrmTicket.php` — BelongsToCompany, HasUlids, SoftDeletes; relationships: contact, assignedTo, comments
- `CrmTicketComment.php` — BelongsToCompany, HasUlids; relationships: ticket, user
- `CrmQuote.php` — BelongsToCompany, HasUlids, SoftDeletes; relationships: deal, contact, items
- `CrmQuoteItem.php` — HasUlids only (no company_id column on items table); relationships: quote

Service layer:
- `app/Contracts/Crm/CrmDealServiceInterface.php` — createDeal, moveToPipeline, markWon, markLost, seedDefaultStages
- `app/Services/Crm/CrmDealService.php` — full implementation; seedDefaultStages seeds 6 default stages: Prospecting(10%), Qualification(20%), Proposal(40%), Negotiation(60%), Closed Won(100% is_won), Closed Lost(0% is_lost)
- `app/Providers/Crm/CrmServiceProvider.php` — binds CrmDealServiceInterface → CrmDealService

Filament Resources (`app/Filament/Crm/Resources/`):
- `CrmContactResource.php` + 3 pages — group: Contacts, icon: heroicon-o-user; table: full name, email, company_name, status badge, phone; form 2-col; canAccess: crm.contacts
- `CrmCompanyResource.php` + 3 pages — group: Contacts, icon: heroicon-o-building-office; table: name, domain, industry, size, phone; canAccess: crm.contacts
- `DealStageResource.php` + 3 pages — group: Sales, icon: heroicon-o-funnel; table: name, order, probability%, is_won/is_lost icons; defaultSort sort_order; canAccess: crm.pipeline
- `CrmDealResource.php` + 3 pages — group: Sales, icon: heroicon-o-currency-dollar; table: title, contact, stage, value (money), expected_close_date, status badge, owner; Actions: EditAction + mark_won (visible when open) + mark_lost (form with lost_reason input); canAccess: crm.pipeline
- `CrmActivityResource.php` + 3 pages — group: Sales, icon: heroicon-o-clock; type badge colors; mutateFormDataBeforeCreate sets user_id=auth()->id(); canAccess: crm.pipeline
- `CrmTicketResource.php` + 3 pages — group: Support, icon: heroicon-o-ticket; status + priority badges; Action::make('resolve') visible when status≠resolved, sets resolved_at=now(); canAccess: crm.tickets
- `CrmQuoteResource.php` + 3 pages — group: Sales, icon: heroicon-o-document-text; auto-generate number (Q-XXXXXX) in mutateFormDataBeforeCreate; canAccess: crm.quotes

Dashboard & Widget:
- `app/Filament/Crm/Pages/Dashboard.php` — extends BaseDashboard, 3 columns
- `app/Filament/Crm/Widgets/CrmOverviewWidget.php` — StatsOverviewWidget: Open Deals count, Total Pipeline Value (sum of open deal values, formatted), Open Tickets count

**Decisions made:**
- pivot table `crm_contact_company` uses composite PK [crm_contact_id, crm_company_id] following ADR decision-2026-05-10-pivot-composite-pk
- `CrmQuoteItem` has no `company_id` column (child of crm_quotes which carries company_id) — consistent with crm_ticket_comments pattern
- `CrmActivity.user_id` set automatically in `mutateFormDataBeforeCreate` (CreateCrmActivity page) — not shown in form; same pattern used in HR leave requests
- Quote number auto-generated in `CreateCrmQuote::mutateFormDataBeforeCreate` using `Q-` prefix + 6-char uniqid suffix; proper sequential numbering deferred to a service layer in a future session
- `CrmPanelProvider` not registered in `bootstrap/providers.php` — left for wiring agent (same pattern as HrPanelProvider)

**Problems encountered:**
- None — all patterns were cleanly derived from existing HR and Projects domain implementations

---

## Gaps Discovered

None discovered in this session. The following are known future gaps for this module:

- `crm_tickets` spec mentions a ticket number (`TKT-2026-00001`) but the migration has no `number` column; the builder spec was adapted to omit it since sequential numbering was deprioritised to a later session
- `crm_activities` spec has `created_by` as the user FK name; implementation uses `user_id` (consistent with HR patterns) — minor spec drift, no functional impact

---

## Left Brain Files Updated

- `left-brain/domains/05_crm/contact-company-management.md` — status: in-progress, right_brain_log linked (updated before build)
- `left-brain/domains/05_crm/sales-pipeline.md` — status: in-progress, right_brain_log linked (updated before build)
- `left-brain/domains/05_crm/customer-support-helpdesk.md` — status: in-progress, right_brain_log linked (updated before build)
- `left-brain/domains/05_crm/quotes-proposals.md` — status: in-progress, right_brain_log linked (updated before build)
