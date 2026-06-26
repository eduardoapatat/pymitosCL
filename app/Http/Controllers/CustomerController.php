<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Attributes\Controllers\Authorize;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Display the company's customers.
     */
    public function index(): JsonResponse
    {
        $customers = Customer::all();

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
    #[Authorize('view', 'customer')]
    public function show(Customer $customer): JsonResponse
    {
        return response()->json($customer);
    }

    /**
     * Update the given customer.
     */
    #[Authorize('update', 'customer')]
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
    #[Authorize('delete', 'customer')]
    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        Log::info('Customer deleted', [
            'customer_id' => $customer->id,
            'company_id' => $customer->company_id,
        ]);

        return to_route('customers.index');
    }
}
