@extends('adminlte::page')

@section('title', '500 - Erro no Servidor')

@section('content_header')
<h1 class="m-0 text-dark">500 - Erro no Servidor</h1>
@stop

@section('content')
<div class="error-page">
    <h2 class="headline text-danger"> 500</h2>

    <div class="error-content">
        <h3><i class="fas fa-exclamation-triangle text-danger"></i> Oops! Algo deu errado.</h3>

        <p>
            Trabalharemos para consertar isso imediatamente.
            Enquanto isso, você pode <a href="{{ route('admin') }}">retornar ao painel</a>.
        </p>

    </div>
    <!-- /.error-content -->
</div>
<!-- /.error-page -->
@stop