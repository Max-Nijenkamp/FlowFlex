---
type: module
domain: Marketing & Content
panel: marketing
cssclasses: domain-marketing
phase: 5
status: planned
migration_range: 400000–449999
last_updated: 2026-05-09
---

# Digital Asset Management (DAM)

Centralized brand asset library — logos, templates, imagery, videos, fonts, brand guidelines. Version control, approval workflows, licensing metadata, and agency/contractor sharing. Replaces Bynder, Brandfolder, Canto, and Cloudinary (DAM side).

**Panel:** `marketing`  
**Phase:** 5

---

## Why Separate From File Storage

Core Platform's `File Storage` module handles generic file attachments (invoice PDFs, HR documents, project files). DAM is different:
- Assets have **brand context** (which logo version? which campaign?)
- Assets have **rights/licensing** (photo expires 2027-06, editorial use only)
- Assets need **version chains** (logo v1 → v2 → v3, old versions still accessible)
- Assets are **shared externally** with agencies, designers, press
- Assets need **search by visual similarity** (AI-powered)

---

## Features

### Asset Library
- Upload: images (JPEG, PNG, WebP, SVG), video (MP4, MOV), documents (PDF, AI, PSD, INDD), fonts (OTF, TTF), raw files (CR2, ARW)
- Folder structure + tag-based organisation
- Smart collections (auto-tagged by AI: "contains logo", "product photography", "headshot")
- Bulk upload + CSV metadata import
- Storage: S3-compatible with CDN delivery

### Brand Portal (public-facing)
- Public or password-protected brand portal (`brand.company.com`)
- Press kit download page
- Approved assets for partners/agencies to download
- Usage guidelines displayed alongside assets
- Download in multiple formats/resolutions (with resize-on-the-fly via Cloudflare Images or imgix)

### Version Control
- Version chain per asset (v1 → v2 → v3)
- Side-by-side version comparison
- Restore previous version
- Version comments (why was this updated?)
- "Active version" flag (which version should be used now)

### Rights & Licensing
- Licence type per asset (royalty-free, rights-managed, editorial, exclusive)
- Expiry date with 30-day alert
- Model/property release documents attached to assets
- Geographic restriction (asset licensed for EU use only)
- Usage restriction tags (no social media, print only, etc.)

### Approval Workflows
- New asset upload → review queue → approved/rejected
- Change to active asset → re-approval required
- Reviewer comments on specific regions (annotation on image)
- Approved assets automatically available in CMS & Email Marketing

### AI Features
- Auto-tagging on upload (object recognition, colour palette extraction)
- Visual similarity search ("find other assets like this one")
- Background removal (PNG export with transparent background)
- Brand compliance check (does this asset use approved brand colours/fonts?)
- Alt-text generation for accessibility

### Integrations
- Canva (access DAM assets directly from Canva)
- Adobe Creative Cloud (CC library sync)
- Figma (asset browser plugin)
- CMS module (insert DAM assets directly into content blocks)
- Email Marketing (insert DAM assets into email templates)
- Social Media Management (asset picker uses DAM)

---

## Data Model

```erDiagram
    dam_assets {
        ulid id PK
        ulid company_id FK
        string name
        string type
        string mime_type
        string storage_path
        integer file_size_bytes
        string status
        json metadata
        json ai_tags
        json colour_palette
        timestamp expires_at
        string licence_type
        boolean requires_approval
    }

    dam_asset_versions {
        ulid id PK
        ulid asset_id FK
        integer version_number
        string storage_path
        boolean is_active
        string change_notes
        ulid uploaded_by FK
    }

    dam_asset_collections {
        ulid id PK
        ulid company_id FK
        string name
        string type
        json filter_rules
        boolean is_public
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `AssetApproved` | Approval complete | CMS (available in media picker), Email (available in template) |
| `AssetLicenceExpiring` | 30 days before expiry | Notifications (marketing manager) |
| `AssetLicenceExpired` | Expiry date passed | Asset (auto-archive), Notifications (urgent: remove from live use) |

---

## Permissions

```
marketing.dam.view-any
marketing.dam.upload
marketing.dam.approve
marketing.dam.manage-licences
marketing.dam.manage-portal
```

---

## Competitors Displaced

Bynder · Brandfolder · Canto · Extensis Portfolio · Cloudinary DAM · Widen Collective

---

## Related

- [[MOC_Marketing]]
- [[MOC_CorePlatform]] — file storage is the underlying infra
- [[cms-website-builder]] — DAM assets used in CMS
- [[email-marketing]] — DAM assets used in email templates
