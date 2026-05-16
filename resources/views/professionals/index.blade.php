@extends('layouts.app')

@section('content')
<div class="card card-modern p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Profissionais</h4>
        <a href="{{ route('professionals.create') }}" class="btn btn-primary">Novo profissional</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle table-hover">
            <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Função</th>
                    <th>Lotação</th>
                    <th>Regime</th>
                    <th>Situação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            @forelse($professionals as $professional)
                <tr>
                    <td>
                        {{ $professional->name }}
                        @if($professional->class_registry)
                            <br><small class="text-muted">{{ $professional->class_registry }}</small>
                        @endif
                    </td>
                    <td>{{ $professional->cpf ?? '—' }}</td>
                    <td>{{ $professional->position?->name ?? ($professional->position ?? '—') }}</td>
                    <td>{{ $professional->unit }}</td>
                    <td>
                        @switch($professional->contract_type)
                            @case('efetivo') <span class="badge text-bg-primary">Efetivo</span> @break
                            @case('temporario') <span class="badge text-bg-warning text-dark">Temporário</span> @break
                            @case('estagio') <span class="badge text-bg-info">Estágio</span> @break
                            @default <span class="text-muted">—</span>
                        @endswitch
                    </td>
                    <td>
                        <span class="badge {{ $professional->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                            {{ $professional->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>
                    <td class="d-flex gap-2">
                        <a href="{{ route('professionals.edit', $professional) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                        <form action="{{ route('professionals.destroy', $professional) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-muted text-center py-3">Nenhum profissional cadastrado.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
