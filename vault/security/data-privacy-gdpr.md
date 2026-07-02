---
domain: security
type: security
build-status: planned
status: unverified
color: "#EF4444"
updated: 2026-06-20
---

# Data Privacy & GDPR

EU-hosted, GDPR-first. The platform-level privacy machinery is built and owned by
[[../domains/core/data-privacy/_module]]; this note is the security-side summary.

| Capability | Implementation |
|---|---|
| **DSAR** (subject access) | `DsarRequest` model + states + `DsarDeadlineReminderCommand` |
| **Consent** | `ConsentLog` (append-only) |
| **Erasure** | Hard-delete on GDPR erasure (soft-delete would defeat it); `PersonalDataRegistry` drives the cascade |
| **PII at rest** | Encrypted columns — see [[encryption]] |

> [!note] Built vs planned
> `PurgeCancelledCompaniesCommand` (retention purge) is **not** built yet (only the DSAR reminder command
> exists). Per-table erasure cascades in `architecture/data-lifecycle.md` are **planned** for domains that
> don't exist yet. See AUDIT E11.

## Related

- [[encryption]] · [[../domains/core/data-privacy/_module]] · [[../architecture/data-lifecycle]] · [[_moc|Security MOC]]
