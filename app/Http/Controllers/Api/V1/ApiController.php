<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        /** @var \App\Models\Company $company */
        $company = $request->attributes->get('api_company');

        $activeModuleCount = $company->modules()
            ->wherePivot('is_enabled', true)
            ->count();

        return response()->json([
            'company' => [
                'id'                  => $company->id,
                'name'                => $company->name,
                'slug'                => $company->slug,
                'email'               => $company->email,
                'timezone'            => $company->timezone,
                'locale'              => $company->locale?->value,
                'currency'            => $company->currency?->value,
                'active_module_count' => $activeModuleCount,
            ],
        ]);
    }

    public function modules(Request $request): JsonResponse
    {
        /** @var \App\Models\Company $company */
        $company = $request->attributes->get('api_company');

        /** @var \App\Models\ApiKey $apiKey */
        $apiKey = $request->attributes->get('api_key');

        $modules = $company->modules()
            ->wherePivot('is_enabled', true)
            ->when(
                ! empty($apiKey->scopes),
                fn ($q) => $q->whereIn('key', $apiKey->scopes)
            )
            ->orderBy('sort_order')
            ->get(['modules.id', 'modules.key', 'modules.name', 'modules.domain', 'modules.panel_id'])
            ->map(fn ($module) => [
                'id'       => $module->id,
                'key'      => $module->key,
                'name'     => $module->name,
                'domain'   => $module->domain,
                'panel_id' => $module->panel_id,
            ]);

        return response()->json(['modules' => $modules]);
    }
}
