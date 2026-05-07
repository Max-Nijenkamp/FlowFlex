<?php

namespace App\Http\Controllers\Api\V1\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\CrmContact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $company = $request->attributes->get('api_company');

        $contacts = CrmContact::where('company_id', $company->id)
            ->with('crmCompany')
            ->orderBy('last_name')
            ->paginate(25);

        return response()->json([
            'data' => $contacts->map(fn (CrmContact $contact) => [
                'id'         => $contact->id,
                'first_name' => $contact->first_name,
                'last_name'  => $contact->last_name,
                'full_name'  => $contact->full_name,
                'email'      => $contact->email,
                'phone'      => $contact->phone,
                'job_title'  => $contact->job_title,
                'type'       => $contact->type?->value,
                'company'    => $contact->crmCompany?->name,
            ]),
            'meta' => [
                'total'        => $contacts->total(),
                'per_page'     => $contacts->perPage(),
                'current_page' => $contacts->currentPage(),
                'last_page'    => $contacts->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $company = $request->attributes->get('api_company');

        $contact = CrmContact::where('company_id', $company->id)
            ->with('crmCompany')
            ->findOrFail($id);

        return response()->json([
            'data' => [
                'id'           => $contact->id,
                'first_name'   => $contact->first_name,
                'last_name'    => $contact->last_name,
                'full_name'    => $contact->full_name,
                'email'        => $contact->email,
                'phone'        => $contact->phone,
                'job_title'    => $contact->job_title,
                'type'         => $contact->type?->value,
                'company'      => $contact->crmCompany?->name,
                'linkedin_url' => $contact->linkedin_url,
                'tags'         => $contact->tags,
                'notes'        => $contact->notes,
            ],
        ]);
    }
}
