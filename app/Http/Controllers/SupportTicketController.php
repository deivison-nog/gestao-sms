<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index()
    {
        return view('support.index', [
            'tickets' => SupportTicket::query()->with(['openedBy', 'responses.user'])->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:informatica,estrutura,insumos'],
            'description' => ['required', 'string'],
        ]);

        SupportTicket::query()->create([
            ...$validated,
            'status' => 'aberto',
            'opened_by' => $request->user()->id,
        ]);

        return back()->with('status', 'Chamado aberto com sucesso.');
    }

    public function update(Request $request, SupportTicket $supportTicket)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:aberto,pendente,fechado'],
        ]);

        $supportTicket->update($validated);

        return back()->with('status', 'Status do chamado atualizado.');
    }

    public function destroy(SupportTicket $supportTicket)
    {
        $supportTicket->delete();

        return back()->with('status', 'Chamado removido.');
    }
}
