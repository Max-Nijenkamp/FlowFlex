---
type: builder-log
module: crm-phase8
domain: CRM & Sales
panel: crm
phase: 8
started: 2026-05-12
status: complete
color: "#F97316"
left_brain_source: "[[MOC_CRM]]"
last_updated: 2026-05-12
---

# Builder Log — CRM & Sales Phase 8 Extensions

## Summary

Phase 8 CRM Extensions. Seven new modules extending the existing CRM panel: Customer Data Platform, Client Portal, Loyalty Programme, Deal Room, Sales Sequences & Cadences, Revenue Intelligence, and AI Sales Coach. Delivered as a self-contained extension layer with a separate `CrmExtensionsServiceProvider` — no modifications to existing Phase 3 files.

---

## Sessions

### 2026-05-12 — Phase 8 CRM Extensions Full Build

**What was built:**

Migrations (range 830001–830010, all in `database/migrations/`):
- `2026_05_12_830001_create_customer_data_profiles_table.php` — unified CDP profiles (company_id FK, contact_id nullable FK crm_contacts, unified_id unique nullable, sources json, email, phone, lifetime_value decimal 15,2, first/last_seen_at date, attributes json)
- `2026_05_12_830002_create_client_portal_configs_table.php` — portal config per company (company_id unique FK, title, subdomain nullable, is_active bool, allowed_features json, logo_path, primary_color)
- `2026_05_12_830003_create_loyalty_programs_table.php` — loyalty programme (company_id, name, points_per_currency_unit decimal 10,4, tiers json, is_active bool)
- `2026_05_12_830004_create_loyalty_transactions_table.php` — points ledger (company_id, contact_id FK, program_id FK, points int, transaction_type enum earn/redeem/expire/adjust, reference_type, reference_id ulid nullable, notes) — NO softDeletes (immutable ledger)
- `2026_05_12_830005_create_deal_rooms_table.php` — deal room (company_id, deal_id nullable FK crm_deals, title, description text, access_token unique, stakeholders json, files json, status enum active/closed, expires_at, view_count int, created_by ulid)
- `2026_05_12_830006_create_sales_sequences_table.php` — sequence header (company_id, name, description, status enum active/inactive, step_count int, created_by)
- `2026_05_12_830007_create_sales_sequence_steps_table.php` — sequence steps (company_id, sequence_id FK, step_number, action_type enum email/call/task/wait, delay_days, template text, subject)
- `2026_05_12_830008_create_sales_sequence_enrollments_table.php` — contact enrollment (company_id, sequence_id FK, contact_id FK crm_contacts, status enum active/paused/completed/unsubscribed, current_step int, enrolled_at, completed_at)
- `2026_05_12_830009_create_revenue_intelligence_table.php` — period snapshots (company_id, period string e.g. '2026-05', pipeline_value, weighted_pipeline, avg_deal_size, win_rate_pct, avg_sales_cycle_days int, forecast_accuracy_pct, calculated_at; unique [company_id, period])
- `2026_05_12_830010_create_sales_coaching_insights_table.php` — AI insights (company_id, user_id FK users, insight_type enum deal_risk/coaching_tip/win_probability/objection_pattern, content text, related_deal_id ulid nullable, confidence_score decimal 5,4, is_actioned bool) — NO softDeletes

