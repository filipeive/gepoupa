@extends('adminlte::page')

@section('title', 'Gestão de Poupanças')

@section('content_header')
    <h1>Gestão de Poupanças</h1>
@stop

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($totalSavings, 2, ',', '.') }} MZN</h3>
                <p>Total em Poupanças</p>
            </div>
            <div class="icon">
                <i class="fas fa-piggy-bank"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $pendingSocialFunds }}</h3>
                <p>Fundos Sociais Pendentes</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Poupanças</h3>
        <div class="card-tools">
            <a href="{{ route('admin.savings.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nova Poupança
            </a>
        </div>
    </div>
    
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Membro</th>
                        <th>Valor Poupança</th>
                        <th>Data Pagamento</th>
                        <th>Fundo Social</th>
                        <th>Status</th>
                        <th>Comprovante</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($savings as $saving)
                    <tr>
                        <td>{{ $saving->user->name }}</td>
                        <td>{{ number_format($saving->amount, 2, ',', '.') }} MZN</td>
                        <td>{{ $saving->payment_date->format('d/m/Y') }}</td>
                        <td>
                            @if($saving->socialFund)
                                {{ number_format($saving->socialFund->amount + $saving->socialFund->penalty_amount, 2, ',', '.') }} MZN
                            @else
                                Pendente
                            @endif
                        </td>
                        <td>
                            @if($saving->socialFund)
                                <span class="badge badge-{{ $saving->socialFund->status === 'paid' ? 'success' : 'warning' }}">
                                    {{ $saving->socialFund->status === 'paid' ? 'Pago' : 'Pendente' }}
                                </span>
                            @else
                                <span class="badge badge-danger">Não registrado</span>
                            @endif
                        </td>
                        <td>
                            @if($saving->proof_file)
                                <a href="{{ Storage::url($saving->proof_file) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-file"></i> Ver
                                </a>
                            @else
                                <span class="badge badge-secondary">Sem comprovante</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.savings.show', $saving) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.savings.edit', $saving) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.savings.destroy', $saving) }}" method="POST" 
                                      onsubmit="return confirm('Tem certeza que deseja excluir?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $savings->links() }}
        </div>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Hi!');
    </script>
@stop