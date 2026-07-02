---
domain: marketing
module: forms
feature: form-builder
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Form Builder

Compose a lead-capture form from typed fields and configure what happens on submit.

## Behaviour

- Add fields from a registry: text, email, phone, select, checkbox, textarea, consent-checkbox. Exactly one email field is mandatory.
- Per-field: key (unique), label, required flag, options (for select).
- Configure `submit_action`: enrol in a sequence, notify users; set redirect or thank-you message.

## UI

- **Kind**: simple-resource
- **Page**: `FormResource` (`/marketing/forms`) — Forms nav group.
- **Layout**: table (name, slug, submissions, active) + form (field **repeater** + submit-action panel + embed-code copy box).
- **Key interactions**: add/reorder fields in the repeater; set submit action; copy embed snippet; submissions relation tab.
- **States**: empty (no forms → CTA) · loading · error (missing email field; duplicate keys) · selected (field row editing).
- **Gating**: `marketing.forms.create` / `marketing.forms.update`.

## Data

- Owns / writes: `mkt_forms` (own module).
- Reads: sequence list (for enrol action), user list (for notify) — read-only.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Reads: sequences from [[../../email-sequences/_module|marketing.sequences]] (enrol config).
- Feeds: definition consumed by [[embed-hosted]] + [[public-submit]].
- Shared entity: none written.

## Unknowns

- Conditional fields + file-upload field deferred. See [[../unknowns]].

## Related

- [[../_module|Forms]] · [[embed-hosted]] · [[public-submit]]
