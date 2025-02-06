@extends('adminlte::page')
@section('title', 'Calcular Distribuição de Juros')

@section('content_header')
    <h1>Calcular Distribuição de Juros</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-money-bill"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Juros Disponíveis para Distribuição</span>
                            <span class="info-box-number">{{ number_format($undistributedInterest, 2) }} MZN</span>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('interest-management.distribute') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="distribution_date">Data da Distribuição</label>
                    <input type="date" name="distribution_date" id="distribution_date" 
                           class="form-control @error('distribution_date') is-invalid @enderror"
                           value="{{ old('distribution_date', date('Y-m-d')) }}">
                    @error('distribution_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Descrição</label>
                    <input type="text" name="description" id="description" 
                           class="form-control @error('description') is-invalid @enderror"
                           value="{{ old('description') }}">
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="table-responsive">
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
                            @foreach($members as $member)
                                @php
                                    $percentage = $totalSavings > 0 ? 
                                        ($member->total_savings / $totalSavings) * 100 : 0;
                                    $amount = $undistributedInterest * ($percentage / 100);
                                @endphp
                                <tr>
                                    <td>{{ $member->name }}</td>
                                    <td>{{ number_format($member->total_savings, 2) }} MZN</td>
                                    <td>{{ number_format($percentage, 2) }}%</td>
                                    <td>
                                        <input type="hidden" 
                                               name="distributions[{{ $member->id }}][user_id]" 
                                               value="{{ $member->id }}">
                                        <input type="number" step="0.01" 
                                               name="distributions[{{ $member->id }}][amount]" 
                                               class="form-control"
                                               value="{{ number_format($amount, 2, '.', '') }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Confirmar Distribuição</button>
                    <a href="{{ route('interest-management.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@stop