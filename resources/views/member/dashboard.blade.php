@extends('layouts.member')

@section('member_content')
<div class="row">
    <!-- Card de Poupanças -->
    <div class="col-md-4">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($totalSavings, 2) }} MZN</h3>
                <p>Total Poupanças</p>
            </div>
            <div class="icon">
                <i class="fas fa-piggy-bank"></i>
            </div>
            <a href="{{ route('member.savings') }}" class="small-box-footer">
                Mais informações <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Card de Empréstimos Ativos -->
    <div class="col-md-4">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $activeLoans->count() }}</h3>
                <p>Empréstimos Ativos</p>
            </div>
            <div class="icon">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <a href="{{ route('member.loans') }}" class="small-box-footer">
                Mais informações <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Card de Fundos Sociais -->
    <div class="col-md-4">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $socialFunds->count() }}</h3>
                <p>Pagamentos de Fundo Social</p>
            </div>
            <div class="icon">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
            <a href="{{ route('member.social-funds') }}" class="small-box-footer">
                Mais informações <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Tabela de Últimos Pagamentos de Fundo Social -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Últimos Pagamentos de Fundo Social</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($socialFunds as $socialFund)
                    <tr>
                        <td>{{ $socialFund->payment_date->format('d/m/Y') }}</td>
                        <td>{{ number_format($socialFund->amount, 2) }} MZN</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection