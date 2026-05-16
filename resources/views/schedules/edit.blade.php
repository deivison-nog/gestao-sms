@extends('layouts.app')

@section('content')
<div class="card card-modern p-3">
    <h4>Editar escala</h4>
    <form method="POST" action="{{ route('schedules.update', $schedule) }}" class="row g-3 mt-1">
        @csrf
        @method('PUT')
        <div class="col-md-4"><label class="form-label">Data</label><input type="date" name="scheduled_date" value="{{ $schedule->scheduled_date->format('Y-m-d') }}" class="form-control" required></div>
        <div class="col-md-4"><label class="form-label">Profissional</label><select name="professional_id" class="form-select"><option value="">Sem profissional</option>@foreach($professionals as $professional)<option value="{{ $professional->id }}" {{ (int)$schedule->professional_id === (int)$professional->id ? 'selected' : '' }}>{{ $professional->name }}</option>@endforeach</select></div>
        <div class="col-md-4"><label class="form-label">Unidade</label><input type="text" name="unit" value="{{ $schedule->unit }}" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Observação</label><textarea name="observation" class="form-control">{{ $schedule->observation }}</textarea></div>
        <div class="col-12"><button class="btn btn-primary">Salvar alterações</button></div>
    </form>
</div>
@endsection
