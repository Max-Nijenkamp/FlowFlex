---
type: roadmap
last-updated: 2026-06-10
color: "#F97316"
---

# Roadmap — Milestones to v1 and Beyond

Milestone-based, no calendar dates (solo dev, aggressive 6–12 month target). A milestone is done when its **exit gate** passes — never start the next milestone's modules before the gate. Sequencing detail per module lives in [[build/BUILD-ORDER]].

**v1 = all 66 MVP modules** (Foundation 8 + Core 15 + HR 15 + Finance 13 + CRM 15). That is the sellable release.

---

## M0 — Foundation (8 modules)

`foundation.scaffold` → `docker` → `tenancy` → `queues` → `email` → `panels` → `permissions` → `tests`

**Exit gate:** `php artisan migrate --seed` clean · demo company + owner login works in `/app` · one passing tenant-isolation test · Horizon + Reverb + Mailpit up in Docker · CI pipeline green on main.

## M1 — Core Workspace (8 modules)

`core.settings` → `core.rbac` → `core.invitations` → `core.billing` → `core.marketplace` → `core.audit` → `core.notifications` → `core.files`

**Exit gate:** staff create a company in `/admin` → owner invited → accepts at `/register/invite/{token}` → completes setup wizard *(wizard ships in M2 — gate uses seeded company until then)* → activates a paid module in marketplace → module gating verified (`hasModule` blocks/allows) · audit trail records it all · notification bell live via Reverb.

## M2 — Core Remainder (7) + HR (15)

Core: `core.import`, `core.webhooks`, `core.api`, `core.setup`, `core.privacy`, `core.i18n`, `core.health`
HR: `hr.profiles` → `hr.org` → `hr.self-service` → `hr.leave` → `hr.onboarding` → `hr.payroll` → remaining HR modules

**Exit gate:** full hire-to-leave-request flow: create employee → org chart shows them → employee self-service login → leave request → manager approves → `LeaveRequestApproved` consumed by payroll stub · `EmployeeHired` event fires with contract payload · HR data import via `core.import` works.

## M3 — Finance (13 modules)

`finance.ledger` → `finance.invoicing` → `finance.expenses` → `finance.bank` → AR/AP → budgets/reporting/tax

**Exit gate:** invoice created → PDF generated → sent by email → paid via Stripe webhook → `InvoicePaid` fires → GL journal entries balance · expense claim → approval → reimbursement flow · `PayrollRunApproved` posts to GL.

## M4 — CRM & Sales (15 modules)

`crm.contacts` → `crm.deals` → `crm.pipeline` → `crm.activities` → `crm.quotes` → remaining (CS modules within `/crm` panel are Phase 3 — not v1)

**Exit gate:** contact → deal → pipeline board (live Kanban) → quote → `DealWon` → invoice stub appears in Finance · Meilisearch contact search works · activity timeline complete.

## M5 — v1 Hardening (no new modules)

Security review (rate limits, headers, canAccess sweep, file-upload rules) · performance pass (N+1 sweep, indexes, cache hit-rates) · backup + restore drill (`spatie/laravel-backup`) · GDPR export verified · onboarding polish (setup wizard UX) · error tracking (Sentry) wired · load smoke test.

**Exit gate = v1 LAUNCH:** a real company can be onboarded (staff-created in `/admin`, owner invited, setup wizard completed), activate HR + Finance + CRM, manage employees, send invoices, run a pipeline — and pay for it via the billing engine.

---

## Post-v1

| Release | Contents |
|---|---|
| **v1.x fast-follows** | Google/Microsoft SSO (socialite, deferred ADR) · polish from first-customer feedback · usage-based pricing groundwork for high-volume modules |
| **M6–M9 (Phase 2)** | `projects` (11) → `support` (7) → `communications` (8) → `dms` (6) — order may swap based on customer pull |
| **Phase 3** | marketing, operations (+procurement), analytics, it, legal, ecommerce, lms, ai, customer-success, workplace, events — strictly on demand signal |
| **Deferred** | 10 stub domains — spec fully only on concrete customer demand |

---

## Standing Rules

- Build order within milestones: [[build/BUILD-ORDER]] (anchors first: `hr.profiles`, `finance.ledger`, `crm.contacts`)
- Cross-domain emitters built before consumers; stub listeners until consumer domain exists
- No milestone skipping — every exit gate is a regression suite for everything before it

---

## Related

- [[build/BUILD-ORDER]] · [[build/STATUS]] · [[architecture/way-of-working]] · [[domains/_overview]]
