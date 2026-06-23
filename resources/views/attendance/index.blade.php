@extends('layouts.app')

@section('content')
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card card-modern p-3">
            <h4>Lançar frequência mensal</h4>
            <form method="POST" action="{{ route('attendance.store') }}" class="row g-2 mt-2">
                @csrf
                <div class="col-12"><label class="form-label">Profissional</label><select name="professional_id" class="form-select" required>@foreach($professionals as $professional)<option value="{{ $professional->id }}">{{ $professional->name }}</option>@endforeach</select></div>
                <div class="col-12"><label class="form-label">Mês</label><input type="month" name="month" value="{{ $month }}" class="form-control" required></div>
                <div class="col-4"><label class="form-label">Trabalhados</label><input type="number" name="worked_days" class="form-control" min="0" max="31" value="0" required></div>
                <div class="col-4"><label class="form-label">F. Just.</label><input type="number" name="justified_absences" class="form-control" min="0" max="31" value="0" required></div>
                <div class="col-4"><label class="form-label">F. Não Just.</label><input type="number" name="unjustified_absences" class="form-control" min="0" max="31" value="0" required></div>
                <div class="col-12"><label class="form-label">Observações</label><textarea name="observations" class="form-control"></textarea></div>
                <div class="col-12"><button class="btn btn-primary">Salvar frequência</button></div>
            </form>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card card-modern p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h4 class="mb-0">Registros de {{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $month)->format('m/Y') }}</h4>
                <form><input type="month" class="form-control" name="month" value="{{ $month }}" onchange="this.form.submit()"></form>
            </div>
            <table class="table table-sm align-middle">
                <thead><tr><th>Profissional</th><th>Trabalhados</th><th>FJ</th><th>FNJ</th><th>Obs.</th><th>Na frequência</th></tr></thead>
                <tbody>
                @forelse($records as $record)
                    <tr>
                        <td>{{ $record->professional->name }}</td>
                        <td>{{ $record->worked_days }}</td>
                        <td>{{ $record->justified_absences }}</td>
                        <td>{{ $record->unjustified_absences }}</td>
                        <td>{{ $record->observations }}</td>
                        <td>
                            <form method="POST" action="{{ route('attendance.professionals.update', $record->professional) }}">@csrf @method('PATCH')
                                <input type="hidden" name="is_frequency_enabled" value="{{ $record->professional->is_frequency_enabled ? '0' : '1' }}">
                                <button class="btn btn-sm {{ $record->professional->is_frequency_enabled ? 'btn-success' : 'btn-secondary' }}">{{ $record->professional->is_frequency_enabled ? 'Ativo' : 'Inativo' }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-muted">Sem registros no mês selecionado.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
