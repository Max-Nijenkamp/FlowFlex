<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Contracts\CRM\DealServiceInterface;
use App\Data\CRM\DealData;
use App\Http\Controllers\Controller;
use App\Models\CRM\Deal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function __construct(
        private readonly DealServiceInterface $deals,
    ) {}

    public function index(): JsonResponse
    {
        $page = Deal::query()->latest()->paginate(min((int) request('per_page', 25), 100));

        return response()->json([
            'data' => collect($page->items())->map(fn (Deal $d) => DealData::fromModel($d)),
            'meta' => ['current_page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'per_page' => $page->perPage(), 'total' => $page->total()],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'stage_id' => ['required', 'string'],
            'value_cents' => ['required', 'integer', 'min:0'],
            'contact_id' => ['nullable', 'string'],
            'account_id' => ['nullable', 'string'],
            'expected_close_date' => ['nullable', 'date'],
        ]);

        $deal = $this->deals->create(
            name: $validated['name'],
            stageId: $validated['stage_id'],
            valueCents: $validated['value_cents'],
            contactId: $validated['contact_id'] ?? null,
            accountId: $validated['account_id'] ?? null,
            expectedCloseDate: $validated['expected_close_date'] ?? null,
        );

        return response()->json(['data' => DealData::fromModel($deal)], 201);
    }
}
