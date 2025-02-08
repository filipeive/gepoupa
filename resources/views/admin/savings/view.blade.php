@extends('adminlte::page')

@section('title', 'Detalhes da Poupança')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Detalhes da Poupança</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{route('savings.index')}}">Poupanças</a></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- Cards Informativos -->
        <div class="row">
            <div class="col-lg-4 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ number_format($saving->amount, 2, ',', '.') }}</h3>
                        <p>Valor desta Poupança</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <a href="#" class="small-box-footer">Mais info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ number_format($userTotalSavings, 2, ',', '.') }}</h3>
                        <p>Total Poupanças do Usuário</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-piggy-bank"></i>
                    </div>
                    <a href="#" class="small-box-footer">Mais info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $userSavingsHistory->count() }}</h3>
                        <p>Total de Depósitos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-list"></i>
                    </div>
                    <a href="#" class="small-box-footer">Mais info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Detalhes da Poupança -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Poupança #{{ $saving->id }}</h3>
                        <div class="card-tools">
                            <a href="{{ route('savings.edit', $saving->id) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <p><strong>Usuário:</strong> {{ $saving->user->name }}</p>
                        <p><strong>Valor:</strong> MZN {{ number_format($saving->amount, 2, ',', '.') }}</p>
                        <p><strong>Data:</strong> {{ $saving->payment_date->format('d/m/Y') }}</p>
                        <p><strong>Descrição:</strong> {{ $saving->description }}</p>
                        @if($saving->proof_file)
                        <p><strong>Comprovante:</strong> <a href="{{ Storage::url($saving->proof_file) }}" target="_blank">Ver Comprovante</a></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Histórico de Poupanças do Usuário -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Histórico de Poupanças</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Valor</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userSavingsHistory as $history)
                                <tr>
                                    <td>{{ $history->id }}</td>
                                    <td>MZN {{ number_format($history->amount, 2, ',', '.') }}</td>
                                    <td>{{ $history->payment_date->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Hi!');
    </script>
@stop