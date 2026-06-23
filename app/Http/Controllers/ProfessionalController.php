<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use App\Models\Professional;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfessionalController extends Controller
{
    public function index()
    {
        return view('professionals.index', [
            'professionals' => Professional::query()->with(['user', 'position'])->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('professionals.create', [
            'users' => User::query()->whereDoesntHave('professional')->orderBy('name')->get(),
            'establishments' => Establishment::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'birth_date'         => ['nullable', 'date'],
            'cpf'                => ['required', 'string', 'max:14', 'unique:professionals,cpf'],
            'class_registry'     => ['nullable', 'string', 'max:50'],
            'position_id'        => ['nullable', 'exists:positions,id'],
            'workload'           => ['nullable', 'string', 'max:20'],
            'contract_type'      => ['nullable', 'in:efetivo,temporario,estagio'],
            'unit'               => ['required', 'string', 'max:255', Rule::exists('establishments', 'name')],
            'is_active'          => ['nullable', 'boolean'],
            'is_frequency_enabled' => ['nullable', 'boolean'],
            'user_id'            => ['nullable', 'exists:users,id'],
        ]);

        Professional::query()->create(array_merge($validated, [
            'is_active' => $request->boolean('is_active', true),
            'is_frequency_enabled' => $request->boolean('is_frequency_enabled', true),
        ]));

        return redirect()->route('professionals.index')->with('status', 'Profissional cadastrado com sucesso.');
    }

    public function edit(Professional $professional)
    {
        return view('professionals.edit', [
            'professional' => $professional,
            'users' => User::query()->where(fn ($query) => $query->whereDoesntHave('professional')->orWhere('id', $professional->user_id))->orderBy('name')->get(),
            'establishments' => Establishment::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Professional $professional)
    {
        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'birth_date'         => ['nullable', 'date'],
            'cpf'                => ['required', 'string', 'max:14', "unique:professionals,cpf,{$professional->id}"],
            'class_registry'     => ['nullable', 'string', 'max:50'],
            'position_id'        => ['nullable', 'exists:positions,id'],
            'workload'           => ['nullable', 'string', 'max:20'],
            'contract_type'      => ['nullable', 'in:efetivo,temporario,estagio'],
            'unit'               => ['required', 'string', 'max:255', Rule::exists('establishments', 'name')],
            'is_active'          => ['nullable', 'boolean'],
            'is_frequency_enabled' => ['nullable', 'boolean'],
            'user_id'            => ['nullable', 'exists:users,id'],
        ]);

        $professional->update(array_merge($validated, [
            'is_active' => $request->boolean('is_active'),
            'is_frequency_enabled' => $request->boolean('is_frequency_enabled'),
        ]));

        return redirect()->route('professionals.index')->with('status', 'Profissional atualizado com sucesso.');
    }

    public function destroy(Professional $professional)
    {
        $professional->delete();

        return redirect()->route('professionals.index')->with('status', 'Profissional removido com sucesso.');
    }
}
