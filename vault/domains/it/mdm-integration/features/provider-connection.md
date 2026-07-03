---
domain: it
module: mdm-integration
feature: provider-connection
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Provider Connection

Connect the company to an MDM provider (Jamf / Intune / Kandji) by entering credentials, which are verified against the provider before being stored 🔐 encrypted.

## Behaviour

- One connection per company (`it_mdm_config.company_id` is unique).
- Admin picks a provider, enters `api_key` (+ `instance_url` *(assumed)*).
- On save, `ConnectMdmData` **verifies the credentials against the provider** — an invalid key is rejected at connect, nothing is stored.
- On success the `api_key` is stored with the **`encrypted` cast** on a `text` column and is **never re-displayed** — the form shows a masked "connected" state; changing the key means re-entering it.

## UI

- **Kind**: custom-page — verify-then-save credential form, not CRUD ([[../../../../architecture/patterns/custom-pages]]).
- **Page**: `MdmConfigPage` at `/app/it/mdm/config` (custom Filament page, form schema).
- **Layout**: provider select + `api_key` (masked, write-only) + `instance_url`; a "connected" status block once configured; "Test connection / Save" action.
- **Key interactions**: submit → verify against provider → on success store encrypted + set `last_synced_at` baseline; on failure show validation error, store nothing.
- **States**: empty (not connected → provider picker + credential fields) · loading (verifying against provider) · error (invalid credentials → rejected inline) · selected (connected → masked key, "reconnect" to re-enter).
- **Gating**: `it.mdm.view-any` to see the page; `it.mdm.manage-config` to submit.

## Data

- Owns / writes: `it_mdm_config` (provider, encrypted `api_key`, `instance_url`, `last_synced_at`) only.
- Reads: nothing cross-domain.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: the stored config drives [[device-sync]] (which provider + credentials to pull with).
- Shared entity: none.

## Test Checklist

### Unit
- [ ] `ConnectMdmData` validation: provider in supported set, api_key required

### Feature (Pest)
- [ ] Invalid credentials rejected at connect -- nothing stored; valid credentials stored with `encrypted` cast on text column
- [ ] One connection per company enforced (unique company_id); re-connect replaces, never duplicates
- [ ] Tenant isolation + permission: `it.mdm.manage-config` required to submit

### Livewire
- [ ] `MdmConfigPage` canAccess() explicit: hidden without `it.mdm.view-any` or module inactive; connected state masks the key and never re-displays it

## Unknowns

> [!warning] UNVERIFIED
> `instance_url` is `*(assumed)*` — required per provider vs. optional, and its validation, is unconfirmed. See [[../unknowns|mdm.unknowns]] #1.

## Related

- [[../_module|MDM Integration]] · [[device-sync]] · [[../security|mdm.security]] · [[../../../../architecture/patterns/encryption]]
