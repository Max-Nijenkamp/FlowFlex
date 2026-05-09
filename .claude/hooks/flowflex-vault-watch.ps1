# FlowFlex Vault Watch Hook
# Fires on PostToolUse (Write/Edit) — reminds Claude to sync right brain
# when a left-brain spec file was modified.
#
# Called by Claude Code with tool params available as args or via stdin JSON.

param(
    [string]$FilePath = ""
)

# Also try to get from environment (Claude Code injects this)
if (-not $FilePath) {
    $FilePath = $env:CLAUDE_TOOL_INPUT_FILE_PATH
}

# Try stdin JSON as fallback
if (-not $FilePath) {
    try {
        $stdinContent = $null
        if ([Console]::IsInputRedirected) {
            $stdinContent = [Console]::In.ReadToEnd()
        }
        if ($stdinContent) {
            $json = $stdinContent | ConvertFrom-Json -ErrorAction SilentlyContinue
            if ($json.file_path) { $FilePath = $json.file_path }
            elseif ($json.params.file_path) { $FilePath = $json.params.file_path }
        }
    } catch { }
}

if (-not $FilePath) { exit 0 }

# Normalise slashes
$FilePath = $FilePath -replace '\\', '/'

# Only care about left-brain spec files
if ($FilePath -notmatch 'flowflex-vault/left-brain/') { exit 0 }

# Extract module name from path
$ModuleName = [System.IO.Path]::GetFileNameWithoutExtension($FilePath)

# Determine if it's a domain module (not a MOC or meta file)
$isMOC    = $ModuleName -match '^MOC_'
$isMeta   = $ModuleName -match '^(00_|_)'

if ($isMOC -or $isMeta) { exit 0 }

# Output reminder — Claude Code injects this as a system-reminder into the conversation
Write-Output ""
Write-Output "[FlowFlex Vault] Left-brain spec modified: $ModuleName"
Write-Output "→ Run /flowflex:sync to update STATUS_Dashboard, builder log, and right-brain relations."
Write-Output "→ Or /flowflex:bug to log a bug. Or /flowflex:done if module is complete."
