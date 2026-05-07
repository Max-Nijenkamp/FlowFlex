---
tags: [brain, state, live]
last_updated: 2026-05-07
---

# Current State

The living snapshot of where FlowFlex is right now. Update this after every phase or major change.

---

## Build Status

| Phase | Status | Tests |
|---|---|---|
| Phase 1 — Foundation | ✅ Complete | Covered |
| Phase 2 — HR & Projects | ✅ Complete | Covered |
| Phase 3 — Finance & CRM | ✅ Complete | Covered |
| Phase 4 — Operations & Marketing | ⏳ Not started | — |
| Phase 5 — Extended Modules | ⏳ Not started | — |
| Phase 6 — Enterprise & Scale | ⏳ Not started | — |

**Test suite:** 580 passing · 0 skipped · 0 failing  
**Last run:** 2026-05-07 (Full i18n — all Filament panels translated to EN/NL/DE; marketing nav language toggle removed)  
**Run command:** `XDEBUG_MODE=off php -d memory_limit=768M vendor/bin/pest --no-coverage`

---

## Active Filament Panels

| Panel ID | Path | Guard | Colour | Status |
|---|---|---|---|---|
| `admin` | `/admin` | `web` | `#2199C8` | ✅ Live |
| `workspace` | `/workspace` | `tenant` | — | ✅ Live |
| `hr` | `/hr` | `tenant` | `#7C3AED` | ✅ Live |
| `projects` | `/projects` | `tenant` | — | ✅ Live |
| `finance` | `/finance` | `tenant` | `#059669` | ✅ Live |
| `crm` | `/crm` | `tenant` | `#2563EB` | ✅ Live |

Future panels (providers exist, no resources yet): `operations`, `communications`, `marketing`, `legal`, `it`, `ecommerce`, `analytics`, `learning`

---

## Models by Domain

| Domain | Models | Panel |
|---|---|---|
| Core | Company, Tenant, User, ApiKey, Module, SubModule, Role, Permission, File, NotificationPreference, Address | admin / workspace |
| Marketing (CMS) | BlogPost, BlogCategory, DemoRequest, ContactSubmission, NewsletterSubscriber, FaqEntry, HelpArticle, HelpCategory, TeamMember, Testimonial, OpenRole, ChangelogEntry | admin |
| HR | Department, Employee, EmployeeDocument, EmployeeCustomField, EmployeeCustomFieldValue, LeaveType, LeavePolicy, LeaveRequest, LeaveBalance, OnboardingTemplate, OnboardingTemplateTask, OnboardingFlow, OnboardingTask, OnboardingCheckin, OnboardingCheckinResponse, PayrollEntity, PayElement, PayRun, PayRunEmployee, PayRunLine, Payslip, SalaryRecord, ContractorPayment, Deduction, TaxConfiguration, PublicHoliday | hr |
| Projects | Task, TaskLabel, TaskDependency, TaskAutomation, TaskAutomationLog, TimeEntry, Timesheet, TimesheetApproval, Document, DocumentFolder, DocumentVersion, DocumentShare | projects |
| Finance | Invoice, InvoiceLine, InvoicePayment, CreditNote, InvoiceEmailEvent, RecurringInvoice, Expense, ExpenseReport, ExpenseCategory, MileageRate | finance |
| CRM | CrmContact, CrmCompany, Deal, Pipeline, DealStage, Ticket, TicketMessage, CannedResponse, TicketSlaRule, TicketSlaBreach, CsatSurvey, CsatResponse, ChatbotRule, CrmContactCustomField, CrmContactCustomFieldValue, CrmActivity, DealNote, SharedInbox, InboxEmail | crm |

---

## API Endpoints (v1)

All under `/api/v1/` · Auth: `Authorization: Bearer {api_key}` · Read-only (GET only)

```
GET /health                         — unauthenticated, rate-limited 60/min
GET /me                             — current company + active modules
GET /modules                        — all active modules for company

GET /hr/employees                   — paginated
GET /hr/employees/{id}
GET /hr/leave-requests
GET /hr/leave-requests/{id}

GET /projects/tasks
GET /projects/tasks/{id}
GET /projects/time-entries
GET /projects/time-entries/{id}

GET /finance/invoices
GET /finance/invoices/{id}
GET /finance/expenses
GET /finance/expenses/{id}

GET /crm/contacts
GET /crm/contacts/{id}
GET /crm/deals
GET /crm/deals/{id}
GET /crm/tickets
GET /crm/tickets/{id}
```

---

## Events & Listeners

36 event→listener pairs registered. All listeners implement `ShouldQueue`.

| Domain | Real listeners | Stub listeners |
|---|---|---|
| HR | 7 (leave, payslip, onboarding) | 7 (profile, role, payrun) |
| Projects | 1 (task assigned) | 9 (doc, task, time) |
| Finance | 4 (invoice, expense) | 4 (invoice created/sent/paid, credit note) |
| CRM | 1 (ticket resolved) | 2 (deal won/lost) |

Stub listeners: body-empty, ShouldQueue, ready for integrations/webhooks.

---

## Phase 1.5 — Marketing Site

All 19 spec pages built and routed. Uses Inertia.js + Vue (deviation from spec's Blade + Livewire — intentional, built during prior session).

| Page | Route | Status |
|---|---|---|
| Homepage | `/` | ✅ |
| Pricing | `/pricing` | ✅ |
| Features overview | `/features` | ✅ |
| Module pages | `/modules/{key}` | ✅ |
| About | `/about` | ✅ |
| Blog | `/blog` | ✅ |
| Blog post | `/blog/{slug}` | ✅ |
| Request demo | `/demo` | ✅ |
| Help centre | `/help` | ✅ |
| Help article | `/help/{slug}` | ✅ |
| Changelog | `/changelog` | ✅ |
| Careers | `/careers` | ✅ |
| Contact | `/contact` | ✅ |
| Status | `/status` | ✅ |
| Privacy Policy | `/legal/privacy` | ✅ |
| Terms of Service | `/legal/terms` | ✅ |
| Cookie Policy | `/legal/cookies` | ✅ |
| DPA | `/legal/dpa` | ✅ |
| AUP | `/legal/aup` | ✅ |
| Security | `/security` | ✅ |

---

## Pending Design Decisions

- `GeneratePayslipPdf` job is a stub — needs a PDF package (e.g. Browsershot, DomPDF) before payslips can be rendered
- `Shared Inbox & Email` full UI deferred — SharedInbox and InboxEmail models wired; EmailReceivedInSharedInbox event stubbed; full UI Phase 8

---

## Next Up: Phase 4

See [[Build Order (Phases)]] for Phase 4 scope.
Panels to build: `operations` + `marketing` (within-platform, not the public site).
