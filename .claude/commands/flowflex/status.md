# /flowflex:status

Show current build status across all domains. Quick read of STATUS_Dashboard with open gaps and recent decisions.

## Usage

```
/flowflex:status
/flowflex:status domain=hr
/flowflex:status full
```

## Arguments

- No args → summary view (all domains, % complete)
- `domain={slug}` → detailed view for one domain (list all modules with status)
- `full` → everything: domains + open gaps + recent decisions

## What this does

1. **Read** `flowflex-vault/right-brain/STATUS_Dashboard.md`
2. **Read** `flowflex-vault/right-brain/gaps/MOC_Gaps.md`
3. **Read** `flowflex-vault/right-brain/evolution/MOC_Evolution.md`

### Summary view (default)

```
## FlowFlex Build Status — 2026-05-09

| Domain           | Phase | Built | Total | Progress |
|------------------|-------|-------|-------|----------|
| Core Platform    | 1     | 0     | 12    | 📅 0%   |
| HR & People      | 2–8   | 1     | 19    | 🔄 5%   |
...

Overall: 1/210 modules built (0.5%)

Open gaps: 3 (1 high, 2 medium)
Recent decisions: 1
```

### Domain detail view

```
## HR & People — Build Status

✅ leave-management (phase 2)
📅 compensation-benefits (phase 3)
📅 org-chart-workforce-planning (phase 4)
...

Open gaps in HR:
- gap_leave-overlap-calculation.md (medium) — overlap calculation edge case
```

### Full view

Everything above plus:
- All open gaps with severity
- All decisions in evolution log
- Next recommended module to build (based on phase order and dependencies)
