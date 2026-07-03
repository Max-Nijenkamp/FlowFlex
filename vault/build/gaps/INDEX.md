---
type: gap-index
color: "#F97316"
---

# Open Gaps

Bugs, spec issues, and missing details discovered during build sessions.

---

## Open Gaps

| ID | Severity | Domain | Module | Description | Discovered |
|---|---|---|---|---|---|
| [[gap-filament5-plugins-unavailable]] | low | foundation | foundation.scaffold | 2 of 4 Filament plugins still lack v5 (fullcalendar, tiptap — needed Phase 2+); shield + activitylog resolved via custom resources in core.rbac/core.audit | 2026-06-11 |
| [[gap-switchboard-expansion-spec-missing]] | low | All | frontend | §14–25 pages built in-system with *(assumed)* copy — regenerated design bundle never landed; diff + swap when it arrives | 2026-06-12 |
| [[gap-bank-accounts-import-page-not-in-manifest]] | low | finance | finance.bank-accounts | ImportStatementPage in Filament Artifacts but absent from Build Manifest | 2026-07-03 |
| [[gap-two-panel-matcher-ui-row-missing]] | low | finance | finance.bank-accounts | Bank-rec + payment-run are two-panel matchers with no ui-strategy row; cite #9* — needs ADR | 2026-07-03 |
| [[gap-projects-estimated-hours-unit-mismatch]] | medium | projects | projects.tasks | estimated_hours decimal vs platform minutes-int convention — migrate or exempt via ADR | 2026-07-03 |
| [[gap-canaccess-verb-not-in-permission-table]] | medium | All | communications.email-channel | canAccess() cites verbs missing from security.md permission tables (9 modules so far incl. crm) — needs vault-wide lint + ADR | 2026-07-03 |
| [[gap-crm-contracts-renewals-page-not-in-manifest]] | low | crm | crm.contracts | Renewals queue page + renew verb declared but ContractRenewalsPage absent from Build Manifest | 2026-07-03 |
| [[gap-crm-money-pii-conventions]] | medium | crm | crm.referral-program | Reward money in jsonb blob (not minor-unit int); lead phone lacks E.164 + encryption decision | 2026-07-03 |
| [[gap-feature-marketing-subscriber-import]] | medium | marketing | marketing.campaigns | No `core.data-import` importer for a marketing subscriber list — blocks the migrate-off-Mailchimp on-ramp (audiences only come from CRM segments) | 2026-07-03 |
| [[gap-feature-events-attendee-import]] | medium | events | events.registrations | Attendee export specced but no bulk attendee/guest-list import — organizers expect roster upload + bulk QR ticketing | 2026-07-03 |
| [[gap-feature-projects-task-import-export]] | medium | projects | projects.tasks | No task CSV/Excel import or export — blocks migrate-off-Asana/Monday on-ramp; only time/gantt export exists | 2026-07-03 |
| [[gap-feature-legal-contract-search-tags]] | medium | legal | legal.contracts | No full-text search (scout) or tagging on contracts — repository only as findable as column filters; adoption killer | 2026-07-03 |
| [[gap-feature-visitor-qr-checkin]] | low | workplace | workplace.visitors | No QR pre-reg→kiosk check-in (simple-qrcode); VMS table stakes + sidesteps encrypted-visitor in-memory lookup | 2026-07-03 |
| [[gap-feature-core-bulk-invite]] | medium | core | core.invitations | Invites are one-at-a-time modal only; no bulk/CSV invite importer for onboarding 50–500 staff | 2026-07-03 |
| [[gap-feature-foundation-email-suppression]] | medium | foundation | foundation.email | Bounce webhook flags hard bounces only; no complaint/soft-bounce suppression list (Gmail 0.3% / Microsoft 2025 enforcement) | 2026-07-03 |
| [[gap-feature-analytics-public-dashboard-share]] | medium | analytics | analytics.dashboards | Sharing is intra-company only; no seatless tokened read-only link for external stakeholders (needs ADR) | 2026-07-03 |
| [[gap-feature-ai-inbound-webhook-trigger]] | medium | ai | ai.workflows | Triggers are internal events + schedule only; no inbound external-webhook ("Catch Hook") to start a flow | 2026-07-03 |
| [[gap-feature-dms-batch-template-generation]] | medium | dms | dms.templates | Generate-from-template is single-doc only; no batch mail-merge (one template × many records → many PDFs) | 2026-07-03 |
| [[gap-feature-inventory-barcode-labels]] | medium | operations | operations.inventory | Barcode scanning flagged but no barcode/QR **label printing** for items or bin locations — half-loop (simple-qrcode + laravel-pdf) | 2026-07-03 |
| [[gap-feature-comms-broadcast-recipient-import]] | medium | communications | communications.broadcast | Broadcast audiences only from CRM segments / HR groups / manual list — no CSV import of external recipients (parallels marketing + events import gaps) | 2026-07-03 |
| [[gap-feature-it-asset-qr-labels]] | medium | it | it.assets | Asset record has `asset_tag` but no QR/barcode **label printing** — blocks scan-to-ticket unified asset+helpdesk loop (simple-qrcode + laravel-pdf) | 2026-07-03 |

## Archived history

Resolved gaps and the 2026-06-11 security-audit worklist described fixes to the pre-2026-06-20 app (code since deleted). Moved to `_archive/build-history/` on 2026-07-03 — their spec-level outcomes are baked into the v3 specs and the Definition of Done. The porting lessons that still matter at build time (pgsql self-FK ordering, notifications jsonb, Filament asset publishing, panel MFA contract) live on as notes in [[../ROADMAP|the roadmap]].

---

## How to Add a Gap

Run `/flowflex:bug ["description"] [module=name] [severity=high|medium|low]` to create a gap file and add it here.

Or create manually: `vault/build/gaps/gap-{slug}.md` with frontmatter `type: gap`, `color: "#F97316"`, `status: open`.
