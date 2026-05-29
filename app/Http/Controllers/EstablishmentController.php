<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use Illuminate\Http\Request;

class EstablishmentController extends Controller
{
    public function index()
    {
        return view('establishments.index', [
            'establishments' => Establishment::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cnes' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        Establishment::query()->create($validated);

        return back()->with('status', 'Estabelecimento cadastrado com sucesso.');
    }

    public function update(Request $request, Establishment $establishment)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cnes' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $establishment->update($validated);

        return back()->with('status', 'Estabelecimento atualizado com sucesso.');
    }

    public function destroy(Establishment $establishment)
    {
        $establishment->delete();

        return back()->with('status', 'Estabelecimento removido com sucesso.');
    }
}
