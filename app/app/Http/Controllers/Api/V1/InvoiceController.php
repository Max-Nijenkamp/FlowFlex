<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Finance\InvoiceServiceInterface;
use App\Data\Finance\CreateInvoiceData;
use App\Data\Finance\InvoiceData;
use App\Data\Finance\RecordPaymentData;
use App\Http\Controllers\Controller;
use App\Models\Finance\Invoice;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly InvoiceServiceInterface $invoices,
    ) {}

    public function index(): JsonResponse
    {
        $page = Invoice::query()->latest('issue_date')->paginate(min((int) request('per_page', 25), 100));

        return response()->json([
            'data' => collect($page->items())->map(fn (Invoice $i) => InvoiceData::fromModel($i)),
            'meta' => ['current_page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'per_page' => $page->perPage(), 'total' => $page->total()],
        ]);
    }

    public function store(CreateInvoiceData $data): JsonResponse
    {
        return response()->json(['data' => InvoiceData::fromModel($this->invoices->create($data))], 201);
    }

    public function send(string $id): JsonResponse
    {
        return response()->json(['data' => InvoiceData::fromModel($this->invoices->send($id))]);
    }

    public function recordPayment(string $id): JsonResponse
    {
        $data = RecordPaymentData::validateAndCreate([
            'invoice_id' => $id,
            ...request()->only(['amount_cents', 'payment_date', 'payment_method', 'reference_number']),
        ]);

        return response()->json(['data' => InvoiceData::fromModel($this->invoices->recordPayment($data))], 201);
    }
}
