{{-- resources/views/admin/reports/partials/social_fund.blade.php --}}
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Membro</th>
                <th>Valor</th>
                <th>Multa</th>
                <th>Juros de Atraso</th>
                <th>Data</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['items'] as $fund)
                <tr>
                    <td>{{ $fund->user->name }}</td>
                    <td>MZN {{ number_format($fund->amount, 2, ',', '.') }}</td>
                    <td>MZN {{ number_format($fund->penalty_amount, 2, ',', '.') }}</td>
                    <td>MZN {{ number_format($fund->late_fee, 2, ',', '.') }}</td>
                    <td>{{ $fund->payment_date->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge badge-{{ $fund->status === 'paid' ? 'success' : 'warning' }}">
                            {{ ucfirst($fund->status) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="1">Total</th>
                <th colspan="5">MZN {{ number_format($data['total'], 2, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Distribuição Mensal</h3>
            </div>
            <div class="card-body">
                <canvas id="monthlyDistributionChart"></canvas>
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
                        Média por Membro
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
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total de Multas
                        <span>MZN {{ number_format($data['stats']['total_penalties'], 2, ',', '.') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total de Juros de Atraso
                        <span>MZN {{ number_format($data['stats']['total_late_fees'], 2, ',', '.') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@section('js')
<script>
    const ctx = document.getElementById('monthlyDistributionChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: Object.keys(@json($data['monthly_data'])),
            datasets: [{
                label: 'Total Mensal',
                data: Object.values(@json($data['monthly_data'])),
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@stop