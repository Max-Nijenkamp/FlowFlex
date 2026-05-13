---
type: builder-log
module: operations-phase4
domain: Operations & Field Service
panel: operations
phase: 4
started: 2026-05-11
status: complete
color: "#F97316"
left_brain_source: "[[MOC_Operations]]"
last_updated: 2026-05-11
---

# Builder Log — Operations Phase 4

Phase 4 of the Operations & Field Service domain. Covers Inventory Management, Asset Management, Purchasing & Procurement, and Field Service Management — all built in a single session.

---

## Sessions

### 2026-05-11 — Phase 4 Operations — Full Build

**What was built:**

**Panel**
- `app/Providers/Filament/OperationsPanelProvider.php` — panel id `operations`, path `/operations`, Color::Amber, middleware copied from HrPanelProvider exactly, navigation groups: Inventory / Assets / Procurement / Field Service / Settings
- `resources/css/filament/operations/theme.css` — copied from hr/theme.css, sources updated to `operations` panel

**Migrations (date-prefixed 2026_05_11_300001–300008)**
- `300001_create_products_table` — ulid PK, company_id FK, sku/name/description/category/unit_of_measure/cost_price/sale_price/reorder_point/is_active, softDeletes, index [company_id, sku]
- `300002_create_inventory_locations_table` — ulid PK, company_id FK, name/type enum(warehouse,store,virtual)/is_active
- `300003_create_stock_movements_table` — ulid PK, company_id FK, product_id FK, location_id nullable FK, type enum(in,out,adjustment,transfer), quantity/reference/notes/moved_at, user_id FK, index [product_id, moved_at]
- `300004_create_physical_assets_table` — ulid PK, company_id FK, name/category/serial_number/purchase_date/purchase_price/location, assigned_to nullable FK users, status enum(available,in_use,under_maintenance,disposed), softDeletes
- `300005_create_suppliers_table` — ulid PK, company_id FK, name/email/phone/address/payment_terms/is_active, softDeletes
- `300006_create_purchase_orders_table` — ulid PK, company_id FK, supplier_id nullable FK, number/order_date/expected_date, status enum(draft,sent,partial,received,cancelled), subtotal/total/notes, softDeletes, index [company_id, status]
- `300007_create_purchase_order_items_table` — ulid PK, purchase_order_id FK, product_id nullable FK, description/quantity_ordered/quantity_received/unit_price/total
- `300008_create_field_jobs_table` — ulid PK, company_id FK, title/contact_name/contact_phone/address, assigned_to nullable FK users, scheduled_at/completed_at, status enum(pending,scheduled,in_progress,completed,cancelled), priority enum(low,normal,high,urgent), description/completion_notes, softDeletes, index [company_id, status]

**Models (`app/Models/Operations/`)**
- `Product` — BelongsToCompany, HasUlids, SoftDeletes; `stockMovements()`, `currentStock()` (SQL SUM with CASE)
- `InventoryLocation` — BelongsToCompany, HasUlids; `stockMovements()`
- `StockMovement` — BelongsToCompany, HasUlids; `product()`, `location()`, `user()`
- `PhysicalAsset` — BelongsToCompany, HasUlids, SoftDeletes; `assignedTo()`
- `Supplier` — BelongsToCompany, HasUlids, SoftDeletes; `purchaseOrders()`
- `PurchaseOrder` — BelongsToCompany, HasUlids, SoftDeletes; `supplier()`, `items()`
- `PurchaseOrderItem` — HasUlids (no BelongsToCompany, no SoftDeletes — child record); `purchaseOrder()`, `product()`
- `FieldJob` — BelongsToCompany, HasUlids, SoftDeletes; `assignedTo()`

**Service Layer**
- `app/Contracts/Operations/InventoryServiceInterface.php` — `addStock()`, `removeStock()`, `getCurrentStock()`
- `app/Services/Operations/InventoryService.php` — concrete implementation using StockMovement::create
- `app/Providers/Operations/OperationsServiceProvider.php` — binds InventoryServiceInterface → InventoryService

**Filament Resources (`app/Filament/Operations/Resources/`)**
All resources follow: canAccess() with CompanyContext + BillingService check, form() using Schema, table() with columns/actions, List/Create/Edit page classes, mutateFormDataBeforeCreate in Create page.

- `ProductResource` — module key `operations.inventory`, group Inventory; table: sku/name/category/cost_price(money)/sale_price/is_active(icon); form: sku/name/description/category/unit_of_measure/cost_price/sale_price/reorder_point/is_active
- `InventoryLocationResource` — module key `operations.inventory`, group Inventory; table: name/type(badge)/is_active; form: name/type(select)/is_active
- `StockMovementResource` — module key `operations.inventory`, group Inventory; table: product.name/type(badge colour-coded)/quantity/reference/moved_at; form: product_id(select)/location_id(select)/type/quantity/reference/moved_at(datetimepicker)/notes; Create page injects user_id from auth()
- `PhysicalAssetResource` — module key `operations.assets`, group Assets; table: name/category/serial_number/status(badge 4 colours)/assignedTo.email; form: full asset form with user select scoped to company
- `SupplierResource` — module key `operations.procurement`, group Procurement; table: name/email/phone/payment_terms(suffix days)/is_active
- `PurchaseOrderResource` — module key `operations.procurement`, group Procurement; table: number/supplier.name/order_date/expected_date/total/status(badge 5 colours)
- `FieldJobResource` — module key `operations.field-service`, group Field Service; table: title/contact_name/assignedTo.email/scheduled_at/status(badge)/priority(badge)

**Dashboard & Widget**
- `app/Filament/Operations/Pages/Dashboard.php` — extends BaseDashboard, 3-column grid
- `app/Filament/Operations/Widgets/OperationsOverviewWidget.php` — 3 stats: Active Products / Open Purchase Orders / Pending Field Jobs; all queries use withoutGlobalScopes()->where('company_id',...)

**Decisions made:**
- PurchaseOrderItem omits BelongsToCompany (child record, always accessed via PO relationship) — consistent with PO having company_id
- `currentStock()` uses a single SQL SUM(CASE WHEN) query rather than two separate queries for performance
- User dropdown in PhysicalAsset and FieldJob scoped via `whereHas('companies')` — relies on User→Company relationship, same pattern as CRM
- OperationsPanelProvider NOT registered in bootstrap/providers.php per spec — manual registration deferred

---

## Gaps Discovered

None identified in this session.

---

## Patterns Applied

- Date-prefixed migration naming convention (ADR 2026-05-10)
- ULID PK + FK pattern (`$table->ulid('id')->primary()`, `$table->foreign()->references('id')->on()`)
- No ULID id on child tables that are accessed only via parent (PurchaseOrderItem)
- `withoutGlobalScopes()->where('company_id', ...)` on all dropdown selects
- `mutateFormDataBeforeCreate` in Create page classes (not in resource)
- canAccess() pattern: auth check → hasCompany() → enforceModuleAccess()
