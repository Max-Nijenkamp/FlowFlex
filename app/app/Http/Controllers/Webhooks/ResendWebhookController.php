<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\Actions\HandleEmailBounceAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResendWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $type = (string) $request->input('type');
        $email = (string) $request->input('data.to.0', $request->input('data.email', ''));

        // Hard bounces only; soft/complaint suppression is a roadmap feature.
        if ($type === 'email.bounced' && $email !== '') {
            HandleEmailBounceAction::run($email);
        }

        return response()->json(['received' => true]);
    }
}
