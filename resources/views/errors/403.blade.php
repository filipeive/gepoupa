@extends('adminlte::page')

@section('title', '403 - Acesso Negado')

@section('content_header')
<h1 class="m-0 text-dark">403 - Acesso Negado</h1>
@stop

@section('content')
<div class="error-page">
    <h2 class="headline text-danger"> 403</h2>

    <div class="error-content">
        <h3><i class="fas fa-ban text-danger"></i> Oops! Acesso negado.</h3>

        <p>
            Você não tem permissão para acessar este recurso.
            Enquanto isso, você pode <a href="{{ route('admin') }}">retornar ao painel</a>.
        </p>

    </div>
    <!-- /.error-content -->
</div>
<!-- /.error-page -->
@stop