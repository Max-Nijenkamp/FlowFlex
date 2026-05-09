# /flowflex:done

Mark a module as fully complete. Fast path — runs `/flowflex:sync` with status=complete and prompts for any final notes.

## Usage

```
/flowflex:done
/flowflex:done module=leave-management
/flowflex:done module=leave-management domain=hr
```

## What this does

1. Identifies the module from args or conversation context
2. Runs the full `/flowflex:sync` flow with `status: complete`
3. Additionally runs the post-build checklist from `flowflex-vault/right-brain/validation/build-checklist.md` — ask the user to confirm each section passed before marking complete
4. Sets left-brain spec `status: complete`
5. Updates STATUS_Dashboard: increments Built count, recalculates %
6. Moves builder log status to `complete`
7. Archives the builder log reference in `right-brain/builder-logs/archive/` (by updating the log's `status: complete` frontmatter — do NOT move the file, just update status)

## Post-build checklist prompt

Before marking complete, ask:
> "Quick checklist before marking complete — did you verify:
> - [ ] Migrations run cleanly?
> - [ ] All tests pass?
> - [ ] Filament resource renders correctly?
> - [ ] Events fire correctly?
> - [ ] Permissions registered?
>
> Type 'yes' to confirm all pass, or describe any failures."

If failures described → create gap files for each, mark module `in-progress` not `complete`.

## Output

```
## ✅ Module Complete: {module-name}

Domain: {domain}
Left-brain status: complete
Builder log: right-brain/builder-logs/{module-name}.md (status: complete)
STATUS_Dashboard: {domain} Built = {n}/{total} ({%}%)
```
