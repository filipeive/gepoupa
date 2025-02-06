@extends('adminlte::page')
@section('title', 'Detalhes do Empréstimo')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Detalhes do Empréstimo</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.loans.index') }}">Empréstimos</a></li>
                <li class="breadcrumb-item active">Detalhes</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Informações do Empréstimo -->
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Informações do Empréstimo</h3>
                </div>
                <div class="card-body">
                    <strong>Membro:</strong>
                    <p class="text-muted">{{ $loan->user->name }}</p>

                    <strong>Valor do Empréstimo:</strong>
                    <p class="text-muted">{{ number_format($loan->amount, 2) }} MZN</p>

                    <strong>Taxa de Juros:</strong>
                    <p class="text-muted">{{ $loan->interest_rate }}%</p>

                    <strong>Data do Pedido:</strong>
                    <p class="text-muted">{{ $loan->request_date->format('d/m/Y') }}</p>

                    <strong>Data de Vencimento:</strong>
                    <p class="text-muted">{{ $loan->due_date->format('d/m/Y') }}</p>

                    <strong>Status:</strong>
                    <p>
                        <span class="badge badge-{{ 
                            $loan->status === 'approved' ? 'success' : 
                            ($loan->status === 'pending' ? 'warning' : 
                            ($loan->status === 'rejected' ? 'danger' : 'info')) 
                        }}">
                            {{ ucfirst($loan->status) }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Card de Resumo Financeiro -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Resumo Financeiro</h3>
                </div>
                <div class="card-body">
                    <div class="info-box bg-light">
                        <div class="info-box-content">
                            <span class="info-box-text">Total Pago</span>
                            <span class="info-box-number">{{ number_format($totalPaid, 2) }} MZN</span>
                        </div>
                    </div>
                    <div class="info-box bg-light">
                        <div class="info-box-content">
                            <span class="info-box-text">Total em Juros</span>
                            <span class="info-box-number">{{ number_format($totalInterest, 2) }} MZN</span>
                        </div>
                    </div>
                    <div class="info-box bg-light">
                        <div class="info-box-content">
                            <span class="info-box-text">Valor Restante</span>
                            <span class="info-box-number">{{ number_format($remainingAmount, 2) }} MZN</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Histórico de Pagamentos e Formulário -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="#payments" data-toggle="tab">
                                Histórico de Pagamentos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#new-payment" data-toggle="tab">
                                Registrar Pagamento
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Tab Histórico de Pagamentos -->
                        <div class="active tab-pane" id="payments">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Valor Principal</th>
                                            <th>Juros</th>
                                            <th>Total</th>
                                            <th>Comprovativo</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($loan->payments as $payment)
                                            <tr>
                                                <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                                <td>{{ number_format($payment->amount, 2) }} MZN</td>
                                                <td>{{ number_format($payment->interest_amount, 2) }} MZN</td>
                                                <td>{{ number_format($payment->amount + $payment->interest_amount, 2) }} MZN</td>
                                                <td>
                                                    @if($payment->proof_file)
                                                        <a href="{{ Storage::url($payment->proof_file) }}" 
                                                           target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-file"></i> Ver
                                                        </a>
                                                    @else
                                                        <span class="text-muted">Sem comprovativo</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.loan-payments.edit', $payment) }}" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    Nenhum pagamento registrado
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tab Novo Pagamento -->
                        <div class="tab-pane" id="new-payment">
                            <form action="{{ route('admin.loans.registerPayment', $loan) }}" 
                                  method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="payment_date">Data do Pagamento</label>
                                    <input type="date" name="payment_date" id="payment_date" 
                                           class="form-control @error('payment_date') is-invalid @enderror" 
                                           value="{{ date('Y-m-d') }}">
                                    @error('payment_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="amount">Valor Principal</label>
                                    <input type="number" step="0.01" name="amount" id="amount" 
                                           class="form-control @error('amount') is-invalid @enderror">
                                    @error('amount')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="interest_amount">Valor dos Juros</label>
                                    <input type="number" step="0.01" name="interest_amount" id="interest_amount" 
                                           class="form-control @error('interest_amount') is-invalid @enderror">
                                    @error('interest_amount')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="proof_file">Comprovativo</label>
                                    <input type="file" name="proof_file" id="proof_file" 
                                           class="form-control-file @error('proof_file') is-invalid @enderror">
                                    @error('proof_file')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    Registrar Pagamento
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop