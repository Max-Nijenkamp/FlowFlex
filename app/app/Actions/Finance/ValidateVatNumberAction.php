<?php

declare(strict_types=1);

namespace App\Actions\Finance;

use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

/**
 * VIES check for EU VAT numbers. Network failure = "unverified" (null), never
 * blocks a save (spec *(assumed)* default).
 */
class ValidateVatNumberAction
{
    use AsAction;

    public function handle(string $vatNumber): ?bool
    {
        $country = substr($vatNumber, 0, 2);
        $number = substr($vatNumber, 2);

        try {
            $response = Http::timeout(5)->get(
                "https://ec.europa.eu/taxation_customs/vies/rest-api/ms/{$country}/vat/{$number}",
            );

            return $response->successful() ? (bool) $response->json('isValid') : null;
        } catch (Throwable) {
            return null; // unverified — never block the save
        }
    }
}
