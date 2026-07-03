---
domain: dms
module: retention-policies
feature: retention-policy
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Retention Policy

Define a rule that governs when documents in a folder subtree or with a given tag are archived or deleted.

## Behaviour

1. Create a policy via `CreateRetentionPolicyData`: `name`, `applies_to` (`type: folder|tag`, `id`), `retention_days` (min 1), `action` (`archive` / `delete`), `clock_from` (`created` / `modified`), `is_active`.
2. `applies_to = folder` matches the folder subtree; `applies_to = tag` matches tagged documents.
3. The retention clock for each document is measured from its `created` or `modified` date per `clock_from`; a document is "expired" once `now - clock_from ≥ retention_days`.
4. Policies are evaluated daily by the [[retention-run|Retention Run]]; this feature only defines them.
5. `is_active = false` pauses a policy without deleting it.
6. Statutory floors from [[../../../../architecture/data-lifecycle|data-lifecycle]] cap deletion policies — a policy that would delete below a statutory class warns at save *(assumed: warning at save)*.

## UI

- **Kind**: simple-resource (`RetentionPolicyResource`).
- **Page**: "Retention Policies" (`/dms/retention-policies`), nav group **Settings**.
- **Columns**: name · applies-to (folder/tag) · retention_days · action · clock_from · is_active.
- **Form**: name; applies-to type + id (folder tree-select / tag select); retention_days (min 1); action (archive/delete); clock_from (created/modified); is_active toggle. Optional **preview affected-count** *(assumed)*.
- **Filters**: action · is_active.
- **Row actions**: edit · delete (soft). **Bulk**: activate / deactivate.
- **States**: empty ("no policies yet — add your first" CTA) · error (toast).
- **Gating**: `dms.retention.manage-policies` + `BillingService::hasModule('dms.retention')`.

## Data

- Owns / writes: `dms_retention_policies` (this module).
- Reads: folders / tags owned by [[../../document-library/_module|dms.library]] (to populate applies-to selectors).
- Cross-domain writes: none — this feature only defines rules; document mutation happens in [[retention-run|Retention Run]] via `dms.library`'s service ([[../../../../security/data-ownership|data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: policies read by [[retention-run|Retention Run]] each night.
- Shared entity: folders / tags (owned by `dms.library`).

## Test Checklist

### Unit
- [ ] Expiry math: document is expired iff `now - clock_from ≥ retention_days`, for both `created` and `modified` clocks
- [ ] `CreateRetentionPolicyData` validation: retention_days min 1, action in archive|delete, applies_to type folder|tag

### Feature (Pest)
- [ ] Folder policy matches subtree documents only; tag policy matches tagged documents only
- [ ] `is_active = false` policy is ignored by the run; bulk activate/deactivate flips evaluation
- [ ] Tenant isolation: policies scoped by company; `dms.retention.manage-policies` required to create/edit

### Livewire
- [ ] `RetentionPolicyResource` form validates fields (retention_days min 1); statutory-floor warning surfaces on delete policies below floor *(assumed)*
- [ ] canAccess(): hidden without `dms.retention.manage-policies` or with `dms.retention` module inactive

## Unknowns

- Preview affected-count *(assumed)*.
- Statutory-floor enforcement: warn vs block — open ([[../unknowns]]).

## Related

- [[../_module|Retention Policies]] · [[retention-run]] · [[legal-hold]] · [[retention-audit-log]]
- [[../../document-library/_module|Document Library]] · [[../../../../architecture/data-lifecycle]]
