{{-- resources/views/admin/reports/partials/savings.blade.php --}}
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Membro</th>
                <th>Valor</th>
                <th>Data</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['items'] as $saving)
                <tr>
                    <td>{{ $saving->user->name }}</td>
                    <td>MZN {{ number_format($saving->amount, 2, ',', '.') }}</td>
                    <td>{{ $saving->payment_date->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge badge-{{ $saving->status === 'paid' ? 'success' : 'primary' }}">
                            {{ ucfirst($saving->status ?? 'Confirmado') }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="1">Total</th>
                <th colspan="3">MZN {{ number_format($data['total'], 2, ',', '.') }}</th>
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
            labels: @json($data['monthly_labels']),
            datasets: [{
                label: 'Total Mensal',
                data: @json($data['monthly_values']),
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