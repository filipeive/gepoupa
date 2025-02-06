{{-- Pagina de Criação de Usuários usando AdminLTE --}}
@extends('adminlte::page')
@section('title', 'Novo Usuário')

@section('content_header')
    <h1>Gestão de Usuários</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/painel') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuários</a></li>
        <li class="breadcrumb-item active">Novo Usuário</li>
    </ol>
@endsection

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                <h5>
                    <i class="icon fas fa-ban"></i>
                    Ocorreu um erro...
                </h5>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Adicionar Novo Usuário</h3>
        </div>
        <div class="card-body">
            {{-- <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Nome Completo</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password" class="col-sm-2 col-form-label">Senha</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password_confirmation" class="col-sm-2 col-form-label">Confirmar Senha</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                               id="password_confirmation" name="password_confirmation">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-success">Cadastrar</button>
                        <a href="{{ route('users.index') }}" class="btn btn-default">Cancelar</a>
                    </div>
                </div>
            </form> --}}
            <form action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}" method="POST">
                @csrf
                @if (isset($user))
                    @method('PUT')
                @endif
            
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Cadastro de Usuário</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Nome:</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required>
                        </div>
            
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
                        </div>
            
                        <div class="form-group">
                            <label for="phone">Telefone:</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}">
                        </div>
            
                        <div class="form-group">
                            <label for="role">Função:</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="member" {{ old('role', $user->role ?? '') == 'member' ? 'selected' : '' }}>Membro</option>
                            </select>
                        </div>
            
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" {{ old('status', $user->status ?? 1) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="status">Ativo</label>
                            </div>
                        </div>
            
                        <div class="form-group">
                            <label for="password">Senha:</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
            
                        <div class="form-group">
                            <label for="password_confirmation">Confirmar Senha:</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                        <a href="{{ route('users.index') }}" class="btn btn-default">Cancelar</a>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>
@endsection
