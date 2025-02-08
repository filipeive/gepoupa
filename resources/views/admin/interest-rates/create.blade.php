{{-- resources/views/admin/interest-rates/create.blade.php --}}
@extends('adminlte::page')

@section('title', 'Nova Taxa de Juros')

@section('content_header')
    <h1>Nova Taxa de Juros</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.interest-rates.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="rate">Taxa (%)</label>
                    <input type="number" step="0.01" 
                           class="form-control @error('rate') is-invalid @enderror" 
                           id="rate" name="rate" value="{{ old('rate') }}">
                    @error('rate')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="effective_date">Data Efetiva</label>
                    <input type="date" 
                           class="form-control @error('effective_date') is-invalid @enderror" 
                           id="effective_date" name="effective_date" 
                           value="{{ old('effective_date') }}">
                    @error('effective_date')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Descrição</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" 
                              rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="{{ route('admin.interest-rates.index') }}" 
                   class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Máscaras e validações
            $('#rate').mask('##0,00', {reverse: true});
        });
    </script>
@stop