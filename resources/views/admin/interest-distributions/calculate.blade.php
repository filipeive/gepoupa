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
                <h3>MZN {{ number_format($totalInterestCollected, 2, ',', '.') }}</h3>
                <p>Total Juros Arrecadados</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>MZN {{ number_format($totalSavingsNonDebtors, 2, ',', '.') }}</h3>
                <p>Poupança (Não Devedores)</p>
            </div>
            <div class="icon">
                <i class="fas fa-piggy-bank"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>MZN {{ number_format($poolForNonDebtors, 2, ',', '.') }}</h3>
                <p>Pool 15% (Não Devedores)</p>
            </div>
            <div class="icon">
                <i class="fas fa-percentage"></i>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Prévia da Distribuição</h3>
    </div>
    <form action="{{ route('interest-distributions.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="cycle_id">Ciclo de Poupança</label>
                <select name="cycle_id" id="cycle_id" class="form-control" required>
                    <option value="">Selecione um ciclo...</option>
                    @foreach($cycles as $cycle)
                        <option value="{{ $cycle->id }}">{{ $cycle->month_year }} - {{ $cycle->name }}</option>
                    @endforeach
                </select>
            </div>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Membro</th>
                        <th>Tipo</th>
                        <th>Valor Bruto</th>
                        <th>Dedução</th>
                        <th>Valor Líquido</th>
                        <th>Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($distributions as $index => $dist)
                        <tr>
                            <td>
                                {{ $dist['name'] }}
                                <input type="hidden" name="distributions[{{ $index }}][user_id]"
                                    value="{{ $dist['user_id'] }}">
                            </td>
                            <td>
                                @if($dist['is_debtor'])
                                    <span class="badge badge-danger">Devedor</span>
                                @else
                                    <span class="badge badge-success">Poupador</span>
                                @endif
                            </td>
                            <td>MZN {{ number_format($dist['gross_amount'], 2, ',', '.') }}</td>
                            <td>MZN {{ number_format($dist['deduction'], 2, ',', '.') }}</td>
                            <td>
                                <strong>MZN {{ number_format($dist['net_amount'], 2, ',', '.') }}</strong>
                                <input type="hidden" name="distributions[{{ $index }}][amount]"
                                    value="{{ $dist['net_amount'] }}">
                            </td>
                            <td>
                                {{ $dist['description'] }}
                                <input type="hidden" name="distributions[{{ $index }}][description]"
                                    value="{{ $dist['description'] }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Confirmar Distribuição
            </button>
        </div>
    </form>
</div>
@stop

@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>
    $(document).ready(function () {
        $('.table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            }
        });
    });
</script>
@stop