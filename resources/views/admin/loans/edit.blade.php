@extends('adminlte::page')
@section('title', 'Editar Empréstimo')

@section('content_header')
    <h1>Editar Empréstimo</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('loans.update', $loan) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label>Membro</label>
                    <p class="form-control-static">{{ $loan->user->name }}</p>
                </div>

                <div class="form-group">
                    <label for="amount">Valor do Empréstimo</label>
                    <input type="number" step="0.01" name="amount" id="amount" 
                           class="form-control @error('amount') is-invalid @enderror"
                           value="{{ old('amount', $loan->amount) }}">
                    @error('amount')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="interest_rate">Taxa de Juros (%)</label>
                    <input type="number" step="0.01" name="interest_rate" id="interest_rate" 
                           class="form-control @error('interest_rate') is-invalid @enderror"
                           value="{{ old('interest_rate', $loan->interest_rate) }}">
                    @error('interest_rate')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="pending" {{ $loan->status === 'pending' ? 'selected' : '' }}>Pendente</option>
                        <option value="approved" {{ $loan->status === 'approved' ? 'selected' : '' }}>Aprovado</option>
                        <option value="rejected" {{ $loan->status === 'rejected' ? 'selected' : '' }}>Rejeitado</option>
                        <option value="paid" {{ $loan->status === 'paid' ? 'selected' : '' }}>Pago</option>
                    </select>
                    @error('status')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="due_date">Data de Vencimento</label>
                    <input type="date" name="due_date" id="due_date" 
                           class="form-control @error('due_date') is-invalid @enderror"
                           value="{{ old('due_date', $loan->due_date->format('Y-m-d')) }}">
                    @error('due_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Atualizar Empréstimo</button>
                    <a href="{{ route('loans.index', $loan) }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@stop