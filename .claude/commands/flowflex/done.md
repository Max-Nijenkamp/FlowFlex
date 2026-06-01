# /flowflex:done

Mark a module fully built and tested. Runs a completion checklist, then marks the spec complete and updates progress.

## Usage

```
/flowflex:done hr.leave
/flowflex:done finance.invoicing
```

## What This Does

### Step 1 — Run completion checklist

Before marking complete, verify with the user:

```
Completion checklist for {module-key}:

- [ ] Migrations run cleanly (php artisan migrate)?
- [ ] All feature tests pass (php artisan test)?
- [ ] Filament resource renders correctly (panel visited manually)?
- [ ] canAccess() returns correctly (permission + module check)?
- [ ] Events fire correctly where applicable?
- [ ] Emails queue correctly where applicable?
- [ ] Tenant isolation test passes (company A can't see company B data)?

Type 'yes' to confirm all pass, or describe any failures.
```

If failures described → create gap files for each via `/flowflex:bug`, keep status `in-progress`.

### Step 2 — Mark spec complete

Find `vault/domains/{domain}/{module}.md`. Update frontmatter:
```yaml
status: complete
```

### Step 3 — Update STATUS.md

Read `vault/build/STATUS.md`.
- Increment Built count for the domain row
- Recalculate Progress %
- Update emoji (✅ if 100%)
- Add row to Recent Sessions: `| {date} | {domain} | {module-key} | ✅ | Complete |`

Write STATUS.md back.

### Step 4 — Output

```
## ✅ Module Complete: {module-key}

Domain: {domain} | Panel: /{panel}
Status: complete
STATUS.md: {domain} Built = {n}/{total} ({%}%)

Next module in {domain}: {next planned module or "domain complete"}
```
