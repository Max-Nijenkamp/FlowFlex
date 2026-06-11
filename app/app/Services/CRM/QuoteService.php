<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Contracts\CRM\QuoteServiceInterface;
use App\Data\CRM\CreateQuoteData;
use App\Models\CRM\Quote;
use App\Support\Scopes\CompanyScope;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuoteService implements QuoteServiceInterface
{
    public function create(CreateQuoteData $data): Quote
    {
        return DB::transaction(function () use ($data): Quote {
            $quote = Quote::create([
                'deal_id' => $data->deal_id,
                'contact_id' => $data->contact_id,
                'valid_until' => $data->valid_until ?? now()->addDays(30)->toDateString(),
            ]);

            $total = Money::ofMinor(0, 'EUR');
            foreach ($data->lines as $line) {
                $lineTotal = Money::ofMinor($line['unit_price_cents'], 'EUR')
                    ->multipliedBy((string) $line['quantity'], RoundingMode::HALF_UP);
                $total = $total->plus($lineTotal);

                $quote->lines()->create([
                    'company_id' => $quote->company_id,
                    'description' => $line['description'],
                    'quantity' => $line['quantity'],
                    'unit_price_cents' => $line['unit_price_cents'],
                    'line_total_cents' => $lineTotal->getMinorAmount()->toInt(),
                ]);
            }

            $quote->update(['total_cents' => $total->getMinorAmount()->toInt()]);

            return $quote->refresh();
        });
    }

    public function send(string $quoteId): Quote
    {
        $quote = Quote::query()->findOrFail($quoteId);

        $quote->update([
            'status' => 'sent',
            'quote_number' => $quote->quote_number
                ?? sprintf('Q-%d-%03d', now()->year, Quote::query()->withTrashed()->whereNotNull('quote_number')->count() + 1),
            'accept_token' => (string) Str::uuid(), // single-use
        ]);

        return $quote->refresh();
    }

    public function acceptByToken(string $token): Quote
    {
        // Public guest path — company context derived from the quote itself.
        $quote = Quote::query()->withoutGlobalScope(CompanyScope::class)
            ->where('accept_token', $token)
            ->where('status', 'sent')
            ->where(fn ($q) => $q->whereNull('valid_until')->orWhere('valid_until', '>=', now()->toDateString()))
            ->firstOrFail();

        $quote->update([
            'status' => 'accepted',
            'accepted_at' => now(),
            'accept_token' => null, // consumed — token is single-use
        ]);

        return $quote->refresh();
    }
}
