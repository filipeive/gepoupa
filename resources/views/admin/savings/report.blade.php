@extends('adminlte::page')

@section('title', 'Relatório de Poupanças')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Relatório de Poupanças</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin') }}">Home</a></li>
                    <li class="breadcrumb-item active">Relatórios</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filtros</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.savings.report') }}" method="GET">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="start_date">Data Inicial</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="end_date">Data Final</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Resultados</h3>
                        <div>
                           {{--  <a href="{{ url()->previous() }}" class="btn btn-secondary">Voltar</a> --}}
                            <a href="{{ route('savings') }}" class="btn btn-default"> <i class="fas fa-arrow-left"></i>  Voltar</a>
                            <a href="{{ route('admin.savings.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success"><i class="fas fa-file-export"></i> Exportar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Usuário</th>
                                    <th>Total de Depósitos</th>
                                    <th>Total Poupança</th>
                                    <th>Média por Depósito</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($savingsReport as $report)
                                <tr>
                                    <td>{{ $report->name }}</td>
                                    <td>{{ $report->total_deposits }}</td>
                                    <td>{{ number_format($report->total_amount, 2, ',', '.') }}</td>
                                    <td>{{ number_format($report->average_amount, 2, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <p><strong>Total de Depósitos:</strong> {{ $totalStats['total_deposits'] }}</p>
                        <p><strong>Total Poupança:</strong> {{ number_format($totalStats['total_amount'], 2, ',', '.') }}</p>
                        <p><strong>Média por Depósito:</strong> {{ number_format($totalStats['average_amount'], 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
