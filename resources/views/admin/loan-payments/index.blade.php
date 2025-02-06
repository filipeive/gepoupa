@extends('adminlte::page')
@section('title', 'Pagamentos de Empréstimos')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Pagamentos de Empréstimos</h1>
        </div>
        <div class="col-sm-6">
            <a href="{{ route('admin.loan-payments.create') }}" class="btn btn-primary float-right">
                <i class="fas fa-plus"></i> Novo Pagamento
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Membro</th>
                            <th>Empréstimo</th>
                            <th>Data</th>
                            <th>Valor Principal</th>
                            <th>Juros</th>
                            <th>Total</th>
                            <th>Comprovativo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <td>
                                    @if ($payment->loan && $payment->loan->user)
                                        {{ $payment->loan->user->name }}
                                    @else
                                        <span class="text-danger">Usuário não encontrado</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($payment->loan)
                                        {{ number_format($payment->loan->amount, 2) }} MZN
                                    @else
                                        <span class="text-danger">Empréstimo não encontrado</span>
                                    @endif
                                </td>
                                <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                <td>{{ number_format($payment->amount, 2) }} MZN</td>
                                <td>{{ number_format($payment->interest_amount, 2) }} MZN</td>
                                <td>{{ number_format($payment->amount + $payment->interest_amount, 2) }} MZN</td>
                                <td>
                                    @if ($payment->proof_file)
                                        <a href="{{ Storage::url($payment->proof_file) }}" target="_blank"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-file"></i> Ver
                                        </a>
                                    @else
                                        <span class="text-muted">Nenhum comprovativo</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.loan-payments.edit', $payment) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.loan-payments.destroy', $payment) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Tem certeza?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $payments->links() }}
        </div>
    </div>
@stop
