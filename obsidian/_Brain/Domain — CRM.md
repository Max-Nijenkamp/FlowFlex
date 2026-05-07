---
tags: [brain, domain, crm]
last_updated: 2026-05-07
---

# Domain — CRM

**Spec:** `05 - CRM & Sales/` — Contact & Company Management, Sales Pipeline, Customer Support & Helpdesk, Shared Inbox & Email  
**All models:** `app/Models/Crm/`  
**All resources:** `app/Filament/Crm/Resources/`  
**Panel:** `/crm` — guard `tenant`, color blue `#2563EB`  
**Module key:** `crm`

All models (except where noted) carry: `BelongsToCompany`, `HasUlids`, `SoftDeletes`, `LogsActivity`.

---

## Models

### CrmContact
**Spec:** `05 - CRM & Sales/Contact & Company Management.md`  
**Table:** `crm_contacts`  
**Purpose:** Central person record in the CRM. Can be a lead, prospect, customer, partner, or other.

**Fillable fields:**
- `company_id` — workspace company (BelongsToCompany scope)
- `crm_company_id` — optional link to a CrmCompany record
- `first_name`, `last_name`, `email`, `phone`
- `type` → `ContactType` enum (Lead/Prospect/Customer/Partner/Other)
- `job_title`, `linkedin_url`, `notes`
- `owner_tenant_id` — which team member owns this contact

**Relations:**
- `crmCompany()` → BelongsTo `CrmCompany` (via `crm_company_id`)
- `deals()` → HasMany `Deal`
- `tickets()` → HasMany `Ticket`
- `customFieldValues()` → HasMany `CrmContactCustomFieldValue`
- `activities()` → MorphMany `CrmActivity` (as `subject`)
- `csatSurveys()` → HasMany `CsatSurvey` (via `crm_contact_id`)
- `inboxEmails()` → HasMany `InboxEmail` (via `crm_contact_id`)
- `owner()` → BelongsTo `Tenant` (via `owner_tenant_id`)

---

### CrmCompany
**Table:** `crm_companies`  
**Purpose:** External company record (customer/prospect org). Distinct from `App\Models\Company` (FlowFlex workspace). Named `CrmCompany` to avoid namespace collision.

**Fillable fields:**
- `company_id` — workspace company
- `name`, `domain`, `industry`
- `employee_count` (int), `website`, `phone`, `email`
- `owner_tenant_id`

**Relations:**
- `contacts()` → HasMany `CrmContact`
- `deals()` → HasMany `Deal`
- `tickets()` → HasMany `Ticket`
- `activities()` → MorphMany `CrmActivity` (as `subject`)
- `owner()` → BelongsTo `Tenant` (via `owner_tenant_id`)

---

### Pipeline
**Spec:** `05 - CRM & Sales/Sales Pipeline.md`  
**Table:** `pipelines`  
**Purpose:** Container for deal stages. A company can have multiple pipelines (e.g. "New Business" vs "Upsell"). One pipeline is the default.

**Fillable fields:**
- `company_id`, `name`, `is_default` (bool)

**Relations:**
- `stages()` → HasMany `DealStage` (ordered by `sort_order`)
- `deals()` → HasMany `Deal`

---

### DealStage
**Table:** `deal_stages`  
**Purpose:** One step in a pipeline (e.g. "Discovery", "Proposal", "Negotiation", "Closed Won"). Each stage has a win probability.

**Fillable fields:**
- `pipeline_id`, `name`, `sort_order` (int), `probability_percent` (int 0-100)

**Relations:**
- `pipeline()` → BelongsTo `Pipeline`
- `deals()` → HasMany `Deal`

---

### Deal
**Spec:** `05 - CRM & Sales/Sales Pipeline.md`  
**Table:** `deals`  
**Purpose:** Tracks a sales opportunity from discovery to close. Linked to a contact, a company, and a pipeline stage.

**Fillable fields:**
- `company_id`, `crm_contact_id`, `crm_company_id`
- `pipeline_id`, `deal_stage_id`
- `title`, `value` (decimal:2), `currency`
- `status` → `DealStatus` enum (Open/Won/Lost)
- `close_probability` (int 0-100)
- `expected_close_date` (date), `closed_at` (datetime)
- `lost_reason`, `owner_tenant_id`

**Casts:** `status` → `DealStatus`, `expected_close_date` → date, `closed_at` → datetime

