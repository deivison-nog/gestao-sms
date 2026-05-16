@extends('layouts.app')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card card-modern p-3"><small>Total de profissionais</small><h3>{{ $totalProfessionals }}</h3></div></div>
    <div class="col-md-3"><div class="card card-modern p-3"><small>Profissionais ativos</small><h3>{{ $activeProfessionals }}</h3></div></div>
    <div class="col-md-3"><div class="card card-modern p-3"><small>Chamados abertos</small><h3>{{ $ticketsByStatus['aberto'] ?? 0 }}</h3></div></div>
    <div class="col-md-3"><div class="card card-modern p-3"><small>Chamados pendentes</small><h3>{{ $ticketsByStatus['pendente'] ?? 0 }}</h3></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card card-modern p-3">
            <h5>Próximas escalas</h5>
            <table class="table table-sm align-middle">
                <thead><tr><th>Data</th><th>Profissional</th><th>Unidade</th></tr></thead>
                <tbody>
                @forelse($nextSchedules as $schedule)
                    <tr>
                        <td>{{ $schedule->scheduled_date->format('d/m/Y') }}</td>
                        <td>{{ $schedule->professional?->name ?? 'Sem profissional' }}</td>
                        <td>{{ $schedule->unit }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-muted">Sem escalas próximas.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card card-modern p-3">
            <h5>Resumo da frequência (mês atual)</h5>
            <p>Dias trabalhados: <strong>{{ $attendanceSummary->worked_days ?? 0 }}</strong></p>
            <p>Faltas justificadas: <strong>{{ $attendanceSummary->justified_absences ?? 0 }}</strong></p>
            <p>Faltas não justificadas: <strong>{{ $attendanceSummary->unjustified_absences ?? 0 }}</strong></p>
        </div>
    </div>
</div>
@endsection
