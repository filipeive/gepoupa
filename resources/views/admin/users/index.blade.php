@extends('adminlte::page')
@section('title', 'Usuários')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Usuários</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Usuários</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="input-group">
                        <input type="text" class="form-control" id="search"
                            placeholder="Pesquisar por nome ou email..." value="{{ $search }}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="searchUsers()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-right">
                    <a href="{{ route('users.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Novo Usuário
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Cargo</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="users-table">
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>
                                <span class="badge {{ $user->role == 'admin' ? 'badge-danger' : 'badge-info' }}">
                                    {{ $user->role == 'admin' ? 'Administrador' : 'Membro' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $user->status ? 'badge-success' : 'badge-warning' }}">
                                    {{ $user->status ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-info"
                                        title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-primary"
                                        title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if ($loggedId !== intval($user->id))
                                        <form class="d-inline" action="{{ route('users.destroy', $user->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-footer clearfix">
            <div class="float-right">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir este usuário?</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
@section('footer')
    @include('adminlte.footer')
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        function searchUsers() {
            let search = document.getElementById('search').value;
            window.location.href = "{{ route('users.index') }}?search=" + search;
        }

        // Pesquisa ao pressionar Enter
        document.getElementById('search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchUsers();
            }
        });

        function confirmDelete(userId) {
            $('#deleteForm').attr('action', `/users/${userId}`);
            $('#deleteModal').modal('show');
        }

        // Fechar alertas automaticamente após 5 segundos
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);

        // Mensagens de sucesso ou erro
        @if (session('success'))
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'Sucesso!',
                body: '{{ session('success') }}'
            });
        @endif

        @if (session('error'))
            $(document).Toasts('create', {
                class: 'bg-danger',
                title: 'Erro!',
                body: '{{ session('error') }}'
            });
        @endif
    </script>
@stop