**Relations:**
- `pipeline()` → BelongsTo `Pipeline`
- `stage()` → BelongsTo `DealStage` (via `deal_stage_id`)
- `contact()` → BelongsTo `CrmContact`
- `crmCompany()` → BelongsTo `CrmCompany`
- `owner()` → BelongsTo `Tenant` (via `owner_tenant_id`)
- `notes()` → HasMany `DealNote`
- `activities()` → MorphMany `CrmActivity` (as `subject`)

**Events fired:**
- `DealWon` — triggered by "Mark Won" action in resource
- `DealLost` — triggered by "Mark Lost" action in resource

**Resource note:** Stage dropdown uses `->options(fn(Get $get) => DealStage::where('pipeline_id', $get('pipeline_id'))->pluck('name', 'id'))` with `Filament\Schemas\Components\Utilities\Get` (NOT `Filament\Forms\Get` — Filament 5 breaking change).

---

### DealNote
**Table:** `deal_notes`  
**Purpose:** Free-text notes attached to a deal by a team member. Append-only log of sales activity and context.

**Fillable fields:**
- `company_id`, `deal_id`, `tenant_id`, `body`

**Relations:**
- `deal()` → BelongsTo `Deal`
- `tenant()` → BelongsTo `Tenant` (author)

---

### Ticket
**Spec:** `05 - CRM & Sales/Customer Support & Helpdesk.md`  
**Table:** `tickets`  
**Purpose:** Support request from a customer. Has a message thread, SLA tracking, and CSAT survey.

**Fillable fields:**
- `company_id`, `crm_contact_id`, `crm_company_id`
- `subject`, `status` → `TicketStatus` enum, `priority` → `TicketPriority` enum
- `assigned_to` → Tenant FK (support agent)
- `resolved_at` (datetime), `sla_breach_at` (datetime)

**Casts:** `status` → `TicketStatus`, `priority` → `TicketPriority`, `resolved_at`/`sla_breach_at` → datetime

**Relations:**
- `contact()` → BelongsTo `CrmContact` (via `crm_contact_id`)
- `crmCompany()` → BelongsTo `CrmCompany` (via `crm_company_id`)
- `assignedTo()` → BelongsTo `Tenant` (via `assigned_to` column — non-standard FK name)
- `messages()` → HasMany `TicketMessage`
- `csatSurveys()` → HasMany `CsatSurvey`
- `slaBreaches()` → HasMany `TicketSlaBreach`

**N+1 prevention:** `TicketResource::getEloquentQuery()` eager-loads `contact` and `assignedTo`.  
**Dropdown scoping:** `assigned_to` dropdown must scope to `company_id` — Tenant has no global scope.

---

### TicketMessage
**Table:** `ticket_messages`  
**Purpose:** A single message in a ticket thread. Can be internal (team-only) or external (visible to customer).

**Fillable fields:**
- `ticket_id`, `sender_tenant_id`, `body` (text), `is_internal` (bool)

**Relations:**
- `ticket()` → BelongsTo `Ticket`
- `sender()` → BelongsTo `Tenant` (via `sender_tenant_id`)

---

### TicketSlaRule
**Table:** `ticket_sla_rules`  
**Purpose:** Defines SLA targets per priority level. If a ticket breaches first-response or resolution time, a `TicketSlaBreach` is recorded.

**Fillable fields:**
- `company_id`, `name`
- `priority` → `TicketPriority` enum (which priority this rule applies to)
- `first_response_hours` (int), `resolution_hours` (int)
- `is_active` (bool)

**Casts:** `priority` → `TicketPriority`, `is_active` → boolean

**Permissions:** `crm.sla-rules.view/create/edit/delete`  
**Resource:** `TicketSlaRuleResource` — nav group: Support

---

### TicketSlaBreach
**Table:** `ticket_sla_breaches`  
**Purpose:** Immutable audit record. Created when a ticket exceeds the SLA threshold. Never soft-deleted.  
**Note:** Does NOT use `SoftDeletes` — breaches are permanent audit records.

**Fillable fields:**
- `company_id`, `ticket_id`, `ticket_sla_rule_id`
- `type` (string: `first_response` or `resolution`)
- `breached_at` (datetime)

**Casts:** `breached_at` → datetime

**Relations:**
- `ticket()` → BelongsTo `Ticket`
- `ticketSlaRule()` → BelongsTo `TicketSlaRule`

**Test pattern for SoftDeletes check:**
```php
// CORRECT — usingSoftDeletes() is not a real method
in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(TicketSlaBreach::class))
// returns false — TicketSlaBreach intentionally has no SoftDeletes
```

---

### CsatSurvey
**Table:** `csat_surveys`  
**Purpose:** Satisfaction survey sent to a customer after ticket resolution. Each survey has a unique URL token.

