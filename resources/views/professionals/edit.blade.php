@extends('layouts.app')

@section('content')
<div class="card card-modern p-3">
    <h4>Editar profissional</h4>
    <form action="{{ route('professionals.update', $professional) }}" method="POST" class="mt-3">
        @csrf
        @method('PUT')
        @include('professionals._form')
        <button class="btn btn-primary mt-3">Atualizar</button>
    </form>
</div>
@endsection
