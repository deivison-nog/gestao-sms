@extends('layouts.app')

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card card-modern p-3">
            <h4>Abrir chamado</h4>
            <form method="POST" action="{{ route('support-tickets.store') }}" class="mt-2">
                @csrf
                <div class="mb-2"><label class="form-label">Título</label><input name="title" class="form-control" required></div>
                <div class="mb-2"><label class="form-label">Categoria</label><select name="category" class="form-select"><option value="informatica">Informática</option><option value="estrutura">Estrutura</option><option value="insumos">Insumos (Limpeza)</option></select></div>
                <div class="mb-2"><label class="form-label">Descrição</label><textarea name="description" class="form-control" required></textarea></div>
                <button class="btn btn-primary">Abrir chamado</button>
            </form>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card card-modern p-3">
            <h4>Chamados</h4>
            <div class="accordion" id="ticketsAccordion">
                @forelse($tickets as $ticket)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="head{{ $ticket->id }}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ticket{{ $ticket->id }}">
                                {{ $ticket->title }} • {{ ucfirst($ticket->category) }} • <span class="ms-1 badge text-bg-info">{{ ucfirst($ticket->status) }}</span>
                            </button>
                        </h2>
                        <div id="ticket{{ $ticket->id }}" class="accordion-collapse collapse" data-bs-parent="#ticketsAccordion">
                            <div class="accordion-body">
                                <p>{{ $ticket->description }}</p>
                                <p class="text-muted">Aberto por: {{ $ticket->openedBy->name }}</p>

                                <form action="{{ route('support-tickets.update', $ticket) }}" method="POST" class="d-flex gap-2 mb-3">
                                    @csrf @method('PUT')
                                    <select name="status" class="form-select form-select-sm w-auto">
                                        @foreach(['aberto', 'pendente', 'fechado'] as $status)
                                            <option value="{{ $status }}" {{ $ticket->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-outline-primary">Atualizar status</button>
                                </form>

                                <h6>Respostas</h6>
                                <ul class="list-group list-group-flush mb-2">
                                    @forelse($ticket->responses as $response)
                                        <li class="list-group-item px-0"><strong>{{ $response->user->name }}:</strong> {{ $response->message }}</li>
                                    @empty
                                        <li class="list-group-item px-0 text-muted">Sem respostas.</li>
                                    @endforelse
                                </ul>

                                <form method="POST" action="{{ route('support-tickets.responses.store', $ticket) }}">
                                    @csrf
                                    <div class="input-group">
                                        <input name="message" class="form-control" placeholder="Responder chamado..." required>
                                        <button class="btn btn-outline-primary">Enviar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">Nenhum chamado encontrado.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
