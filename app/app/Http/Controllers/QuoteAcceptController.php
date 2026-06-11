<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\CRM\QuoteServiceInterface;
use App\Models\CRM\Quote;
use App\Support\Scopes\CompanyScope;
use Illuminate\Contracts\View\View;

/**
 * Public quote-accept surface — guest route group (no app session), token-scoped
 * access only, single-use token, throttled (per crm.quotes security notes).
 */
class QuoteAcceptController extends Controller
{
    public function show(string $token): View
    {
        $quote = Quote::query()->withoutGlobalScope(CompanyScope::class)
            ->with('lines')
            ->where('accept_token', $token)
            ->where('status', 'sent')
            ->firstOrFail();

        return view('crm.quote-accept', ['quote' => $quote, 'token' => $token]);
    }

    public function accept(string $token): View
    {
        $quote = app(QuoteServiceInterface::class)->acceptByToken($token);

        return view('crm.quote-accepted', ['quote' => $quote]);
    }
}
