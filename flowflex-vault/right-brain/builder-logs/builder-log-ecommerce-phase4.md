---
type: builder-log
module: ecommerce-phase4
domain: E-commerce & Sales Channels
panel: ecommerce
phase: 4
started: 2026-05-11
status: in-progress
color: "#F97316"
left_brain_source: "[[MOC_Ecommerce]]"
last_updated: 2026-05-11
---

# Builder Log: E-commerce Phase 4

Left Brain source: [[MOC_Ecommerce]]

---

## Sessions

### Session 2026-05-11

**Goal:** Build the E-commerce panel, core data layer, and Filament resources for Product Catalogue and Order Management (Phase 4 foundation modules).

**Built:**

Panel provider:
- `app/Providers/Filament/EcommercePanelProvider.php` — id: `ecommerce`, path: `/ecommerce`, Color::Cyan, navigation groups: Catalogue / Orders / Customers / Settings, same middleware stack as HrPanelProvider

Theme:
- `resources/css/filament/ecommerce/theme.css` — @source paths for ecommerce Filament classes

Migrations (range 600000–609999):
- `database/migrations/2026_05_11_600001_create_product_categories_table.php` — self-referential FK used 2-step Schema::create + Schema::table pattern (same as GAP-017 HR fix)
- `database/migrations/2026_05_11_600002_create_ecommerce_products_table.php` — enum status (draft/active/archived), compound index [company_id, status]
- `database/migrations/2026_05_11_600003_create_ecommerce_customers_table.php` — unique [company_id, email]
- `database/migrations/2026_05_11_600004_create_ecommerce_orders_table.php` — enum status (pending/processing/shipped/delivered/cancelled/refunded), compound index [company_id, status]
- `database/migrations/2026_05_11_600005_create_ecommerce_order_items_table.php` — snapshot product_name at order time

Models:
- `app/Models/Ecommerce/ProductCategory.php` — BelongsToCompany, HasUlids; parent(), children(), products()
- `app/Models/Ecommerce/EcommerceProduct.php` — BelongsToCompany, HasUlids, SoftDeletes; category(), orderItems()
- `app/Models/Ecommerce/EcommerceCustomer.php` — BelongsToCompany, HasUlids, SoftDeletes; orders(), getFullNameAttribute()
- `app/Models/Ecommerce/EcommerceOrder.php` — BelongsToCompany, HasUlids, SoftDeletes; customer(), items()
- `app/Models/Ecommerce/EcommerceOrderItem.php` — HasUlids only (no company scope — child of order); order(), product()

Filament Resources:
- `app/Filament/Ecommerce/Resources/ProductCategoryResource.php` — group: Catalogue, icon: heroicon-o-tag, canAccess: ecommerce.products; auto-slug on name
- `app/Filament/Ecommerce/Resources/EcommerceProductResource.php` — group: Catalogue, icon: heroicon-o-shopping-bag, canAccess: ecommerce.products; 3-section form (Details / Pricing / Inventory)
- `app/Filament/Ecommerce/Resources/EcommerceCustomerResource.php` — group: Customers, icon: heroicon-o-users, canAccess: ecommerce.orders
- `app/Filament/Ecommerce/Resources/EcommerceOrderResource.php` — group: Orders, icon: heroicon-o-shopping-cart, canAccess: ecommerce.orders; number field disabled on edit; full colour-coded badge for 6 statuses

All resource page trios (List/Create/Edit) with mutateFormDataBeforeCreate injecting company_id.

Dashboard + Widget:
- `app/Filament/Ecommerce/Pages/Dashboard.php`
- `app/Filament/Ecommerce/Widgets/StoreOverviewWidget.php` — Stats: Orders Today, Revenue Today, Active Products

**Decisions made:**
- EcommerceOrderItem does not carry company_id or BelongsToCompany — it is a child of EcommerceOrder which already has the scope. Same pattern as other line-item models in the codebase.
- product_name snapshotted on order items — detaches order history from future product renames/deletes.
- self-referential parent_id FK on product_categories uses the established 2-step Schema::create + Schema::table pattern (GAP-017 resolution).
- Panel NOT registered in bootstrap/providers.php — consistent with all other domain panels (registered manually when domain is activated).
- module keys: `ecommerce.products` for catalogue resources, `ecommerce.orders` for order/customer resources.

**Problems hit:**
- Migration range 400001 was already taken by documents table — marketing migrations started from 400002 onwards. No conflict.

**Patterns found:**
- EcommerceOrderItem as pure child model (HasUlids only, no BelongsToCompany) is the correct pattern for line items. Consistent with CRM quote items.

---

## Gaps Discovered

None discovered in this session.

---

## Lessons

- The 15-module E-commerce domain will need a second session for: Storefront & Checkout (Vue+Inertia), Marketplace Integration, Subscription Products, and the remaining 10 Phase 5 modules.
- module keys `ecommerce.products` and `ecommerce.orders` should be added to ModuleCatalogSeeder and LocalCompanySeeder before the panel is used.

---

## Post-Build Checklist

- [ ] Add `ecommerce.products` and `ecommerce.orders` to ModuleCatalogSeeder
- [ ] Add ecommerce module keys to LocalCompanySeeder active subscriptions
- [ ] Register EcommercePanelProvider in bootstrap/providers.php when activating
- [ ] Run `php artisan migrate` to verify all 5 migrations execute cleanly
- [ ] Verify panel resolves at `/ecommerce`
- [ ] Left Brain spec updated ✅
- [ ] STATUS_Dashboard updated ✅

---

## Related

- [[ACTIVATION_GUIDE]]
- [[STATUS_Dashboard]]
- [[MOC_Ecommerce]]
