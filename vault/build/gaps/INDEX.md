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
| [[gap-canaccess-missing-filament]] | high | All | vault-wide | canAccess() missing on Filament artifacts (~100+ HIGH, all 31 domains) | 2026-06-11 |
| [[gap-public-surfaces-no-guard]] | high | All | vault-wide | Public/external surfaces declare no guest/portal guard boundary | 2026-06-11 |
| [[gap-encrypted-fields-missing]] | high | All | vault-wide | Sensitive PII/secrets not declared in encrypted-fields (7 specs fixed 2026-06-11) | 2026-06-11 |
| [[gap-webhook-verification-assumed]] | high | All | vault-wide | Inbound webhook signature verification stated as assumption, not requirement | 2026-06-11 |
| [[gap-ui-row-not-in-table]] | high | All | lms, workplace | UI kinds not in the ui-strategy decision table (need ADR) | 2026-06-11 |
| [[gap-rate-limiter-missing]] | medium | All | vault-wide | No rate limiter on expensive/abuse-prone surfaces | 2026-06-11 |
| [[gap-file-upload-contract]] | medium | All | vault-wide | File uploads omit whitelist + size + tenant path contract | 2026-06-11 |

Full per-spec worklist (298 line items): [[build/security-audit-2026-06-11]].

---

## Resolved Gaps

| ID | Domain | Description | Resolved |
|---|---|---|---|
| — | — | — | — |

---

## How to Add a Gap

Run `/flowflex:bug ["description"] [module=name] [severity=high|medium|low]` to create a gap file and add it here.

Or create manually: `vault/build/gaps/gap-{slug}.md` with frontmatter `type: gap`, `color: "#F97316"`, `status: open`.
