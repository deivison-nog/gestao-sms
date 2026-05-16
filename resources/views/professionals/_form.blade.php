<div class="row g-3">
    {{-- Nome completo (obrigatório) --}}
    <div class="col-md-8">
        <label class="form-label">Nome completo <span class="text-danger">*</span></label>
        <input class="form-control" name="name" value="{{ old('name', $professional->name ?? '') }}" required>
    </div>

    {{-- Data de nascimento --}}
    <div class="col-md-4">
        <label class="form-label">Data de nascimento</label>
        <input type="date" class="form-control" name="birth_date"
               value="{{ old('birth_date', isset($professional->birth_date) ? $professional->birth_date->format('Y-m-d') : '') }}">
    </div>

    {{-- CPF (obrigatório) --}}
    <div class="col-md-3">
        <label class="form-label">CPF <span class="text-danger">*</span></label>
        <input class="form-control" name="cpf" id="cpf" placeholder="000.000.000-00" maxlength="14"
               value="{{ old('cpf', $professional->cpf ?? '') }}" required>
    </div>

    {{-- Registro de classe --}}
    <div class="col-md-3">
        <label class="form-label">Registro de classe (ex: COREN)</label>
        <input class="form-control" name="class_registry" placeholder="ex: COREN-PE 000000"
               value="{{ old('class_registry', $professional->class_registry ?? '') }}">
    </div>

    {{-- Função (carregada via fetch) --}}
    <div class="col-md-3">
        <label class="form-label">Função</label>
        <select name="position_id" id="position_id" class="form-select">
            <option value="">Carregando funções...</option>
        </select>
    </div>

    {{-- Carga horária --}}
    <div class="col-md-3">
        <label class="form-label">Carga horária</label>
        <input class="form-control" name="workload" placeholder="ex: 40h"
               value="{{ old('workload', $professional->workload ?? '') }}">
    </div>

    {{-- Regime de contratação --}}
    <div class="col-md-4">
        <label class="form-label">Regime de contratação</label>
        <select name="contract_type" class="form-select">
            <option value="">Selecione</option>
            @foreach(['efetivo' => 'Efetivo', 'temporario' => 'Temporário', 'estagio' => 'Estágio'] as $value => $label)
                <option value="{{ $value }}" {{ old('contract_type', $professional->contract_type ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    {{-- Situação (ativo/inativo) --}}
    <div class="col-md-4">
        <label class="form-label">Situação <span class="text-danger">*</span></label>
        <select name="is_active" class="form-select">
            <option value="1" {{ old('is_active', $professional->is_active ?? true) ? 'selected' : '' }}>Ativo</option>
            <option value="0" {{ !old('is_active', $professional->is_active ?? true) ? 'selected' : '' }}>Inativo</option>
        </select>
    </div>

    {{-- Lotação (unidade, UBS, ESF) --}}
    <div class="col-md-4">
        <label class="form-label">Lotação (unidade/UBS/ESF) <span class="text-danger">*</span></label>
        <input class="form-control" name="unit" placeholder="ex: UBS Central, ESF Norte"
               value="{{ old('unit', $professional->unit ?? '') }}" required>
    </div>

    {{-- Exibir na frequência --}}
    <div class="col-md-4 d-flex align-items-center">
        <label class="form-check mt-4">
            <input type="checkbox" class="form-check-input" name="is_frequency_enabled" value="1"
                   {{ old('is_frequency_enabled', $professional->is_frequency_enabled ?? true) ? 'checked' : '' }}>
            <span class="form-check-label">Exibir na frequência</span>
        </label>
    </div>

    {{-- Vincular usuário --}}
    <div class="col-md-8">
        <label class="form-label">Vincular usuário do sistema (opcional)</label>
        <select name="user_id" class="form-select">
            <option value="">Sem vínculo</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}"
                    {{ (string) old('user_id', $professional->user_id ?? '') === (string) $user->id ? 'selected' : '' }}>
                    {{ $user->name }} ({{ $user->email }})
                </option>
            @endforeach
        </select>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const select = document.getElementById('position_id');
    const currentValue = '{{ old("position_id", $professional->position_id ?? "") }}';

    fetch('{{ route("positions.api-list") }}')
        .then(r => r.json())
        .then(positions => {
            select.innerHTML = '<option value="">Selecione a função</option>';
            positions.forEach(pos => {
                const opt = document.createElement('option');
                opt.value = pos.id;
                opt.textContent = pos.name;
                if (String(pos.id) === String(currentValue)) opt.selected = true;
                select.appendChild(opt);
            });
        })
        .catch(() => {
            select.innerHTML = '<option value="">Erro ao carregar funções</option>';
        });

    // CPF mask
    document.getElementById('cpf').addEventListener('input', function (e) {
        let v = e.target.value.replace(/\D/g, '').slice(0, 11);
        if (v.length > 9) v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
        else if (v.length > 6) v = v.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
        else if (v.length > 3) v = v.replace(/(\d{3})(\d{1,3})/, '$1.$2');
        e.target.value = v;
    });
})();
</script>
@endpush
