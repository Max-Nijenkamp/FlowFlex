---
tags: [brain, bugs, registry]
last_updated: 2026-05-07
---

# Bug Registry

Every bug found and fixed. Organised by domain. Use this to spot patterns when new bugs appear.

---

## Phase 1 — Foundation

| Bug | Root Cause | Fix |
|---|---|---|
| `Spatie\Activitylog\Traits\LogsActivity` wrong namespace (43 models) | v5 moved namespace | Changed to `Models\Concerns\LogsActivity` |
| `Spatie\Activitylog\LogOptions` wrong namespace (43 models) | v5 moved namespace | Changed to `Support\LogOptions` |
| `Tenant` missing `getFilamentName()` | Method existed but `HasName` interface not implemented; Filament checks `instanceof HasName` | Added `implements HasName` to Tenant |
| `Filament\Tables\Actions\Action` class not found | Class doesn't exist in Filament 5 | Changed to `Filament\Actions\Action` in LeaveRequestResource, ManageApiKeys, ManageTeam |
| `Filament\Tables\Actions\EditAction` class not found | Same | Changed to `Filament\Actions\EditAction` in ManageTeam |
| `<x-filament-panels::form>` Blade component not found | Filament 3 API — doesn't exist in Filament 5 | Replaced with plain `<form>` + `<x-filament::actions>` in workspace settings views |
| Navigation group `Settings` + items both had icons | Filament 5 forbids icon on both group and items | Removed `$navigationIcon` from all 4 workspace settings pages |
| `ManageApiKeys` bypassed SoftDeletes with `whereNull('deleted_at')` | Manual null check instead of using SoftDeletes scope | Replaced with `->withoutTrashed()` |
| `AuthenticateApiKey` lazy-loaded company (N+1 per API request) | `$apiKey->company` accessed without eager-load | Added `->with('company')` to the ApiKey query |

---

## Phase 2 — HR & Projects

| Bug | Root Cause | Fix |
|---|---|---|
| `Employee::getFullNameAttribute()` double-space on null `middle_name` | String concatenation without filtering nulls | Rewrote: `collect([$first, $middle, $last])->filter()->implode(' ')` |
| `TextInput::uppercase()` method not found (PayrollEntityResource, PublicHolidayResource) | Method doesn't exist in Filament 5 | Replaced with `->dehydrateStateUsing(fn ($s) => $s ? strtoupper($s) : $s)` |
| `OnboardingTemplateResource` relation `templateTasks` mismatch | Relation named `tasks` on model but `templateTasks` in resource | Fixed to `tasks` |
| `LeaveRequest` missing `total_days` on create | Field not calculated in form lifecycle | Added `mutateFormDataBeforeCreate` to compute from date range |
| `TimeEntry` and `Timesheet` used wrong guard `auth()->id()` | Should use `auth('tenant')->id()` in tenant panel | Fixed guard throughout |
| `GeneratePayslipPdf` job bypassed global scope without explicit company_id | Missing company_id in firstOrCreate conditions | Added `withoutGlobalScopes()` + explicit `company_id` |
| `BelongsToCompany` read API company from wrong request bag | Used `auth()->user()` in API context where no auth exists | Fixed to `request()->attributes->get('api_company')` |
| `company_module` pivot ULID not auto-generated | Pivot had no model with `HasUlids` | Created `CompanyModule` pivot model with `HasUlids` |
| SQLite can't DROP PRIMARY KEY column in migration | SQLite limitation | Rewrote migration with driver-check + `DB::statement()` |
| `TaskLabelResource` checked `projects.tasks.*` instead of `projects.task-labels.*` | Copy-paste error from TaskResource | Fixed permission strings; added `projects.task-labels.*` to seeder |
| `Address` model missing `SoftDeletes` + `LogsActivity` | Overlooked — not in tenant scope | Added both traits + migration |
| `CompanyModule` pivot missing `SoftDeletes` + `LogsActivity` | Pivot overlooked | Added both traits + migration |
| `PayRunEmployee` logged `gross_pay`/`net_pay` to activity log | Used `logFillable()` | Restricted `logOnly` to non-sensitive fields |
| `DocumentShare.password_hash` not hashed | No cast defined | Added `hashed` cast, excluded from activity log |

---

## Phase 3 — Finance & CRM

