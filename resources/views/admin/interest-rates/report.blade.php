{{-- resources/views/admin/interest-rates/report.blade.php --}}
@extends('adminlte::page')

@section('title', 'Relatório de Juros')

@section('content_header')
    <h1>Relatório de Juros</h1>
@stop

@section('content')
    <button class="btn btn-secondary">
        <a href="javascript:history.back()" style="color: white; margin:10px; float:right;">
            <i class="fas fa-times"></i> Voltar
        </a>
    </button>
    <br>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filtros</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('interest-rates.report') }}" class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="start_date">Data Inicial</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="start_date" 
                                       name="start_date" 
                                       value="{{ $startDate instanceof \Carbon\Carbon ? $startDate->format('Y-m-d') : '' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="end_date">Data Final</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="end_date" 
                                       name="end_date" 
                                       value="{{ $endDate instanceof \Carbon\Carbon ? $endDate->format('Y-m-d') : '' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($startDate instanceof \Carbon\Carbon && $endDate instanceof \Carbon\Carbon)
        <div class="alert alert-info">
            Período do relatório: {{ $startDate->format('d/m/Y') }} até {{ $endDate->format('d/m/Y') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total de Juros</span>
                    <span class="info-box-number">
                        MZN {{ number_format($interestReport->total_interest ?? 0, 2, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-file-invoice-dollar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total de Empréstimos</span>
                    <span class="info-box-number">
                        {{ $interestReport->total_loans ?? 0 }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Média de Juros</span>
                    <span class="info-box-number">
                        MZN {{ number_format($interestReport->average_interest ?? 0, 2, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Distribuição por Membro</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Membro</th>
                                <th>Total Distribuído</th>
                                <th>Quantidade de Distribuições</th>
                                <th>Média por Distribuição</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($distributionReport ?? [] as $userId => $distributions)
                                <tr>
                                    <td>{{ optional($distributions->first()->user)->name ?? 'N/A' }}</td>
                                    <td>MZN {{ number_format($distributions->sum('amount'), 2, ',', '.') }}</td>
                                    <td>{{ $distributions->count() }}</td>
                                    <td>MZN {{ number_format($distributions->avg('amount'), 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Nenhum dado encontrado para o período selecionado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .info-box-number {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .alert {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Validação das datas
            $('#end_date').change(function() {
                var startDate = $('#start_date').val();
                var endDate = $(this).val();
                
                if(startDate && endDate && startDate > endDate) {
                    alert('A data final não pode ser menor que a data inicial');
                    $(this).val('');
                }
            });

            // Formatação de números na tabela
            $('.table').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Portuguese-Brasil.json"
                }
            });
        });
    </script>
@stop
