---
type: meta
color: "#6B7280"
updated: 2026-07-02
---

# Obsidian Graph Configuration

How to configure the Obsidian graph view to show FlowFlex vault colors by section.

---

## Plugin Required: Dataview (recommended)

Install from Obsidian Community Plugins: **Dataview**. Enables the auto-updating STATUS board.

## Graph Colors Setup

Obsidian graph colors are driven by the `color:` frontmatter field on each note, combined with graph color groups in **Settings → Graph → Groups**.

### Step 1 — Open Graph Color Settings

`Settings → Graph view → Groups` → click `+` to add each group below.

### Step 2 — Color Groups (pre-seeded in `.obsidian/graph.json` — 2026-07-02)

The groups below are already written into `vault/.obsidian/graph.json`, so a fresh checkout gets them automatically. **Order matters** — Obsidian applies the first matching group, so per-domain queries come before the generic `path:domains/` fallback.

**Per-domain** (one color per domain — colors from the Switchboard+ domain set in [[frontend/design-system]]; analytics/support swapped to the domain-panels variant to avoid clashing with the product/build section colors):

| Query | Color | | Query | Color |
|---|---|---|---|---|
| `path:domains/hr/` | `#8B5CF6` Violet | | `path:domains/legal/` | `#F59E0B` Amber |
| `path:domains/finance/` | `#10B981` Emerald | | `path:domains/ecommerce/` | `#14B8A6` Teal |
| `path:domains/crm/` | `#F43F5E` Rose | | `path:domains/lms/` | `#22C55E` Green |
| `path:domains/projects/` | `#6366F1` Indigo | | `path:domains/ai/` | `#818CF8` Light indigo |
| `path:domains/communications/` | `#3B82F6` Blue | | `path:domains/workplace/` | `#84CC16` Lime |
| `path:domains/support/` | `#EA580C` Orange | | `path:domains/events/` | `#FB7185` Light rose |
| `path:domains/dms/` | `#64748B` Slate | | `path:domains/customer-success/` | `#E11D48` Deep rose |
| `path:domains/marketing/` | `#EC4899` Pink | | `path:domains/procurement/` | `#C2410C` Deep orange |
| `path:domains/operations/` | `#FB923C` Light orange | | `path:domains/core/` | `#94A3B8` Light slate |
| `path:domains/analytics/` | `#0284C7` Sky | | `path:domains/foundation/` | `#334155` Dark slate |

**Sections** (after the domain rows):

| Query | Color |
|---|---|
| `path:domains/` | `#4ADE80` (Green — deferred domain stubs fallback) |
| `path:product/` | `#38BDF8` (Sky Blue) |
| `path:architecture/` | `#A78BFA` (Purple) |
| `path:frontend/` | `#FBBF24` (Amber) |
| `path:decisions/` + `path:build/` | `#F97316` (Orange) |
| `path:security/` | `#EF4444` (Red) |
| `path:_meta/` + `path:00-index/` | `#6B7280` (Gray) |

To add/adjust manually: `Settings → Graph view → Groups` — the color picker takes the hex values above. Note the per-file `color:` frontmatter convention (domains `#4ADE80`, build/decisions `#F97316`) is unchanged — the graph groups are the visual layer, frontmatter stays uniform per section.

---

## Recommended Graph Settings

| Setting | Value | Reason |
|---|---|---|
| Node size | `5` | Prevents large nodes from overlapping |
| Link length | `150` | Spreads the graph without making it too sparse |
| Repel force | `10` | Prevents node clustering |
| Show orphans | Off | Hides unlinked notes (reduces noise) |
| Show attachments | Off | Hides images and binary files |
| Depth | `2` | Shows 2 hops from the active file |

---

## Recommended Obsidian Plugins

| Plugin | Purpose |
|---|---|
| Dataview | Auto-updating tables from frontmatter (used in STATUS.md) |
| Templater | Template insertion (not needed — templates are in CLAUDE.md) |
| Calendar | Visual calendar for dated notes (optional) |
| Advanced Tables | Table editing shortcuts (useful for module specs) |

---

## v2 Frontmatter — Dataview Queries

The v2 spec frontmatter ([[_meta/spec-template]]) adds structured fields that Dataview can query directly:

| Field | Example query use |
|---|---|
| `priority` | `WHERE priority = "v1-core"` — what blocks the v1 gate |
| `depends-on` | `WHERE contains(depends-on, "hr.profiles")` — reverse dependency lookup |
| `fires-events` / `consumes-events` | event producer/consumer maps |
| `patterns` | `WHERE contains(patterns, "states")` — which modules use state machines |
| `tables` | find the module owning a table |
| `last-reviewed` | staleness audits |

Pre-built boards: [[00-index/status-board]] (status + priority) and [[_meta/module-graph]] (full dependency table — also maintained as a static table so AI agents get it without Dataview).

---

## Folder Structure in Obsidian

Set Obsidian vault root to `vault/` (not the repo root `FlowFlex/`). This ensures internal links resolve correctly.

If opening the full repo root in Obsidian, all links like `[[domains/hr/_index]]` still resolve because Obsidian searches recursively. Exclude `app/` and `.claude/` via **Settings → Files & Links → Excluded files** to keep the graph focused on the vault.
