#!/usr/bin/env node
/**
 * FlowFlex Vault Watch — PostToolUse hook
 * Fires after Write/Edit/MultiEdit.
 * If a file in app/ was modified → remind to sync vault.
 * If a vault spec was modified → remind to update STATUS.md.
 */

'use strict';

const path = require('path');
const fs = require('fs');

let toolInput = {};
let rawInput = '';

try {
  rawInput = fs.readFileSync('/dev/stdin', 'utf8');
  if (rawInput.trim()) toolInput = JSON.parse(rawInput);
} catch {
  try {
    const envInput = process.env.CLAUDE_TOOL_INPUT;
    if (envInput) toolInput = JSON.parse(envInput);
  } catch { /* ignore */ }
}

const filePath = (
  toolInput?.tool_input?.file_path ||
  toolInput?.file_path ||
  toolInput?.params?.file_path ||
  process.env.CLAUDE_TOOL_INPUT_FILE_PATH ||
  ''
).replace(/\\/g, '/');

if (!filePath) process.exit(0);

const basename = path.basename(filePath, '.md');

// --- Case 1: Code file in app/ was modified → vault sync reminder ---
if (filePath.includes('/app/') && !filePath.includes('/vendor/')) {
  // Try to infer domain from path (e.g. app/Models/HR/Employee.php → hr)
  const domainMatch = filePath.match(/\/app\/(Models|Services|Actions|Filament|States|Listeners|Jobs|Mail|Controllers)\/([A-Z][^/]+)\//i);
  const domain = domainMatch ? domainMatch[2].toLowerCase() : null;

  console.log('');
  console.log('[FlowFlex] Code file modified: ' + path.basename(filePath));
  if (domain) {
    console.log('→ When session is complete: /flowflex:sync ' + domain + '.{module} status=in-progress');
  } else {
    console.log('→ When session is complete: /flowflex:sync {module-key} status=in-progress|complete');
  }
  console.log('→ If bugs found: /flowflex:bug "description" module={key} severity=high|medium|low');
  console.log('→ If module done: /flowflex:done {module-key}');
  process.exit(0);
}

// --- Case 2: Vault domain spec was modified → check STATUS.md ---
if (filePath.includes('/vault/domains/') && filePath.endsWith('.md')) {
  const isIndex = basename === '_index' || basename === '_overview';
  if (isIndex) process.exit(0);

  const domainMatch = filePath.match(/\/vault\/domains\/([^/]+)\//);
  const domain = domainMatch ? domainMatch[1] : 'unknown';

  console.log('');
  console.log('[FlowFlex Vault] Spec modified: ' + basename + ' (' + domain + ')');
  console.log('→ /flowflex:sync ' + domain + '.' + basename + ' status=in-progress');
  console.log('→ Check vault/build/STATUS.md is up to date');
  process.exit(0);
}

// --- Case 3: Build tracking file modified → no reminder needed ---
if (filePath.includes('/vault/build/')) process.exit(0);

process.exit(0);
