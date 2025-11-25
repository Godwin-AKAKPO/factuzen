<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tva_rate' => 'required|numeric|min:0|max:100',
            'items.*.tva_promo' => 'required|numeric|min:0|max:100',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'client_id.required' => 'Veuillez sélectionner un client.',
            'client_id.exists' => 'Le client sélectionné n\'existe pas.',
            'date.required' => 'La date d\'émission est requise.',
            'date.date' => 'La date d\'émission doit être une date valide.',
            'due_date.required' => 'La date d\'échéance est requise.',
            'due_date.date' => 'La date d\'échéance doit être une date valide.',
            'due_date.after_or_equal' => 'La date d\'échéance ne peut pas être antérieure à la date d\'émission.',
            'notes.max' => 'Les notes ne peuvent pas dépasser 1000 caractères.',
            'items.required' => 'Au moins un article est requis.',
            'items.min' => 'Au moins un article est requis.',
            'items.*.description.required' => 'La description de l\'article est requise.',
            'items.*.description.max' => 'La description ne peut pas dépasser 255 caractères.',
            'items.*.quantity.required' => 'La quantité est requise.',
            'items.*.quantity.integer' => 'La quantité doit être un nombre entier.',
            'items.*.quantity.min' => 'La quantité doit être au moins 1.',
            'items.*.unit_price.required' => 'Le prix unitaire est requis.',
            'items.*.unit_price.numeric' => 'Le prix unitaire doit être un nombre.',
            'items.*.unit_price.min' => 'Le prix unitaire ne peut pas être négatif.',
            'items.*.tva_rate.required' => 'Le taux de TVA est requis.',
            'items.*.tva_rate.numeric' => 'Le taux de TVA doit être un nombre.',
            'items.*.tva_rate.min' => 'Le taux de TVA ne peut pas être négatif.',
            'items.*.tva_rate.max' => 'Le taux de TVA ne peut pas dépasser 100%.',
            'items.*.tva_promo.required' => 'Le taux de Promo est requis.',
            'items.*.tva_promo.numeric' => 'Le taux de Promo doit être un nombre.',
            'items.*.tva_promo.min' => 'Le taux de Promo ne peut pas être négatif.',
            'items.*.tva_promo.max' => 'Le taux de Promo ne peut pas dépasser 100%.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'client_id' => 'client',
            'date' => 'date d\'émission',
            'due_date' => 'date d\'échéance',
            'notes' => 'notes',
            'items' => 'articles',
            'items.*.description' => 'description',
            'items.*.quantity' => 'quantité',
            'items.*.unit_price' => 'prix unitaire',
            'items.*.tva_rate' => 'taux de TVA',
            'items.*.tva_promo' => 'taux de Promo',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // S'assurer que le client appartient à l'utilisateur connecté
        if ($this->has('client_id')) {
            $client = \App\Models\Client::where('id', $this->client_id)
                                       ->where('user_id', auth()->id())
                                       ->first();
            
            if (!$client) {
                $this->merge(['client_id' => null]);
            }
        }
    }
}