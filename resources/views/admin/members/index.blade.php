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
                <form action="{{ route('members.index') }}" method="GET" class="form-inline">
                    <div class="input-group mr-2">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por nome ou email..."
                            value="{{ $search }}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <select name="status" class="form-control mr-2">
                        <option value="">Todos os Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Ativos</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inativos</option>
                    </select>
                </form>
            </div>
            <div class="col-md-4 text-right">
                <div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fas fa-file-export"></i> Exportar
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('members.export', ['format' => 'pdf']) }}">
                            <i class="far fa-file-pdf"></i> PDF
                        </a>
                        <a class="dropdown-item" href="{{ route('members.export', ['format' => 'excel']) }}">
                            <i class="far fa-file-excel"></i> Excel
                        </a>
                    </div>
                </div>
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
                                <div class="btn-group">
                                    <a href="{{ route('members.show', $member) }}" class="btn btn-sm btn-info"
                                        title="Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('reports.member', $member) }}" class="btn btn-sm btn-secondary"
                                        title="Relatório">
                                        <i class="fas fa-file-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer">
        <div class="row">
            <div class="col-md-6">
                {{ $members->links('pagination::bootstrap-5') }}
            </div>
            <div class="col-md-6 text-right">
                <small class="text-muted">Total de membros: {{ $members->total() }}</small>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .table td,
    .table th {
        vertical-align: middle;
    }

    .btn-group {
        white-space: nowrap;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();

        // Atualiza a página quando o select de status muda
        $('select[name="status"]').change(function () {
            $(this).closest('form').submit();
        });
    });
</script>
@stop