<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Data\CRM\ContactData;
use App\Http\Controllers\Controller;
use App\Models\CRM\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(): JsonResponse
    {
        $page = Contact::query()->latest()->paginate(min((int) request('per_page', 25), 100));

        return response()->json([
            'data' => collect($page->items())->map(fn (Contact $c) => ContactData::fromModel($c)),
            'meta' => ['current_page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'per_page' => $page->perPage(), 'total' => $page->total()],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'job_title' => ['nullable', 'string', 'max:150'],
            'lifecycle_stage' => ['nullable', 'in:lead,mql,sql,opportunity,customer,evangelist'],
        ]);
        $validated['owner_id'] = $request->user()->id;

        return response()->json(['data' => ContactData::fromModel(Contact::create($validated))], 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json(['data' => ContactData::fromModel(Contact::query()->findOrFail($id))]);
    }
}
