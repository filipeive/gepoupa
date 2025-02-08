@extends('adminlte::page')

@section('title', 'Editar Poupança')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Editar Poupança</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Poupanças</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Editar Poupança #{{ $saving->id }}</h3>
                    </div>
                    <form action="{{ route('savings.update', $saving->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group">
                                <label for="user_id">Usuário</label>
                                <select name="user_id" id="user_id" class="form-control" required>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $saving->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="amount">Valor</label>
                                <input type="number" step="0.01" name="amount" id="amount" class="form-control" value="{{ $saving->amount }}" required>
                            </div>
                            <div class="form-group">
                                <label for="date">Data</label>
                                <input type="date" name="date" id="date" class="form-control" value="{{ $saving->payment_date->format('Y-m-d') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Descrição</label>
                                <textarea name="description" id="description" class="form-control">{{ $saving->description }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="proof_file">Comprovante</label>
                                <input type="file" name="proof_file" id="proof_file" class="form-control">
                                @if($saving->proof_file)
                                <p class="mt-2"><a href="{{ Storage::url($saving->proof_file) }}" target="_blank">Ver Comprovante Atual</a></p>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Atualizar</button>
                            <a href="{{ route('savings.index') }}" class="btn btn-default">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            bsCustomFileInput.init();
        });
    </script>
@stop