#!/usr/bin/env node
/**
 * FlowFlex Vault Watch — PostToolUse hook
 * Fires after Write/Edit/MultiEdit. If a left-brain spec was modified,
 * outputs a reminder to sync the right brain.
 */

'use strict';

const path = require('path');

let toolInput = {};

// Read tool input from stdin (Claude Code passes tool call JSON here)
let rawInput = '';
try {
  const fs = require('fs');
  rawInput = fs.readFileSync('/dev/stdin', 'utf8');
  if (rawInput.trim()) {
    toolInput = JSON.parse(rawInput);
  }
} catch {
  // stdin not available or not JSON — try env vars
  try {
    const envInput = process.env.CLAUDE_TOOL_INPUT;
    if (envInput) toolInput = JSON.parse(envInput);
  } catch { /* ignore */ }
}

// Get file path from various possible locations
const filePath = (
  toolInput?.tool_input?.file_path ||
  toolInput?.file_path ||
  toolInput?.params?.file_path ||
  process.env.CLAUDE_TOOL_INPUT_FILE_PATH ||
  ''
).replace(/\\/g, '/');

if (!filePath) process.exit(0);

// Only care about left-brain spec files (not MOCs or meta files)
if (!filePath.includes('flowflex-vault/left-brain/')) process.exit(0);

const basename = path.basename(filePath, '.md');
const isMOC  = basename.startsWith('MOC_') || basename.startsWith('00_') || basename.startsWith('_');
const isMeta = ['obsidian-setup', 'dataview-queries', '_conventions', '_index'].includes(basename);

if (isMOC || isMeta) process.exit(0);

// Determine which domain this is in
const domainMatch = filePath.match(/left-brain\/domains\/(\d+_[^/]+)\//);
const domain = domainMatch ? domainMatch[1].replace(/^\d+_/, '') : 'unknown';

// Output reminder — Claude Code injects this as context
console.log('');
console.log(`[FlowFlex Vault] Left-brain spec modified: ${basename} (${domain})`);
console.log('→ Run /flowflex:sync to update STATUS_Dashboard, builder log, and right-brain relations.');
console.log('→ Run /flowflex:bug "description" to log a bug found during this change.');
console.log('→ Run /flowflex:done once the module is fully built and tested.');
