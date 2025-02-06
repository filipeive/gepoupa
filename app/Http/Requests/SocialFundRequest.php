<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialFundRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'status' => 'required|in:pending,paid,late',
            'penalty_amount' => 'nullable|numeric|min:0',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'O membro é obrigatório.',
            'amount.required' => 'O valor é obrigatório.',
            'amount.numeric' => 'O valor deve ser um número.',
            'payment_date.required' => 'A data de pagamento é obrigatória.',
            'payment_date.date' => 'A data de pagamento deve ser uma data válida.',
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'O status selecionado é inválido.',
            'proof_file.mimes' => 'O comprovante deve ser um arquivo do tipo: pdf, jpg, jpeg, png.',
            'proof_file.max' => 'O comprovante não pode ser maior que 2MB.',
        ];
    }
}