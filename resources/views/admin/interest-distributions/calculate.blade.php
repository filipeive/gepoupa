{{-- resources/views/admin/interest-distributions/calculate.blade.php --}}
@extends('adminlte::page')

@section('title', 'Cálculo de Distribuição de Juros')

@section('content_header')
    <h1>Cálculo de Distribuição de Juros</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-4 col-md-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>R$ {{ number_format($undistributedInterest, 2, ',', '.') }}</h3>
                    <p>Juros Não Distribuídos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>R$ {{ number_format($totalSavings, 2, ',', '.') }}</h3>
                    <p>Total em Poupança</p>
                </div>
                <div class="icon">
                    <i class="fas fa-piggy-bank"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Distribuição por Membro</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Membro</th>
                        <th>Total em Poupança</th>
                        <th>Percentual</th>
                        <th>Valor a Receber</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $member)
                        @php
                            $percentage = $totalSavings > 0 ? ($member->total_savings / $totalSavings) * 100 : 0;
                            $toReceive = $undistributedInterest * ($percentage / 100);
                        @endphp
                        <tr>
                            <td>{{ $member->name }}</td>
                            <td>R$ {{ number_format($member->total_savings, 2, ',', '.') }}</td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ $percentage }}%"
                                         aria-valuenow="{{ $percentage }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ number_format($percentage, 2) }}%
                                    </div>
                                </div>
                            </td>
                            <td>R$ {{ number_format($toReceive, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <form action="{{ route('admin.interest-distributions.distribute') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-share-alt"></i> Realizar Distribuição
                </button>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('.table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
                },
                order: [[2, 'desc']]
            });
        });
    </script>
@stop