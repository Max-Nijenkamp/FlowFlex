---
type: adr
date: 2026-06-11
status: decided
domain: All
color: "#F97316"
---

# Static Analysis: Plain PHPStan + @property Docblocks (Larastan Deferred)

## Context

Building `foundation.scaffold`, the Larastan PHPStan extension (`larastan/larastan` v3.10.0, also the abandoned `nunomaduro/larastan` alias) crashes silently — exit 1, zero output on every stream — the moment it is loaded, on this stack (PHP 8.4.20 ARM64, phpstan 2.2.2, Laravel 13.15). Reproduced with both package names, bare config, single-process, 8G memory, JIT/PCRE off, all error formats. Plain PHPStan (no Larastan) runs perfectly and found 2 real issues.

Larastan's value is auto-awareness of Eloquent magic attributes (DB-backed `$model->column`). Without it, plain PHPStan flags `$this->first_name` etc. as undefined properties.

## Options Considered

1. **Block all builds until Larastan is fixed.** Rejected — leaves the entire project with no static-analysis gate for an unknown duration over an upstream/env bug.
2. **Baseline/ignore the model-property errors.** Rejected — hides real type information; violates the no-suppression rule.
3. **Plain PHPStan (level 5) + explicit `@property` docblocks on models.** Chosen — gives a green, meaningful gate now; the docblocks document each model's schema explicitly (arguably better than Larastan's inference) and are required for IDE autocomplete anyway.

## Decision

- `phpstan.neon` runs **plain PHPStan level 5** over `app/`, **without** the Larastan extension include.
- Every Eloquent model declares its columns + relations + accessors via `@property` / `@property-read` docblocks. Added to `Company`, `User`, `Admin`; this becomes a model convention.
- `larastan/larastan` stays in `composer.json` (dev) but is **not** wired into the gate — re-enable the include once the crash is resolved upstream/in-env.
- Quality gate for every module = **Pint + PHPStan (plain) + Pest**, all green.

## Consequences

- Static-analysis gate works today: scaffold passes phpstan 0 errors, Pint clean, Pest 10/10.
- New models MUST carry `@property` docblocks (add to spec-template model guidance / testing-pattern when convenient).
- Closes [[build/gaps/gap-larastan-laravel-boot-crash]].
- Revisit: when Larastan loads cleanly, re-add the include and the docblocks remain as documentation.

## Related

- [[build/gaps/gap-larastan-laravel-boot-crash]]
- [[domains/foundation/laravel-scaffold/_module]]
- [[architecture/ci-cd]]
- [[architecture/patterns/testing-pattern]]
