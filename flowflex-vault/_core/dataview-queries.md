---
type: meta
status: stable
last_updated: 2026-05-09
---

# Dataview Query Library

Pre-built queries for the FlowFlex vault. Requires **Dataview** plugin installed.

> Install: Settings → Community Plugins → Browse → search "Dataview" → Install → Enable

---

## Dynamic Status Dashboard

Replace the manual STATUS_Dashboard table with this live query:

```dataview
TABLE
  length(rows) AS "Total Modules",
  min(rows.phase) AS "First Phase"
FROM "left-brain/domains"
WHERE type = "module"
GROUP BY domain
SORT domain ASC
```

---

## All Modules by Phase

```dataview
TABLE domain, status, phase
FROM "left-brain/domains"
WHERE type = "module"
SORT phase ASC, domain ASC
```

---

## Phase 1 Modules (build first)

```dataview
TABLE domain, file.name AS "Module"
FROM "left-brain/domains"
WHERE type = "module" AND phase = 1
SORT domain ASC
```

---

## Phase 2 Modules

```dataview
TABLE domain, file.name AS "Module"
FROM "left-brain/domains"
WHERE type = "module" AND phase = 2
SORT domain ASC
```

---

## All Modules in a Specific Domain

Change `"02_hr"` to any domain folder:

```dataview
TABLE phase, status, migration_range
FROM "left-brain/domains/02_hr"
WHERE type = "module"
SORT phase ASC
```

---

## Modules Grouped by Domain (with count)

```dataview
TABLE length(rows) AS "Modules", rows.phase AS "Phases"
FROM "left-brain/domains"
WHERE type = "module"
GROUP BY domain
SORT domain ASC
```

---

## All Architecture Notes

```dataview
TABLE file.mtime AS "Last Updated"
FROM "left-brain/architecture"
WHERE type = "architecture-note" OR type = "architecture"
SORT file.mtime DESC
```

---

## Recently Updated Files

```dataview
TABLE file.folder AS "Location", last_updated
FROM "left-brain"
WHERE last_updated
SORT last_updated DESC
LIMIT 20
```

---

## All Entity Files

```dataview
TABLE domain, table AS "DB Table"
FROM "left-brain/entities"
WHERE type = "entity"
SORT file.name ASC
```

---

## Module Count Per Domain (inline)

Use in any note body:

```dataviewjs
const modules = dv.pages('"left-brain/domains"').where(p => p.type === "module");
const byDomain = {};
for (const m of modules) {
  const d = m.domain || "Unknown";
  byDomain[d] = (byDomain[d] || 0) + 1;
}
const rows = Object.entries(byDomain).sort((a,b) => a[0].localeCompare(b[0]));
dv.table(["Domain", "Modules"], rows);
```

---

## Progress Tracker (built/total)

When you start marking modules as `status: in-progress` or `status: complete`, this query auto-updates:

```dataviewjs
const all = dv.pages('"left-brain/domains"').where(p => p.type === "module");
const built = all.where(p => p.status === "complete" || p.status === "in-progress");
const total = all.length;
const done = built.filter(p => p.status === "complete").length;
const pct = total > 0 ? Math.round((done / total) * 100) : 0;

dv.paragraph(`**Build Progress:** ${done} / ${total} modules complete (${pct}%)`);
dv.paragraph(`**In Progress:** ${built.filter(p => p.status === "in-progress").length}`);
```

---

## Find All Files Linking to a Specific Entity

Example: all files that reference `entity-contact`:

```dataview
LIST
FROM [[entity-contact]]
SORT file.path ASC
```

---

## Notes Updated Today

```dataview
LIST file.mtime
FROM "left-brain"
WHERE date(file.mtime) = date(today)
SORT file.mtime DESC
```
