<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use App\Models\User;
use Illuminate\Http\Request;

class ProfessionalController extends Controller
{
    public function index()
    {
        return view('professionals.index', [
            'professionals' => Professional::query()->with('user')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('professionals.create', [
            'users' => User::query()->whereDoesntHave('professional')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'is_frequency_enabled' => ['nullable', 'boolean'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        Professional::query()->create([
            ...$validated,
            'is_active' => $request->boolean('is_active', true),
            'is_frequency_enabled' => $request->boolean('is_frequency_enabled', true),
        ]);

        return redirect()->route('professionals.index')->with('status', 'Profissional cadastrado com sucesso.');
    }

    public function edit(Professional $professional)
    {
        return view('professionals.edit', [
            'professional' => $professional,
            'users' => User::query()->where(fn ($query) => $query->whereDoesntHave('professional')->orWhere('id', $professional->user_id))->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Professional $professional)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'is_frequency_enabled' => ['nullable', 'boolean'],
            'user_id' => ['nullable', 'exists:users,id'],
        ]);

        $professional->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
            'is_frequency_enabled' => $request->boolean('is_frequency_enabled'),
        ]);

        return redirect()->route('professionals.index')->with('status', 'Profissional atualizado com sucesso.');
    }

    public function destroy(Professional $professional)
    {
        $professional->delete();

        return redirect()->route('professionals.index')->with('status', 'Profissional removido com sucesso.');
    }
}
