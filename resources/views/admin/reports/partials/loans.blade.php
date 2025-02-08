{{-- resources/views/admin/reports/partials/loans.blade.php --}}
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Membro</th>
                <th>Valor</th>
                <th>Taxa de Juros</th>
                <th>Data do Pedido</th>
                <th>Data de Vencimento</th>
                <th>Status</th>
                <th>Total Pago</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['items'] as $loan)
                <tr>
                    <td>{{ $loan->user->name ?? 'N/A' }}</td>
                    <td>{{ number_format($loan->amount, 2) }} MZN</td>
                    <td>{{ $loan->interest_rate }}%</td>
                    <td>{{ $loan->request_date->format('d/m/Y') }}</td>
                    <td>{{ $loan->due_date->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge badge-{{
                            $loan->status === 'approved' ? 'success' : 
                            ($loan->status === 'paid' ? 'primary' : 
                            ($loan->status === 'pending' ? 'warning' : 
                            ($loan->status === 'rejected' ? 'danger' : 'info')))
                        }}">                        
                            {{ ucfirst($loan->status) }}
                        </span>
                    </td>
                    <td>{{ number_format($loan->payments->sum('amount'), 2) }} MZN</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="1">Total</th>
                <th>{{ number_format($data['total'], 2) }} MZN</th>
                <th colspan="5"></th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Distribuição por Status</h3>
            </div>
            <div class="card-body">
                <canvas id="statusDistributionChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Estatísticas</h3>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Média por Empréstimo
                        <span>{{ number_format($data['stats']['average'], 2) }} MZN</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Maior Valor
                        <span>{{ number_format($data['stats']['max'], 2) }} MZN</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Menor Valor
                        <span>{{ number_format($data['stats']['min'], 2) }} MZN</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total de Juros
                        <span>{{ number_format($data['total_interest'], 2) }} MZN</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Evolução Mensal</h3>
            </div>
            <div class="card-body">
                <canvas id="monthlyLoansChart"></canvas>
            </div>
        </div>
    </div>
</div>

@section('js')
<script>
    // Gráfico de distribuição por status
    const statusCtx = document.getElementById('statusDistributionChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: ['Pendente', 'Aprovado', 'Rejeitado', 'Pago'],
            datasets: [{
                data: [
                    {{ $data['stats']['status_counts']['pending'] }},
                    {{ $data['stats']['status_counts']['approved'] }},
                    {{ $data['stats']['status_counts']['rejected'] }},
                    {{ $data['stats']['status_counts']['paid'] }}
                ],
                backgroundColor: [
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // Gráfico de evolução mensal
    const monthlyCtx = document.getElementById('monthlyLoansChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: Object.keys(@json($data['monthly_data'])),
            datasets: [{
                label: 'Valor Total de Empréstimos',
                data: Object.values(@json($data['monthly_data'])),
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('pt-BR', {
                                style: 'currency',
                                currency: 'MZN'
                            });
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y.toLocaleString('pt-BR', {
                                style: 'currency',
                                currency: 'MZN'
                            });
                        }
                    }
                }
            }
        }
    });
</script>
@stop