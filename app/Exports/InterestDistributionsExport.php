<?php
// app/Exports/InterestDistributionsExport.php

namespace App\Exports;

use App\Models\InterestDistribution;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InterestDistributionsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return InterestDistribution::with(['cycle', 'user'])
            ->orderBy('distribution_date', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Ciclo',
            'Membro',
            'Valor',
            'Data da Distribuição',
            'Descrição',
            'Data de Criação'
        ];
    }

    public function map($distribution): array
    {
        return [
            $distribution->id,
            $distribution->cycle->month_year ?? 'N/A',
            $distribution->user->name ?? 'N/A',
            number_format($distribution->amount, 2, ',', '.'),
            $distribution->distribution_date->format('d/m/Y'),
            $distribution->description,
            $distribution->created_at->format('d/m/Y H:i:s')
        ];
    }
}