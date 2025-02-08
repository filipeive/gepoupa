<?php
// app/Exports/InterestDistributionReport.php

namespace App\Exports;

use App\Models\InterestDistribution;
use Illuminate\Support\Collection;

class InterestDistributionReport extends BaseReport
{
    public function collection()
    {
        return InterestDistribution::with(['cycle', 'user'])
            ->get()
            ->map(function ($distribution) {
                return [
                    'Ciclo' => $distribution->cycle->month_year,
                    'Membro' => $distribution->user->name,
                    'Valor' => 'R$ ' . number_format($distribution->amount, 2, ',', '.'),
                    'Data' => $distribution->distribution_date->format('d/m/Y'),
                    'Descrição' => $distribution->description
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Ciclo',
            'Membro',
            'Valor',
            'Data',
            'Descrição'
        ];
    }
}