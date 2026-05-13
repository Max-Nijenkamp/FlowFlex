---
type: build
category: activation
color: "#F97316"
---

# How to Start a Build Session

Step-by-step guide for starting, running, and completing a module build. Follow these steps in order every session.

---

## Before You Start

Read these files in order before writing any code:

1. [[build/STATUS]] — identify what domain and module is next to build
2. [[domains/{domain}/INDEX]] — understand the full domain context and module list
3. [[domains/{domain}/{module}]] — read the module spec in full (data model, features, permissions)
4. [[architecture/filament-patterns]] — critical Filament 5 patterns; do not skip this
5. [[architecture/tech-stack]] — confirm PHP, Laravel, Filament, and package versions
6. [[architecture/patterns/belongs-to-company]] — required BelongsToCompany trait and CompanyScope
7. [[architecture/patterns/interface-service]] — Interface → ServiceProvider → Service pattern

---

## During the Build

Create files in this order. Each step depends on the previous.

1. **Migration** — ULID primary key, `company_id` foreign key, domain-specific columns, `timestamps()`, `softDeletes()`
2. **Model** — extends `Model`, uses `HasUlids`, `BelongsToCompany`, `SoftDeletes` traits; defines `$fillable`, relationships, and casts
3. **Factory** — uses `fake()` for realistic data; always sets `company_id` from the seeder context
4. **Contract interface** — `app/Contracts/{Domain}/{Model}ServiceInterface.php` — defines the public service API
5. **Service implementation** — `app/Services/{Domain}/{Model}Service.php` — implements the interface, uses the repository pattern if complex
6. **ServiceProvider binding** — `app/Providers/{Domain}/{Domain}ServiceProvider.php` — binds interface to concrete implementation in `register()`
7. **Filament Resource** — `App\Filament\App\Resources\{Model}Resource.php` — includes `canAccess()` checking module activation, ListRecords / CreateRecord / EditRecord pages
8. **Test file** — `tests/Feature/{Domain}/{Model}Test.php` — covers list, create, update, delete, and permission boundary

---

## After the Build

1. Create a build log at `build/logs/{domain}-{YYYY-MM-DD}.md` using [[_meta/templates/tpl_build-log]]
2. Update [[build/STATUS]] — increment Built count for the domain, recalculate %, update emoji, update `last-updated`, add row to Recent Sessions
3. If bugs or spec gaps found: create `build/gaps/gap-{slug}.md` using [[_meta/templates/tpl_gap]], add row to [[build/gaps/INDEX]]
4. If an architectural decision was made: create `build/decisions/adr-{YYYY-MM-DD}-{slug}.md` using [[_meta/templates/tpl_adr]], add row to [[build/decisions/INDEX]]
5. Update the module spec frontmatter: set `status: complete` and `last-updated: YYYY-MM-DD`

---

## Key Commands (Docker)

Run all artisan commands via Docker exec — never run artisan directly on the host.

```bash
# Run migrations
docker exec flowflex_app php artisan migrate

# Run tests for a specific domain
docker exec flowflex_app php artisan test tests/Feature/{Domain}/ --no-coverage

# Run a single test file
docker exec flowflex_app php artisan test tests/Feature/{Domain}/{Model}Test.php --no-coverage

# Rebuild Vite assets (required after adding a new panel entry point)
docker exec flowflex_app npm run build

# Clear all caches
docker exec flowflex_app php artisan route:clear && php artisan cache:clear && php artisan config:clear && php artisan view:clear

# Refresh and seed local data
docker exec flowflex_app php artisan migrate:fresh --seed --seeder=LocalDemoDataSeeder
```

---

## Common Mistakes to Avoid

- **Forgetting `company_id`** on a migration — every table that holds tenant data needs this column and the `CompanyScope` global scope on the model
- **Not binding the interface** in a ServiceProvider — the container will throw a resolution error at runtime
- **Missing `canAccess()` on a Resource** — modules must check the tenant's activated module list before rendering
- **Running artisan on the host** — always use `docker exec flowflex_app`
- **Not seeding demo data** — every new domain phase must add a section to `LocalDemoDataSeeder` with realistic data for the FlowFlex Demo company

---

## Related

- [[build/STATUS]] — current progress
- [[build/gaps/INDEX]] — open gaps
- [[build/decisions/INDEX]] — architectural decisions
- [[_meta/templates/tpl_build-log]] — build log template
- [[_meta/templates/tpl_gap]] — gap file template
- [[_meta/templates/tpl_adr]] — ADR template
- [[architecture/filament-patterns]] — Filament 5 conventions
- [[architecture/tech-stack]] — stack versions
