@extends('adminlte::page')
@section('title', 'Editar Pagamento')

@section('content_header')
    <h1>Editar Pagamento</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('loan-payments.update', $loanPayment) }}" 
                  method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Empréstimo</label>
                    <p class="form-control-static">
                        {{ $loanPayment->loan->user->name }} - 
                        {{ number_format($loanPayment->loan->amount, 2) }} MZN
                    </p>
                </div>

                <div class="form-group">
                    <label for="payment_date">Data do Pagamento</label>
                    <input type="date" name="payment_date" id="payment_date" 
                           class="form-control @error('payment_date') is-invalid @enderror"
                           value="{{ old('payment_date', $loanPayment->payment_date->format('Y-m-d')) }}">
                    @error('payment_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="amount">Valor Principal</label>
                    <input type="number" step="0.01" name="amount" id="amount" 
                           class="form-control @error('amount') is-invalid @enderror"
                           value="{{ old('amount', $loanPayment->amount) }}">
                    @error('amount')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="interest_amount">Valor dos Juros</label>
                    <input type="number" step="0.01" name="interest_amount" id="interest_amount" 
                           class="form-control @error('interest_amount') is-invalid @enderror"
                           value="{{ old('interest_amount', $loanPayment->interest_amount) }}">
                    @error('interest_amount')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="proof_file">Novo Comprovativo</label>
                    <input type="file" name="proof_file" id="proof_file" 
                           class="form-control-file @error('proof_file') is-invalid @enderror">
                    @if($loanPayment->proof_file)
                        <small class="form-text text-muted">
                            Comprovativo atual: 
                            <a href="{{ Storage::url($loanPayment->proof_file) }}" target="_blank">
                                Ver arquivo
                            </a>
                        </small>
                    @endif
                    @error('proof_file')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Atualizar Pagamento</button>
                    <a href="{{ route('loan-payments.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@stop