---
type: module
domain: HR & People
panel: hr
phase: 2
status: complete
migration_range: 100000–109999
last_updated: 2026-05-12
right_brain_log: "[[builder-log-hr-phase2]]"
---

# Employee Profiles

Core module for managing employee records within a company. Supports full lifecycle from hire to termination.

## Module Key
`hr.profiles`

## Features
- Employee creation, update, termination
- Employment types: full_time, part_time, contractor, intern
- Department and job title tracking
- Manager hierarchy (self-referential)
- Status tracking: active, inactive, on_leave, terminated
- Emergency contact info
- Custom fields (JSON)

## Files
- Migration: `100001_create_employees_table`
- Model: `App\Models\HR\Employee`
- Service: `App\Services\HR\EmployeeService`
- Interface: `App\Contracts\HR\EmployeeServiceInterface`
- DTOs: `CreateEmployeeData`, `UpdateEmployeeData`
- Events: `EmployeeHired`, `EmployeeTerminated`
- Filament: `App\Filament\Hr\Resources\EmployeeResource`
- Factory: `Database\Factories\HR\EmployeeFactory`
- Tests: `tests/Feature/HR/EmployeeServiceTest.php`
