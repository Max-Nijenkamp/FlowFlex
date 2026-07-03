---
domain: events
module: speakers
feature: speaker-submit
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Speaker Self-Submit

A signed-token public form for speakers to supply/update their bio and photo without an account.

## Behaviour

- The organizer shares the signed `submit_token` URL.
- Speaker opens the form → updates bio (sanitized) + photo (MIME-whitelisted, size-capped) → saved to their `ev_speakers` record.
- Invalid/expired token → 404. Rate-limited.

## UI

- **Kind**: public-vue
- **Page**: "Speaker Submit" (`/speakers/submit/{token}`) — Vue + Inertia, ui-strategy row #16.
- **Layout**: single-column form — bio editor, photo upload with preview, social links; submit + saved confirmation.
- **Key interactions**: upload photo (client preview) → save → success screen.
- **States**: empty (prefilled with current values) · loading (uploading) · error (invalid token 404 / bad file type / too large) · success (saved confirmation).
- **Gating**: signed-token guard (no login); rate-limited.

## Data

- Owns / writes: `ev_speakers` only (`bio`, `photo_media_id`, `social_links`).
- Reads: own speaker record via token.
- Cross-domain writes: NONE ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: updates the directory record used by assignments + landing.
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Signed token validation: expiry + speaker binding; bio purified

### Feature (Pest)
- [ ] Valid token updates bio/photo without auth; invalid/expired token -> 403/404; photo follows the upload contract (mime/size)
- [ ] Public endpoint rate-limited on guest guard *(assumed)*; token never grants access to other speakers

### Livewire
- (none -- public signed-token form)

## Unknowns

- Token expiry / revocation — see [[../unknowns]].

## Related

- [[../_module|Speakers]] · [[speaker-directory]]
