@extends('adminlte::page')
@section('title', 'Gestão de Empréstimos')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Gestão de Empréstimos</h1>
    </div>
    <div class="col-sm-6">
        <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#createLoanModal">
            <i class="fas fa-plus"></i> Novo Empréstimo
        </button>
        <a href="{{ route('loans.export') }}" class="btn btn-success sm float-right" style="margin-right: 10px">
            <i class="fas fa-file-excel"></i> Exportar para Excel
        </a>
    </div>
</div>
@stop

@section('content')
<!-- Estatísticas -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $loanStats['total'] }}</h3>
                <p>Total de Empréstimos</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $loanStats['active'] }}</h3>
                <p>Empréstimos Ativos</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $loanStats['pending'] }}</h3>
                <p>Pendentes de Aprovação</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $loanStats['paid'] }}</h3>
                <p>Empréstimos Pagos</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-double"></i>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filtros</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('loans.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">Todos</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente
                            </option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprovado
                            </option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejeitado
                            </option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Pago</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="user_id">Membro</label>
                        <select name="user_id" id="user_id" class="form-control">
                            <option value="">Todos</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}" {{ request('user_id') == $member->id ? 'selected' : '' }}>
                                    {{ $member->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="start_date">Data Inicial</label>
                        <input type="date" name="start_date" id="start_date" class="form-control"
                            value="{{ request('start_date') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="end_date">Data Final</label>
                        <input type="date" name="end_date" id="end_date" class="form-control"
                            value="{{ request('end_date') }}">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="{{ route('loans.index') }}" class="btn btn-secondary">Limpar Filtros</a>
        </form>
    </div>
</div>
<!-- Lista de Empréstimos -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Empréstimos</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Membro</th>
                        <th>Valor</th>
                        <th>Taxa de Juros</th>
                        <th>Saldo Atual</th> {{-- Added: Current Balance --}}
                        <th>Data do Pedido</th>
                        <th>Vencimento</th>
                        <th>Status</th>
                        <th>Total Pago</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($loans as $loan)
                        <tr>
                            <td>
                                @if ($loan->user)
                                    {{ $loan->user->name }}
                                @else
                                    <span class="text-danger">Usuário não encontrado</span>
                                @endif
                            </td>
                            <td>{{ number_format($loan->amount, 2) }} MZN</td>
                            <td>{{ $loan->interest_rate }}%</td> {{-- Corrected position --}}
                            <td>
                                <span class="text-danger font-weight-bold">
                                    {{ number_format($loan->current_balance, 2) }} MZN
                                </span>
                            </td> {{-- Corrected position --}}
                            <td>{{ $loan->request_date->format('d/m/Y') }}</td>
                            <td>{{ $loan->due_date->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge badge-{{ $loan->status === 'approved'
                                    ? 'success'
                                    : ($loan->status === 'pending'
                                        ? 'warning'
                                        : ($loan->status === 'rejected'
                                            ? 'danger'
                                            : 'info')) }}">
                                    {{ ucfirst($loan->status) }}
                                </span>
                            </td>
                            <td>
                                {{ number_format($loan->payments->sum('amount'), 2) }} MZN
                            </td>
                            <td>
                                <a href="{{ route('loans.show', $loan) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if ($loan->status !== 'paid')
                                    <a href="#" class="btn btn-sm btn-primary" data-toggle="modal"
                                        data-target="#editLoanModal{{ $loan->id }}">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $loans->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Modal de Criação de Empréstimo -->
<div class="modal fade" id="createLoanModal" tabindex="-1" role="dialog" aria-labelledby="createLoanModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createLoanModalLabel">Novo Empréstimo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('loans.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="user_id">Membro</label>
                        <select name="user_id" id="user_id" class="form-control" required>
                            <option value="">Selecione um membro</option>
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="amount">Valor</label>
                        <input type="number" name="amount" id="amount" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="interest_rate">Taxa de Juros (%)</label>
                        <input type="number" name="interest_rate" id="interest_rate" class="form-control" step="0.01"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="request_date">Data do Pedido</label>
                        <input type="date" name="request_date" id="request_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="due_date">Data de Vencimento</label>
                        <input type="date" name="due_date" id="due_date" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modais de Edição de Empréstimo -->
@foreach ($loans as $loan)
    <div class="modal fade" id="editLoanModal{{ $loan->id }}" tabindex="-1" role="dialog"
        aria-labelledby="editLoanModalLabel{{ $loan->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editLoanModalLabel{{ $loan->id }}">Editar Empréstimo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('loans.update', $loan) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="pending" {{ $loan->status === 'pending' ? 'selected' : '' }}>Pendente
                                </option>
                                <option value="approved" {{ $loan->status === 'approved' ? 'selected' : '' }}>Aprovado
                                </option>
                                <option value="rejected" {{ $loan->status === 'rejected' ? 'selected' : '' }}>
                                    Rejeitado
                                </option>
                                <option value="paid" {{ $loan->status === 'paid' ? 'selected' : '' }}>Pago</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@stop

@section('css')
<style>
    .table td,
    .table th {
        vertical-align: middle;
    }

    .small-box {
        cursor: pointer;
    }

    .small-box:hover {
        transform: scale(1.05);
        transition: transform 0.3s ease;
    }
</style>
@stop

@section('js')
<script>
    // Feedback ao criar empréstimo
    @if (session('success'))
        toastr.success("{{ session('success') }}");
    @endif

    // Feedback ao atualizar empréstimo
    @if (session('error'))
        toastr.error("{{ session('error') }}");
    @endif
</script>
@stop