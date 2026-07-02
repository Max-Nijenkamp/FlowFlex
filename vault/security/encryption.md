---
domain: security
type: security
build-status: planned
status: unverified
color: "#EF4444"
updated: 2026-06-20
---

# Encryption (PII at rest)

Sensitive personal fields use Laravel's `encrypted` cast, stored in `text` columns (ciphertext is
longer than plaintext, and is not queryable).

**Designated encrypted fields** (convention — applies when the owning domain is built): national ID,
date of birth, IBAN / bank account, salary / compensation figures. Each module spec declares its own
`encrypted-fields:` frontmatter (e.g. `["hr_employees.national_id"]`).

> [!note] None encrypted today
> All `encrypted-fields` declarations point at `hr_*` / `finance_*` tables that were **stripped**. No
> encrypted columns exist in the current platform shell — they return when those domains are rebuilt.

Pattern + cast detail: [[../architecture/patterns/encryption]].

## Related

- [[data-privacy-gdpr]] · [[../architecture/patterns/encryption]] · [[_moc|Security MOC]]
