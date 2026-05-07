<?php

namespace App\Http\Controllers\Api\V1\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\Deal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $company = $request->attributes->get('api_company');

        $deals = Deal::where('company_id', $company->id)
            ->with(['contact', 'stage', 'crmCompany'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return response()->json([
            'data' => $deals->map(fn (Deal $deal) => [
                'id'                   => $deal->id,
                'title'                => $deal->title,
                'value'                => $deal->value,
                'currency'             => $deal->currency,
                'status'               => $deal->status?->value,
                'contact'              => $deal->contact?->full_name,
                'company'              => $deal->crmCompany?->name,
                'stage'                => $deal->stage?->name,
                'close_probability'    => $deal->close_probability,
                'expected_close_date'  => $deal->expected_close_date?->toDateString(),
            ]),
            'meta' => [
                'total'        => $deals->total(),
                'per_page'     => $deals->perPage(),
                'current_page' => $deals->currentPage(),
                'last_page'    => $deals->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $company = $request->attributes->get('api_company');

        $deal = Deal::where('company_id', $company->id)
            ->with(['contact', 'stage', 'pipeline', 'crmCompany'])
            ->findOrFail($id);

        return response()->json([
            'data' => [
                'id'                   => $deal->id,
                'title'                => $deal->title,
                'value'                => $deal->value,
                'currency'             => $deal->currency,
                'status'               => $deal->status?->value,
                'contact'              => $deal->contact?->full_name,
                'company'              => $deal->crmCompany?->name,
                'pipeline'             => $deal->pipeline?->name,
                'stage'                => $deal->stage?->name,
                'close_probability'    => $deal->close_probability,
                'expected_close_date'  => $deal->expected_close_date?->toDateString(),
                'closed_at'            => $deal->closed_at?->toDateTimeString(),
                'lost_reason'          => $deal->lost_reason,
            ],
        ]);
    }
}
