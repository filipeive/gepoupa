@extends('adminlte::page')
@section('title', 'Detalhes do Pagamento')

@section('content_header')
    <h1>Detalhes do Pagamento</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informações do Pagamento</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Membro</dt>
                        <dd class="col-sm-8">{{ $loanPayment->loan->user->name }}</dd>

                        <dt class="col-sm-4">Valor do Empréstimo</dt>
                        <dd class="col-sm-8">{{ number_format($loanPayment->loan->amount, 2) }} MZN</dd>

                        <dt class="col-sm-4">Data do Pagamento</dt>
                        <dd class="col-sm-8">{{ $loanPayment->payment_date->format('d/m/Y') }}</dd>

                        <dt class="col-sm-4">Valor Principal</dt>
                        <dd class="col-sm-8">{{ number_format($loanPayment->amount, 2) }} MZN</dd>

                        <dt class="col-sm-4">Valor dos Juros</dt>
                        <dd class="col-sm-8">{{ number_format($loanPayment->interest_amount, 2) }} MZN</dd>

                        <dt class="col-sm-4">Total Pago</dt>
                        <dd class="col-sm-8">
                            {{ number_format($loanPayment->amount + $loanPayment->interest_amount, 2) }} MZN
                        </dd>

                        <dt class="col-sm-4">Comprovativo</dt>
                        <dd class="col-sm-8">
                            @if($loanPayment->proof_file)
                                <a href="{{ Storage::url($loanPayment->proof_file) }}" 
                                   target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-file"></i> Ver Comprovativo
                                </a>
                            @else
                                <span class="text-muted">Sem comprovativo</span>
                            @endif
                        </dd>
                    </dl>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.loan-payments.edit', $loanPayment) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <form action="{{ route('admin.loan-payments.destroy', $loanPayment) }}" 
                          method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Tem certeza que deseja excluir este pagamento?')">
                            <i class="fas fa-trash"></i> Excluir
                        </button>
                    </form>
                    <a href="{{ route('admin.loan-payments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informações do Empréstimo</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Status do Empréstimo</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-{{ 
                                $loanPayment->loan->status === 'approved' ? 'success' : 
                                ($loanPayment->loan->status === 'pending' ? 'warning' : 
                                ($loanPayment->loan->status === 'rejected' ? 'danger' : 'info')) 
                            }}">
                                {{ ucfirst($loanPayment->loan->status) }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Taxa de Juros</dt>
                        <dd class="col-sm-8">{{ $loanPayment->loan->interest_rate }}%</dd>

                        <dt class="col-sm-4">Data do Pedido</dt>
                        <dd class="col-sm-8">{{ $loanPayment->loan->request_date->format('d/m/Y') }}</dd>

                        <dt class="col-sm-4">Data de Vencimento</dt>
                        <dd class="col-sm-8">{{ $loanPayment->loan->due_date->format('d/m/Y') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@stop