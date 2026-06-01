# /flowflex:spec

Fetch and display a module spec from the vault. Read-only.

## Usage

```
/flowflex:spec hr.leave
/flowflex:spec finance.invoicing
/flowflex:spec crm.pipeline
/flowflex:spec core.billing
```

## Arguments

- First arg: module key (required)

## What This Does

### Step 1 — Resolve file path

Parse domain and module from key:
- `hr.leave` → `vault/domains/hr/leave-management.md`
- `finance.invoicing` → `vault/domains/finance/invoicing.md`
- `crm.pipeline` → `vault/domains/crm/pipeline.md`

If filename is not obvious from the key suffix, scan `vault/domains/{domain}/` for the file whose `module-key:` frontmatter value matches exactly.

### Step 2 — Read and display the spec

Read the full file. Display:

```
## Spec: {module-key}
Domain: {domain} | Panel: /{panel} | Status: {planned|in-progress|complete}

### What It Does
{2-sentence summary}

### Core Features
{full bullet list}

### Data Model
{tables and key columns}
{ERD if present}

### Filament
{resources, custom pages, widgets}

### Related
{links list}
```

### Step 3 — Show related open gaps (if any)

Read `vault/build/gaps/INDEX.md`. Filter rows where `discovered-in` matches this module key. If any open gaps exist, show them below the spec:

```
⚠️  Open gaps for {module-key}:
- gap-{slug} ({severity}) — {description}
```
