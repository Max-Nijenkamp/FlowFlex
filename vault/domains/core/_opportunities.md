---
domain: core
type: note
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Core — Opportunities

Concrete differentiators for FlowFlex's platform/admin layer, grounded in real 2024–2026 gaps and feature requests that competing admin/RBAC/billing/compliance tooling commonly leaves unaddressed. Each item maps to a `core` module.

### 1. Per-module custom roles with three-layer (page / operation / data) granularity, self-serve by tenant admins
- **Gap**: Enterprise buyers repeatedly ask for customer-defined custom roles that combine permissions across page-level, operation-level, and data-level layers — but most B2B SaaS ships only a fixed set of roles (Admin/Member/Viewer), forcing larger customers to file one-off permission requests. The three-layer model is described as a best practice, not a shipped default.
- **FlowFlex angle**: `core.rbac` already uses spatie/laravel-permission with `team_id = company_id`. Extend it so tenant admins compose custom roles in-panel, scoped per activated module (module marketplace already gates which modules exist), with data-level scoping (own-records vs team vs company) as a first-class dimension rather than bolted-on policies.
- **Source**: https://www.perpetualny.com/blog/how-to-design-effective-saas-roles-and-permissions (accessed 2026-07-02)

### 2. Real-time usage metering + fractional/mixed-interval billing that Stripe Billing can't natively do
- **Gap**: Stripe Billing lacks real-time usage visibility (only periodic summaries), can't do fractional/per-event billing, enforces uniform billing periods across products in one subscription, and its low rate limits choke high-volume metering. Companies moving to usage-based models (60%+ of new SaaS) hit these walls and reach for Orb/Metronome.
- **FlowFlex angle**: `core.billing-engine` owns subscriptions and module activation. Ship a live usage meter (Redis counters → daily rollup) with a real-time "current spend" widget in `/app`, support per-module metered add-ons with independent intervals, and keep Stripe purely as the payment rail (matches the "raw SDK, not Cashier" ADR) rather than the metering system of record.
- **Source**: https://www.withorb.com/blog/stripe-limitations-for-usage-based-billing (accessed 2026-07-02)

