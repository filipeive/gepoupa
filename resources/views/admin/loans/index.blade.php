@extends('adminlte::page')
@section('title', 'Gestão de Empréstimos')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Gestão de Empréstimos</h1>
        </div>
        <div class="col-sm-6">
            <a href="{{ route('admin.loans.create') }}" class="btn btn-primary float-right">
                <i class="fas fa-plus"></i> Novo Empréstimo
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
            <div class="small-box bg-success">
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
                                <td>{{ $loan->interest_rate }}%</td>
                                <td>{{ $loan->request_date->format('d/m/Y') }}</td>
                                <td>{{ $loan->due_date->format('d/m/Y') }}</td>
                                <td>
                                    <span
                                        class="badge badge-{{ $loan->status === 'approved'
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
                                                <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $loans->links() }}
        </div>
    </div>
@stop

@section('css')
    <style>
        .table td,
        .table th {
            vertical-align: middle;
        }
    </style>
@stop
