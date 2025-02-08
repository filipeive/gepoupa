{{-- resources/views/admin/reports/partials/interest.blade.php --}}
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ciclo</th>
                <th>Membro</th>
                <th>Valor</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['items'] as $distribution)
                <tr>
                    <td>{{ $distribution->cycle->month_year }}</td>
                    <td>{{ $distribution->user->name }}</td>
                    <td>MZN {{ number_format($distribution->amount, 2, ',', '.') }}</td>
                    <td>{{ $distribution->distribution_date->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">Total</th>
                <th colspan="2">MZN {{ number_format($data['total'], 2, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Distribuição por Função</h3>
            </div>
            <div class="card-body">
                <canvas id="roleDistributionChart"></canvas>
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
                        Média por Distribuição
                        <span>MZN {{ number_format($data['stats']['average'], 2, ',', '.') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Maior Valor
                        <span>MZN {{ number_format($data['stats']['max'], 2, ',', '.') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Menor Valor
                        <span>MZN {{ number_format($data['stats']['min'], 2, ',', '.') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@section('js')
<script>
    // Gráfico de distribuição por função
    const roleCtx = document.getElementById('roleDistributionChart').getContext('2d');
    new Chart(roleCtx, {
        type: 'pie',
        data: {
            labels: @json($data['role_distributions']->pluck('role')),
            datasets: [{
                data: @json($data['role_distributions']->pluck('total_amount')),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
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
    const monthlyCtx = document.getElementById('monthlyDistributionChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: Object.keys(@json($data['monthly_data'])),
            datasets: [{
                label: 'Distribuição Mensal',
                data: Object.values(@json($data['monthly_data'])),
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