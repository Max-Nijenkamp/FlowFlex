#!/bin/bash
# Brain context injector — runs on every UserPromptSubmit
# Outputs a compact brain summary so Claude always has current state in context.
# Output appears as <system-reminder> in the conversation.

BRAIN="obsidian/_Brain"
PROJECT_DIR="${CLAUDE_PROJECT_DIR:-.}"
BRAIN_DIR="${PROJECT_DIR}/${BRAIN}"

if [ ! -d "$BRAIN_DIR" ]; then
  exit 0
fi

# ── Current State snapshot ──────────────────────────────────────────────────
CURRENT_STATE="${BRAIN_DIR}/Current State.md"
if [ -f "$CURRENT_STATE" ]; then
  # Extract build status table lines and test count
  BUILD_STATUS=$(grep -A 8 "## Build Status" "$CURRENT_STATE" 2>/dev/null | head -12)
  TEST_LINE=$(grep "Test suite:" "$CURRENT_STATE" 2>/dev/null | head -1)
  PENDING=$(grep -A 5 "## Pending Design" "$CURRENT_STATE" 2>/dev/null | grep "^-" | head -3)
fi

# ── Critical pattern reminders ───────────────────────────────────────────────
PATTERNS="${BRAIN_DIR}/Patterns.md"
if [ -f "$PATTERNS" ]; then
  # Pull just the model traits line and the key traps
  TRAIT_LINE=$(grep "BelongsToCompany, HasUlids, LogsActivity, SoftDeletes" "$PATTERNS" 2>/dev/null | head -1)
  FILAMENT_LINE=$(grep "Filament\\\\Schemas\\\\Schema" "$PATTERNS" 2>/dev/null | head -1)
  TENANT_TRAP=$(grep "Tenant has no" "$PATTERNS" 2>/dev/null | head -1)
fi

# ── Most recent bug phase heading ────────────────────────────────────────────
BUG_REG="${BRAIN_DIR}/Bug Registry.md"
if [ -f "$BUG_REG" ]; then
  LAST_BUGS=$(grep -A 15 "## Phase 3 Gap-Fill" "$BUG_REG" 2>/dev/null | grep "^|" | head -6)
fi

# ── Output ───────────────────────────────────────────────────────────────────
echo "FLOWFLEX BRAIN CONTEXT"
echo "======================"
echo ""

if [ -n "$BUILD_STATUS" ]; then
  echo "## Build Status"
  echo "$BUILD_STATUS"
  echo ""
fi

if [ -n "$TEST_LINE" ]; then
  echo "$TEST_LINE"
  echo "Run: XDEBUG_MODE=off php -d memory_limit=768M vendor/bin/pest --no-coverage"
  echo ""
fi

echo "## Non-Negotiable Patterns"
echo "- All models: BelongsToCompany + HasUlids + SoftDeletes + LogsActivity"
echo "- Filament 5: use Filament\\Schemas\\Schema (NOT Filament\\Forms\\Form)"
echo "- Spatie: Models\\Concerns\\LogsActivity + Support\\LogOptions (NOT root namespace)"
echo "- Tenant has NO BelongsToCompany — always scope tenant dropdowns to company_id manually"
echo "- API controllers: \$request->attributes->get('api_company') — NEVER auth()->user()"
echo "- Projects panel: auth('tenant')->id() — NOT auth()->id()"
echo "- TicketPriority enum: Low/Normal/High/Urgent — NO Medium case"
echo "- TaskPriority backing values: p1_critical/p2_high/p3_medium/p4_low"
echo "- Datetime cast tests: use \\DateTimeInterface::class (app uses CarbonImmutable)"
echo "- OnboardingTemplate relation name: tasks() NOT templateTasks()"
echo "- chatbot trigger_keywords form: pass CSV string, not PHP array"
echo ""

if [ -n "$PENDING" ]; then
  echo "## Pending Decisions"
  echo "$PENDING"
  echo ""
fi

echo "## Brain Files (read before writing code)"
echo "- /brain → reads all brain files for current task"
echo "- obsidian/_Brain/Domain — [HR|Projects|Finance|CRM|Core Platform].md → full model/relation detail"
echo "- obsidian/_Brain/Bug Registry.md → bug patterns by phase"
echo "- obsidian/_Brain/Patterns.md → enforced code patterns"
echo "- /brain-update → sync brain after finishing work"
