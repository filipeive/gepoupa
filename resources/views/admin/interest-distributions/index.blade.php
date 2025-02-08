{{-- resources/views/admin/interest-distributions/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Distribuição de Juros')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Distribuição de Juros</h1>
        <div>
            <a href="{{ route('interest-distributions.export') }}" 
               class="btn btn-success">
                <i class="fas fa-file-excel"></i> Exportar
            </a>
            <a href="{{ route('interest-distributions.create') }}" 
               class="btn btn-primary">
                <i class="fas fa-plus"></i> Nova Distribuição
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Distribuições Realizadas</h3>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Ciclo</th>
                        <th>Membro</th>
                        <th>Valor</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($distributions as $distribution)
                        <tr>
                            <td>{{ $distribution->cycle->month_year }}</td>
                            <td>{{ $distribution->user->name }}</td>
                            <td>R$ {{ number_format($distribution->amount, 2, ',', '.') }}</td>
                            <td>{{ $distribution->distribution_date->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('admin.interest-distributions.show', $distribution) }}" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $distributions->links() }}
        </div>
    </div>

    {{-- Resumo das Distribuições --}}
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Resumo por Função</h3>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($roleDistributions as $role)
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-percentage"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ ucfirst($role->role) }}</span>
                                <span class="info-box-number">{{ $role->amount }}%</span>
                            </div>
                        </div>
                    </div>
                @endforeach
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
            $('.table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
                }
            });
        });
    </script>
@stop