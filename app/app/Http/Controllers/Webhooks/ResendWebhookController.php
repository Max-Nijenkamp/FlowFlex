<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\Actions\RecordSoftBounceAction;
use App\Actions\SuppressEmailAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResendWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $type = (string) $request->input('type');
        $email = (string) $request->input('data.to.0', $request->input('data.email', ''));

        if ($email !== '') {
            match ($type) {
                'email.bounced' => SuppressEmailAction::run($email, 'bounce'),
                'email.complained' => SuppressEmailAction::run($email, 'complaint'),
                'email.delivery_delayed' => RecordSoftBounceAction::run($email),
                default => null,
            };
        }

        return response()->json(['received' => true]);
    }
}
