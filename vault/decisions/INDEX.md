---
type: decision-index
color: "#F97316"
updated: 2026-07-03
---

# Decision Log

Architectural decisions, one file per decision. Moved from `build/decisions/` to top-level
`/decisions/` during the 2026-06-20 vault rebuild. Newest first.

| Date | Decision | Status |
|---|---|---|
| 2026-07-03 | [[decisions/decision-2026-07-03-pos-kiosk-ui-row\|Kiosk / scan-station UI-strategy row (#20) — shared-terminal scan/touch surfaces]] | proposed |
| 2026-07-03 | [[decisions/decision-2026-07-03-public-endpoint-limiters\|Public / guest endpoint rate limiters registered]] | decided |
| 2026-07-02 | [[decisions/decision-2026-07-02-optimistic-locking-standard\|Optimistic locking — platform standard for concurrent edits]] | decided |
| 2026-07-02 | [[decisions/decision-2026-07-02-spec-template-v3-exploded-format\|Spec template v3 — exploded format, per-feature test checklists, concurrency notes]] | decided |
| 2026-07-02 | [[decisions/decision-2026-07-02-browser-test-convention\|Browser test convention — automated Playwright smoke suite]] | decided |
| 2026-07-02 | [[decisions/decision-2026-07-02-rate-limit-and-token-hardening\|Rate-limit & token hardening — action throttles, company quotas, Sanctum rotation]] | decided |
| 2026-06-20 | [[decisions/decision-2026-06-20-full-mapping-conventions\|Full-mapping conventions (data ownership, RBAC, per-feature UI, relations)]] | decided |
| 2026-06-20 | [[decisions/decision-2026-06-20-cross-domain-write-resolutions\|Cross-domain write resolutions (projections + domain-local erasure)]] | decided |
| 2026-06-20 | [[decisions/decision-2026-06-20-app-project-removed\|App project removed — vault is now a greenfield blueprint]] | decided |
| 2026-06-20 | [[decisions/decision-2026-06-20-workspace-hub-and-login-model\|Workspace hub (domain selector) + two-login model]] | decided |
| 2026-06-19 | [[decisions/decision-2026-06-19-strip-to-app-admin-shell\|Strip the app back to the App + Admin shell (remove HR / Finance / CRM)]] | decided |
| 2026-06-12 | [[decisions/decision-2026-06-12-switchboard-plus-design-system\|Adopt "Switchboard+" design system]] | decided |
| 2026-06-12 | [[decisions/decision-2026-06-12-custom-pipelines\|Custom pipelines (Pipedrive pattern)]] | decided · ⚠ code reverted by 2026-06-19 strip |
| 2026-06-11 | [[decisions/decision-2026-06-11-ui-strategy-new-rows\|UI Strategy — add rows 17–19]] | decided |
| 2026-06-11 | [[decisions/decision-2026-06-11-static-analysis-without-larastan\|Static analysis: plain PHPStan]] | decided |
| 2026-06-11 | [[decisions/decision-2026-06-11-security-contract-hardening\|Security contract hardening]] | decided |
| 2026-06-11 | [[decisions/decision-2026-06-11-perceived-performance-standard\|Perceived-performance standard]] | decided |
| 2026-06-11 | [[decisions/decision-2026-06-11-owner-only-settings-modules\|Owner-only settings + marketplace]] | decided |
| 2026-06-11 | [[decisions/decision-2026-06-11-mvp-v1-deviations\|MVP v1 implementation deviations]] | decided · ⚠ code reverted by 2026-06-19 strip |
| 2026-06-11 | [[decisions/decision-2026-06-11-flat-namespace-foldering\|Flat namespace foldering]] | decided |
| 2026-06-11 | [[decisions/decision-2026-06-11-2fa-and-mandatory-email-verification\|Self-service 2FA + mandatory email verification]] | decided |
| 2026-06-10 | [[decisions/decision-2026-06-10-no-public-registration\|No public self-registration — invite-only]] | decided |
| 2026-06-10 | [[decisions/decision-2026-06-10-all-filament-hybrid-ui\|All-Filament hybrid UI]] | decided |
| 2026-06-01 | [[decisions/decision-2026-06-01-stripe-cashier-vs-sdk\|Raw Stripe SDK vs Laravel Cashier]] | decided |
| 2026-06-01 | [[decisions/decision-2026-06-01-salary-history\|Salary history tracking]] | decided |
| 2026-06-01 | [[decisions/decision-2026-06-01-panel-consolidation\|Panel consolidation: 21 → 19 panels]] | decided |
| 2026-06-01 | [[decisions/decision-2026-06-01-hybrid-service-pattern\|Hybrid service pattern: Actions + Interface→Service]] | decided |
| 2026-06-01 | [[decisions/decision-2026-06-01-full-phase-3-spec\|Fully spec all modules up front]] | decided |
| 2026-06-01 | [[decisions/decision-2026-06-01-domain-merges\|Domain merges]] | decided |
| 2026-06-01 | [[decisions/decision-2026-06-01-domain-defers\|Domain defers: 10 domains deferred]] | decided |
| 2026-06-01 | [[decisions/decision-2026-06-01-currency-precision\|Currency precision — integer storage]] | decided |

## How to add a decision

Create `decisions/decision-{YYYY-MM-DD}-{slug}.md` with frontmatter `type: adr`, `date`,
`status: decided|proposed`, `domain`, `color: "#F97316"` — then add a row above (newest first).
Body: Context, Options Considered, Decision, Consequences, Related.

## Related

- [[00-index/MOC|Vault MOC]] · [[_archive/ROADMAP|Archived roadmap]]
