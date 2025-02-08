@extends('adminlte::page')

@section('title', 'Listagem de Poupanças')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Listagem de Poupanças</h1>
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
@stop

@section('content')
    <div class="container-fluid">
        <!-- Filtros e Pesquisa -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Filtros</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form id="filterForm" method="GET" class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Período</label>
                            <div class="input-group">
                                <input type="date" name="date_from" class="form-control"
                                    value="{{ request('date_from') }}">
                                <div class="input-group-append">
                                    <span class="input-group-text">até</span>
                                </div>
                                <input type="date" name="date_to" class="form-control"
                                    value="{{ request('date_to') }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Usuário</label>
                            <select name="user_id" class="form-control select2">
                                <option value="">Todos</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Valor Mínimo</label>
                            <input type="number" name="min_amount" class="form-control"
                                value="{{ request('min_amount') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Valor Máximo</label>
                            <input type="number" name="max_amount" class="form-control"
                                value="{{ request('max_amount') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <a href="{{ route('savings.index') }}" class="btn btn-default">
                                    <i class="fas fa-times"></i> Limpar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cards Informativos -->
        <div class="row">
            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ number_format($totalSavings, 2, ',', '.') }}</h3>
                        <p>Total Poupanças</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-piggy-bank"></i>
                    </div>
                    <a href="#" class="small-box-footer">Mais info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $savings->total() }}</h3>
                        <p>Total Registros</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-list"></i>
                    </div>
                    <a href="#" class="small-box-footer">Mais info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $monthlySavings->sum('total') }}</h3>
                        <p>Poupança Mensal</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <a href="#" class="small-box-footer">Mais info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $savings->count() }}</h3>
                        <p>Registros Recentes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="#" class="small-box-footer">Mais info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Listagem de Poupanças -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Poupanças</h3>
                <div class="card-tools">
                    <div class="btn-group">
                        <a href="{{ route('savings.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nova Poupança
                        </a>
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                            data-toggle="dropdown">
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('savings.report') }}">
                                <i class="fas fa-chart-bar"></i> Relatório
                            </a>
                            <a class="dropdown-item" href="{{ route('savings.export.excel') }}" id="exportExcel">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </a>
                            <a class="dropdown-item" href="savings.export.pdf" id="exportPDF">
                                <i class="fas fa-file-pdf"></i> Exportar PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>
                                <a
                                    href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                    ID
                                    @if (request('sort') == 'id')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Usuário</th>
                            <th>Valor</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($savings as $saving)
                            <tr>
                                <td>{{ $saving->id }}</td>
                                <td>
                                    <img src="{{ $saving->user->avatar_url }}" class="img-circle mr-2"
                                        style="width: 30px">
                                    {{ $saving->user->name }}
                                </td>
                                <td>
                                    <span class="badge badge-success">
                                        MZN {{ number_format($saving->amount, 2, ',', '.') }}
                                    </span>
                                </td>
                                <td>{{ $saving->payment_date->format('d/m/Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('savings.show', $saving) }}" class="btn btn-sm btn-info"
                                            data-toggle="tooltip" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('savings.edit', $saving) }}" class="btn btn-sm btn-primary"
                                            data-toggle="tooltip" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-saving"
                                            data-id="{{ $saving->id }}" data-toggle="tooltip" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Nenhum registro encontrado</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-sm-4">
                        Mostrando {{ $savings->firstItem() }} até {{ $savings->lastItem() }} de {{ $savings->total() }}
                        registros
                    </div>
                    {{-- <div class="col-sm-8">
                        {{ $savings->links() }}
                    </div> --}}
                    <div class="col-sm-8">
                        {{ $savings->links('pagination::bootstrap-5') }}
                    </div>                    
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Poupanças Mensais</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlySavingsChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Distribuição por Usuário</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="userDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Tem certeza que deseja excluir este registro?
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css">
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializa Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: 'Selecione um usuário'
            });

            // Tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Confirmação de exclusão
            $('.delete-saving').click(function() {
                const id = $(this).data('id');
                $('#deleteForm').attr('action', `/savings/${id}`);
                $('#deleteModal').modal('show');
            });

            // Gráficos
            const monthlySavingsData = @json($monthlySavings);
            const ctx = document.getElementById('monthlySavingsChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: monthlySavingsData.map(item => `${item.month}/${item.year}`),
                    datasets: [{
                        label: 'Poupanças Mensais',
                        data: monthlySavingsData.map(item => item.total),
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Evolução das Poupanças'
                        }
                    }
                }
            });
        });
    </script>
@stop
