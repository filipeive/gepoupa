<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MembersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $members;

    public function __construct($members)
    {
        $this->members = $members;
    }

    public function collection()
    {
        return $this->members;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nome',
            'Email',
            'Telefone',
            'Status',
            'Total Poupado',
            'Total Empréstimos',
            'Data de Registro'
        ];
    }

    public function map($member): array
    {
        return [
            $member->id,
            $member->name,
            $member->email,
            $member->phone ?? 'N/A',
            $member->status ? 'Ativo' : 'Inativo',
            number_format($member->savings->sum('amount'), 2, ',', '.'),
            number_format($member->loans->sum('amount'), 2, ',', '.'),
            $member->created_at->format('d/m/Y H:i')
        ];
    }
}
