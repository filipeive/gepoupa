@extends('adminlte::page')

@section('title', 'Gestão de Juros')

@section('content_header')
<h1>Gestão de Juros</h1>
@stop

@section('content')
<div class="row">
    <!-- Card de Taxa de Juros Atual -->
    <div class="col-lg-4">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Taxa de Juros Atual</h3>
            </div>
            <div class="card-body">
                @if($interestRates->isNotEmpty())
                    <h2 class="text-center">{{ number_format($interestRates->first()->rate, 2) }}%</h2>
                    <p class="text-muted text-center">
                        Vigente desde: {{ $interestRates->first()->effective_date->format('d/m/Y') }}
                    </p>
                @else
                    <p class="text-center">Nenhuma taxa definida</p>
                @endif
                <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#setRateModal">
                    <i class="fas fa-plus"></i> Definir Nova Taxa
                </button>
            </div>
        </div>
    </div>

    <!-- Card de Total de Juros Coletados -->
    <div class="col-lg-4">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Total de Juros Coletados</h3>
            </div>
            <div class="card-body">
                <h2 class="text-center">MZN {{ number_format($totalInterestCollected, 2, ',', '.') }}</h2>
                <a href="{{ route('interest-rates.calculate') }}" class="btn btn-success btn-block">
                    <i class="fas fa-calculator"></i> Calcular Distribuição
                </a>
            </div>
        </div>
    </div>

    <!-- Card de Relatórios -->
    <div class="col-lg-4">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Relatórios</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('interest-rates.report') }}" class="btn btn-info btn-block">
                    <i class="fas fa-chart-bar"></i> Ver Relatórios
                </a>
                <a href="{{ route('interest-rates.export') }}" class="btn btn-outline-info btn-block">
                    <i class="fas fa-file-excel"></i> Exportar Excel
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico de Juros Mensais -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Juros Mensais</h3>
            </div>
            <div class="card-body">
                <canvas id="monthlyInterestChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Histórico de Distribuições -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Histórico de Distribuições</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Membro</th>
                            <th>Valor</th>
                            <th>Descrição</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($distributions as $distribution)
                            <tr>
                                <td>{{ $distribution->distribution_date->format('d/m/Y') }}</td>
                                <td>{{ $distribution->user->name }}</td>
                                <td>MZN {{ number_format($distribution->amount, 2, ',', '.') }}</td>
                                <td>{{ $distribution->description }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $distributions->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal para Definir Nova Taxa -->
<div class="modal fade" id="setRateModal" tabindex="-1" role="dialog" aria-labelledby="setRateModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('interest-rates.set') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="setRateModalLabel">Definir Nova Taxa de Juros</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rate">Taxa (%)</label>
                        <input type="number" class="form-control" id="rate" name="rate" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="effective_date">Data de Vigência</label>
                        <input type="date" class="form-control" id="effective_date" name="effective_date" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Preparar dados para o gráfico
        const monthlyData = @json($monthlyInterest);
        const labels = monthlyData.map(item => `${item.month}/${item.year}`);
        const values = monthlyData.map(item => item.total);

        // Criar gráfico
        const ctx = document.getElementById('monthlyInterestChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Juros Mensais (MZN)',
                    data: values,
                    backgroundColor: 'rgba(60,141,188,0.9)',
                    borderColor: 'rgba(60,141,188,0.8)',
                    borderWidth: 1
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
    });
</script>
@stop