<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->bearerToken() ?? $request->header('X-API-Key');

        if (! $key) {
            return response()->json(['error' => 'API key required'], 401);
        }

        $apiKey = ApiKey::where('key_hash', hash('sha256', $key))
            ->whereNull('deleted_at')
            ->first();

        if (! $apiKey || $apiKey->isExpired()) {
            return response()->json(['error' => 'Invalid or expired API key'], 401);
        }

        $apiKey->updateQuietly(['last_used_at' => now()]);

        $request->attributes->set('api_company', $apiKey->company);
        $request->attributes->set('api_key', $apiKey);

        return $next($request);
    }
}
