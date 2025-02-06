@extends('adminlte::page')
@section('title', 'Gestão de Membros')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Gestão de Membros</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Membros</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-8">
                <form action="{{ route('admin.members.index') }}" method="GET" class="form-inline">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Buscar por nome ou email..." value="{{ $search }}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Total Poupado</th>
                        <th>Fundos Sociais</th>
                        <th>Empréstimos Ativos</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $member)
                    <tr>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->email }}</td>
                        <td>{{ $member->phone }}</td>
                        <td>{{ number_format($member->savings()->sum('amount'), 2) }} MZN</td>
                        <td>{{ number_format($member->socialFunds()->sum('amount'), 2) }} MZN</td>
                        <td>{{ $member->loans()->where('status', 'approved')->count() }}</td>
                        <td>
                            <span class="badge badge-{{ $member->status ? 'success' : 'danger' }}">
                                {{ $member->status ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('members.show', $member) }}" 
                               class="btn btn-sm btn-info" title="Detalhes">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card-footer">
        {{ $members->links() }}
    </div>
</div>
@stop

@section('css')
<style>
    .table td, .table th {
        vertical-align: middle;
    }
</style>
@stop