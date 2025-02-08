{{-- resources/views/admin/interest-rates/calculate.blade.php --}}
@extends('adminlte::page')

@section('title', 'Calcular Distribuição de Juros')

@section('content_header')
    <h1>Calcular Distribuição de Juros</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Juros Disponíveis para Distribuição</h3>
                </div>
                <div class="card-body">
                    <h2 class="text-center mb-4">
                        MZN {{ number_format($undistributedInterest, 2, ',', '.') }}
                    </h2>

                    <form action="{{ route('interest-rates.distribute') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="cycle_id">Ciclo</label>
                            <select name="cycle_id" id="cycle_id" class="form-control" required>
                                @foreach ($cycles as $cycle)
                                    <option value="{{ $cycle->id }}">
                                        {{ $cycle->name }} ({{ $cycle->start_date->format('d/m/Y') }} -
                                        {{ $cycle->end_date->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="distribution_date">Data da Distribuição</label>
                            <input type="date" class="form-control" id="distribution_date" name="distribution_date"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="description">Descrição</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Membro</th>
                                    <th>Total em Poupança</th>
                                    <th>Percentual</th>
                                    <th>Valor a Receber</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($members as $member)
                                    <tr>
                                        <td>{{ $member->name }}</td>
                                        <td>MZN {{ number_format($member->total_savings, 2, ',', '.') }}</td>
                                        <td>
                                            {{ number_format(($member->total_savings / $totalSavings) * 100, 2) }}%
                                        </td>
                                        <td>
                                            @php
                                                $amount =
                                                    ($member->total_savings / $totalSavings) * $undistributedInterest;
                                            @endphp
                                            <input type="hidden" name="distributions[{{ $member->id }}][user_id]"
                                                value="{{ $member->id }}">
                                            <input type="number" class="form-control"
                                                name="distributions[{{ $member->id }}][amount]"
                                                value="{{ number_format($amount, 2, '.', '') }}" step="0.01" required>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check"></i> Confirmar Distribuição
                            </button>
                            <a href="{{ route('interest-rates.index') }}" class="btn btn-default">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Aqui você pode adicionar JavaScript adicional se necessário
        });
    </script>
@stop
