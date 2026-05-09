---
type: moc
section: right-brain/validation
last_updated: 2026-05-08
---

# Validation — Module Review Checklists

Post-build validation checklist results per module.

---

## Module Validation Index

| Module | Domain | Validated | Pass/Fail |
|---|---|---|---|
| _none yet_ | | | |

---

## Standard Validation Checklist

Run this against every completed module:

### Database
- [ ] Migration runs on fresh database
- [ ] Migration rolls back cleanly
- [ ] All foreign keys reference correct tables
- [ ] All indices applied (`company_id`, unique constraints)
- [ ] Soft deletes column present

### Model
- [ ] `HasUlids` trait applied
- [ ] `BelongsToCompany` trait applied
- [ ] `SoftDeletes` trait applied
- [ ] `LogsActivity` trait applied with correct `$logAttributes`
- [ ] All enum casts defined

### Service
- [ ] Implements the Interface fully
- [ ] All methods fire correct events
- [ ] No direct `Request` usage — only DTOs
- [ ] No N+1 queries (check with Telescope)

### DTO
- [ ] Validation attributes on all required fields
- [ ] `fromModel()` factory method present
- [ ] TypeScript interface generated correctly

### Filament Resource
- [ ] List page renders with correct columns
- [ ] Create form validates correctly
- [ ] Edit form pre-fills correctly
- [ ] Delete action uses soft delete
- [ ] Navigation item shows in correct group
- [ ] Permission check on resource (`canViewAny`, `canCreate`, etc.)

### Events
- [ ] All events carry `company_id`
- [ ] All events documented in domain MOC
- [ ] Cross-domain listeners queued

### Security
- [ ] No direct `company_id` in route parameters (use auth context)
- [ ] File uploads go to `companies/{id}/...` path
- [ ] No sensitive data in logs

---

## How to Create a Validation Report

1. Copy checklist above into `right-brain/validation/validation-{module}.md`
2. Run through every item
3. Mark pass ✅ or fail ❌ with notes
4. Link to any gaps discovered
5. Add entry to index in this MOC

---

## Related

- [[STATUS_Dashboard]]
- [[ACTIVATION_GUIDE]]
