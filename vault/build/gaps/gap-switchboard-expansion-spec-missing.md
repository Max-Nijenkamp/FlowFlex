---
type: gap
severity: low
category: spec
status: open
domain: All
color: "#F97316"
discovered: 2026-06-12
discovered-in: frontend
---

# Switchboard+ §14–28 built without the regenerated design bundle

## Context

The founder's design assistant produced an expanded handoff (sections 1–28: module catalogue, switch-over, trust, changelog, 404, patchwork calculator, case study, status page, help center, first-run wizard, billing page, email templates, employee profile, fresh-module empty state). The regenerated zip never landed in the repo — `design_handoff_flowflex_site/` still holds the original 13-screen bundle.

## Problem

Sections 14–25 were designed and built in-system using the established Switchboard+ language. Layouts and copy are *(assumed)* — final-quality brand voice, but not the designer's pixel spec. Content lives centrally in `app/Support/Marketing/MarketingContent.php` (changelog, help articles, case studies) for an easy swap.

## Impact

Low — pages are consistent with the system and shippable. Risk: divergence from the designer's intended layouts/copy when the real bundle arrives.

## Proposed Solution

When the updated zip lands: diff each page against its artboard, swap copy in MarketingContent + page templates, and close this gap. Pages affected: Catalogue, SwitchOver, Trust, Changelog, Patchwork, CaseStudy, Status, Help/*, NotFound, mail theme.

## Update 2026-06-12 (evening)

**The updated bundle LANDED**: `vault/product/design_handoff_flowflex_site 2/` (212-line README §1–28 + `pages/extras-public.jsx`, `extras-app.jsx`, `extras2-*.jsx`). Panel-chrome items from §12 (panel-switcher chips, user card, topbar search/bell) + §27 cmdk + §28 empty-state are now implemented. **Remaining**: diff the in-system §14–25 pages (Catalogue, SwitchOver, Trust, Changelog, Patchwork, CaseStudy, Status, Help, 404, emails) against the extras artboards and reconcile layouts/copy — e.g. §14 wants domain filter pills, §17 wants a timeline rail + state pills, §21 wants an editable tool repeater + PDF capture CTA, §23 wants uptime tick bars.
