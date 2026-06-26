<?php

namespace App\Http\Requests;

use App\Concerns\CustomerValidationRules;
use App\Models\Customer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    use CustomerValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $customer = $this->route('customer');

        if (! $customer instanceof Customer) {
            return [];
        }

        return $this->customerRules(
            $this->user()->company_id,
            $customer->id,
        );
    }
}
