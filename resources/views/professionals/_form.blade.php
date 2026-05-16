<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Nome</label><input class="form-control" name="name" value="{{ old('name', $professional->name ?? '') }}" required></div>
    <div class="col-md-3"><label class="form-label">Função/Cargo</label><input class="form-control" name="position" value="{{ old('position', $professional->position ?? '') }}" required></div>
    <div class="col-md-3"><label class="form-label">Unidade</label><input class="form-control" name="unit" value="{{ old('unit', $professional->unit ?? '') }}" required></div>
    <div class="col-md-4">
        <label class="form-label">Vincular usuário (opcional)</label>
        <select name="user_id" class="form-select">
            <option value="">Sem vínculo</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ (string) old('user_id', $professional->user_id ?? '') === (string) $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 d-flex align-items-center"><label class="form-check mt-4"><input type="checkbox" class="form-check-input" name="is_active" value="1" {{ old('is_active', $professional->is_active ?? true) ? 'checked' : '' }}> <span class="form-check-label">Ativo</span></label></div>
    <div class="col-md-4 d-flex align-items-center"><label class="form-check mt-4"><input type="checkbox" class="form-check-input" name="is_frequency_enabled" value="1" {{ old('is_frequency_enabled', $professional->is_frequency_enabled ?? true) ? 'checked' : '' }}> <span class="form-check-label">Exibir na frequência</span></label></div>
</div>
