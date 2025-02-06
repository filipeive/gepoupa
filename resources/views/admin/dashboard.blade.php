@extends('adminlte::page')
@section('title', 'Dashboard')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css">
<style>
    .small-box {
        transition: transform .3s;
    }
    .small-box:hover {
        transform: translateY(-5px);
    }
    .progress-description {
        font-size: 12px;
    }
</style>
@stop

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Dashboard</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <!-- Cards Informativos -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($data['totalSavings'], 2, ',', '.') }} MT</h3>
                    <p>Total Poupado</p>
                </div>
                <div class="icon">
                    <i class="fas fa-piggy-bank"></i>
                </div>
                <a href="#" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($data['activeLoans'], 2, ',', '.') }} MT</h3>
                    <p>Empréstimos Ativos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <a href="#" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $data['activeMembers'] }}</h3>
                    <p>Membros Ativos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="#" class="small-box-footer">Mais informações <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($socialFundsData['total'], 2, ',', '.') }} MT</h3>
                    <p>Fundo Social</p>
                    <div class="progress-description">
                        <small>
                            <span class="badge badge-warning">Pendentes: {{ $socialFundsData['pending'] }}</span>
                            <span class="badge badge-success">Pagos: {{ $socialFundsData['paid'] }}</span>
                            <span class="badge badge-danger">Atrasados: {{ $socialFundsData['late'] }}</span>
                        </small>
                    </div>
                    @if($socialFundsData['penalty_total'] > 0)
                        <div class="mt-2">
                            <small class="text-warning">
                                Multas: {{ number_format($socialFundsData['penalty_total'], 2, ',', '.') }} MT
                            </small>
                        </div>
                    @endif
                </div>
                <div class="icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <a href="{{ route('admin.social-funds.index') }}" class="small-box-footer">
                    Mais informações <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <!-- Gráfico de Poupança -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-1"></i>
                        Evolução da Poupança
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="savingsChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de Empréstimos -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Status dos Empréstimos
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="loansChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimas Atividades e Próximos Vencimentos -->
    <div class="row">
        <!-- Últimas Atividades -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-1"></i>
                        Últimas Atividades
                    </h3>
                </div>
                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        @foreach($recentActivities as $activity)
                        <li class="item">
                            <div class="product-info">
                                <a href="javascript:void(0)" class="product-title">
                                    {{ $activity['user'] }}
                                    <span class="badge badge-success float-right">{{ number_format($activity['amount'], 2, ',', '.') }} MT</span>
                                </a>
                                <span class="product-description">
                                    {{ $activity['type'] }} - {{ $activity['date']->format('d/m/Y') }}
                                </span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer text-center">
                    <a href="javascript:void(0)" class="uppercase">Ver Todas Atividades</a>
                </div>
            </div>
        </div>

        <!-- Próximos Vencimentos -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock mr-1"></i>
                        Próximos Vencimentos
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Membro</th>
                                <th>Tipo</th>
                                <th>Valor</th>
                                <th>Vencimento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingPayments as $payment)
                            <tr>
                                <td>{{ $payment['user'] }}</td>
                                <td><span class="badge badge-primary">{{ $payment['type'] }}</span></td>
                                <td>{{ number_format($payment['amount'], 2, ',', '.') }} MT</td>
                                <td>
                                    {{ $payment['due_date'] ? $payment['due_date']->format('d/m/Y') : 'Data não disponível' }}
                                </td>                                
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script>
    // Gráfico de Poupança
    var ctxSavings = document.getElementById('savingsChart').getContext('2d');
    var savingsChart = new Chart(ctxSavings, {
        type: 'line',
        data: {
            labels: @json($savingsChartData['labels']),
            datasets: [{
                label: 'Total Poupado (MT)',
                data: @json($savingsChartData['data']),
                borderColor: '#17a2b8',
                tension: 0.1,
                fill: false
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de Empréstimos
    var ctxLoans = document.getElementById('loansChart').getContext('2d');
    var loansChart = new Chart(ctxLoans, {
        type: 'doughnut',
        data: {
            labels: @json($loansChartData['labels']),
            datasets: [{
                data: @json($loansChartData['data']),
                backgroundColor: ['#28a745', '#dc3545', '#ffc107']
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
@stop