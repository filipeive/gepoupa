@extends('adminlte::page')

@section('title', 'Dashboard do Membro')

@section('content_header')
    <h1>Dashboard do Membro</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            @yield('member_content')
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Dashboard do Membro carregado!');
    </script>
@stop