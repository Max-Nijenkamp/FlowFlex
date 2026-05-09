---
type: module
domain: HR & People
panel: hr
cssclasses: domain-hr
phase: 3
status: planned
migration_range: 100000–149999
last_updated: 2026-05-09
---

# Employee Self-Service Portal (ESS)

Employee-facing Vue+Inertia portal. Employees manage their own HR data without going through HR admin. Separate authenticated frontend (`auth:employee` guard) — distinct from both the Filament HR panel (admin) and the Client Portal (customers).

**Panel:** `hr` (backend) + Vue+Inertia frontend (`/my/` routes)  
**Phase:** 3  
**Auth Guard:** `auth:employee`

---

## Why Phase 3 (Not Phase 8)

Onboarding (Phase 2) creates employees. From day 1, employees need to:
- Submit leave requests
- View their own payslips
- Update emergency contacts
- See their schedule

Without ESS, HR admins handle every single employee request manually. At 10+ employees this is unsustainable.

---

## Features

### Personal Dashboard (`/my/dashboard`)
- Today: who's in office (Workplace integration), my upcoming events
- My leave balance (per type)
- My open tasks (from onboarding/offboarding checklists)
- My recent payslips
- Company announcements (from Communications module)
- Quick actions: request leave, log time, report absence

### My Profile (`/my/profile`)
- View personal details (name, DOB, address, bank account)
- Edit: phone, emergency contact, preferred pronouns, dietary requirements
- Upload profile photo
- View employment details (job title, department, manager, start date) — read-only
- View contract type, working hours — read-only

### My Leave (`/my/leave`)
- View leave balance by type (annual, sick, parental, etc.)
- Submit leave request (date range, type, reason)
- View pending/approved/rejected requests
- Cancel approved leave (if within cancellation window)
- Team leave calendar (who else is off same time)
- My leave history

### My Pay (`/my/payslips`)
- View all payslips (most recent first)
- Download payslip PDF
- View payslip breakdown (gross, deductions, net, tax code)
- Annual P60 / Jaaropgave download
- Update bank account details (requires ID verification step)
- View year-to-date earnings summary

### My Time (`/my/time`)
- Clock in/out (if Scheduling & Shifts module enabled)
- View timesheet for current/previous weeks
- Submit manual time entry (if Projects time tracking enabled)
- View overtime balance

### My Documents (`/my/documents`)
- View documents HR has shared with me (contract, policies, offer letter)
- Download and e-sign documents (Legal E-Signature integration)
- View certificate of employment
- Request reference letter (submits request to HR)

### My Benefits (`/my/benefits`)
- View enrolled benefits (if Benefits & Perks module enabled)
- Enrol in new benefits during open enrolment
- View benefit documents (insurance card, pension statement)

### My Learning (`/my/learning`)
- My assigned courses (LMS integration)
- My certifications and expiry dates
- Request external training

### Settings
- Notification preferences (email, push, SMS per event type)
- Language preference
- Change password
- Connected devices (session management)

---

## Technical Notes

ESS is a Vue+Inertia frontend, **not** Filament:
- Routes at `/my/...` — protected by `auth:employee` middleware
- Employee authenticates with work email (same `users` table, employee flag)
- OR separate `employee_portal_users` auth (if employee doesn't have Filament access)
- Branded: company logo, primary colour from `companies.branding`
- Mobile-first responsive design
- PWA (installable on iOS/Android without native app)

```php
// routes/employee-portal.php
Route::middleware(['auth:employee', 'company.scope'])->prefix('my')->group(function () {
    Route::get('/dashboard', [ESSDashboardController::class, 'index']);
    Route::get('/leave', [ESSLeaveController::class, 'index']);
    Route::post('/leave', [ESSLeaveController::class, 'store']);
    Route::get('/payslips', [ESSPayslipController::class, 'index']);
    // etc.
});
```

---

## Data Model

No new tables required — ESS is a read/write layer over existing HR tables with employee-scoped access. Key constraint: **employees can only access their own records**.

```php
// ESSLeaveController — strict self-scope
public function index(): Response
{
    $employee = auth('employee')->user()->employee;
    return Inertia::render('ESS/Leave/Index', [
        'requests' => $employee->leaveRequests()->with('type')->paginate(20),
        'balances' => $employee->leaveBalances,
    ]);
}
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `LeaveRequestSubmitted` | Employee submits | HR (manager notification, approval queue) |
| `BankDetailsUpdated` | Employee changes bank account | HR (review queue), Payroll (flag for next run) |
| `DocumentSignedByEmployee` | Employee e-signs doc | Legal (update status), HR (complete onboarding task) |

---

## Permissions

ESS is its own auth context — not Spatie Permission:
- Employees automatically have access to their own records only
- No permission needed — the auth guard enforces it
- HR admins can impersonate any employee in ESS (audit logged)

---

## Competitors Displaced

BambooHR (ESS) · Personio (ESS) · Workday (ESS) · HiBob (ESS) · Rippling (ESS)

---

## Related

- [[MOC_HR]]
- [[entity-employee]]
- [[MOC_Frontend]] — ESS is a Vue+Inertia portal like Client Portal
- [[client-portal]] — same architecture pattern, different auth guard and data
