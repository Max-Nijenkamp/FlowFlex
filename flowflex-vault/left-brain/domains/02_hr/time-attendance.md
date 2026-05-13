---
type: module
domain: HR & People
panel: hr
cssclasses: domain-hr
phase: 4
status: complete
migration_range: 100000–149999
last_updated: 2026-05-12
---

# Time & Attendance

Hardware-linked clocking system for shift workers. Physical clock-in/out via biometric terminal, RFID badge, QR code, or mobile geofence. Different from Projects Time Tracking (which is timer-based for knowledge workers).

**Panel:** `hr`  
**Phase:** 4

---

## When to Use vs Projects Time Tracking

| | Time & Attendance (this) | Projects Time Tracking |
|---|---|---|
| Worker type | Shift workers, factory, retail, care | Knowledge workers, freelancers |
| Clock method | Physical terminal / biometric / geofence | Web timer, manual entry |
| Output | Attendance records, overtime calculation | Timesheets, project cost allocation |
| Integration | Payroll (hours worked → pay) | Payroll + client billing |

---

## Features

### Clock-In Methods
- **NFC / RFID badge** — tap card at terminal (Suprema, ZKTeco, HID integration)
- **Fingerprint biometric** — terminal fingerprint reader (GDPR: requires explicit consent + DPA)
- **QR code** — unique per-employee QR displayed on mobile; scan at entrance tablet
- **PIN** — 4-6 digit PIN at shared terminal (least secure, for low-risk sites)
- **Mobile geofence** — clock in via phone app when within X metres of site (GPS verification)
- **Selfie with liveness** — photo at clock-in (prevents buddy punching)

### Attendance Rules Engine
- Shift assignment: employee assigned to shift(s) per week
- Grace period: e.g. 5 minutes late not flagged
- Early departure: flag if leaves before shift end
- Break deduction: auto-deduct X minutes for shifts >Y hours (configurable per country)
- Overtime rules: hours above scheduled time = overtime tier 1, 2, 3
- Night shift premium, weekend premium (multiplier per hour type)

### Absence Management
- Unexpected absence: employee doesn't clock in → auto-mark as absent, notify manager
- Late arrival: clocked in >grace period → late event recorded
- Early leave: clocked out before shift end → early leave event
- Return from leave: auto-expected clock-in after approved leave ends

### Manager Dashboard
- Live view: who is currently clocked in on each site
- Daily attendance summary: present / absent / late / on leave
- Weekly timesheet per employee: clock-in, clock-out, break, total hours
- Exceptions report: late, absent, overtime for approval

### Timesheet Approval & Payroll Export
- Manager reviews and approves timesheets weekly
- Disputes: employee can raise query on incorrect record (with note)
- On approval → hours exported to Payroll module
- Export formats: Payroll native, CSV, PDF

### Compliance
- GDPR: biometric data requires explicit consent; consent withdraw = revert to alternative method
- Working Time Directive: flag employees approaching 48-hour weekly average (EU)
- Rest period compliance: flag if <11 hours between shifts (EU Working Time Directive)
- Maximum daily hours per country rules

---

## Data Model

```erDiagram
    attendance_records {
        ulid id PK
        ulid company_id FK
        ulid employee_id FK
        ulid shift_id FK
        timestamp clocked_in_at
        timestamp clocked_out_at
        string clock_in_method
        string clock_in_location
        integer total_minutes
        integer overtime_minutes
        string status
        boolean manager_approved
        ulid approved_by FK
    }

    shift_assignments {
        ulid id PK
        ulid company_id FK
        ulid employee_id FK
        date shift_date
        time start_time
        time end_time
        string site
        decimal pay_multiplier
    }
```

---

## Permissions

```
hr.attendance.clock-in-out
hr.attendance.view-own
hr.attendance.view-team
hr.attendance.approve-timesheets
hr.attendance.manage-shifts
hr.attendance.manage-terminals
```

---

## Related

- [[MOC_HR]]
- [[entity-employee]]
- [[MOC_Projects]] — Projects Time Tracking is separate (knowledge workers)
- [[MOC_Finance]] — approved timesheets → payroll run
