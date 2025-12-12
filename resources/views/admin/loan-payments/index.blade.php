@extends('adminlte::page')
@section('title', 'Pagamentos de Empréstimos')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> 
                        <i class="fas fa-money-bill-wave text-primary"></i>
                        Pagamentos de Empréstimos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active"> Pagamentos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-sm-6">
        </div>
        <div class="col-sm-6">
            <a href="{{ route('loan-payments.create') }}" class="btn btn-primary float-right">
                <i class="fas fa-plus"></i> Novo Pagamento
            </a>
        </div>
    </div>
@stop


@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <div class="row">
                <div class="col-md-8">
                    <form action="{{ route('loan-payments.index') }}" method="GET" class="form-inline">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Pesquisar membro..."
                                value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <div class="float-right">
                        <button type="button" class="btn btn-outline-secondary" data-toggle="modal"
                            data-target="#filterModal">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        <a href="{{ route('loan-payments.index', ['export' => true] + request()->all()) }}"
                            class="btn btn-outline-secondary">
                            <i class="fas fa-download"></i> Exportar
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Filtros -->
        <div class="modal fade" id="filterModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('loan-payments.filter') }}" method="GET">
                        <div class="modal-header">
                            <h5 class="modal-title">Filtrar Pagamentos</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Período</label>
                                <div class="input-group">
                                    <input type="date" name="date_from" class="form-control"
                                        value="{{ request('date_from') }}">
                                    <div class="input-group-append input-group-prepend">
                                        <span class="input-group-text">até</span>
                                    </div>
                                    <input type="date" name="date_to" class="form-control"
                                        value="{{ request('date_to') }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Valor</label>
                                <div class="input-group">
                                    <input type="number" name="amount_min" class="form-control" placeholder="Mínimo"
                                        value="{{ request('amount_min') }}">
                                    <div class="input-group-append input-group-prepend">
                                        <span class="input-group-text">até</span>
                                    </div>
                                    <input type="number" name="amount_max" class="form-control" placeholder="Máximo"
                                        value="{{ request('amount_max') }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                            <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>
                                <a href="{{ route('loan-payments.index', ['sort' => 'loan.user.name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'] + request()->all()) }}"
                                    class="text-dark">
                                    Membro
                                    @if (request('sort') == 'loan.user.name')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Empréstimo</th>
                            <th>Data</th>
                            <th>Valor Principal</th>
                            <th>Juros</th>
                            <th>Total</th>
                            <th>Comprovativo</th>
                            <th width="120">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <td>
                                    @if ($payment->loan && $payment->loan->user)
                                        <span class="text-primary">{{ $payment->loan->user->name }}</span>
                                    @else
                                        <span class="badge badge-danger">Usuário não encontrado</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($payment->loan)
                                        <strong>{{ number_format($payment->loan->amount, 2) }} MZN</strong>
                                    @else
                                        <span class="badge badge-danger">Empréstimo não encontrado</span>
                                    @endif
                                </td>
                                <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                <td class="text-right">{{ number_format($payment->amount, 2) }} MZN</td>
                                <td class="text-right">{{ number_format($payment->interest_amount, 2) }} MZN</td>
                                <td class="text-right">
                                    <strong>{{ number_format($payment->amount + $payment->interest_amount, 2) }}
                                        MZN</strong>
                                </td>
                                <td>
                                    @if ($payment->proof_file)
                                        <a href="{{ Storage::url($payment->proof_file) }}" target="_blank"
                                            class="btn btn-sm btn-info" data-toggle="tooltip" title="Ver comprovativo">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                    @else
                                        <span class="badge badge-secondary">Sem comprovativo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('loan-payments.edit', $payment) }}"
                                            class="btn btn-sm btn-primary" data-toggle="tooltip" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('loan-payments.destroy', $payment) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" data-toggle="tooltip"
                                                title="Excluir"
                                                onclick="return confirm('Tem certeza que deseja excluir este pagamento?')">
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
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col-md-6">
                    {{ $payments->links('pagination::bootstrap-5') }}
                </div>
                <div class="col-md-6 text-right">
                    <small class="text-muted">Total de pagamentos: {{ $payments->total() }}</small>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .table td {
            vertical-align: middle;
        }

        .btn-group {
            white-space: nowrap;
        }
    </style>
@stop

@section('js')
    <script>
          $(function () {
        $('[data-toggle="tooltip"]').tooltip();
        
        // Manter modal aberta se houver erros de validação
        @if($errors->any())
            $('#filterModal').modal('show');
        @endif

        // Limpar filtros
        $('.clear-filters').click(function() {
            window.location.href = "{{ route('loan-payments.index') }}";
        });
    });
    </script>
@stop
