---
type: adr
date: 2026-05-12
status: decided
color: "#F97316"
domain: Learning & Development
---

# ADR: LMS Migration Range — Actual (480001–480015) vs Spec (700000–749999)

## Context

The LMS left-brain spec (`MOC_LMS.md`) specified a migration range of `700000–749999`. However, when Phase 7 LMS was built on 2026-05-12, the sequential date-based migration naming convention (`2026_05_12_480001_*`) was used instead.

All domains built from Phase 4 onwards follow the `YYYY_MM_DD_NNNNNN_` prefix pattern where `NNNNNN` is an incrementing counter within the build session. The spec-defined ranges (e.g. 700000–749999 for LMS) are legacy planning placeholders from vault design time and were never intended as literal migration file prefixes — they described logical namespace ranges for human reference.

## Options Considered

1. **Use spec range (700000–749999)** — Rename all 15 migrations to use this prefix. Creates a gap in the date-ordered migration sequence and diverges from every other Phase 4–8 domain.
2. **Use sequential date-based range (480001–480015)** — Consistent with Phase 5–8 build conventions (460001–460010 for AI, 450001–450006 for Analytics, 480001 series continues from where CRM left off).

## Decision

Use sequential date-based migration naming (`2026_05_12_480001_*` through `2026_05_12_480015_*`). This is consistent with every other recently-built domain.

The spec migration ranges should be treated as logical documentation references only, not literal file name prefixes.

## Consequences

- All 15 LMS migrations are sequenced correctly relative to the rest of the codebase.
- The spec `migration_range:` frontmatter in each LMS module file has been updated to reflect the actual ranges used.
- Future domain builds should follow date-based sequential naming regardless of spec-defined range placeholders.
- The spec ranges remain useful as namespace buckets for understanding which domain "owns" a conceptual range, but actual files use date prefix.

## Related

- [[MOC_LMS]]
- [[decision-2026-05-10-migration-naming-convention]]
- [[builder-log-lms-phase7]]
