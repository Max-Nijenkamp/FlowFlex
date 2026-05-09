---
type: moc
domain: Business Travel
panel: travel
cssclasses: domain-travel
phase: 7
color: "#1D4ED8"
last_updated: 2026-05-09
---

# Business Travel — Map of Content

End-to-end business travel booking, policy enforcement, trip management, and carbon tracking. Replaces Navan (formerly TripActions), TravelPerk, and Concur.

**Panel:** `travel`  
**Phase:** 7  
**Migration Range:** `910000–929999`  
**Colour:** Blue `#1D4ED8` / Light: `#EFF6FF`  
**Icon:** `heroicon-o-paper-airplane`

---

## Why As A Standalone Domain (Not Finance Module)

Travel Management is complex enough to warrant its own panel:
- GDS (Global Distribution System) integration for real flight/hotel inventory
- Complex approval chains with deadline-based auto-approval
- Trip management lifecycle (planning → approval → booking → travel → expense)
- Duty of care (know where every employee is at any moment)
- Corporate rate negotiation management

The Finance domain has `travel-expense-management.md` for the expense/reimbursement side. This domain handles the **booking and policy** side.

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| Travel Booking Portal | 7 | planned | Self-serve flight, hotel, car, rail booking within policy |
| Travel Policy Engine | 7 | planned | Class rules, advance booking windows, max hotel rates by city |
| Trip Approvals & Workflow | 7 | planned | Manager approval, budget check, deadline-based auto-approval |
| Duty of Care & Traveller Safety | 7 | planned | Live traveller tracking, emergency alerts, safety check-ins |
| Preferred Supplier Management | 7 | planned | Corporate rates, negotiated hotels, preferred airlines |
| Corporate Carbon Tracking | 7 | planned | CO₂ per trip, fleet-wide emissions, offset purchasing |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `TripApproved` | Approvals | Travel Booking (proceed), Notifications (traveller) |
| `TripBookingConfirmed` | Booking Portal | Notifications (traveller + manager), Finance (pre-commit budget) |
| `TravelllerInDistressZone` | Duty of Care | Notifications (HR, security team) |
| `CarbonBudgetExceeded` | Carbon Tracking | Notifications (travel manager) |

---

## Filament Panel Structure

**Navigation Groups:**
- `Booking` — My Trips, New Trip, Booking History
- `Policy` — Travel Policies, City Rates, Preferred Suppliers
- `Approvals` — Pending Approvals, Approval History
- `Safety` — Live Travellers, Alerts, Check-ins
- `Sustainability` — Carbon Dashboard, Offsets

---

## Permissions Prefix

`travel.booking.*` · `travel.policy.*` · `travel.approvals.*`  
`travel.duty-of-care.*` · `travel.carbon.*`

---

## Competitors Displaced

Navan (TripActions) · TravelPerk · SAP Concur · CTM · American Express GBT

---

## Related

- [[MOC_Domains]]
- [[MOC_Finance]] — travel expenses → Finance `travel-expense-management`
- [[MOC_HR]] — employee travel linked to employee records
- [[MOC_Workplace]] — office visit scheduling complements travel
