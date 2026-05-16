<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportTicketResponse;
use Illuminate\Http\Request;

class SupportTicketResponseController extends Controller
{
    public function store(Request $request, SupportTicket $supportTicket)
    {
        $validated = $request->validate([
            'message' => ['required', 'string'],
        ]);

        SupportTicketResponse::query()->create([
            'ticket_id' => $supportTicket->id,
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
        ]);

        return back()->with('status', 'Resposta enviada com sucesso.');
    }
}