**Fillable fields:**
- `company_id`, `ticket_id`, `crm_contact_id`
- `token` (string, UNIQUE — 64 char random, NOT NULL)
- `sent_at` (datetime), `expires_at` (datetime)

**Casts:** `sent_at`, `expires_at` → datetime (returns `CarbonImmutable` — assert `\DateTimeInterface` in tests)

**Relations:**
- `ticket()` → BelongsTo `Ticket`
- `contact()` → BelongsTo `CrmContact` (via `crm_contact_id`)
- `response()` → HasOne `CsatResponse`

---

### CsatResponse
**Table:** `csat_responses`  
**Purpose:** Customer's response to a CSAT survey. Contains numeric rating and optional free-text comment.

**Fillable fields:**
- `company_id`, `csat_survey_id`, `rating` (int 1-5), `comment` (text, nullable), `responded_at` (datetime)

**Casts:** `responded_at` → datetime

**Relations:**
- `survey()` → BelongsTo `CsatSurvey` (via `csat_survey_id`)

---

### CannedResponse
**Table:** `canned_responses`  
**Purpose:** Pre-written replies for common support queries. Support agents insert these into ticket messages to save time.

**Fillable fields:**
- `company_id`, `title`, `body`, `category`

---

### CrmContactCustomField
**Table:** `crm_contact_custom_fields`  
**Purpose:** Workspace-defined custom field definitions. Each company can define fields that appear on every contact record (e.g., "Account Manager", "Source", "NPS Score").

**Fillable fields:**
- `company_id`, `name`
- `type` (string: text/number/date/boolean/dropdown)
- `options` (array — dropdown options; JSON cast)
- `is_required` (bool), `sort_order` (int)

**Casts:** `options` → array, `is_required` → boolean

**Relations:**
- `values()` → HasMany `CrmContactCustomFieldValue`

---

### CrmContactCustomFieldValue
**Table:** `crm_contact_custom_field_values`  
**Purpose:** Per-contact value for each custom field definition. One row per contact per custom field.

**Fillable fields:**
- `company_id`, `crm_contact_id`, `crm_contact_custom_field_id`, `value` (string)

**Relations:**
- `contact()` → BelongsTo `CrmContact`
- `customField()` → BelongsTo `CrmContactCustomField`

---

### CrmActivity
**Table:** `crm_activities`  
**Purpose:** Activity log for CRM entities (calls, emails, meetings, notes). Polymorphic — can be attached to a contact, company, or deal.

**Fillable fields:**
- `company_id`, `tenant_id` (who performed the activity)
- `subject_type`, `subject_id` — polymorphic morph (can point to CrmContact, CrmCompany, or Deal)
- `type` (string: call/email/meeting/note/task)
- `description` (text)
- `metadata` (JSON array — extra data like call duration, attendees)
- `occurred_at` (datetime)

**Casts:** `metadata` → array, `occurred_at` → datetime

**Relations:**
- `subject()` → MorphTo (resolves to CrmContact, CrmCompany, or Deal)
- `tenant()` → BelongsTo `Tenant` (who performed it)

