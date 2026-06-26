<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Display the company's customers.
     */
    public function index(Request $request): JsonResponse
    {
        $customers = Customer::forCompany($request->user()->company_id)
            ->orderBy('razon_social', 'asc')
            ->get();

        return response()->json($customers);
    }

    /**
     * Store a newly created customer for the current company.
     */
    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $customer = $request->user()->company
            ->customers()
            ->create($request->validated());

        Log::info('Customer created', [
            'customer_id' => $customer->id,
            'company_id' => $customer->company_id,
        ]);

        return to_route('customers.index');
    }

    /**
     * Display the given customer.
     */
    public function show(Request $request, Customer $customer): JsonResponse
    {
        $this->authorizeCompany($request, $customer);

        return response()->json($customer);
    }

    /**
     * Update the given customer.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        Log::info('Customer updated', [
            'customer_id' => $customer->id,
            'company_id' => $customer->company_id,
        ]);

        return to_route('customers.index');
    }

    /**
     * Remove the given customer.
     */
    public function destroy(Request $request, Customer $customer): RedirectResponse
    {
        $this->authorizeCompany($request, $customer);

        $customer->delete();

        Log::info('Customer deleted', [
            'customer_id' => $customer->id,
            'company_id' => $customer->company_id,
        ]);

        return to_route('customers.index');
    }

    /**
     * Ensure the customer belongs to the current user's company.
     */
    private function authorizeCompany(Request $request, Customer $customer): void
    {
        abort_unless($customer->company_id === $request->user()->company_id, 404);
    }
}
