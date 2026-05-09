---
type: right-brain
section: validation
last_updated: 2026-05-09
---

# Build Checklist

Per-module checklist to run before marking a module as ✅ complete on [[STATUS_Dashboard]].

---

## Pre-Build

- [ ] Left Brain spec read and understood
- [ ] All linked entity notes read
- [ ] All relevant concept notes read
- [ ] Dependencies checked on [[STATUS_Dashboard]] — all required prior modules are ✅
- [ ] Migration range selected from domain range (see [[_conventions]])
- [ ] Builder log created at `right-brain/builder-logs/{module-name}.md` from [[tpl_builder-log]]

---

## Migration

- [ ] Migration file created with correct range number
- [ ] All columns match Left Brain data model
- [ ] Soft deletes included where required (`softDeletes deleted_at`)
- [ ] `company_id` foreign key present (multi-tenancy — see [[concept-multi-tenancy]])
- [ ] ULIDs used as primary keys
- [ ] `php artisan migrate` runs cleanly on fresh database
- [ ] `php artisan migrate:rollback` runs cleanly

---

## Model

- [ ] Model extends correct base class
- [ ] `SoftDeletes` trait applied
- [ ] `BelongsToCompany` scope applied (global scope for multi-tenancy)
- [ ] All relationships defined
- [ ] `$fillable` list complete
- [ ] Casts defined for JSON columns, dates, booleans
- [ ] DTO created (`app/DTOs/{Domain}/{Model}DTO.php`)

---

## Service / Interface

- [ ] Interface defined in `app/Interfaces/{Domain}/{Module}ServiceInterface.php`
- [ ] Service implements interface
- [ ] Service bound in ServiceProvider
- [ ] All CRUD operations implemented
- [ ] Events fired at correct points
- [ ] Validation in service (not in controller)

---

## Filament Resource

- [ ] Resource created in correct panel (`app/Filament/{Panel}/Resources/`)
- [ ] List page: columns, filters, search working
- [ ] Create form: all fields, correct validation messages
- [ ] Edit form: pre-populated, saves correctly
- [ ] View page: readable layout
- [ ] Bulk actions: delete (with soft delete check)
- [ ] Navigation icon and group set

---

## Events

- [ ] All events listed in Left Brain spec are implemented
- [ ] Events dispatch correctly (verify in logs)
- [ ] Listeners registered in EventServiceProvider
- [ ] Consumed events: listeners handle correctly

---

## Permissions

- [ ] All permission strings registered
- [ ] Filament resource respects permissions
- [ ] `php artisan permission:create` run for all new permissions

---

## Tests

- [ ] Unit test for service layer (key business logic)
- [ ] Feature test for Filament resource (CRUD happy path)
- [ ] Event dispatch tests
- [ ] `php artisan test --filter={Module}` passes

---

## Post-Build

- [ ] Left Brain spec updated to match any deviations made during build
- [ ] Builder log sessions section completed
- [ ] Any gaps found during build → gap file created from [[tpl_gap]]
- [ ] Any ADRs needed → decision file created from [[tpl_adr]]
- [ ] [[STATUS_Dashboard]] updated — module marked ✅
- [ ] Builder log archived to `right-brain/builder-logs/archive/`

---

## Related

- [[MOC_Validation]]
- [[ACTIVATION_GUIDE]]
- [[tpl_builder-log]]
