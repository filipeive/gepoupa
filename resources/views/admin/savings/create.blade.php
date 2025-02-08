@extends('adminlte::page')

@section('title', 'Criar Poupança')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Criar Poupança</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('savings.index') }}">Poupanças</a></li>
                    <li class="breadcrumb-item active">Criar</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-ban"></i> Erro!</h5>
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Nova Poupança</h3>
                    </div>
                    <form action="{{ route('savings.store') }}" method="POST" enctype="multipart/form-data" id="savingForm">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="user_id">Usuário <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" class="form-control select2 @error('user_id') is-invalid @enderror" required>
                                    <option value="">Selecione um usuário</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="amount">Valor <span class="text-danger">*</span></label>
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

                            <div class="form-group">
                                <label for="date">Data <span class="text-danger">*</span></label>
                                <input type="date" 
                                       name="date" 
                                       id="date" 
                                       class="form-control @error('date') is-invalid @enderror" 
                                       value="{{ old('date', date('Y-m-d')) }}"
                                       required>
                                @error('date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="description">Descrição</label>
                                <textarea name="description" 
                                          id="description" 
                                          class="form-control @error('description') is-invalid @enderror"
                                          rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="proof_file">Comprovante</label>
                                <div class="custom-file">
                                    <input type="file" 
                                           class="custom-file-input @error('proof_file') is-invalid @enderror" 
                                           id="proof_file" 
                                           name="proof_file">
                                    <label class="custom-file-label" for="proof_file">Escolher arquivo</label>
                                    @error('proof_file')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Salvar
                            </button>
                            <a href="{{ route('savings.index') }}" class="btn btn-default">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
<script>
$(document).ready(function() {
    // Inicializa Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: 'Selecione um usuário'
    });

    // Inicializa Custom File Input
    bsCustomFileInput.init();

    // Validação do formulário
    $('#savingForm').on('submit', function(e) {
        let isValid = true;
        
        // Validar usuário
        if (!$('#user_id').val()) {
            isValid = false;
            $('#user_id').addClass('is-invalid');
        }

        // Validar valor
        if (!$('#amount').val() || parseFloat($('#amount').val()) <= 0) {
            isValid = false;
            $('#amount').addClass('is-invalid');
        }

        // Validar data
        if (!$('#date').val()) {
            isValid = false;
            $('#date').addClass('is-invalid');
        }

        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Por favor, preencha todos os campos obrigatórios!'
            });
        }
    });

    // Limpar validação ao alterar campo
    $('input, select, textarea').on('change', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>
@stop