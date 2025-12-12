@extends('adminlte::page')

@section('title', 'Distribuições de Poupança')

@section('content_header')
<div class="d-flex justify-content-between">
    <h1>Distribuições de Poupança</h1>
    <a href="{{ route('saving-distributions.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Nova Distribuição
    </a>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Ciclo</th>
                    <th>Membro</th>
                    <th>Valor Distribuído</th>
                </tr>
            </thead>
            <tbody>
                @forelse($distributions as $distribution)
                    <tr>
                        <td>{{ $distribution->distribution_date->format('d/m/Y') }}</td>
                        <td>{{ $distribution->cycle->month_year }}</td>
                        <td>{{ $distribution->user->name }}</td>
                        <td>MZN {{ number_format($distribution->total_saved, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Nenhuma distribuição encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $distributions->links() }}
    </div>
</div>
@stop