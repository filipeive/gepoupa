@extends('adminlte::page')
@section('title', 'Gestão de Juros')

@section('content_header')
    <h1>Gestão de Juros</h1>
@stop

@section('content')
    <div class="row">
        <!-- Card de Estatísticas -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Resumo de Juros</h3>
                </div>
                <div class="card-body">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success"><i class="fas fa-money-bill"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total de Juros Coletados</span>
                            <span class="info-box-number">{{ number_format($totalInterestCollected, 2) }} MZN</span>
                        </div>
                    </div>

                    <h5>Juros Mensais</h5>
                    <canvas id="monthlyInterestChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Card de Taxa de Juros -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Definir Taxa de Juros</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.interest-management.set-rate') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="rate">Taxa de Juros (%)</label>
                            <input type="number" step="0.01" name="rate" id="rate" 
                                   class="form-control @error('rate') is-invalid @enderror"
                                   value="{{ old('rate') }}">
                            @error('rate')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="effective_date">Data de Vigência</label>
                            <input type="date" name="effective_date" id="effective_date" 
                                   class="form-control @error('effective_date') is-invalid @enderror"
                                   value="{{ old('effective_date', date('Y-m-d')) }}">
                            @error('effective_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Descrição</label>
                            <input type="text" name="description" id="description" 
                                   class="form-control @error('description') is-invalid @enderror"
                                   value="{{ old('description') }}">
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Definir Taxa</button>
                    </form>

                    <hr>

                    <h5>Histórico de Taxas</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Taxa</th>
                                    <th>Data de Vigência</th>
                                    <th>Descrição</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($interestRates as $rate)
                                    <tr>
                                        <td>{{ $rate->rate }}%</td>
                                        <td>{{ $rate->effective_date->format('d/m/Y') }}</td>
                                        <td>{{ $rate->description }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card de Distribuição -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Distribuição de Juros</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.interest-management.calculate') }}" 
                       class="btn btn-primary btn-block mb-3">
                        Calcular Nova Distribuição
                    </a>

                    <h5>Últimas Distribuições</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Membro</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($distributions as $distribution)
                                    <tr>
                                        <td>{{ $distribution->distribution_date->format('d/m/Y') }}</td>
                                        <td>{{ $distribution->user->name }}</td>
                                        <td>{{ number_format($distribution->amount, 2) }} MZN</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $distributions->links() }}
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de juros mensais
    const ctx = document.getElementById('monthlyInterestChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyInterest->pluck('month')->map(function($month) {
                return date('M/Y', mktime(0, 0, 0, $month, 1, 2023));
            })) !!},
            datasets: [{
                label: 'Juros Mensais',
                data: {{ json_encode($monthlyInterest->pluck('total')) }},
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
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
</script>
@stop