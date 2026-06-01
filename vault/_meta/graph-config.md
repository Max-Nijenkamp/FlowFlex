---
type: meta
color: "#6B7280"
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

### Step 2 — Add Color Groups

| Query | Color |
|---|---|
| `path:product/` | `#38BDF8` (Sky Blue) |
| `path:architecture/` | `#A78BFA` (Purple) |
| `path:domains/` | `#4ADE80` (Green) |
| `path:build/` | `#F97316` (Orange) |
| `path:frontend/` | `#FBBF24` (Amber) |
| `path:_meta/` | `#6B7280` (Gray) |

In the Groups panel, use the `path:` filter. The color picker in Obsidian uses hex — paste the hex values above.

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

## Folder Structure in Obsidian

Set Obsidian vault root to `vault/` (not the repo root `FlowFlex/`). This ensures internal links resolve correctly.

If opening the full repo root in Obsidian, all links like `[[domains/hr/_index]]` still resolve because Obsidian searches recursively. Exclude `app/` and `.claude/` via **Settings → Files & Links → Excluded files** to keep the graph focused on the vault.
