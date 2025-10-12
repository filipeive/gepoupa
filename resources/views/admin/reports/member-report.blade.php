@extends('adminlte::page')

@section('title', 'Relatório do Membro')

@section('content_header')
    <h1>Relatório Detalhado - {{ $user->name }}</h1>
@stop

@section('content')
    <div class="row">
        <!-- Resumo Geral -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Resumo Geral</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-piggy-bank"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Poupança</span>
                                    <span class="info-box-number">{{ number_format($totalSavings, 2) }} MZN</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon"><i class="fas fa-hand-holding-usd"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Empréstimos</span>
                                    <span class="info-box-number">{{ number_format($totalLoans, 2) }} MZN</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Juros Recebidos</span>
                                    <span class="info-box-number">{{ number_format($totalInterest, 2) }} MZN</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Fundo Social</span>
                                    <span class="info-box-number">{{ number_format($socialFund, 2) }} MZN</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Histórico de Poupanças -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Histórico de Poupanças</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Valor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($savings as $saving)
                            <tr>
                                <td>{{ $saving->created_at->format('d/m/Y') }}</td>
                                <td>{{ number_format($saving->amount, 2) }} MZN</td>
                                <td><span class="badge badge-success">Confirmado</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Empréstimos Ativos -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Empréstimos</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>Prazo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loans as $loan)
                            <tr>
                                <td>{{ $loan->created_at->format('d/m/Y') }}</td>
                                <td>{{ number_format($loan->amount, 2) }} MZN</td>
                                <td>{!! $loan->status_badge !!}</td>
                                <td>{{ $loan->due_date->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
