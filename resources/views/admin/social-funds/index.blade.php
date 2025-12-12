<!-- resources/views/admin/social-funds/index.blade.php -->
@extends('adminlte::page')

@section('title', 'Gestão de Fundos Sociais')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Fundos Sociais</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#createModal">
            <i class="fas fa-plus"></i> Novo Pagamento
        </button>
    </div>
@stop
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-tools">
                <form action="{{ route('social-funds.index') }}" method="GET" class="form-inline">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Buscar membro..."
                            value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Membro</th>
                        <th>Valor</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Comprovante</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($socialFunds as $fund)
                        <tr>
                            <td>{{ $fund->id }}</td>
                            <td>{{ $fund->user->name }}</td>
                            <td>{{ number_format($fund->amount, 2) }} MZN</td>
                            <td>{{ $fund->payment_date->format('d/m/Y') }}</td>
                            <td>
                                <span
                                    class="badge badge-{{ $fund->status === 'paid' ? 'success' : ($fund->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($fund->status) }}
                                </span>
                            </td>
                            <td>
                                @if ($fund->proof_file)
                                    <a href="{{ Storage::url($fund->proof_file) }}" target="_blank">
                                        <i class="fas fa-file-alt"></i> Ver comprovante
                                    </a>
                                @else
                                    <span class="text-muted">Sem comprovante</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('social-funds.show', $fund) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('social-funds.edit', $fund) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('social-funds.destroy', $fund) }}" method="POST"
                                        onsubmit="return confirm('Tem certeza?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Nenhum registro encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($socialFunds->hasPages())
            <div class="card-footer">
                {{ $socialFunds->links() }}
            </div>
        @endif
    </div>

    <!-- Card de Estatísticas -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($totalFunds, 2) }} MZN</h3>
                    <p>Total Arrecadado</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($approvedFunds, 2) }} MZN</h3>
                    <p>Pagamentos Aprovados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($pendingFunds, 2) }} MZN</h3>
                    <p>Pagamentos Pendentes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $membersWithoutPayment }}</h3>
                    <p>Membros sem Pagamento</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-times"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Criação de Fundo Social -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Novo Pagamento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('social-funds.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="user_id">Membro</label>
                            <select name="user_id" id="user_id" class="form-control">
                                <!-- Carregar membros dinamicamente ou manualmente -->
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="amount">Valor</label>
                            <input type="number" name="amount" id="amount" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="payment_date">Data de Pagamento</label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="proof_file">Comprovante (opcional)</label>
                            <input type="file" name="proof_file" id="proof_file" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="pending">Pendente</option>
                                <option value="paid">Pago</option>
                                <option value="late">Atrasado</option>
                            </select>                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@stop