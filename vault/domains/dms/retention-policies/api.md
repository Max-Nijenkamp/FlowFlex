---
domain: dms
module: retention-policies
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Retention Policies — API / DTOs

All input flows through `spatie/laravel-data` DTOs — never `$request->all()`.

## `CreateRetentionPolicyData`

| Field | Type | Rules |
|---|---|---|
| `name` | string | required |
| `applies_to` | object | `{ type: in:folder,tag, id: ulid }` |
| `retention_days` | int | required, `min:1` |
| `action` | string | required, `in:archive,delete` |
| `clock_from` | string | required, `in:created,modified` |

## `PlaceLegalHoldData`

| Field | Type | Rules |
|---|---|---|
| `document_id` | ulid | required |
| `reason` | string | required, `max:1000` |

## Service Surface

| Call | Returns | Notes |
|---|---|---|
| `RetentionService::evaluate()` | `RetentionResult` | Invoked by `ProcessRetentionCommand`; not an HTTP endpoint. Chunked, per-document `try/catch`. |
| `PlaceLegalHoldAction` | `LegalHold` | One active hold per document enforced. |
| `ReleaseLegalHoldAction` | `LegalHold` | Sets `released_at`. |

## Public / Portal Endpoints

None. Retention is an internal `/dms` settings + background surface. No public route; all mutation goes through Filament resources gated by permission + module.
