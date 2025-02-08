<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SavingCycle;
use Carbon\Carbon;

class SavingCycleController extends Controller
{
    public function index()
    {
        $cycles = SavingCycle::orderBy('start_date', 'desc')->paginate(10);
        return view('admin.saving-cycles.index', compact('cycles'));
    }

    public function create()
    {
        return view('admin.saving-cycles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'month_year' => 'required|string|size:7'
        ]);

        SavingCycle::create($validated);

        return redirect()->route('admin.saving-cycles.index')
            ->with('success', 'Ciclo de poupança criado com sucesso!');
    }

    public function show(SavingCycle $cycle)
    {
        $cycle->load(['savings', 'interestDistributions']);
        return view('admin.saving-cycles.show', compact('cycle'));
    }

    public function edit(SavingCycle $cycle)
    {
        return view('admin.saving-cycles.edit', compact('cycle'));
    }

    public function update(Request $request, SavingCycle $cycle)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,completed',
            'month_year' => 'required|string|size:7'
        ]);

        $cycle->update($validated);

        return redirect()->route('admin.saving-cycles.index')
            ->with('success', 'Ciclo de poupança atualizado com sucesso!');
    }

    public function complete(SavingCycle $cycle)
    {
        if ($cycle->status === 'active') {
            $cycle->update([
                'status' => 'completed',
                'end_date' => Carbon::now()
            ]);
            
            return redirect()->route('admin.saving-cycles.index')
                ->with('success', 'Ciclo de poupança finalizado com sucesso!');
        }

        return redirect()->route('admin.saving-cycles.index')
            ->with('error', 'Este ciclo já está finalizado!');
    }
}