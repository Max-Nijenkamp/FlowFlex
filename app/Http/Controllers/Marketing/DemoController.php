<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\DemoRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DemoController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Marketing/Demo');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name'         => ['required', 'string', 'max:255'],
            'last_name'          => ['required', 'string', 'max:255'],
            'email'              => ['required', 'email', 'max:255'],
            'company_name'       => ['required', 'string', 'max:255'],
            'company_size'       => ['required', 'in:1-10,11-50,51-200,201-500,500+'],
            'modules_interested' => ['nullable', 'array'],
            'heard_from'         => ['nullable', 'string', 'max:255'],
            'notes'              => ['nullable', 'string', 'max:500'],
            'phone'              => ['nullable', 'string', 'max:50'],
            'consent'            => ['required', 'accepted'],
        ]);

        DemoRequest::create([
            ...$validated,
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
            'utm_source'   => $request->input('utm_source'),
            'utm_medium'   => $request->input('utm_medium'),
            'utm_campaign' => $request->input('utm_campaign'),
            'utm_content'  => $request->input('utm_content'),
            'utm_term'     => $request->input('utm_term'),
        ]);

        return back()->with('success', 'demo_submitted');
    }
}
