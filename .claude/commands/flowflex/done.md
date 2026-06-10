# /flowflex:done

Mark a module fully built and tested. Runs a completion checklist, then marks the spec complete and updates progress.

## Usage

```
/flowflex:done hr.leave
/flowflex:done finance.invoicing
```

## What This Does

### Step 0 — Hard-dependency gate

Read the spec's `depends-on:` frontmatter. If any hard dependency is not `status: complete`, **refuse completion**: a module cannot be done while its hard deps aren't. Report which dep blocks.

### Step 1 — Run completion checklist (Definition of Done)

The full DoD lives in `vault/architecture/way-of-working.md` — apply it. Verify with the user:

```
Completion checklist for {module-key}:

- [ ] Every file in the spec's ## Build Manifest exists?
- [ ] Spec ## Test Checklist: every box covered by a passing test?
- [ ] Quality gates green (Pint, Larastan, php artisan test)?
- [ ] migrate:fresh --seed runs clean?
- [ ] canAccess() on every resource/page (permission + module check)?
- [ ] Events fired match spec fires-events; listeners queued + WithCompanyContext?
- [ ] Tenant isolation test passes (company A can't see company B data)?
- [ ] No open high-severity gap against this module?
- [ ] Spec updated to match what was actually built?

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
