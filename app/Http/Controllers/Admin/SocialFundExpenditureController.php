<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialFundExpenditure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SocialFundExpenditureController extends Controller
{
    public function index()
    {
        $expenditures = SocialFundExpenditure::latest('expenditure_date')->paginate(10);
        return view('admin.social-fund-expenditures.index', compact('expenditures'));
    }

    public function create()
    {
        return view('admin.social-fund-expenditures.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'expenditure_date' => 'required|date',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('proof_file')) {
            $validated['proof_file'] = $request->file('proof_file')->store('social-fund-expenditures', 'public');
        }

        SocialFundExpenditure::create($validated);

        return redirect()->route('social-fund-expenditures.index')
            ->with('success', 'Despesa registrada com sucesso!');
    }
}
