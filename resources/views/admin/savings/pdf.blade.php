<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relatório de Poupanças</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .filters {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            padding: 10px 0;
        }
        .total {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Relatório de Poupanças</h2>
        <p>Data de geração: {{ $date }}</p>
    </div>

    @if(!empty($filters))
    <div class="filters">
        <h4>Filtros Aplicados:</h4>
        <ul>
            @if(isset($filters['date_from']))
                <li>Data inicial: {{ \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') }}</li>
            @endif
            @if(isset($filters['date_to']))
                <li>Data final: {{ \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') }}</li>
            @endif
            @if(isset($filters['user_id']))
                <li>Usuário: {{ \App\Models\User::find($filters['user_id'])->name }}</li>
            @endif
        </ul>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Membro</th>
                <th>Valor</th>
                <th>Data do Pagamento</th>
                <th>Data de Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($savings as $saving)
                <tr>
                    <td>{{ $saving->id }}</td>
                    <td>{{ $saving->user->name }}</td>
                    <td>MZN {{ number_format($saving->amount, 2, ',', '.') }}</td>
                    <td>{{ $saving->payment_date->format('d/m/Y') }}</td>
                    <td>{{ $saving->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        <p>Total: MZN {{ number_format($totalAmount, 2, ',', '.') }}</p>
    </div>

    <div class="footer">
        <p>Página 1</p>
    </div>
</body>
</html>