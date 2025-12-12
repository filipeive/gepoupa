@extends('adminlte::page')

@section('title', '404 - Página Não Encontrada')

@section('content_header')
<h1 class="m-0 text-dark">404 - Página Não Encontrada</h1>
@stop

@section('content')
<div class="error-page">
    <h2 class="headline text-warning"> 404</h2>

    <div class="error-content">
        <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! Página não encontrada.</h3>

        <p>
            Não conseguimos encontrar a página que você estava procurando.
            Enquanto isso, você pode <a href="{{ route('admin') }}">retornar ao painel</a>.
        </p>

    </div>
    <!-- /.error-content -->
</div>
<!-- /.error-page -->
@stop