Models (`app/Models/Crm/` — new only):
- `CustomerDataProfile.php` — BelongsToCompany, HasUlids, SoftDeletes; casts: sources/attributes array, lifetime_value decimal, dates; contact() BelongsTo
- `ClientPortalConfig.php` — BelongsToCompany, HasUlids, SoftDeletes; casts: is_active bool, allowed_features array
- `LoyaltyProgram.php` — BelongsToCompany, HasUlids, SoftDeletes; casts: tiers array, is_active bool; transactions() HasMany
- `LoyaltyTransaction.php` — BelongsToCompany, HasUlids (NO SoftDeletes); casts: points int; program()/contact() BelongsTo
- `DealRoom.php` — BelongsToCompany, HasUlids, SoftDeletes; casts: stakeholders/files array, expires_at datetime, view_count int; deal() BelongsTo
- `SalesSequence.php` — BelongsToCompany, HasUlids, SoftDeletes; casts: step_count int; steps() HasMany ordered, enrollments() HasMany
- `SalesSequenceStep.php` — BelongsToCompany, HasUlids, SoftDeletes; casts: step_number/delay_days int; sequence() BelongsTo
- `SalesSequenceEnrollment.php` — BelongsToCompany, HasUlids, SoftDeletes; casts: current_step int, enrolled_at/completed_at datetime; sequence()/contact() BelongsTo
- `RevenueIntelligence.php` — BelongsToCompany, HasUlids, SoftDeletes; all metric fields cast decimal/int/datetime
- `SalesCoachingInsight.php` — BelongsToCompany, HasUlids (NO SoftDeletes); casts: confidence_score decimal, is_actioned bool; user()/relatedDeal() BelongsTo

Service Contracts (`app/Contracts/Crm/` — new only):
- `CustomerDataServiceInterface.php` — upsertProfile, mergeProfiles, updateLifetimeValue, getProfile, getCompanyProfiles
- `ClientPortalServiceInterface.php` — configure, activate, deactivate, getConfig, getPortalUrl
- `LoyaltyServiceInterface.php` — createProgram, earnPoints, redeemPoints, getBalance, getHistory
- `DealRoomServiceInterface.php` — create, addFile, close, recordView, getCompanyRooms
- `SalesSequenceServiceInterface.php` — createSequence, addStep, enroll, advance, unenroll
- `RevenueIntelligenceServiceInterface.php` — calculate, getLatest, getHistory, getForecast
- `SalesCoachServiceInterface.php` — recordInsight, markActioned, getActiveInsights, getDealInsights

Service Implementations (`app/Services/Crm/` — new only):
- `CustomerDataService.php` — upsert by email/contact_id/unified_id; merge deduplicates sources array, merges attributes (primary wins), sums LTV, picks earliest first_seen/latest last_seen; secondary soft-deleted
- `ClientPortalService.php` — firstOrNew by company_id (idempotent); portal URL uses subdomain if present, else `/portal/{company_id}`
- `LoyaltyService.php` — earnPoints/redeemPoints write to ledger; redeemPoints throws InvalidArgumentException if balance insufficient; getBalance uses SUM(points)
- `DealRoomService.php` — create auto-generates access_token via Str::random(40); addFile appends to json array with added_at timestamp; recordView uses increment()
- `SalesSequenceService.php` — addStep increments sequence step_count; advance marks completed with completed_at when current_step >= step_count
- `RevenueIntelligenceService.php` — calculates from live CrmDeal data; updateOrCreate by [company_id, period]; getForecast returns weighted_pipeline as next period estimate
- `SalesCoachService.php` — thin CRUD wrapper; getActiveInsights filters is_actioned=false

Service Provider (`app/Providers/Crm/`):
- `CrmExtensionsServiceProvider.php` — separate from CrmServiceProvider; binds all 7 new interface→implementation pairs; must be registered in bootstrap/providers.php

Filament Resources (`app/Filament/Crm/Resources/` — new only, each with List/Create/Edit pages):
- `CustomerDataProfileResource.php` — nav group "Contacts", icon heroicon-o-user-circle, canAccess: crm.contacts; table: email, unified_id, LTV money, last/first_seen dates
- `ClientPortalConfigResource.php` — nav group "Settings", icon heroicon-o-globe-alt, canAccess: crm.pipeline; form: title, subdomain, toggle is_active, ColorPicker, CheckboxList features
- `LoyaltyProgramResource.php` — nav group "CRM", icon heroicon-o-star, canAccess: crm.pipeline; table: name, points/€1, active icon
- `DealRoomResource.php` — nav group "Pipeline", icon heroicon-o-briefcase, canAccess: crm.pipeline; table: title, linked deal, status badge, view_count, expires_at
- `SalesSequenceResource.php` — nav group "Pipeline", icon heroicon-o-list-bullet, canAccess: crm.pipeline; table: name, status badge, step_count
- `RevenueIntelligenceResource.php` — nav group "Reports", icon heroicon-o-chart-bar, canAccess: crm.pipeline; READ-ONLY (canCreate() = false, no Create/Edit pages); table: period, pipeline_value, weighted_pipeline, avg_deal_size, win_rate_pct, avg_sales_cycle_days, calculated_at
- `SalesCoachInsightResource.php` — nav group "Pipeline", icon heroicon-o-light-bulb, canAccess: crm.pipeline; table: insight_type badge (danger/info/success/warning), rep email, content limit 80, confidence, is_actioned icon