**Test pattern:** Use `subject_type` + `subject_id` + `description`, NOT `crm_contact_id`/`subject` (those columns don't exist).

---

### ChatbotRule
**Table:** `chatbot_rules`  
**Purpose:** Keyword-triggered auto-response rules for incoming support tickets. When a ticket subject/body contains a trigger keyword, the canned response body is auto-sent.

**Fillable fields:**
- `company_id`, `name`
- `trigger_keywords` (array cast — stored as JSON, but form uses CSV string)
- `response_body` (text)
- `is_active` (bool), `sort_order` (int)

**Casts:** `trigger_keywords` → array, `is_active` → boolean

**Form field pattern:** The Filament form field uses `->dehydrateStateUsing(fn ($s) => $s ? explode(',', $s) : [])` so it accepts a comma-separated string. Tests must pass `'trigger_keywords' => 'word1, word2'` (string), NOT `['word1', 'word2']` (array).

**Permissions:** `crm.chatbot-rules.view/create/edit/delete`

---

### SharedInbox
**Table:** `shared_inboxes`  
**Purpose:** An email inbox shared across the support team. Emails received here become `InboxEmail` records.  
**Status:** Models and events wired. Full UI deferred to Phase 8 (Communications extension).

**Fillable fields:**
- `company_id`, `name`, `email_address` (NOT `email` — column is `email_address`), `is_active` (bool)

**Casts:** `is_active` → boolean

**Relations:**
- `emails()` → HasMany `InboxEmail`

---

### InboxEmail
**Table:** `inbox_emails`  
**Purpose:** A single email received in a SharedInbox. Can be linked to a CRM contact and assigned to a team member.  
**Status:** Model wired. Full inbox UI deferred to Phase 8.

**Fillable fields:**
- `company_id`, `shared_inbox_id`, `crm_contact_id` (nullable), `assigned_tenant_id` (nullable)
- `message_id` (string, UNIQUE, NOT NULL — the SMTP/IMAP message ID, e.g. `<msg-123@example.com>`)
- `from_email`, `from_name` (nullable)
- `subject`, `body_html` (text), `body_text` (text)
- `status` (string: unread/read/archived/replied)
- `received_at` (datetime)

**Casts:** `received_at` → datetime

**Relations:**
- `sharedInbox()` → BelongsTo `SharedInbox`
- `contact()` → BelongsTo `CrmContact` (via `crm_contact_id`)
- `assignedTenant()` → BelongsTo `Tenant` (via `assigned_tenant_id`)

**Test pattern:** `message_id` is NOT NULL — always include in test fixtures: `'message_id' => '<msg-' . uniqid() . '@example.com>'`.

---

## Resources (CRM Panel)

| Resource | Model | Nav Group | Permissions | Key Features |
|---|---|---|---|---|
| `CrmContactResource` | `CrmContact` | Contacts | `crm.contacts.*` | Full CRUD, CrmActivitiesRelationManager, CrmContactCustomFieldsRelationManager |
| `CrmCompanyResource` | `CrmCompany` | Contacts | `crm.companies.*` | Full CRUD, CrmActivitiesRelationManager |
| `PipelineResource` | `Pipeline` | Sales | `crm.pipelines.*` | CRUD |
| `DealStageResource` | `DealStage` | Sales | `crm.deal-stages.*` | CRUD |
| `DealResource` | `Deal` | Sales | `crm.deals.*` | CRUD, DealNotesRelationManager, won/lost actions, stage dropdown dependent on pipeline |
| `TicketResource` | `Ticket` | Support | `crm.tickets.*` | CRUD, assign action, scoped tenant dropdown, N+1 eager-load |
| `CannedResponseResource` | `CannedResponse` | Support | `crm.canned-responses.*` | CRUD |
| `TicketSlaRuleResource` | `TicketSlaRule` | Support | `crm.sla-rules.*` | CRUD |
| `ChatbotRuleResource` | `ChatbotRule` | Support | `crm.chatbot-rules.*` | CRUD, trigger_keywords as CSV string in form |
| `CsatSurveyResource` | `CsatSurvey` | Support | `crm.csat.*` | view/create/delete only |

---

## Enums

### ContactType
`App\Enums\Crm\ContactType`  
`Lead`, `Prospect`, `Customer`, `Partner`, `Other`

### DealStatus
`App\Enums\Crm\DealStatus`  
`Open`, `Won`, `Lost`

### TicketStatus
`App\Enums\Crm\TicketStatus`  
`Open`, `InProgress`, `PendingCustomer`, `Resolved`, `Closed`  
Colors: Open=danger, InProgress=warning, PendingCustomer=info, Resolved=success, Closed=gray

### TicketPriority
`App\Enums\Crm\TicketPriority`  
`Low`, `Normal`, `High`, `Urgent` — **no `Medium` case**  
Colors: Low=gray, Normal=info, High=warning, Urgent=danger

---

## Events (Phase 3)

All wired in `EventServiceProvider`. All listeners implement `ShouldQueue`.

| Event | Listener | Type |
|---|---|---|
| `TicketCreated` | `NotifyAssignedAgent` | stub |
| `TicketResolved` | `SendCsatSurvey` | real |
| `DealWon` | `LogDealWon` | stub |
| `DealLost` | `LogDealLost` | stub |
| `EmailReceivedInSharedInbox` | (stub) | stub — Phase 8 |

---

## Cross-Domain Notes

- `Invoice.contact_id` → `CrmContact.id` — Finance → CRM (invoice linked to a CRM contact)
- `Ticket.assigned_to` → `Tenant.id` — support agent is a workspace team member
- `Deal.owner_tenant_id` → `Tenant.id` — sales rep is a workspace team member
- `CrmCompany` ≠ `Company` — do not confuse. `Company` = FlowFlex workspace, `CrmCompany` = external customer
- All tenant dropdowns in CRM must scope to `company_id` — `Tenant` has no `BelongsToCompany` global scope
