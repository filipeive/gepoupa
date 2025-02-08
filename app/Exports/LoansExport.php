<?php
namespace App\Exports;

use App\Models\Loan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class LoansExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Loan::with('user')->get()->map(function ($loan) {
            return [
                'Membro' => $loan->user->name,
                'Valor' => $loan->amount,
                'Taxa de Juros' => $loan->interest_rate,
                'Data do Pedido' => $loan->request_date->format('d/m/Y'),
                'Vencimento' => $loan->due_date->format('d/m/Y'),
                'Status' => $loan->status,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Membro',
            'Valor',
            'Taxa de Juros',
            'Data do Pedido',
            'Vencimento',
            'Status',
        ];
    }
}