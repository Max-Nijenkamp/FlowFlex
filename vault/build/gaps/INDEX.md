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
| [[gap-filament5-plugins-unavailable]] | medium | foundation | foundation.scaffold | 4 Filament plugins (shield, tiptap, fullcalendar, activitylog) have no Filament 5 release yet — external; non-blocking for foundation, re-check at each dependent module | 2026-06-11 |

All 7 audit gaps resolved at spec level 2026-06-11 (see below). Code-level enforcement is carried into each module build via the Definition of Done. Full per-spec worklist: [[build/security-audit-2026-06-11]].

---

## Resolved Gaps

| ID | Domain | Description | Resolved |
|---|---|---|---|
| [[gap-canaccess-missing-filament]] | All | canAccess() missing on Filament artifacts — backfilled into 165 specs | 2026-06-11 |
| [[gap-public-surfaces-no-guard]] | All | Public surfaces no guest/portal guard — Security notes added to 14 specs | 2026-06-11 |
| [[gap-encrypted-fields-missing]] | All | Sensitive PII/secrets unencrypted — 7 specs fixed | 2026-06-11 |
| [[gap-webhook-verification-assumed]] | All | Webhook verification assumed — promoted to requirement in 3 specs | 2026-06-11 |
| [[gap-rate-limiter-missing]] | All | Missing rate limiters — Security notes added to 50 specs | 2026-06-11 |
| [[gap-file-upload-contract]] | All | Upload contract omitted — Security notes added to 24 specs | 2026-06-11 |
| [[gap-ui-row-not-in-table]] | All | UI kinds not in table — ADR + rows 17–19, 3 specs re-cited | 2026-06-11 |
| [[gap-larastan-laravel-boot-crash]] | foundation | Larastan crash — switched gate to plain PHPStan + @property docblocks (ADR) | 2026-06-11 |

---

## How to Add a Gap

Run `/flowflex:bug ["description"] [module=name] [severity=high|medium|low]` to create a gap file and add it here.

Or create manually: `vault/build/gaps/gap-{slug}.md` with frontmatter `type: gap`, `color: "#F97316"`, `status: open`.
