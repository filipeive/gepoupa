<!-- resources/views/admin/social-funds/show.blade.php -->
@extends('adminlte::page')

@section('title', 'Detalhes do Pagamento')

@section('content_header')
    <h1>Detalhes do Pagamento de Fundo Social</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informações do Pagamento</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl>
                                <dt>Membro</dt>
                                <dd>{{ $socialFund->user->name }}</dd>

                                <dt>Valor</dt>
                                <dd>{{ number_format($socialFund->amount, 2) }} MZN</dd>

                                <dt>Data do Pagamento</dt>
                                <dd>{{ $socialFund->payment_date->format('d/m/Y') }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl>
                                <dt>Status</dt>
                                <dd>
                                    <span class="badge badge-{{ $socialFund->status === 'approved' ? 'success' : 
                                        ($socialFund->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($socialFund->status) }}
                                    </span>
                                </dd>

                                <dt>Data de Registro</dt>
                                <dd>{{ $socialFund->created_at->format('d/m/Y H:i') }}</dd>

                                <dt>Última Atualização</dt>
                                <dd>{{ $socialFund->updated_at ? $socialFund->updated_at->format('d/m/Y H:i') : 'N/A' }}</dd>
                            </dl>
                        </div>
                    </div>

                    @if($socialFund->notes)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Observações</h5>
                                <p>{{ $socialFund->notes }}</p>
                            </div>
                        </div>
                    @endif

                    @if($socialFund->proof_file)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Comprovante de Pagamento</h5>
                                <a href="{{ Storage::url($socialFund->proof_file) }}" 
                                   target="_blank" 
                                   class="btn btn-info">
                                    <i class="fas fa-file-alt"></i> Visualizar Comprovante
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('social-funds.edit', $socialFund) }}" 
                       class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <form action="{{ route('social-funds.destroy', $socialFund) }}" 
                          method="POST" 
                          style="display: inline-block;"
                          onsubmit="return confirm('Tem certeza que deseja excluir este pagamento?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Excluir
                        </button>
                    </form>
                    <a href="{{ route('social-funds.index') }}" 
                       class="btn btn-secondary float-right">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Histórico de Pagamentos do Membro -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Histórico do Membro</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Valor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($memberHistory as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                    <td>{{ number_format($payment->amount, 2) }} MZN</td>
                                    <td>
                                        <span class="badge badge-{{ $payment->status === 'approved' ? 'success' : 
                                            ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@stop