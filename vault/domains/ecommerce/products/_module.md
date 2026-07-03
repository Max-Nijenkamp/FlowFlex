---
domain: ecommerce
module: products
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Product Catalogue

Product records with pricing, images, categories, and inventory linkage — the catalogue that the storefront sells from. The E-commerce anchor, built first in `/ecommerce`.

## Module-key

`ecommerce.products`

**Priority:** p3  
**Panel:** ecommerce  
**Permission prefix:** `ecommerce.products`  
**Tables:** `ec_products`, `ec_categories`

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing/_module\|Billing]] | Module gating (`hasModule`) |
| Hard | [[../../core/rbac/_module\|RBAC]] | Permissions, `canAccess()` |
| Hard | [[../../core/file-storage/_module\|File Storage]] | Product image gallery |
| Soft | [[../../operations/inventory/_module\|Inventory]] | Stock from an ops item link; internal `stock_quantity` field otherwise |
| Soft | [[../variants/_module\|Variants]] | Per-variant SKU / price / stock |
| Soft | [[../../core/data-import/_module\|Data Import]] · [[../../finance/tax-management/_module\|Tax]] | Bulk import, tax classes |

## Core Features

- **Product record** — name, slug, description, SKU, price, compare-at price, category, status.
- **Rich content** — Tiptap description (purified), image gallery (Media Library).
- **Categories** — tree of `ec_categories` (collections *(assumed: categories only v1)*).
- **Status lifecycle** — `draft → active → archived`.
- **Inventory linkage** — `ops_item_id` when operations active (stock via `StockService`); else internal `stock_quantity`.
- **SEO + digital flag** — meta title/description; `is_digital` skips fulfilment/shipping.
- **Pricing** — minor units (`brick/money`); tax class per product.

## See features/

- [[features/manage-catalogue|Manage Catalogue]] — product + category CRUD.
- [[features/stock-linkage|Stock Linkage]] — the single `ProductStock` API bridging ops vs internal stock.

## Build Manifest

```
database/migrations/xxxx_create_ec_categories_table.php
database/migrations/xxxx_create_ec_products_table.php
app/Models/Ecommerce/{EcProduct,EcCategory}.php
app/Data/Ecommerce/CreateProductData.php
app/Support/Ecommerce/ProductStock.php
app/Providers/Ecommerce/EcommerceServiceProvider.php
app/Filament/Ecommerce/Resources/{EcProductResource,EcCategoryResource}.php
database/factories/Ecommerce/{EcProductFactory,EcCategoryFactory}.php
tests/Feature/Ecommerce/{ProductTest,ProductStockTest}.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot see, edit, or read stock for company B products/categories.
- [ ] Module gating: artifacts hidden when `ecommerce.products` inactive.
- [ ] Duplicate SKU / slug rejected.
- [ ] Stock API: ops-linked reads `StockService`; internal uses field.
- [ ] Compare-at must exceed price.
- [ ] Draft invisible on storefront + public search.
- [ ] Description purified; category cycle rejected.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads/Commands | `StockService::available/reserve/release` | operations.inventory | Only when `ops_item_id` set; never writes ops tables |
| Reads | tax classes | finance.tax-management | `tax_class` label read for order-time tax calc |

**Data ownership:** `ecommerce.products` writes only `ec_products` + `ec_categories`. Stock backed by operations is read/reserved through `StockService` (its owning-service API), never by writing `ops_*` tables ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../variants/_module|Variants]] · [[../orders/_module|Orders]] · [[../storefront/_module|Storefront]]
- [[../../../glossary]]
