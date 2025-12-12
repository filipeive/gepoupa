<!-- resources/views/admin/users/show.blade.php -->
@extends('adminlte::page')

@section('title', 'Detalhes do Usuário')

@section('content_header')
    <h1>Detalhes do Usuário</h1>
@stop

@section('content')
<div class="row">
    <!-- Coluna de Informações Pessoais -->
    <div class="col-md-3">
        <!-- Profile Image -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle"
                         src="{{ asset('vendor/adminlte/dist/img/user4-128x128.jpg') }}"
                         alt="User profile picture">
                </div>

                <h3 class="profile-username text-center">{{ $user->name }}</h3>
                <p class="text-muted text-center">{{ $user->role }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Email</b> <a class="float-right">{{ $user->email }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Telefone</b> <a class="float-right">{{ $user->phone ?? 'N/A' }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Status</b> 
                        <span class="float-right badge badge-{{ $user->status ? 'success' : 'danger' }}">
                            {{ $user->status ? 'Ativo' : 'Inativo' }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Resumo Financeiro Box -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Resumo Financeiro</h3>
            </div>
            <div class="card-body">
                <strong><i class="fas fa-piggy-bank mr-1"></i> Total Poupado</strong>
                <p class="text-muted">
                    {{ number_format($totalSavings, 2) }} MZN
                </p>
                <hr>
                <strong><i class="fas fa-hand-holding-usd mr-1"></i> Fundo Social</strong>
                <p class="text-muted">
                    {{ number_format($totalSocialFunds, 2) }} MZN
                </p>
                <hr>
                <strong><i class="fas fa-credit-card mr-1"></i> Empréstimos Ativos</strong>
                <p class="text-muted">
                    {{ $activeLoans->count() }}
                </p>
            </div>
        </div>
    </div>

    <!-- Coluna de Detalhes -->
    <div class="col-md-9">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link active" href="#savings" data-toggle="tab">
                            Poupanças
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#social_funds" data-toggle="tab">
                            Fundos Sociais
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#loans" data-toggle="tab">
                            Empréstimos
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Tab Poupanças -->
                    <div class="active tab-pane" id="savings">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Valor</th>
                                        <th>Comprovativo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->savings as $saving)
                                    <tr>
                                        <td>{{ $saving->payment_date->format('d/m/Y') }}</td>
                                        <td>{{ number_format($saving->amount, 2) }} MZN</td>
                                        <td>
                                            @if($saving->proof_file)
                                                <a href="{{ Storage::url($saving->proof_file) }}" 
                                                   class="btn btn-sm btn-info" target="_blank">
                                                    <i class="fas fa-file"></i> Ver
                                                </a>
                                            @else
                                                <span class="badge badge-warning">Sem comprovativo</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Nenhuma poupança registrada</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab Fundos Sociais -->
                    <div class="tab-pane" id="social_funds">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Valor</th>
                                        <th>Status</th>
                                        <th>Multa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->socialFunds as $fund)
                                    <tr>
                                        <td>{{ $fund->payment_date->format('d/m/Y') }}</td>
                                        <td>{{ number_format($fund->amount, 2) }} MZN</td>
                                        <td>
                                            <span class="badge badge-{{ $fund->status == 'paid' ? 'success' : 'warning' }}">
                                                {{ $fund->status }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($fund->penalty_amount, 2) }} MZN</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Nenhum fundo social registrado</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab Empréstimos -->
                    <div class="tab-pane" id="loans">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Valor</th>
                                        <th>Status</th>
                                        <th>Juros</th>
                                        <th>Vencimento</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($activeLoans as $loan)
                                    <tr>
                                        <td>{{ $loan->request_date->format('d/m/Y') }}</td>
                                        <td>{{ number_format($loan->amount, 2) }} MZN</td>
                                        <td>
                                            <span class="badge badge-{{ $loan->status == 'approved' ? 'success' : 'warning' }}">
                                                {{ $loan->status }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($loan->interest_rate, 2) }}%</td>
                                        <td>{{ $loan->due_date->format('d/m/Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Nenhum empréstimo registrado</td>
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
@stop
@section('footer')
    @include('adminlte.footer')
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Ativar as tabs do Bootstrap
            $('a[data-toggle="tab"]').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });
        });
    </script>
@stop
