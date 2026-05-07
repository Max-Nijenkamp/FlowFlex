<?php

namespace App\Http\Controllers\Api\V1\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $company = $request->attributes->get('api_company');

        $tickets = Ticket::where('company_id', $company->id)
            ->with(['contact', 'crmCompany'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return response()->json([
            'data' => $tickets->map(fn (Ticket $ticket) => [
                'id'          => $ticket->id,
                'subject'     => $ticket->subject,
                'status'      => $ticket->status?->value,
                'priority'    => $ticket->priority?->value,
                'contact'     => $ticket->contact?->full_name,
                'company'     => $ticket->crmCompany?->name,
                'assigned_to' => $ticket->assigned_to,
                'resolved_at' => $ticket->resolved_at?->toDateTimeString(),
                'created_at'  => $ticket->created_at?->toDateTimeString(),
            ]),
            'meta' => [
                'total'        => $tickets->total(),
                'per_page'     => $tickets->perPage(),
                'current_page' => $tickets->currentPage(),
                'last_page'    => $tickets->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $company = $request->attributes->get('api_company');

        $ticket = Ticket::where('company_id', $company->id)
            ->with(['contact', 'crmCompany', 'messages'])
            ->findOrFail($id);

        return response()->json([
            'data' => [
                'id'            => $ticket->id,
                'subject'       => $ticket->subject,
                'status'        => $ticket->status?->value,
                'priority'      => $ticket->priority?->value,
                'contact'       => $ticket->contact?->full_name,
                'company'       => $ticket->crmCompany?->name,
                'assigned_to'   => $ticket->assigned_to,
                'resolved_at'   => $ticket->resolved_at?->toDateTimeString(),
                'sla_breach_at' => $ticket->sla_breach_at?->toDateTimeString(),
                'created_at'    => $ticket->created_at?->toDateTimeString(),
                'messages_count'=> $ticket->messages->count(),
            ],
        ]);
    }
}
