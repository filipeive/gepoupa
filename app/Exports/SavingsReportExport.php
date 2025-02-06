<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class SavingsReportExport implements FromCollection, WithHeadings
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return DB::table('savings')
            ->join('users', 'savings.user_id', '=', 'users.id')
            ->whereBetween('savings.payment_date', [$this->startDate, $this->endDate])
            ->select(
                'users.name',
                DB::raw('COUNT(*) as total_deposits'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('AVG(amount) as average_amount')
            )
            ->groupBy('users.id', 'users.name')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Usuário',
            'Total de Depósitos',
            'Total Poupança',
            'Média por Depósito',
        ];
    }
}
