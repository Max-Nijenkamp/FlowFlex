---
type: gap
severity: medium
category: bug
status: resolved
color: "#F97316"
discovered: 2026-05-10
discovered_in: phase0-phase1-audit
last_updated: 2026-05-10
---

# Gap: DataImportService row numbers reset to 1 for every 100-row chunk

## Context

Found during Phase 0+1 full audit. `DataImportService::parseAndStoreRows()` uses `array_chunk($rows, 100)` then iterates over each chunk. Row numbers were derived from `array_keys($chunk)` which re-indexes from 0 for each chunk.

## The Problem

Row 101 would be stored with `row_number = 1`, row 201 with `row_number = 1`, etc. In a 300-row import:
- Rows 1–100: numbered 1–100 ✅
- Rows 101–200: numbered 1–100 ❌ (should be 101–200)
- Rows 201–300: numbered 1–100 ❌ (should be 201–300)

This breaks the error report UX — users would see "error on row 5" but row 5 in the UI is actually row 105 or 205 in their file.

## Resolution ✅

Added a running `$offset` counter: `$offset += count($chunk)` after each chunk. Row number = `$offset - count($chunk) + $index + 1`.

## Links

- Source builder log: [[core-platform-phase1]]
