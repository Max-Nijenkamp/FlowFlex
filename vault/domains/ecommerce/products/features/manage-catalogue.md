---
domain: ecommerce
module: products
feature: manage-catalogue
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Manage Catalogue

Product and category CRUD — the merchant-facing catalogue the storefront sells from.

## Behaviour

- Create/edit/archive products: name, slug, SKU, price, compare-at, category, status, digital flag, tax class, SEO, images.
- Duplicate SKU or slug within the company is rejected.
- Compare-at price must exceed price when set.
- Status lifecycle `draft → active → archived`; drafts and archived products are invisible on the storefront + public search.
- Categories form a tree (`parent_category_id`); a cycle is rejected.
- Rich description is purified (htmlpurifier); image gallery via Media Library.

## UI

- **Kind**: simple-resource
- **Page**: `EcProductResource` (`/ecommerce/products`) + `EcCategoryResource` (`/ecommerce/categories`), nav group **Catalogue**.
- **Layout**: products table (image thumb, name, SKU, price, status, category) with status + category filters; edit form uses sections (Details · Pricing · Inventory · Media · SEO); Tiptap description; Media Library gallery. Categories as a tree-select resource.
- **Key interactions**: create/edit form; "Publish" row/header action (draft → active), gated `ecommerce.products.publish`; archive action; category tree add/reparent.
- **States**: empty (no products → "add your first product" CTA) · loading (table skeleton) · error (validation toast: duplicate SKU/slug, compare-at ≤ price, category cycle) · selected (row → edit).
- **Gating**: view `ecommerce.products.view-any`; create/edit `ecommerce.products.create`/`.update`; publish `.publish`; categories `.manage-categories`.

## Data

- Owns / writes: `ec_products`, `ec_categories` only.
- Reads: `finance.tax-management` tax classes (labels); `operations.inventory` for the item picker when linking stock.
- Cross-domain writes: none — stock changes go through `ProductStock` → `StockService` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing (event-wise).
- Feeds: supplies priced/stocked lines to [[../../orders/_module|Orders]] and [[../../storefront/_module|Storefront]] via read queries.
- Shared entity: tax classes owned by `finance.tax-management`; stock items owned by `operations.inventory`.

## Unknowns

- Digital-asset delivery model deferred (see [[../unknowns]]).

## Related

- [[../_module|Product Catalogue]] · [[stock-linkage]] · [[../../variants/_module|Variants]]
