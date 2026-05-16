@extends('layouts.app')

@section('content')
<div class="card card-modern p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Profissionais</h4>
        <a href="{{ route('professionals.create') }}" class="btn btn-primary">Novo profissional</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Nome</th><th>Cargo</th><th>Unidade</th><th>Status</th><th>Ações</th></tr></thead>
            <tbody>
            @forelse($professionals as $professional)
                <tr>
                    <td>{{ $professional->name }}</td>
                    <td>{{ $professional->position }}</td>
                    <td>{{ $professional->unit }}</td>
                    <td>
                        <span class="badge {{ $professional->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $professional->is_active ? 'Ativo' : 'Inativo' }}</span>
                    </td>
                    <td class="d-flex gap-2">
                        <a href="{{ route('professionals.edit', $professional) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                        <form action="{{ route('professionals.destroy', $professional) }}" method="POST">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Excluir</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-muted">Nenhum profissional cadastrado.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
