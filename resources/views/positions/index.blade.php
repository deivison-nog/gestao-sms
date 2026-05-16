@extends('layouts.app')

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card card-modern p-3">
            <h5>Nova função/cargo</h5>
            <form method="POST" action="{{ route('positions.store') }}" class="mt-2">
                @csrf
                <div class="mb-2">
                    <label class="form-label">Nome da função <span class="text-danger">*</span></label>
                    <input name="name" class="form-control" placeholder="ex: Enfermeiro, Médico..." required value="{{ old('name') }}">
                </div>
                <button class="btn btn-primary">Cadastrar</button>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card card-modern p-3">
            <h5>Funções cadastradas</h5>
            <table class="table align-middle">
                <thead class="table-light">
                    <tr><th>Nome</th><th>Situação</th><th>Ações</th></tr>
                </thead>
                <tbody>
                @forelse($positions as $position)
                    <tr>
                        <td>
                            <form method="POST" action="{{ route('positions.update', $position) }}" class="d-flex gap-2 align-items-center">
                                @csrf @method('PUT')
                                <input name="name" class="form-control form-control-sm" value="{{ $position->name }}" required>
                                <input type="hidden" name="is_active" value="{{ $position->is_active ? '1' : '0' }}">
                                <button class="btn btn-sm btn-outline-primary text-nowrap">Salvar</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('positions.update', $position) }}">
                                @csrf @method('PUT')
                                <input type="hidden" name="name" value="{{ $position->name }}">
                                <input type="hidden" name="is_active" value="{{ $position->is_active ? '0' : '1' }}">
                                <button class="btn btn-sm {{ $position->is_active ? 'btn-success' : 'btn-secondary' }}">
                                    {{ $position->is_active ? 'Ativa' : 'Inativa' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('positions.destroy', $position) }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-muted">Nenhuma função cadastrada.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
