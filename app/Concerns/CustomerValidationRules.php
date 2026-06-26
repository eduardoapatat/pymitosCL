<?php

namespace App\Concerns;

use App\Rules\Rut;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

trait CustomerValidationRules
{
    /**
     * Get the validation rules used to validate customers.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function customerRules(int $companyId, ?int $customerId = null): array
    {
        return [
            'rut' => [
                'required',
                'string',
                'max:20',
                new Rut,
                Rule::unique('customers', 'rut')
                    ->where('company_id', $companyId)
                    ->ignore($customerId),
            ],
            'razon_social' => ['required', 'string', 'max:255'],
            'giro' => ['nullable', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'comuna' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
        ];
    }
}
