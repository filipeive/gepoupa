{{-- resources/views/admin/interest-distributions/create.blade.php --}}
@extends('adminlte::page')

@section('title', 'Nova Distribuição de Juros')

@section('content_header')
    <h1>Nova Distribuição de Juros</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('interest-distributions.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cycle_id">Ciclo</label>
                            <select name="cycle_id" id="cycle_id" class="form-control select2 @error('cycle_id') is-invalid @enderror" required>
                                <option value="">Selecione um ciclo</option>
                                @foreach($cycles as $cycle)
                                    <option value="{{ $cycle->id }}" {{ old('cycle_id') == $cycle->id ? 'selected' : '' }}>
                                        {{ $cycle->month_year }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cycle_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="amount">Valor</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">MZN</span>
                                </div>
                                <input type="number" 
                                       step="0.01" 
                                       name="amount" 
                                       id="amount" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       value="{{ old('amount') }}"
                                       required>
                                @error('amount')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="distribution_date">Data da Distribuição</label>
                            <input type="date" 
                                   name="distribution_date" 
                                   id="distribution_date" 
                                   class="form-control @error('distribution_date') is-invalid @enderror"
                                   value="{{ old('distribution_date') }}"
                                   required>
                            @error('distribution_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="description">Descrição</label>
                            <textarea name="description" 
                                      id="description" 
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="3"
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    
                        <!--voltar para a pagina anterior cpm javascript -->
                        <a href="javascript:history.back()" class="btn btn-secondary mr-2">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar
                        </button>
                    </div>
                </div>
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
            $('.select2').select2({
                theme: 'bootstrap4'
            });
        });
    </script>
@stop