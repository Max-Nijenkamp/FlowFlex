Update the FlowFlex Brain after completing work. Only update what actually changed. Do not touch vision notes (obsidian/00-14/).

## Step 1 — Get current test count
Run: `XDEBUG_MODE=off php -d memory_limit=768M vendor/bin/pest --no-coverage 2>&1 | tail -3`
Note the passing count.

## Step 2 — Decide which Brain files need updating

Update `obsidian/_Brain/Current State.md` if ANY of:
- Test count changed
- New panel added or activated
- New model, resource, or route added
- Phase status changed (phase X → complete)
- New pending decision arose
- Phase 1.5 page list changed

Update `obsidian/_Brain/Bug Registry.md` if:
- Any bug was found and fixed during this session
- Add: bug description | root cause | exact fix applied
- File bugs under the correct phase section

Update `obsidian/_Brain/Codebase Map.md` if:
- New files were created: models, resources, factories, controllers, routes, Vue pages
- Update the relevant section with the new file path and purpose

Update `obsidian/_Brain/Patterns.md` if:
- A new enforced pattern was established
- An existing pattern was corrected or clarified
- A Filament 5 API gotcha was discovered

Update domain files (Domain — HR/Projects/Finance/CRM/Core Platform) if:
- New model added → document all fillable fields, casts, relations, and purpose
- New resource added → add to the Resources table with permissions and key features
- New enum added or enum values corrected
- New cross-domain FK added
- Existing model had fields or relations changed

Update `obsidian/_Brain/Relations Map.md` if:
- New foreign key added to any model
- New cross-domain relation established

Update `obsidian/_Brain/Test Suite.md` if:
- Test count changed (update header line)
- New test files added (add to structure)
- New pitfall discovered (add to pitfalls table)
- New factory states added

Update `obsidian/_Brain/Features.md` if:
- New user-facing feature added to any panel
- New API endpoint added
- New marketing site page added

## Step 3 — What NOT to update

- Do NOT touch `obsidian/00-14/` vision notes — those describe what FlowFlex should be, not what is
- Do NOT add "fixed on DATE" entries to vision notes
- Do NOT touch `obsidian/_Brain/Brain Index.md` unless a brand new Brain note file was created
- Do NOT update domain files that were not touched in this session
- Do NOT overwrite existing correct information — only add/correct what changed

## Step 4 — Verify

After updating, re-read the files you changed to confirm the information is accurate and internally consistent. Check that test count in Current State matches the actual pest output.
