<?php

declare(strict_types=1);

namespace App\Actions\Core;

use App\Data\Core\CreateDsarRequestData;
use App\Events\Core\DSARRequestSubmitted;
use App\Models\Core\DsarRequest;
use Carbon\CarbonImmutable;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateDsarRequestAction
{
    use AsAction;

    public function handle(CreateDsarRequestData $data): DsarRequest
    {
        $request = DsarRequest::create([
            'subject_email' => $data->subject_email,
            'request_type' => $data->request_type,
            'due_at' => now()->addDays(30),
        ]);

        event(new DSARRequestSubmitted(
            company_id: $request->company_id,
            dsar_request_id: $request->id,
            request_type: $request->request_type,
            subject_email: $request->subject_email,
            due_at: CarbonImmutable::parse($request->due_at),
        ));

        return $request;
    }
}
