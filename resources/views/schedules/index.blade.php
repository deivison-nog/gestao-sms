@extends('layouts.app')

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card card-modern p-3">
            <div class="d-flex justify-content-between mb-2"><h4 class="mb-0">Cronograma</h4><button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#scheduleModal">Novo agendamento</button></div>
            <div id="calendar"></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card card-modern p-3">
            <h5>Próximos lançamentos</h5>
            <ul class="list-group list-group-flush">
                @forelse($schedules as $schedule)
                    <li class="list-group-item px-0">
                        <div class="fw-semibold">{{ $schedule->scheduled_date->format('d/m/Y') }} - {{ $schedule->professional?->name ?? 'Sem profissional' }}</div>
                        <small class="text-muted">{{ $schedule->unit }} • {{ $schedule->observation }}</small>
                        <div class="mt-2 d-flex gap-2">
                            <a href="{{ route('schedules.edit', $schedule) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                            <form action="{{ route('schedules.destroy', $schedule) }}" method="POST">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Remover/Retirar nome</button></form>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item px-0 text-muted">Nenhuma escala cadastrada.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('schedules.store') }}">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Novo agendamento</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Data</label><input type="date" name="scheduled_date" id="scheduled_date" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Profissional</label><select name="professional_id" class="form-select"><option value="">Sem profissional</option>@foreach($professionals as $professional)<option value="{{ $professional->id }}">{{ $professional->name }} - {{ $professional->position }}</option>@endforeach</select></div>
                    <div class="mb-3"><label class="form-label">Unidade/Estabelecimento</label><input name="unit" class="form-control" required></div>
                    <div><label class="form-label">Observação</label><textarea name="observation" class="form-control" rows="3"></textarea></div>
                </div>
                <div class="modal-footer"><button class="btn btn-primary">Salvar</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/fullcalendar/index.global.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'pt-br',
            events: [
                @foreach($schedules as $schedule)
                {
                    title: @json(($schedule->professional?->name ?? 'Sem profissional') . ' - ' . $schedule->unit),
                    date: @json($schedule->scheduled_date->format('Y-m-d')),
                },
                @endforeach
            ],
            dateClick: function(info) {
                document.getElementById('scheduled_date').value = info.dateStr;
                new bootstrap.Modal(document.getElementById('scheduleModal')).show();
            }
        });
        calendar.render();
    });
</script>
@endpush
