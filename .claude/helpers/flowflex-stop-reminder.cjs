#!/usr/bin/env node
/**
 * FlowFlex Stop Reminder — fires on Stop hook.
 * Checks if any app/ files were edited this session.
 * If yes, reminds to sync the vault.
 */
'use strict';

const fs = require('fs');
const path = require('path');

// Check if there's any pending vault sync needed
// by looking at the stop event context
let toolInput = {};
try {
  const raw = fs.readFileSync('/dev/stdin', 'utf8');
  if (raw.trim()) toolInput = JSON.parse(raw);
} catch { /* ignore */ }

// Always output the reminder — it's low noise and high value
console.log('');
console.log('[FlowFlex] Session ending. Vault sync checklist:');
console.log('  /flowflex:sync {module-key} status=in-progress   — update spec + STATUS.md');
console.log('  /flowflex:done {module-key}                       — mark module complete');
console.log('  /flowflex:bug "description" module={key}          — log any bugs found');
console.log('  /flowflex:decision "title" status=decided         — log any decisions made');
console.log('');
console.log('  Skip this if no app/ code was written this session.');
