<?php

namespace App\Http\Controllers\Api\V1\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $company = $request->attributes->get('api_company');

        $expenses = Expense::where('company_id', $company->id)
            ->with(['tenant', 'expenseCategory'])
            ->orderBy('expense_date', 'desc')
            ->paginate(25);

        return response()->json([
            'data' => $expenses->map(fn (Expense $expense) => [
                'id'           => $expense->id,
                'description'  => $expense->description,
                'amount'       => $expense->amount,
                'currency'     => $expense->currency,
                'expense_date' => $expense->expense_date?->toDateString(),
                'status'       => $expense->status?->value,
                'tenant_id'    => $expense->tenant_id,
                'category'     => $expense->expenseCategory?->name,
                'vendor'       => $expense->vendor,
            ]),
            'meta' => [
                'total'        => $expenses->total(),
                'per_page'     => $expenses->perPage(),
                'current_page' => $expenses->currentPage(),
                'last_page'    => $expenses->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $company = $request->attributes->get('api_company');

        $expense = Expense::where('company_id', $company->id)
            ->with(['tenant', 'expenseCategory', 'expenseReport'])
            ->findOrFail($id);

        return response()->json([
            'data' => [
                'id'               => $expense->id,
                'description'      => $expense->description,
                'amount'           => $expense->amount,
                'currency'         => $expense->currency,
                'expense_date'     => $expense->expense_date?->toDateString(),
                'status'           => $expense->status?->value,
                'tenant_id'        => $expense->tenant_id,
                'category'         => $expense->expenseCategory?->name,
                'vendor'           => $expense->vendor,
                'mileage_km'       => $expense->mileage_km,
                'rejection_reason' => $expense->rejection_reason,
                'approved_at'      => $expense->approved_at?->toDateTimeString(),
            ],
        ]);
    }
}
