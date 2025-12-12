@extends('adminlte::page')

@section('title', 'Detalhes da Distribuição')

@section('content_header')
<h1>Detalhes da Distribuição</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informações da Distribuição</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Membro</dt>
                    <dd class="col-sm-8">{{ $distribution->user->name }}</dd>

                    <dt class="col-sm-4">Ciclo</dt>
                    <dd class="col-sm-8">{{ $distribution->cycle->month_year }}</dd>

                    <dt class="col-sm-4">Valor</dt>
                    <dd class="col-sm-8">MZN {{ number_format($distribution->amount, 2, ',', '.') }}</dd>

                    <dt class="col-sm-4">Data</dt>
                    <dd class="col-sm-8">{{ $distribution->distribution_date->format('d/m/Y') }}</dd>

                    <dt class="col-sm-4">Descrição</dt>
                    <dd class="col-sm-8">{{ $distribution->description }}</dd>
                </dl>
            </div>
            <div class="card-footer">
                <a href="{{ route('interest-distribution.index') }}" class="btn btn-default">Voltar</a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pagamentos Relacionados</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Membro</th>
                            <th>Valor Juros</th>
                            <th>Data Pagamento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($relatedPayments as $payment)
                            <tr>
                                <td>{{ $payment->loan->user->name }}</td>
                                <td>MZN {{ number_format($payment->interest_amount, 2, ',', '.') }}</td>
                                <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Nenhum pagamento relacionado encontrado.</td>
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
@stop