### 3. Cryptographically tamper-evident (WORM / hash-chained) audit log with instant governance feed
- **Gap**: True immutability (append-only, cryptographically signed, WORM storage) is a stated compliance and trust requirement, yet many SaaS delay it because they assume it's complex/expensive, shipping only mutable app-DB activity logs that can be altered or deleted — which defeats SOC 2 / HIPAA / SOX evidentiary value.
- **FlowFlex angle**: `core.audit-log` builds on spatie/laravel-activitylog but is currently a normal table. Add hash-chaining (each entry references prior entry's hash), optional append-only object-lock export, and per-retention-regime policies (SOC 2 ≥1yr, HIPAA 6yr, SOX 7yr). Surface a governance-ready, filterable feed so risk teams trust the log without re-validation.
- **Source**: https://hoop.dev/blog/immutable-audit-logs-the-foundation-of-saas-governance (accessed 2026-07-02)

### 4. AI-assisted CSV/Excel importer: fuzzy column auto-mapping + pre-submit per-row validation
- **Gap**: The best-practice import flow (File → Map → Validate → Submit) with fuzzy/ML column auto-mapping and errors surfaced per-row *before* data is committed is a "nice-to-have" most SaaS still lack — teams either hand-roll brittle importers or pay for Flatfile/OneSchema/CSVbox. Poor import UX drives support tickets and dirty data.
- **FlowFlex angle**: `core.data-import` (uses maatwebsite/laravel-excel) can ship a reusable multi-step import wizard as a Filament custom page: fuzzy-match uploaded headers to target fields, remember historical mappings per company, validate every row against the module's Data class, and show a "N rows ready, M errors" preview before the import job queues. Reusable across CRM/HR/Finance imports.
- **Source**: https://blog.csvbox.io/column-mapping-saas/ (accessed 2026-07-02)

### 5. Cross-channel notification preference center with per-type frequency (real-time / daily / weekly digest / off)
- **Gap**: Notification fatigue is well documented (46–63 pushes/day/user); the proven fix is a preference center giving per-type, per-channel frequency control including digest bundling. Apps with comprehensive preference controls see 43% lower opt-out and 31% higher engagement — yet many SaaS still send every event individually with no digest option.
- **FlowFlex angle**: `core.notifications` can own a company-wide + per-user preference center: for each event type (from the event-bus), the user chooses channel(s) and cadence (real-time / daily digest / weekly summary / off). A scheduled digest job batches queued notifications. Because FlowFlex is all-in-one, one preference center governs HR, Finance, CRM, etc. — a genuine advantage over point tools each nagging separately.
- **Source**: https://www.suprsend.com/post/notification-preference-center (accessed 2026-07-02)

### 6. Self-serve webhook reliability console: DLQ, safe replay (single + bulk), and failure alerts
- **Gap**: Reliable outbound webhooks need exponential backoff + jitter, a dead-letter queue for exhausted attempts, and — critically — self-serve replay (individual for testing, bulk for outage recovery) plus alerting on repeated failure. Many SaaS expose webhooks but give customers no visibility or replay, so a downtime on the receiver's side means silently lost events.
- **FlowFlex angle**: `core.webhooks` (Laravel queue + Horizon already in stack) can ship a Filament console showing every delivery attempt, a DLQ view, one-click and rate-limited bulk replay after a fix, and an alert when an endpoint fails N times consecutively. Idempotency keys on every payload make replay safe.
- **Source**: https://hookdeck.com/webhooks/guides/dead-letter-queues-webhook-reliability (accessed 2026-07-02)

### 7. Built-in self-service DSAR portal (access + export + erasure) instead of a bolt-on compliance vendor
- **Gap**: DSAR volume jumped 43% YoY in 2025 and the DSAR-tooling market is heading to $5.6B by 2033 — but the automation (identity-verified requestor portal, cross-system discovery, automated erasure workflows) is almost always a separate enterprise product (OneTrust, DataGrail, Securiti) integrated via connectors, not native to the app holding the data.
- **FlowFlex angle**: Because FlowFlex *is* the system of record across domains, `core.data-privacy` can offer a native, identity-verified DSAR portal that assembles a data export and runs GDPR-cascade erasure across every module in one action — no external discovery layer or 500-connector integration needed. This is a structural advantage all-in-one has over point-solution stacks.
- **Source**: https://transcend.io/blog/best-tools-for-handling-user-data-subject-access-requests (accessed 2026-07-02)

### 8. Guided workspace onboarding: activation checklist + templates/demo data for time-to-first-value
- **Gap**: Leading products cut time-to-first-value with a 3–5 item activation checklist tied to real milestones, plus templates/demo data so a user sees value before inviting the team. In B2B the account can be "onboarded" while daily users are lost — yet many admin-heavy SaaS drop users into an empty panel with no guided setup.
- **FlowFlex angle**: `core.workspace-hub` (with `core.setup-wizard`) can drive a per-role activation checklist on the `/app` dashboard (e.g. "activate a module, invite a teammate, import your first records, enable 2FA"), backed by the existing LocalDemoDataSeeder pattern to seed realistic starter data per activated module. Decouples individual activation from full team rollout.
- **Source**: https://www.candu.ai/blog/best-saas-onboarding-examples-checklist-practices-for-2025 (accessed 2026-07-02)

### 9. Per-role / passkey-first 2FA enforcement policies set by tenant admins
- **Gap**: 2025 best practice is per-role MFA enforcement (stronger for admins/privileged users) with passkeys acting as a combined first+second factor. Regulators (Salesforce, Azure) are mandating phishing-resistant MFA for privileged users. Many SaaS still offer only account-wide, opt-in TOTP with no admin-set policy and no passkey support.
- **FlowFlex angle**: `core.two-factor-auth` can let a company admin set enforcement policy per role (e.g. require passkey/WebAuthn for any owner/admin role, TOTP-or-better for members), with a grace-period rollout and an audit-logged record of who is/ isn't compliant. Ties directly into `core.rbac` roles.
- **Source**: https://www.waldosecurity.com/post/how-to-enable-mfa-for-saas-applications-in-2025-a-practical-guide-for-it-and-security-leaders (accessed 2026-07-02)

### 10. Own-your-logic module marketplace: no vendor-controlled roadmap lock-in
- **Gap**: Teams on low-code admin/internal-tool platforms (Retool, Forest Admin) complain that features critical to their workflow sit on a public roadmap they don't control, and the tool-between-app-and-DB adds latency at operational scale. The differentiator customers want is owning the logic while still getting fast, composable modules.
- **FlowFlex angle**: `core.module-marketplace` (BillingService-gated activation) is the anti-Retool: every module is real Laravel/Filament code in-repo, activated per company, with no third-party layer between UI and Postgres. Positioning: composable all-in-one modules with owned business logic and no external roadmap dependency — the exact gap low-code admin tools leave open.
- **Source**: https://yaro-labs.com/blog/retool-alternatives (accessed 2026-07-02)
> [!warning] UNVERIFIED
> The source discusses general Retool/low-code limitations (roadmap-you-don't-control, added latency); framing FlowFlex's in-repo module marketplace as the specific competitive answer is our extrapolation, not a claim made by the source.

## Related

- [[../../decisions/decision-2026-06-20-full-mapping-conventions]]
- [[_index]]