| Bug | Root Cause | Fix |
|---|---|---|
| `ExpenseResource` tenant dropdown unscoped — cross-company leak | `Tenant::query()` with no scope + non-existent `name` column | Scoped to `company_id`; labels from `first_name + last_name` |
| `TicketResource` tenant dropdown same issue | Same | Same fix |
| `PayrollEntity.tax_reference` stored unencrypted | Tax ID is sensitive, no `encrypted` cast | Renamed to `tax_reference_encrypted` + `encrypted` cast + migration |
| `PayrollEntityResource` form bound to old field name | Form still used `tax_reference` after model rename | Updated form field and table column to `tax_reference_encrypted` |
| `Payslip.pdf_path` in `$fillable` | Would expose raw S3 path when PDF implemented | Removed from fillable; use `pdf_file_id` via FileStorageService |
| 22 events fired with no listeners (silently dropped) | Events created but not wired in EventServiceProvider | Created stub + real listeners for all 22; wired all |
| `TicketResource` N+1 on `assigned_to` column | Called `Tenant::find()` per row | Added `assignedTo()` BelongsTo on Ticket; eager-loaded |
| `Ticket` model missing `assignedTo()` relation | Not created during Phase 3 build | Added BelongsTo to Tenant via `assigned_to` FK |
| `Tenant` missing `getFullNameAttribute()` accessor | `fullName()` method existed but no Eloquent accessor | Added `getFullNameAttribute()` wrapping `fullName()` |
| All 12 Marketing models missing `LogsActivity` | Overlooked in initial build | Added trait + `getActivitylogOptions()` to all 12 |
| No factories for Finance/CRM models | Not created during Phase 3 build | Created 13 factories with states |
| `Company` + `Tenant` missing `HasFactory` | Factories couldn't be nested | Added `HasFactory` to both |
| Finance/CRM tables missing `[company_id, status]` indexes | Not added during Phase 3 migrations | Added migration with all compound + single-column indexes |
| 7 models had no policies (ExpenseReport, RecurringInvoice, InvoiceLine, InvoicePayment, InvoiceEmailEvent, ContractorPayment, Deduction) | Not created during Phase 3 build | Created all 7 policies + registered in AppServiceProvider |
| Future panel providers referenced non-existent directories | Empty directories not created | Created directories with `.gitkeep` |
| `/api/v1/health` no rate limiting | Missing throttle middleware | Added `throttle:60,1` |
| `CreditNoteResource` N+1 on `invoice.number` | No `getEloquentQuery()` override | Added with `->with(['invoice'])` |
| `InvoiceResource` no `getEloquentQuery()` at all | Not added during Phase 3 build | Added override |
| `DealResource` closure used `\Filament\Forms\Get` type hint | Filament 5 uses `\Filament\Schemas\Components\Utilities\Get` | Fixed type hint in `deal_stage_id` select options closure |
| `TicketPriority::Medium` case doesn't exist | Enum has `Low/Normal/High/Urgent` — no `Medium` | Use `TicketPriority::Normal` |

---

## Phase 3 Gap-Fill — 2026-05-07

| Bug | Root Cause | Fix |
|---|---|---|
| Brain incorrectly stated Shared Inbox models were wired in Phase 3 | Models did not exist in Phase 3 build | Built SharedInbox, InboxEmail, EmailReceivedInSharedInbox event in gap-fill session |
| RecurringInvoicePolicy used `finance.invoices.*` permissions | Copy-paste from InvoicePolicy | Updated to `finance.recurring-invoices.*` |
| ExpenseReportPolicy used `finance.expenses.*` permissions | Copy-paste from ExpensePolicy | Updated to `finance.expense-reports.*` |
| TicketSlaRuleResourceTest used `crm.sla-rules.*` permissions | Wrong name — resource uses `crm.ticket-sla-rules.*` | Updated test permission strings |
| Task model needed `parent_id` column + `parent()`/`children()` relationships | Spec required subtask support via `parent_id` FK | Added migration, fillable, and both relationships |
| `TaskPriority` enum has no `medium` backing value | Enum uses `p3_medium` | Fixed test to use correct enum value |
| `TicketSlaBreach::usingSoftDeletes()` call fails | `usingSoftDeletes()` is not an Eloquent method | Use `in_array(SoftDeletes::class, class_uses_recursive($model))` instead |
| `CsatSurvey` test missing required `token` column | `token` is NOT NULL in migration | Add `Str::uuid()->toString()` to test fixture |
| `InboxEmail` test missing required `message_id` column | `message_id` is NOT NULL in migration | Add `message_id` to test fixture |
| `CrmActivity` test used wrong column names | Model uses morph `subject_type`/`subject_id` + `description`, not `crm_contact_id`/`subject` | Fixed test to use correct column names |
| `SharedInbox` column is `email_address` not `email` | Model/migration uses `email_address` | Fixed all test fixtures |
| `CsatSurvey`/`CrmActivity` datetime cast test — `CarbonImmutable` vs `Carbon` | App uses `CarbonImmutable` via `Date::use()` | Assert `\DateTimeInterface::class` instead of `\Illuminate\Support\Carbon::class` |
| `ChatbotRule` form field `trigger_keywords` expects comma-string not array | Field uses `dehydrateStateUsing` to split CSV | Pass comma-separated string in Livewire test, not PHP array |

---

## Pattern: Most Common Bug Types

1. **Wrong Spatie namespace** — always `Models\Concerns\LogsActivity` and `Support\LogOptions`
2. **Unscoped tenant dropdowns** — `Tenant` has no global scope; always scope manually to `company_id`
3. **Missing `getEloquentQuery()` eager-load** — every resource with relation columns needs this
4. **Events without listeners** — every event must be in `EventServiceProvider::$listen`
5. **Sensitive fields not encrypted** — any field containing tax IDs, bank details, API keys → `encrypted` cast
6. **Missing traits on models** — all 4 traits required; easy to miss in rapid builds
7. **Filament 3 API usage** — `Filament\Tables\Actions\Action`, `Filament\Forms\Form`, blade components — all gone in Filament 5
