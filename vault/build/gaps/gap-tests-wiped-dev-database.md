---
type: gap
severity: high
category: bug
status: resolved
domain: foundation
color: "#F97316"
discovered: 2026-07-04
discovered-in: foundation.test-suite
resolved: 2026-07-04
---

# `php artisan test --parallel` silently wiped the pgsql dev database

## Context

Recurring "cannot log in" reports on the dev stack: the demo users vanished twice on 2026-07-04 (and probably explain the same complaint at session start). Reseeding fixed it each time — until the next test run.

## Problem

phpunit.xml's `<env>` overrides (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`) are **non-forced** by default: they only apply when the variable isn't already present in the real process environment. `php artisan test --parallel` boots artisan first (which loads `.env`, putting `DB_CONNECTION=pgsql` into the real environment) and then spawns ParaTest workers that inherit it — so the whole suite ran against the **real pgsql database**, and `RefreshDatabase` `migrate:fresh`ed it on every run. Plain `vendor/bin/pest` runs kept using sqlite, which made the failures look random.

## Impact

- Dev database silently dropped + remigrated on every parallel test run (all seeded data lost).
- Would have been catastrophic against any shared/staging database.

## Solution (shipped)

1. **`force="true"` on every phpunit `<env>` entry** — the overrides now always win.
2. **Belt + braces guard in `Tests\TestCase::setUp()`**: any test booting on a non-sqlite connection throws immediately with a pointer to this gap, so a regression fails the whole suite loudly instead of wiping data. The guard is itself proven: it turned 50 passing-on-pgsql tests into 50 immediate failures before the fix.

## Lesson

A green suite is not evidence it ran on the test database. Any suite whose harness swaps connections needs a fail-fast connection assert.
