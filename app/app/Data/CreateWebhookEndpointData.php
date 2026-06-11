<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class CreateWebhookEndpointData extends Data
{
    /** @param list<string> $events */
    public function __construct(
        public readonly string $url,
        public readonly array $events,
    ) {}

    /** @return array<string, mixed> */
    public static function rules(): array
    {
        return [
            'url' => ['required', 'url', 'starts_with:https://'],
            'events' => ['required', 'array', 'min:1'],
            'events.*' => ['string'],
        ];
    }

    /** @return array<string, string> */
    public static function messages(): array
    {
        return [
            'url.starts_with' => 'Webhook URLs must use HTTPS.',
        ];
    }
}
