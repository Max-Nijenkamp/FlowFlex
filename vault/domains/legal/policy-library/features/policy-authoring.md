---
domain: legal
module: policy-library
feature: policy-authoring
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Policy Authoring

Draft and maintain a policy: title, category, rich-text body, effective date, and review cycle.

## Behaviour

- Rich text body edited with Tiptap, purified on save (htmlpurifier).
- Category free-text/set (privacy, security, HR, code of conduct).
- `review_date` flags policies due for periodic review.
- Draft policies are not visible to employees until [[./publication-versioning|published]].
- Optional link to a compliance control ([[../../compliance-registers/_module|legal.compliance]]).

## UI

- **Kind**: simple-resource
- **Page**: `PolicyResource` — list + create/edit at `/legal/policies`.
- **Layout**: table (title, category, version, status, effective/review dates); form = title/category + Tiptap body + dates + audience picker; publish/version actions.
- **Key interactions**: edit body (Tiptap); set audience; save draft; trigger publish (delegates to publication feature); review-due badge.
- **States**: empty ("Create your first policy") · loading (skeleton) · error (validation) · selected (row → edit).
- **Gating**: view `legal.policies.view-any`; create/edit `legal.policies.create`.

## Data

- Owns / writes: `legal_policies`.
- Reads: `hr.profiles` departments for the audience picker; `legal.compliance` controls for linking (read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: published policies drive acknowledgements.
- Shared entity: `hr` departments (owned by hr.profiles).

## Test Checklist

### Unit
- [ ] Body is purified on save — script tags stripped by htmlpurifier
- [ ] `review_date` in the past flags the policy review-due

### Feature (Pest)
- [ ] Draft policy invisible to employee self-service until published
- [ ] Compliance-control link resolves read-only; audience departments read from hr.profiles

### Livewire
- [ ] `PolicyResource` form validates title/category; Tiptap body persists purified
- [ ] Denied without `legal.policies.create`

## Unknowns

- `*(assumed)*` multi-language not modelled — [[../unknowns]].

## Related

- [[../_module|Policy Library]] · [[./publication-versioning]] · [[./acknowledgement-tracking]]
