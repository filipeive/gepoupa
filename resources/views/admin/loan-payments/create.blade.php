@extends('adminlte::page')
@section('title', 'Novo Pagamento')

@section('content_header')
    <h1>Registrar Novo Pagamento</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('loan-payments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group">
                    <label for="loan_id">Empréstimo</label>
                    <select name="loan_id" id="loan_id" class="form-control @error('loan_id') is-invalid @enderror">
                        <option value="">Selecione um empréstimo</option>
                        @foreach($loans as $loan)
                            <option value="{{ $loan->id }}">
                                {{ $loan->user->name }} - {{ number_format($loan->amount, 2) }} MZN
                            </option>
                        @endforeach
                    </select>
                    @error('loan_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="payment_date">Data do Pagamento</label>
                    <input type="date" name="payment_date" id="payment_date" 
                           class="form-control @error('payment_date') is-invalid @enderror"
                           value="{{ old('payment_date', date('Y-m-d')) }}">
                    @error('payment_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="amount">Valor Principal</label>
                    <input type="number" step="0.01" name="amount" id="amount" 
                           class="form-control @error('amount') is-invalid @enderror"
                           value="{{ old('amount') }}">
                    @error('amount')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="interest_amount">Valor dos Juros</label>
                    <input type="number" step="0.01" name="interest_amount" id="interest_amount" 
                           class="form-control @error('interest_amount') is-invalid @enderror"
                           value="{{ old('interest_amount') }}">
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

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Registrar Pagamento</button>
                    <a href="{{ route('loan-payments.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#loan_id').select2();
    });
</script>
@stop