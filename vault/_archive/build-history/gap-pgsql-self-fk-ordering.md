---
type: gap
severity: high
category: data-model
status: resolved
domain: All
color: "#F97316"
discovered: 2026-06-11
discovered-in: hr.profiles
---

# Self-referencing FKs failed on Postgres (passed on sqlite)

## Context
Docker stack (pgsql) could not run `migrate:fresh` — `hr_departments.parent_department_id` self-FK aborted with "no unique constraint matching given keys".

## Problem
`->constrained()` self-references inside the same `Schema::create()` get ordered before the primary-key constraint on Postgres. SQLite (the test driver) tolerates it, so the suite stayed green while the dev stack was broken. Three sites: hr_departments.parent_department_id, hr_employees.manager_id, fin_accounts.parent_account_id.

## Resolution (same day)
Self-FKs moved to `Schema::table()` alters after creation. Rule going forward: **never self-reference with constrained() inside Schema::create — always a post-create alter.**

## Lesson
SQLite-only testing hides Postgres constraint-ordering issues; periodically run migrate:fresh against the docker pgsql stack.
