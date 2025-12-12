@extends('adminlte::page')

@section('title', '419 - Página Expirada')

@section('content_header')
<h1 class="m-0 text-dark">419 - Página Expirada</h1>
@stop

@section('content')
<div class="error-page">
    <h2 class="headline text-warning"> 419</h2>

    <div class="error-content">
        <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! A página expirou.</h3>

        <p>
            Sua sessão expirou. Por favor, atualize a página e tente novamente.
            Enquanto isso, você pode <a href="{{ route('admin') }}">retornar ao painel</a>.
        </p>

    </div>
    <!-- /.error-content -->
</div>
<!-- /.error-page -->
@stop