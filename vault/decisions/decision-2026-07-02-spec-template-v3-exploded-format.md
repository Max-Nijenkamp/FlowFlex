---
type: adr
date: 2026-07-02
status: decided
domain: All
color: "#F97316"
---

# Spec Template v3 — Codify the Exploded-Folder Format, Per-Feature Test Checklists, Concurrency Notes

## Context

[[_meta/spec-template]] ("v2", frozen 2026-06-11) describes a **monolithic single-file spec** — but reality moved on. The 2026-06-20 rebuild ([[decisions/decision-2026-06-20-full-mapping-conventions]]) exploded every module into a folder (`_module.md` + `architecture.md` + `api.md` + `security.md` + `data-model.md` + `features/*.md` + `decisions.md`/`unknowns.md`). All 172 active modules use the exploded format; the frozen template no longer matches any spec, including its own two "golden specs". The 2026-07-02 audit also found:

- 3 divergent `_module.md` metadata styles and 2 `_index.md` styles across domains.
- 52 modules with no `## Filament` heading anywhere in their folder (finance 13/13, legal 6/6, core 16/20, hr 9/15, foundation 7/8, ai 1/4).
- ~527 `features/*.md` files with **zero** test checklists; ~13 modules missing the module-level checklist.
- No concurrency declaration anywhere (see [[decisions/decision-2026-07-02-optimistic-locking-standard]]).

The template's frozen status requires an ADR to change. This is that ADR.

## Options Considered

1. **Rewrite the template to describe the exploded format (v3) and backfill via propagation waves.** Chosen.
2. **Collapse specs back to monolithic v2 files.** Rejected — the exploded format is what enabled per-feature depth; collapsing loses it and touches ~1400 files for zero content gain.
3. **Leave the template stale, document conventions ad hoc.** Rejected — 23 propagation workers and every future session need one authoritative contract.

## Decision

1. **Template v3** rewrites [[_meta/spec-template]] to describe the exploded folder: which section lives in which file, per-file mandatory headings, and the `_module.md` hub role. Golden spec: [[domains/crm/deals/_module]].
2. **One `_module.md` metadata style** — the golden bold-label style (`## Module-key` heading, `**Priority:** / **Panel:** / **Permission prefix:** / **Tables:**` lines). Table-style and inline-bullet-style hubs are migrated; **migration never deletes content**, only restructures.
3. **`## Filament Artifacts` is mandatory in every module's `architecture.md`** — every artifact cites its [[architecture/ui-strategy]] row #, custom pages cite their [[architecture/patterns/page-blueprints]] kind, resources cite named tweaks from the ui-strategy Resource Tweak Taxonomy. Backend-only modules state `**Filament Artifacts:** None (backend module)` explicitly — absence is no longer legal.
4. **`## Concurrency` is mandatory in every module's `architecture.md`** — declares optimistic / pessimistic / document-lock / n-a per write path, per [[decisions/decision-2026-07-02-optimistic-locking-standard]].
5. **Per-feature Test Checklists** — every `features/*.md` gains a `## Test Checklist` with `### Unit`, `### Feature`, and (when the feature has UI) `### Livewire` subsections. `_module.md` keeps the **rollup** checklist whose first two lines are always tenant isolation + module gating. [[_meta/feature-template]] updated accordingly.
6. **Rich `_index.md` style** — every domain index carries frontmatter (`domain-key`, `panel`, `phase`, `module-count`) and a module table with a **Kind highlights** column (the [[domains/projects/_index]] style).
7. The v2 conventions that still hold are carried into v3 unchanged: `*(assumed)*` marker, verbatim migration, event payload exactness, money/phone/encryption rules, and the security contract from [[decisions/decision-2026-06-11-security-contract-hardening]].

## Consequences

- [[_meta/spec-template]] rewritten (v3, frozen again — next change needs an ADR); [[_meta/feature-template]] gains the Test Checklist skeleton.
- Backfill: 2026-07 propagation waves normalise all 21 domains (batched, grep-gated, committed per batch).
- `_meta/module-graph.md` and `domain-panels.md` are refreshed at the end of the propagation program; a generated [[_meta/artifact-registry]] becomes the module→artifact source of truth.
- CLAUDE.md updated: stale `build/decisions/` and `build/STATUS.md` paths corrected (ADRs live at `decisions/`, status at [[00-index/status-board]]).

## Related

- [[_meta/spec-template]] · [[_meta/feature-template]]
- [[decisions/decision-2026-06-20-full-mapping-conventions]]
- [[decisions/decision-2026-07-02-optimistic-locking-standard]]
- [[decisions/decision-2026-06-11-security-contract-hardening]]
