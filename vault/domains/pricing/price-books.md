---
type: module
domain: Pricing Management
panel: pricing
module-key: pricing.price-books
status: planned
color: "#4ADE80"
---

# Price Books

> Price book management — create versioned price books, assign to customers or segments, and control effective dates.

**Panel:** `pricing`
**Module key:** `pricing.price-books`

---

## What It Does

Price Books are the top-level container for pricing. A price book groups a set of product prices and discount rules that apply to a defined customer segment or individual account. Businesses maintain multiple price books — a standard list price book, a wholesale book, a VIP customer book, and individual account-specific books. Each price book has an effective date range, a currency, and a status (draft or published). When a quote or order is generated, the system selects the applicable price book based on the customer's assignment.

---

## Features

### Core
- Price book creation: name, currency, effective start and end dates, status (draft/published)
- Price book types: list price, wholesale, partner, promotional, and account-specific
- Customer assignment: assign a price book to all customers, a customer segment, or individual accounts
- Product price list: view all products and their prices within a price book
- Price book duplication: clone an existing price book as the basis for a new version
- Status publishing: draft price books are not used for quoting until published

### Advanced
- Price book versioning: maintain historical versions; roll back to a prior version if needed
- Bulk price adjustment: apply a percentage uplift or reduction across all products in a price book
- Tiered price books: configure a priority order so account-specific books override segment books override list price
- Expiry alerts: notify pricing managers when a price book is approaching its end date with no successor
- Multi-currency: maintain separate price books per currency with exchange rate management
- Price book approval: require manager approval before a price book is published

### AI-Powered
- Optimal book structure recommendation: analyse quoting data to recommend which price book segments drive the highest win rate
- Price book gap detection: identify products present in one price book but missing from another
- Seasonal adjustment: suggest price adjustments based on historical seasonal demand patterns

---

## Data Model

```erDiagram
    price_books {
        ulid id PK
        ulid company_id FK
        string name
        string book_type
        string currency
        date effective_from
        date effective_to
        string status
        integer priority
        timestamps created_at_updated_at
    }

    price_book_assignments {
        ulid id PK
        ulid company_id FK
        ulid price_book_id FK
        string assignee_type
        string assignee_id
        timestamps created_at_updated_at
    }

    price_books ||--o{ price_book_assignments : "assigned via"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `price_books` | Price book records | `id`, `company_id`, `name`, `book_type`, `currency`, `effective_from`, `effective_to`, `status`, `priority` |
| `price_book_assignments` | Customer/segment assignments | `id`, `price_book_id`, `assignee_type`, `assignee_id` |

---

## Permissions

```
pricing.price-books.view
pricing.price-books.create
pricing.price-books.edit
pricing.price-books.publish
pricing.price-books.assign
```

---

## Filament

- **Resource:** `App\Filament\Pricing\Resources\PriceBookResource`
- **Pages:** `ListPriceBooks`, `CreatePriceBook`, `EditPriceBook`, `ViewPriceBook`
- **Custom pages:** `PriceBookProductListPage`, `PriceBookCustomerAssignmentsPage`
- **Widgets:** `ActivePriceBooksWidget`, `ExpiringPriceBooksWidget`
- **Nav group:** Price Books

---

## Displaces

| Feature | FlowFlex | Vendavo | PROS | Zilliant |
|---|---|---|---|---|
| Multiple price books | Yes | Yes | Yes | Yes |
| Customer assignment | Yes | Yes | Yes | Yes |
| Price book versioning | Yes | Yes | Yes | Yes |
| AI segment optimisation | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[product-pricing]] — product prices are managed within each price book
- [[discount-rules]] — discount rules layered on top of price book prices
- [[crm/INDEX]] — customer price book assignment managed from CRM account record
- [[ecommerce/INDEX]] — active price book published to storefront
