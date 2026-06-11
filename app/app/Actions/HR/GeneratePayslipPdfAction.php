<?php

declare(strict_types=1);

namespace App\Actions\HR;

use App\Models\HR\Payslip;
use App\Settings\CompanyIdentitySettings;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\LaravelPdf\Facades\Pdf;

class GeneratePayslipPdfAction
{
    use AsAction;

    /** Caller must hold hr.payroll.view-sensitive — amounts are decrypted here. */
    public function handle(string $payslipId): string
    {
        $payslip = Payslip::query()->with('employee')->findOrFail($payslipId);

        $path = "companies/{$payslip->company_id}/hr_payslips/{$payslip->id}/payslip.pdf";

        Pdf::view('pdf.payslip', [
            'payslip' => $payslip,
            'amounts' => $payslip->amounts(),
            'companyName' => app(CompanyIdentitySettings::class)->name ?: config('app.name'),
        ])->disk(config('filesystems.default'))->save($path);

        $payslip->update(['pdf_path' => $path]);

        return $path;
    }
}
