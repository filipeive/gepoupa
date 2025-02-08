<?php

namespace App\Exports;

use App\Models\Saving;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class SavingsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Saving::with('user');

        // Aplicar os mesmos filtros do index
        if ($this->request->filled('user_id')) {
            $query->where('user_id', $this->request->user_id);
        }

        if ($this->request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $this->request->date_from);
        }

        if ($this->request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $this->request->date_to);
        }

        if ($this->request->filled('min_amount')) {
            $query->where('amount', '>=', $this->request->min_amount);
        }

        if ($this->request->filled('max_amount')) {
            $query->where('amount', '<=', $this->request->max_amount);
        }

        return $query->orderBy('payment_date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Membro',
            'Valor',
            'Data do Pagamento',
            'Data de Registro',
            'Última Atualização'
        ];
    }

    public function map($saving): array
    {
        return [
            $saving->id,
            $saving->user->name,
            number_format($saving->amount, 2, ',', '.'),
            $saving->payment_date->format('d/m/Y'),
            $saving->created_at->format('d/m/Y H:i'),
            $saving->updated_at->format('d/m/Y H:i')
        ];
    }
}