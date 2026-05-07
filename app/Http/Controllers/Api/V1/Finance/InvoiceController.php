<?php

namespace App\Http\Controllers\Api\V1\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $company = $request->attributes->get('api_company');

        $invoices = Invoice::where('company_id', $company->id)
            ->with('lines')
            ->orderBy('issue_date', 'desc')
            ->paginate(25);

        return response()->json([
            'data' => $invoices->map(fn (Invoice $invoice) => [
                'id'         => $invoice->id,
                'number'     => $invoice->number,
                'contact_id' => $invoice->contact_id,
                'currency'   => $invoice->currency,
                'issue_date' => $invoice->issue_date?->toDateString(),
                'due_date'   => $invoice->due_date?->toDateString(),
                'status'     => $invoice->status?->value,
                'subtotal'   => $invoice->subtotal,
                'tax_amount' => $invoice->tax_amount,
                'total'      => $invoice->total,
                'paid_amount'=> $invoice->paid_amount,
            ]),
            'meta' => [
                'total'        => $invoices->total(),
                'per_page'     => $invoices->perPage(),
                'current_page' => $invoices->currentPage(),
                'last_page'    => $invoices->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $company = $request->attributes->get('api_company');

        $invoice = Invoice::where('company_id', $company->id)
            ->with(['lines', 'payments', 'creditNote'])
            ->findOrFail($id);

        return response()->json([
            'data' => [
                'id'             => $invoice->id,
                'number'         => $invoice->number,
                'contact_id'     => $invoice->contact_id,
                'currency'       => $invoice->currency,
                'issue_date'     => $invoice->issue_date?->toDateString(),
                'due_date'       => $invoice->due_date?->toDateString(),
                'status'         => $invoice->status?->value,
                'notes'          => $invoice->notes,
                'discount_type'  => $invoice->discount_type,
                'discount_value' => $invoice->discount_value,
                'tax_rate'       => $invoice->tax_rate,
                'subtotal'       => $invoice->subtotal,
                'tax_amount'     => $invoice->tax_amount,
                'total'          => $invoice->total,
                'paid_amount'    => $invoice->paid_amount,
                'lines'          => $invoice->lines->map(fn ($line) => [
                    'id'          => $line->id,
                    'description' => $line->description,
                    'quantity'    => $line->quantity,
                    'unit_price'  => $line->unit_price,
                    'tax_rate'    => $line->tax_rate,
                    'subtotal'    => $line->subtotal,
                ]),
            ],
        ]);
    }
}
