---
type: adr
date: 2026-06-01
status: decided
color: "#F97316"
---

# Currency Precision — Integer Storage Strategy

---

## Context

We store monetary amounts as integers to avoid floating-point arithmetic errors. But "cents" is ambiguous across currencies:

| Currency | Minor Unit | Example |
|---|---|---|
| EUR, GBP, USD | 1/100 (cents) | €10.00 = 1000 |
| JPY, KRW | 1/1 (no decimals) | ¥1000 = 1000 |
| KWD, BHD | 1/1000 (fils) | 1.000 KWD = 1000 |
| BTC | 1/100,000,000 (satoshi) | Not in scope |

Storing all amounts as "cents" works for EUR/GBP/USD but breaks for JPY (1000 stored as 100000 — a 100x error) and KWD (1000 stored as 1000 — a 1000x error).

---

## Decision

**Store amounts in the currency's smallest indivisible unit (ISO 4217 minor unit), not always "cents".**

Column naming: `amount_minor` (not `amount_cents`).

Use `brick/money` to handle arithmetic — it understands minor unit conventions per currency.

---

## Implementation

```php
use Brick\Money\Money;
use Brick\Money\Currency;

// Store: create from a decimal and get the minor amount
$money = Money::of('10.00', 'EUR'); // €10.00
$stored = $money->getMinorAmount()->toInt(); // 1000

$money = Money::of('10', 'JPY'); // ¥10
$stored = $money->getMinorAmount()->toInt(); // 10 (JPY has 0 decimal places)

// Read: reconstruct from stored minor amount
$money = Money::ofMinor(1000, 'EUR'); // €10.00
$money = Money::ofMinor(10, 'JPY');   // ¥10
```

Migration pattern:

```php
$table->bigInteger('total_minor');  // stored as minor unit per currency
$table->string('currency', 3);     // ISO 4217 code: EUR, GBP, JPY
```

---

## V1 Scope

FlowFlex v1 targets EU SMEs. Primary currencies: EUR, GBP, CHF, PLN, SEK, NOK, DKK. All are 2-decimal currencies. The JPY/KWD edge case does not apply in practice for v1.

**For v1**: column naming `amount_cents` is acceptable shorthand (the actual stored values are correct for 2-decimal currencies). When multi-currency is actively built (Phase 2, `finance.currency` module), rename columns to `amount_minor` and add a `currency` column per amount.

---

## Consequences

- `brick/money` handles all arithmetic — no raw integer math on monetary amounts
- Displays: always format via company locale from Company Settings, never format amounts directly in PHP
- When the Multi-Currency module is inactive: assume company base currency, no per-record currency field
- When the Multi-Currency module is active: each invoice/expense record carries its own `currency` column

---

## Related

- [[architecture/packages]] — `brick/money`
- [[domains/finance/multi-currency]]
- [[domains/core/company-settings]]
