@extends('adminlte::page')
@section('title', 'Detalhes do Empréstimo')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Detalhes do Empréstimo</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('loans.index') }}">Empréstimos</a></li>
                <li class="breadcrumb-item active">Detalhes</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Informações do Empréstimo -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informações do Empréstimo</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Membro:</strong> {{ $loan->user->name }}</p>
                            <p><strong>Valor:</strong> {{ number_format($loan->amount, 2) }} MZN</p>
                            <p><strong>Taxa de Juros:</strong> {{ $loan->interest_rate }}%</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Data do Pedido:</strong> {{ $loan->request_date->format('d/m/Y') }}</p>
                            <p><strong>Vencimento:</strong> {{ $loan->due_date->format('d/m/Y') }}</p>
                            <p><strong>Status:</strong>
                                <span
                                    class="badge badge-{{ $loan->status === 'approved' ? 'success' : ($loan->status === 'pending' ? 'warning' : 'primary') }}">
                                    {{ ucfirst($loan->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Total Pago:</strong> {{ number_format($totalPaid, 2) }} MZN</p>
                            <p><strong>Juros Pago:</strong> {{ number_format($totalInterest, 2) }} MZN</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Valor Restante:</strong> {{ number_format($remainingAmount, 2) }} MZN</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Ações Rápidas</h3>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-primary btn-block mb-3" data-toggle="modal"
                        data-target="#registerPaymentModal">
                        <i class="fas fa-plus"></i> Registrar Pagamento
                    </button>
                    <a href="{{ route('loans.edit', $loan) }}" class="btn btn-warning btn-block mb-3">
                        <i class="fas fa-edit"></i> Editar Empréstimo
                    </a>
                    <form action="{{ route('loans.destroy', $loan) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block"
                            onclick="return confirm('Tem certeza que deseja excluir este empréstimo?')">
                            <i class="fas fa-trash"></i> Excluir Empréstimo
                        </button>
                    </form>
                </div>
                <div class="card-footer">
                    <a href="{{ route('loans.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Registro de Pagamento -->
    <div class="modal fade" id="registerPaymentModal" tabindex="-1" role="dialog"
        aria-labelledby="registerPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerPaymentModalLabel">Registrar Pagamento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('loans.register-payment', $loan) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="amount">Valor do Pagamento</label>
                            <input type="number" name="amount" id="amount" class="form-control" step="0.01"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="interest_amount">Juros Pago</label>
                            <input type="number" name="interest_amount" id="interest_amount" class="form-control"
                                step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="payment_date">Data do Pagamento</label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="proof_file">Comprovante</label>
                            <input type="file" name="proof_file" id="proof_file" class="form-control-file">
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
@section('footer')
    @include('adminlte.footer')
@stop
@section('css')
    <style>
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .card-title {
            margin-bottom: 0;
        }

        .badge {
            font-size: 90%;
        }

        .btn-block {
            margin-bottom: 10px;
        }
    </style>
@stop

@section('js')
    <script>
        // Feedback ao registrar pagamento
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        // Feedback ao atualizar empréstimo
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@stop
