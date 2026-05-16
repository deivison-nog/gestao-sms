<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        return view('positions.index', [
            'positions' => Position::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:positions,name'],
        ]);

        Position::query()->create([...$validated, 'is_active' => true]);

        return back()->with('status', 'Função cadastrada com sucesso.');
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', "unique:positions,name,{$position->id}"],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $position->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', 'Função atualizada com sucesso.');
    }

    public function destroy(Position $position)
    {
        $position->delete();

        return back()->with('status', 'Função removida com sucesso.');
    }

    /** API endpoint used by the professional form via JS fetch */
    public function apiList()
    {
        return response()->json(
            Position::query()->where('is_active', true)->orderBy('name')->get(['id', 'name'])
        );
    }
}
