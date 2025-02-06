@extends('adminlte::page')
@section('title', 'Novo Empréstimo')

@section('content_header')
    <h1>Novo Empréstimo</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{-- route('loans.store') --}}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="user_id">Membro</label>
                    <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror">
                        <option value="">Selecione um membro</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="amount">Valor do Empréstimo</label>
                    <input type="number" step="0.01" name="amount" id="amount" 
                           class="form-control @error('amount') is-invalid @enderror">
                    @error('amount')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="interest_rate">Taxa de Juros (%)</label>
                    <input type="number" step="0.01" name="interest_rate" id="interest_rate" 
                           class="form-control @error('interest_rate') is-invalid @enderror" 
                           value="10">
                    @error('interest_rate')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="request_date">Data do Pedido</label>
                    <input type="date" name="request_date" id="request_date" 
                           class="form-control @error('request_date') is-invalid @enderror" 
                           value="{{ date('Y-m-d') }}">
                    @error('request_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="due_date">Data de Vencimento</label>
                    <input type="date" name="due_date" id="due_date" 
                           class="form-control @error('due_date') is-invalid @enderror">
                    @error('due_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Registrar Empréstimo</button>
                    <a href="{{ url('painel/loans') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Inicializar select2 para melhor experiência de seleção
        $('#user_id').select2();
    });
</script>
@stop