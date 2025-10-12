<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LoanPaymentsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Membro',
            'Empréstimo',
            'Data',
            'Valor Principal',
            'Juros',
            'Total',
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->loan->user->name ?? 'N/A',
            $payment->loan->amount ?? 0,
            $payment->payment_date->format('d/m/Y'),
            $payment->amount,
            $payment->interest_amount,
            $payment->amount + $payment->interest_amount,
        ];
    }
}
