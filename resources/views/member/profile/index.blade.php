@extends('adminlte::page')
@section('title', 'Profile')

@section('content_header')
    <h1>Meu Perfil</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/painel') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="breadcrumb-item active">Meu Perfil</li>
    </ol>
@endsection

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            <h5><i class="icon fas fa-ban"></i> Ocorreu um erro...</h5>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Usuário</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('profile.save', $user->id) }}">
            @csrf
            @method('PUT')
            
            <div class="form-group row">
                <label for="name" class="col-sm-2 col-form-label">Nome Completo</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}">
                </div>
            </div>

            <div class="form-group row">
                <label for="email" class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" id="email" value="{{ $user->email }}" disabled>
                    <input type="hidden" name="email_original" value="{{ $user->email }}">
                </div>
            </div>

            <div class="form-group row">
                <label for="phone" class="col-sm-2 col-form-label">Telefone</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone }}">
                </div>
            </div>
            @if ($user->role !== 'member')
            <div class="form-group row">
                <label for="role" class="col-sm-2 col-form-label">Cargo</label>
                <div class="col-sm-10">
                    <select class="form-control" id="role" name="role">
                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="member" {{ $user->role == 'member' ? 'selected' : '' }}>Membro</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="status" class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-10">
                    <select class="form-control" id="status" name="status">
                        <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Ativo</option>
                        <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
            </div>
            @endif

            <div class="form-group row">
                <label for="password" class="col-sm-2 col-form-label">Senha</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" id="password" name="password">
                </div>
            </div>

            <div class="form-group row">
                <label for="password_confirmation" class="col-sm-2 col-form-label">Confirmar Senha</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-10 offset-sm-2">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <a href="{{ route('users.index') }}" class="btn btn-default">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
