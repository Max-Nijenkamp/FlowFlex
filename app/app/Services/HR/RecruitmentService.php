<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Contracts\HR\EmployeeServiceInterface;
use App\Data\HR\CreateEmployeeData;
use App\Models\HR\Applicant;
use App\Models\HR\Employee;
use App\Models\HR\JobRequisition;
use App\Models\HR\Offer;
use App\States\HR\Applicant\Hired;
use App\Support\Scopes\CompanyScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecruitmentService
{
    public function openRequisition(string $title, string $description, string $employmentType, ?string $departmentId = null, int $headcount = 1): JobRequisition
    {
        return JobRequisition::create([
            'title' => $title,
            'description' => $description,
            'employment_type' => $employmentType,
            'department_id' => $departmentId,
            'headcount' => $headcount,
            'status' => 'open',
            'open_date' => now()->toDateString(),
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(5)),
        ]);
    }

    /** Public careers path — company resolved + validated from the requisition slug. */
    public function apply(string $requisitionSlug, string $firstName, string $lastName, string $email, ?string $phone = null): Applicant
    {
        $requisition = JobRequisition::query()->withoutGlobalScope(CompanyScope::class)
            ->where('slug', $requisitionSlug)
            ->where('status', 'open')
            ->firstOrFail();

        return Applicant::query()->withoutGlobalScope(CompanyScope::class)->create([
            'company_id' => $requisition->company_id,
            'requisition_id' => $requisition->id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'source' => 'careers',
        ]);
    }

    public function moveStage(string $applicantId, string $stateClass): Applicant
    {
        $applicant = Applicant::query()->findOrFail($applicantId);
        $applicant->status->transitionTo($stateClass);

        return $applicant->refresh();
    }

    public function makeOffer(string $applicantId, int $salaryCents, string $startDate): Offer
    {
        return Offer::create([
            'applicant_id' => $applicantId,
            'salary_raw' => (string) $salaryCents,
            'start_date' => $startDate,
        ]);
    }

    /** Converts applicant → employee via EmployeeService::hire (fires EmployeeHired there). */
    public function hire(string $applicantId): Employee
    {
        $applicant = Applicant::query()->with('requisition')->findOrFail($applicantId);
        $offer = Offer::query()->where('applicant_id', $applicantId)->latest()->first();

        return DB::transaction(function () use ($applicant, $offer): Employee {
            $employee = app(EmployeeServiceInterface::class)->hire(CreateEmployeeData::from([
                'first_name' => $applicant->first_name,
                'last_name' => $applicant->last_name,
                'email' => $applicant->email,
                'phone' => $applicant->phone,
                'hire_date' => $offer?->start_date?->toDateString() ?? now()->toDateString(),
                'job_title' => $applicant->requisition->title,
                'department_id' => $applicant->requisition->department_id,
                'employment_type' => $applicant->requisition->employment_type,
            ]));

            $applicant->status->transitionTo(Hired::class);

            // Salary from the offer lands on the payroll profile (stub created by listener).
            if ($offer !== null && $offer->salary_raw !== null) {
                app(CompensationService::class)->adjustSalary(
                    $employee->id,
                    (int) $offer->salary_raw,
                    'hire',
                    $offer->start_date->toDateString(),
                );
            }

            // Auto-close the requisition when headcount is filled.
            $hired = Applicant::query()
                ->where('requisition_id', $applicant->requisition_id)
                ->where('status', 'hired')
                ->count();
            if ($hired >= $applicant->requisition->headcount) {
                $applicant->requisition->update(['status' => 'closed']);
            }

            return $employee;
        });
    }
}
