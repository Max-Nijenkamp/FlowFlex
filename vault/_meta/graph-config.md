---
type: meta
color: "#6B7280"
---

# Obsidian Graph Color Config

Set these in Obsidian → Settings → Graph → Groups. Use the `color:` frontmatter field on every file to drive node coloring.

| Section | Display color | Hex | Frontmatter value |
|---|---|---|---|
| `product/` | Sky blue | `#38BDF8` | `color: "#38BDF8"` |
| `architecture/` | Purple | `#A78BFA` | `color: "#A78BFA"` |
| `domains/` | Green | `#4ADE80` | `color: "#4ADE80"` |
| `build/` | Orange | `#F97316` | `color: "#F97316"` |
| `_meta/` | Gray | `#6B7280` | `color: "#6B7280"` |
| `frontend/` | Amber | `#FBBF24` | `color: "#FBBF24"` |

## Graph Filter Recommendations

- Hide `_meta/` nodes for cleaner view
- Enable "existing files only" links
- Set node size = degree (more links = bigger node)
- Group filter: `path:product` → Sky blue, `path:architecture` → Purple, `path:domains` → Green, `path:build` → Orange
