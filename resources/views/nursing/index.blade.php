@extends('layouts.app')

@section('content')
<div class="card card-modern p-3">
    <h4>Enfermagem</h4>
    <p class="text-muted">Profissionais cadastrados como enfermeiro e técnico de enfermagem.</p>
    <table class="table align-middle">
        <thead><tr><th>Nome</th><th>Cargo</th><th>Unidade</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($professionals as $professional)
            <tr>
                <td>{{ $professional->name }}</td>
                <td>{{ $professional->position }}</td>
                <td>{{ $professional->unit }}</td>
                <td>{{ $professional->is_active ? 'Ativo' : 'Inativo' }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-muted">Nenhum profissional de enfermagem encontrado.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
