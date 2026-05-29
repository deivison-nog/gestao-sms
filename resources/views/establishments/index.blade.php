@extends('layouts.app')

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card card-modern p-3">
            <h5>Novo estabelecimento</h5>
            <form method="POST" action="{{ route('establishments.store') }}" class="mt-2">
                @csrf
                <div class="mb-2">
                    <label class="form-label">Nome <span class="text-danger">*</span></label>
                    <input name="name" class="form-control" required value="{{ old('name') }}">
                </div>
                <div class="mb-2">
                    <label class="form-label">CNES</label>
                    <input name="cnes" class="form-control" value="{{ old('cnes') }}">
                </div>
                <div class="mb-2">
                    <label class="form-label">Endereço</label>
                    <input name="address" class="form-control" value="{{ old('address') }}">
                </div>
                <button class="btn btn-primary">Cadastrar</button>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card card-modern p-3">
            <h5>Estabelecimentos cadastrados</h5>
            <table class="table align-middle">
                <thead class="table-light">
                    <tr><th>Nome</th><th>CNES</th><th>Endereço</th><th>Ações</th></tr>
                </thead>
                <tbody>
                @forelse($establishments as $establishment)
                    <tr>
                        <td colspan="3">
                            <form method="POST" action="{{ route('establishments.update', $establishment) }}" class="row g-2">
                                @csrf @method('PUT')
                                <div class="col-md-4"><input name="name" class="form-control form-control-sm" value="{{ $establishment->name }}" required></div>
                                <div class="col-md-3"><input name="cnes" class="form-control form-control-sm" value="{{ $establishment->cnes }}"></div>
                                <div class="col-md-5 d-flex gap-2">
                                    <input name="address" class="form-control form-control-sm" value="{{ $establishment->address }}">
                                    <button class="btn btn-sm btn-outline-primary text-nowrap">Salvar</button>
                                </div>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('establishments.destroy', $establishment) }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted">Nenhum estabelecimento cadastrado.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
