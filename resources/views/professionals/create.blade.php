@extends('layouts.app')

@section('content')
<div class="card card-modern p-3">
    <h4>Novo profissional</h4>
    <form action="{{ route('professionals.store') }}" method="POST" class="mt-3">
        @csrf
        @include('professionals._form')
        <button class="btn btn-primary mt-3">Salvar</button>
    </form>
</div>
@endsection
