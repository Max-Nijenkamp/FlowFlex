---
domain: crm
module: quotes
feature: versioning
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Quote Versioning

Create a new version of an existing quote; the prior version is **locked** (read-only, audit trail).

- New version inherits line items; edits apply to the new version only.
- Old versions remain viewable for history; only one active version at a time.

## UI
- **Kind**: simple-resource — version behaviour on the quote resource (a version list on the quote view/infolist).
- **Page**: `QuoteResource` view page; "New version" action + a version-history relation/repeatable in the infolist.
- **Layout**: quote view showing the active version editable, with a read-only list of prior locked versions (version no., created-at, status).
- **Key interactions**: "New version" clones line items into a new version and locks the old; opening a locked version is read-only.
- **States**: empty (single/original version) · loading (creating new version) · error (blocked — e.g. version created off an accepted quote) · selected (viewing a locked prior version)
- **Gating**: `crm.quotes.update` to create a version; `crm.quotes.view` to read history.

## Data
- Owns / writes: `crm_quotes`, `crm_quote_lines` — a new version is a new quote record (or version group) with cloned lines; the prior version row is flagged locked/read-only. *(assumed: versioning modelled on `crm_quotes` via a `version`/`parent_quote_id` column; no separate versions table)*
- Reads: `crm_quotes`, `crm_quote_lines` (self).
- Cross-domain writes: via events only ([[../../../../security/data-ownership]]).

## Relations
- Consumes: nothing cross-domain.
- Feeds: nothing cross-domain — versioning is internal to quotes.
- Shared entity: the originating deal (`crm_deals`, owned by [[../../deals/_module|crm.deals]]) — only one open quote version per deal *(assumed)*, read-only here.

## Test Checklist

### Unit
- [ ] New version clones line items into a fresh version and flags the prior version locked / read-only
- [ ] Only one open version per deal enforced *(assumed)*

### Feature (Pest)
- [ ] "New version" locks the prior version; a locked version rejects edits
- [ ] A version created off an accepted quote is blocked
- [ ] Tenant isolation: the version action only touches the company's own quote

### Livewire
- [ ] "New version" action creates + opens the new draft; requires `crm.quotes.update`; denied without it
- [ ] Locked prior versions render read-only in the version-history infolist

## Related

- [[../_module|Quotes]] · [[pdf-generation]] · [[public-acceptance]]
