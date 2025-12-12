@extends('adminlte::page')

@section('title', 'Nova Distribuição de Poupança')

@section('content_header')
<h1>Nova Distribuição de Poupança</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Selecionar Ciclo</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('saving-distributions.create') }}" method="GET">
            <div class="form-group">
                <label for="cycle_id">Ciclo de Poupança</label>
                <div class="input-group">
                    <select name="cycle_id" id="cycle_id" class="form-control" required>
                        <option value="">Selecione um ciclo...</option>
                        @foreach($cycles as $cycle)
                            <option value="{{ $cycle->id }}" {{ (isset($selectedCycle) && $selectedCycle->id == $cycle->id) ? 'selected' : '' }}>
                                {{ $cycle->month_year }} - {{ $cycle->name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="input-group-append">
                        <button type="submit" class="btn btn-info btn-flat">Carregar</button>
                    </span>
                </div>
            </div>
        </form>
    </div>
</div>

@if(isset($selectedCycle) && $members->isNotEmpty())
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Prévia da Distribuição - {{ $selectedCycle->month_year }}</h3>
        </div>
        <form action="{{ route('saving-distributions.store') }}" method="POST">
            @csrf
            <input type="hidden" name="cycle_id" value="{{ $selectedCycle->id }}">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Membro</th>
                            <th>Total Poupado</th>
                            <th>Dívida Total</th>
                            <th>Valor Líquido</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $index => $member)
                            <tr>
                                <td>
                                    {{ $member->name }}
                                    <input type="hidden" name="distributions[{{ $index }}][user_id]" value="{{ $member->id }}">
                                </td>
                                <td>
                                    MZN {{ number_format($member->total_saved_in_cycle, 2, ',', '.') }}
                                    <input type="hidden" name="distributions[{{ $index }}][amount]"
                                        value="{{ $member->total_saved_in_cycle }}">
                                </td>
                                <td>
                                    <span class="text-danger">
                                        MZN {{ number_format($member->total_debt, 2, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    @if($member->net_distribution >= 0)
                                        <span class="text-success font-weight-bold">
                                            MZN {{ number_format($member->net_distribution, 2, ',', '.') }} (Receber)
                                        </span>
                                    @else
                                        <span class="text-danger font-weight-bold">
                                            MZN {{ number_format($member->net_distribution, 2, ',', '.') }} (Deve)
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total Geral</th>
                            <th>MZN {{ number_format($members->sum('total_saved_in_cycle'), 2, ',', '.') }}</th>
                            <th>MZN {{ number_format($members->sum('total_debt'), 2, ',', '.') }}</th>
                            <th>MZN {{ number_format($members->sum('net_distribution'), 2, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Confirmar Distribuição
                </button>
            </div>
        </form>
    </div>
@elseif(isset($selectedCycle))
    <div class="alert alert-warning">
        Nenhuma poupança encontrada para este ciclo.
    </div>
@endif
@stop