@extends('layouts.app')

@section('content')
<div class="card card-modern p-3">
    <h4>Permissões por função</h4>
    <p class="text-muted">Edite os itens do menu lateral por função/cargo.</p>
    @foreach($roles as $role)
        <form action="{{ route('roles.update', $role) }}" method="POST" class="border rounded p-3 mb-3 bg-white">
            @csrf
            @method('PUT')
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">{{ $role->name }}</h5>
                <button class="btn btn-primary btn-sm">Salvar permissões</button>
            </div>
            <div class="row g-2">
                @foreach($permissions as $permission)
                    <div class="col-md-4">
                        <label class="form-check">
                            <input type="checkbox" class="form-check-input" name="permissions[]" value="{{ $permission->key }}" {{ $role->menuPermissions->contains('id', $permission->id) ? 'checked' : '' }}>
                            <span class="form-check-label">{{ $permission->label }}</span>
                        </label>
                    </div>
                @endforeach
            </div>
        </form>
    @endforeach
</div>
@endsection
