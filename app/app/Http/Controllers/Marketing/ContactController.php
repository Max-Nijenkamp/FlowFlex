<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Data\ContactMessageData;
use App\Mail\ContactMessageMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController
{
    public function store(Request $request): RedirectResponse
    {
        // Honeypot: bots fill the hidden "website" field — accept silently so
        // they can't tell, send nothing (security.md public-endpoint pattern).
        if ($request->filled('website')) {
            return back()->with('success', 'Thanks — we reply within one business day.');
        }

        $data = ContactMessageData::validateAndCreate($request);

        Mail::to(config('mail.from.address'))->send(new ContactMessageMail($data));

        return back()->with('success', 'Thanks — we reply within one business day.');
    }
}
