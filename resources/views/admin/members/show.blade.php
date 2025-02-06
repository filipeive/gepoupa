<!-- resources/views/social-funds/index.blade.php -->
@extends('adminlte::page')
@section('title', 'Socialfunds')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Detalhes do Membro</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Membros</li>
            </ol>
        </div>
    </div>
@stop
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <!-- Perfil do Membro -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <h3 class="profile-username text-center">{{ $member->name }}</h3>
                        <p class="text-muted text-center">{{ $member->email }}</p>
                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Total Poupado</b>
                                <a class="float-right">{{ number_format($totalSavings, 2) }} MZN</a>
                            </li>
                            <li class="list-group-item">
                                <b>Fundos Sociais</b>
                                <a class="float-right">{{ number_format($totalSocialFunds, 2) }} MZN</a>
                            </li>
                            <li class="list-group-item">
                                <b>Empréstimos Ativos</b>
                                <a class="float-right">{{ $activeLoans->count() }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link active" href="#savings" data-toggle="tab">Poupanças</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#social" data-toggle="tab">Fundos Sociais</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#loans" data-toggle="tab">Empréstimos</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Tab Poupanças -->
                            <div class="active tab-pane" id="savings">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Valor</th>
                                            <th>Comprovativo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($member->savings as $saving)
                                            <tr>
                                                <td>{{ $saving->payment_date }}</td>
                                                <td>{{ number_format($saving->amount, 2) }} MZN</td>
                                                <td>
                                                    @if ($saving->proof_file)
                                                        <a href="{{ Storage::url($saving->proof_file) }}"
                                                            target="_blank">Ver</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tab Fundos Sociais -->
                            <div class="tab-pane" id="social">
                                <!-- Resumo dos Fundos Sociais -->
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="info-box bg-info">
                                            <span class="info-box-icon"><i class="fas fa-money-bill"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Contribuído</span>
                                                <span class="info-box-number">{{ number_format($totalSocialFunds, 2) }}
                                                    MZN</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box bg-warning">
                                            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Pendente</span>
                                                <span class="info-box-number">{{ number_format($pendingSocialFunds, 2) }}
                                                    MZN</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box bg-danger">
                                            <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total em Multas</span>
                                                <span class="info-box-number">
                                                    {{ number_format($member->socialFunds()->sum('penalty_amount'), 2) }}
                                                    MZN
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabela de Fundos Sociais -->
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Data de Pagamento</th>
                                                <th>Valor</th>
                                                <th>Status</th>
                                                <th>Multa</th>
                                                <th>Comprovativo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($member->socialFunds()->orderBy('payment_date', 'desc')->get() as $fund)
                                                <tr>
                                                    <td>{{ $fund->payment_date->format('d/m/Y') }}</td>
                                                    <td>{{ number_format($fund->amount, 2) }} MZN</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-{{ $fund->status === 'paid' ? 'success' : ($fund->status === 'pending' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($fund->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ number_format($fund->penalty_amount, 2) }} MZN</td>
                                                    <td>
                                                        @if ($fund->proof_file)
                                                            <a href="{{ Storage::url($fund->proof_file) }}" target="_blank"
                                                                class="btn btn-sm btn-info">
                                                                <i class="fas fa-file"></i> Ver
                                                            </a>
                                                        @else
                                                            <span class="text-muted">Sem comprovativo</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab Empréstimos -->
                            <!-- Na tab de empréstimos -->
                            <div class="tab-pane" id="loans">
                                <!-- Resumo de Empréstimos -->
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="small-box bg-info">
                                            <div class="inner">
                                                <h3>{{ $loanStats['total'] }}</h3>
                                                <p>Total de Empréstimos</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-credit-card"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="small-box bg-warning">
                                            <div class="inner">
                                                <h3>{{ $loanStats['active'] }}</h3>
                                                <p>Empréstimos Ativos</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="small-box bg-success">
                                            <div class="inner">
                                                <h3>{{ $loanStats['paid'] }}</h3>
                                                <p>Empréstimos Pagos</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="small-box bg-danger">
                                            <div class="inner">
                                                <h3>{{ $loanStats['pending'] }}</h3>
                                                <p>Empréstimos Pendentes</p>
                                            </div>
                                            <div class="icon">
                                                <i class="fas fa-hourglass-half"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabela de Empréstimos -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Data do Pedido</th>
                                                <th>Valor</th>
                                                <th>Taxa de Juros</th>
                                                <th>Status</th>
                                                <th>Vencimento</th>
                                                <th>Total Pago</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($activeLoans as $loan)
                                                <tr>
                                                    <td>{{ $loan->request_date->format('d/m/Y') }}</td>
                                                    <td>{{ number_format($loan->amount, 2) }} MZN</td>
                                                    <td>{{ $loan->interest_rate }}%</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-{{ $loan->status === 'approved' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($loan->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $loan->due_date->format('d/m/Y') }}</td>
                                                    <td>
                                                        @php
                                                            $loanPayment = $loanPayments->firstWhere('id', $loan->id);
                                                            $totalPaid = $loanPayment ? $loanPayment->total_paid : 0;
                                                        @endphp
                                                        {{ number_format($totalPaid, 2) }} MZN
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('loans.show', $loan->id) }}"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> Detalhes
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">Nenhum empréstimo encontrado
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
