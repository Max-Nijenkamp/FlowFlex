<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\HR\JobRequisition;
use App\Services\HR\RecruitmentService;
use App\Support\Scopes\CompanyScope;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Public careers surface — guest routes, throttled, honeypot-protected
 * (hr.recruitment spec). Company resolved from the requisition slug.
 */
class CareersController extends Controller
{
    public function show(string $slug): View
    {
        $requisition = JobRequisition::query()->withoutGlobalScope(CompanyScope::class)
            ->where('slug', $slug)
            ->where('status', 'open')
            ->firstOrFail();

        return view('careers.show', ['requisition' => $requisition]);
    }

    public function apply(Request $request, string $slug): RedirectResponse
    {
        // Honeypot: bots fill the hidden field.
        if ($request->filled('website')) {
            return redirect()->back();
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        app(RecruitmentService::class)->apply(
            requisitionSlug: $slug,
            firstName: $validated['first_name'],
            lastName: $validated['last_name'],
            email: $validated['email'],
            phone: $validated['phone'] ?? null,
        );

        return redirect()->back()->with('applied', true);
    }
}
