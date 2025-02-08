<?php
// app/Exports/InterestRateReport.php

namespace App\Exports;

use App\Models\InterestRates;
use Illuminate\Support\Collection;

class InterestRateReport extends BaseReport
{
    public function collection()
    {
        return interestRates::select(
            'rate',
            'effective_date',
            'description',
            'created_at'
        )->get()->map(function ($item) {
            return [
                'Taxa' => $item->rate . '%',
                'Data Efetiva' => $item->effective_date->format('d/m/Y'),
                'Descrição' => $item->description,
                'Data Registro' => $item->created_at->format('d/m/Y'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Taxa',
            'Data Efetiva',
            'Descrição',
            'Data Registro'
        ];
    }
}