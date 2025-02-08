@extends('adminlte::page')

@section('title', 'Relatórios')

@section('content_header')
    <h1>Relatórios e Estatísticas</h1>
@stop

@section('content')
    {{-- Cards de Resumo --}}
    <div class="row">
        <div class="col-lg-3 col-md-6 col-12">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>MZN {{ number_format($totalSavings, 2, ',', '.') }}</h3>
                    <p>Total em Poupanças</p>
                </div>
                <div class="icon">
                    <i class="fas fa-piggy-bank"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>MZN {{ number_format($totalLoans, 2, ',', '.') }}</h3>
                    <p>Total em Empréstimos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>MZN {{ number_format($totalSocialFund, 2, ',', '.') }}</h3>
                    <p>Fundo Social</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>MZN {{ number_format($totalInterestDistributed, 2, ',', '.') }}</h3>
                    <p>Juros Distribuídos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-percentage"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="row">
        <div class="col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Evolução das Poupanças</h3>
                </div>
                <div class="card-body">
                    <canvas id="savingsChart" style="max-height: 400px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Status dos Empréstimos</h3>
                </div>
                <div class="card-body">
                    <canvas id="loansChart" style="max-height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Gerador de Relatórios --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Gerar Relatório Personalizado</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.generate') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 col-12 mb-3">
                        <div class="form-group">
                            <label>Tipo de Relatório</label>
                            <select name="report_type" class="form-control">
                                <option value="savings">Poupanças</option>
                                <option value="loans">Empréstimos</option>
                                <option value="social_fund">Fundo Social</option>
                                <option value="interest">Distribuição de Juros</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-12 mb-3">
                        <div class="form-group">
                            <label>Data Inicial</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3 col-12 mb-3">
                        <div class="form-group">
                            <label>Data Final</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2 col-12 mb-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                Gerar Relatório
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Top 5 Poupadores --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Top 5 Poupadores</h3>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Membro</th>
                        <th>Total Poupado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($savingsStats as $stat)
                        <tr>
                            <td>{{ $stat->name }}</td>
                            <td>MZN {{ number_format($stat->total, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de Evolução das Poupanças
    const savingsCtx = document.getElementById('savingsChart').getContext('2d');
    new Chart(savingsCtx, {
        type: 'line',
        data: {
            labels: @json(array_keys($yearlyStats)),
            datasets: [{
                label: 'Total de Poupanças',
                data: @json(array_values($yearlyStats)),
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        }
    });

    // Gráfico de Status dos Empréstimos
    const loansCtx = document.getElementById('loansChart').getContext('2d');
    new Chart(loansCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pendentes', 'Aprovados', 'Pagos'],
            datasets: [{
                data: [
                    {{ $loanStats['pending'] }},
                    {{ $loanStats['approved'] }},
                    {{ $loanStats['paid'] }}
                ],
                backgroundColor: [
                    'rgb(255, 205, 86)',
                    'rgb(54, 162, 235)',
                    'rgb(75, 192, 192)'
                ]
            }]
        }
    });
</script>
@stop
