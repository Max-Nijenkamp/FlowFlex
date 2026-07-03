---
domain: communications
module: whatsapp
feature: template-management
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Template Management

Create WhatsApp message templates, submit them to the provider for approval, and track approval status locally.

## Behaviour

- Create a `draft` template: name (provider regex), category (marketing / utility / authentication), language, body with `{{n}}` placeholders, sample variables.
- `SubmitTemplateAction` submits a draft to the provider → status `pending`, records `external_template_id`.
- `SyncTemplateStatusJob` polls hourly, upserting `pending → approved / rejected` by `external_template_id`.
- Only `approved` templates are selectable for sending.

## UI

- **Kind**: simple-resource
- **Page**: `WhatsAppTemplateResource` (`/comms/whatsapp/templates`) — Settings nav group.
- **Layout**: table (name, category, language, approval-status badge) + form (body with placeholder editor, variables repeater).
- **Key interactions**: create draft → "Submit for approval" row action → badge tracks status; rejected shows reason.
- **States**: empty (no templates → CTA) · loading · error (submit rejected by provider) · selected (row highlighted).
- **Gating**: `comms.whatsapp.manage-templates`.

## Data

- Owns / writes: `comms_whatsapp_templates` (own module).
- Reads: provider template API (status).
- Cross-domain writes: none.

## Relations

- Consumes: nothing internal.
- Feeds: approved templates used by [[../../broadcast/_module|comms.broadcast]] (soft) and by outbound sends outside the 24h window ([[window-sending]]).
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Template name validated against the provider regex; category/language required
- [ ] Only `approved` templates are selectable for sending

### Feature (Pest)
- [ ] `SubmitTemplateAction` submits a draft, sets status `pending`, records `external_template_id` (`Http::fake`)
- [ ] `SyncTemplateStatusJob` upserts `pending → approved / rejected` by `external_template_id`
- [ ] Tenant isolation: a template is never visible/submittable across companies

### Livewire
- [ ] "Submit for approval" row action transitions the badge; denied without `comms.whatsapp.manage-templates`

## Related

- [[../_module|WhatsApp]] · [[window-sending]] · [[../architecture]]
