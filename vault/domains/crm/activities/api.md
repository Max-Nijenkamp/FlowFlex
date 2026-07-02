---
domain: crm
module: activities
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Activities — API & DTOs

See also [[data-model|activities.data-model]], [[../../../architecture/api-design]].

---

## DTOs

### LogActivityData (input)

| Field | Type | Validation |
|---|---|---|
| type | string | required, in:call,email,meeting,task,note |
| subject | string | required, max:255 |
| description | ?string | max:5000 |
| contact_id | ?string | ulid in company |
| deal_id | ?string | ulid in company |
| account_id | ?string | ulid in company |
| activity_date | CarbonImmutable | required |
| duration_minutes | ?int | min:1 |
| due_at | ?CarbonImmutable | required_if type=task ("Tasks need a due date.") |

Cross-field: at least one of contact_id / deal_id / account_id required ("Link the activity to a contact, deal, or account.").

### ActivityData (output)

Mirrors all `crm_activities` columns plus:
- `is_overdue` — computed boolean (is_complete=false AND due_at < now)
- `owner_name` — denormalized from users

---

## Timeline Endpoint (internal — no public API)

`TimelineQuery::for(Model $model): CursorPaginator`

Used internally by Filament view pages (Contact, Deal, Account). Returns activities ordered by `activity_date DESC`, cursor-paginated. Not exposed as a REST endpoint — consumed via Livewire component `ActivityTimeline`.
