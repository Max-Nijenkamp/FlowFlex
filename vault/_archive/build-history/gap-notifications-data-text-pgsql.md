---
type: gap
severity: high
category: bug
status: resolved
domain: core
color: "#F97316"
discovered: 2026-06-11
discovered-in: core.notifications
resolved: 2026-06-11
---

# notifications.data was text — Filament bell crashes on pgsql

## Context

The notifications table used Laravel's classic `$table->text('data')`. Filament's database-notifications bell filters `data->>'format' = 'filament'` — a JSON operator pgsql only supports on json/jsonb columns. sqlite's `json_extract` works on text, so the suite stayed green (same family as [[gap-pgsql-self-fk-ordering]]).

## Problem

Every authenticated /app page 500'd in docker (`operator does not exist: text ->> unknown`) once databaseNotifications rendered.

## Resolution

Create migration amended to `jsonb('data')`; alter migration casts the live pgsql column (`USING data::jsonb`). PHP-side date grouping kept in widgets to avoid further driver-specific SQL.
