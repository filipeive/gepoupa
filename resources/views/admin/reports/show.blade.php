{{-- resources/views/admin/reports/show.blade.php --}}
@extends('adminlte::page')

@section('title', 'Relatório Gerado')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Relatório Detalhado</h1>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Imprimir
        </button>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Período: {{ \Carbon\Carbon::parse($data['start_date'])->format('d/m/Y') }} 
                até {{ \Carbon\Carbon::parse($data['end_date'])->format('d/m/Y') }}
            </h3>
        </div>
        <div class="card-body">
            @if($data['type'] === 'savings')
                @include('admin.reports.partials.savings')
            @elseif($data['type'] === 'loans')
                @include('admin.reports.partials.loans')
            @elseif($data['type'] === 'social_fund')
                @include('admin.reports.partials.social_fund')
            @else
                @include('admin.reports.partials.interest')
            @endif
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
@stop