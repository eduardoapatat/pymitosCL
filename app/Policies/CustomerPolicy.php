<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    /**
     * Determine whether the user can view the customer.
     */
    public function view(User $user, Customer $customer): bool
    {
        return $this->belongsToCompany($user, $customer);
    }

    /**
     * Determine whether the user can update the customer.
     */
    public function update(User $user, Customer $customer): bool
    {
        return $this->belongsToCompany($user, $customer);
    }

    /**
     * Determine whether the user can delete the customer.
     */
    public function delete(User $user, Customer $customer): bool
    {
        return $this->belongsToCompany($user, $customer);
    }

    /**
     * Ensure the customer belongs to the user's company.
     */
    private function belongsToCompany(User $user, Customer $customer): bool
    {
        return $customer->company_id === $user->company_id;
    }
}
