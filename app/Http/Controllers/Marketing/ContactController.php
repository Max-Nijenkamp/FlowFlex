<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\ContactSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'subject' => ['required', 'in:sales,support,billing,press,partnership,other'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactSubmission::create($validated);

        return back()->with('success', 'contact_submitted');
    }
}