Factories (`database/factories/Crm/` — all new):
- `CustomerDataProfileFactory.php`, `ClientPortalConfigFactory.php`, `LoyaltyProgramFactory.php`, `LoyaltyTransactionFactory.php`, `DealRoomFactory.php`, `SalesSequenceFactory.php`, `SalesSequenceStepFactory.php`, `SalesSequenceEnrollmentFactory.php`, `RevenueIntelligenceFactory.php`, `SalesCoachingInsightFactory.php`

Tests (`tests/Feature/Crm/` — new files only):
- `CustomerDataServiceTest.php` — upsert by email, update existing on re-upsert, update LTV, merge profiles (sums LTV + deduplicates sources + deletes secondary), company-scoped (5 tests)
- `ClientPortalServiceTest.php` — configure, activate, deactivate, getPortalUrl with subdomain, idempotent configure per company (5 tests)
- `LoyaltyServiceTest.php` — createProgram, earnPoints, redeemPoints, throw on insufficient balance, correct balance, company-scoped (6 tests)
- `DealRoomServiceTest.php` — create with auto-token, addFile, close, recordView increments counter, company-scoped (5 tests)
- `SalesSequenceServiceTest.php` — createSequence, addStep increments count, enroll, advance, advance to completed, unenroll, company-scoped (7 tests)
- `RevenueIntelligenceServiceTest.php` — calculate, idempotent recalculate, getLatest returns most recent period, company-scoped (4 tests)
- `SalesCoachServiceTest.php` — recordInsight, markActioned, getActiveInsights filters unactioned only, company-scoped (4 tests)

**Decisions made:**
- `CrmExtensionsServiceProvider` is a separate provider from `CrmServiceProvider` — extensions do not touch existing Phase 3 bindings; must be registered independently in `bootstrap/providers.php`
- `LoyaltyTransaction` has NO SoftDeletes — points ledger is immutable; deleted records would corrupt balance calculations
- `SalesCoachingInsight` has NO SoftDeletes — AI insights are ephemeral; hard-delete is acceptable
- `RevenueIntelligenceResource` is read-only (`canCreate() = false`) — data must be generated via `RevenueIntelligenceService::calculate()`, not manually entered
- `DealRoomService::create()` auto-generates `access_token` via `Str::random(40)` if not provided — consistent with URL-safe token pattern
- `ClientPortalConfig` uses `company_id UNIQUE` — one portal config per company; `configure()` is idempotent via `firstOrNew`
- `revenue_intelligence` table uses `UNIQUE [company_id, period]` constraint — `calculate()` uses `updateOrCreate` to allow recalculation without duplication

**Problems encountered:**
- None — all patterns cleanly derived from Phase 3 and Phase 6 (AI) domain implementations

---

## Gaps Discovered

None discovered in this session.

**Known future gaps for Phase 8 modules:**
- `SalesSequenceService::advance()` does not automatically dispatch the next step action (email/call/task) — enrollment advances the step counter only; actual automation requires a scheduled job + action dispatcher (Phase 8 follow-up)
- `RevenueIntelligenceService::calculate()` uses a fixed `avg_sales_cycle_days = 30` placeholder — real calculation requires historical closed deal timestamps; deferred to Phase 8 data pipeline session
- `ClientPortalConfig` has `logo_path` but no file upload wiring in the Filament form — Spatie Media Library integration deferred

---

## Left Brain Files Updated

- `left-brain/domains/05_crm/MOC_CRM.md` — Phase 8 module statuses tracked via this log
