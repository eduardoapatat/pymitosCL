<?php

use App\Models\Company;
use App\Models\Customer;
use App\Models\User;

function userWithCompany(): User
{
    return User::factory()->for(Company::factory())->create();
}

it('lists only customers of the user company', function () {
    $user = userWithCompany();
    Customer::factory()->count(2)->for($user->company)->create();
    Customer::factory()->count(3)->create();

    $response = $this->actingAs($user)->getJson(route('customers.index'));

    $response->assertOk()->assertJsonCount(2);
});

it('creates a customer in the user company with a valid rut', function () {
    $user = userWithCompany();

    $response = $this->actingAs($user)->post(route('customers.store'), [
        'rut' => '12.345.678-5',
        'razon_social' => 'Ferreteria Don Pepe SpA',
        'giro' => 'Venta al por menor',
    ]);

    $response->assertRedirect(route('customers.index'));
    $this->assertDatabaseHas('customers', [
        'company_id' => $user->company_id,
        'rut' => '12.345.678-5',
        'razon_social' => 'Ferreteria Don Pepe SpA',
    ]);
});

it('rejects an invalid rut', function () {
    $user = userWithCompany();

    $response = $this->actingAs($user)->post(route('customers.store'), [
        'rut' => '12.345.678-9',
        'razon_social' => 'Cliente Invalido',
    ]);

    $response->assertSessionHasErrors('rut');
    $this->assertDatabaseEmpty('customers');
});

it('allows the same rut for different companies', function () {
    $userA = userWithCompany();
    $userB = userWithCompany();

    Customer::factory()->for($userA->company)->create(['rut' => '12.345.678-5']);

    $response = $this->actingAs($userB)->post(route('customers.store'), [
        'rut' => '12.345.678-5',
        'razon_social' => 'Mismo RUT otra empresa',
    ]);

    $response->assertRedirect(route('customers.index'));
    expect(Customer::where('rut', '12.345.678-5')->count())->toBe(2);
});

it('rejects a duplicate rut within the same company', function () {
    $user = userWithCompany();
    Customer::factory()->for($user->company)->create(['rut' => '12.345.678-5']);

    $response = $this->actingAs($user)->post(route('customers.store'), [
        'rut' => '12.345.678-5',
        'razon_social' => 'Duplicado',
    ]);

    $response->assertSessionHasErrors('rut');
});

it('updates a customer of the user company', function () {
    $user = userWithCompany();
    $customer = Customer::factory()->for($user->company)->create();

    $response = $this->actingAs($user)->put(route('customers.update', $customer), [
        'rut' => $customer->rut,
        'razon_social' => 'Razon Social Actualizada',
    ]);

    $response->assertRedirect(route('customers.index'));
    expect($customer->fresh()->razon_social)->toBe('Razon Social Actualizada');
});

it('deletes a customer of the user company', function () {
    $user = userWithCompany();
    $customer = Customer::factory()->for($user->company)->create();

    $response = $this->actingAs($user)->delete(route('customers.destroy', $customer));

    $response->assertRedirect(route('customers.index'));
    $this->assertModelMissing($customer);
});

it('cannot view a customer from another company', function () {
    $user = userWithCompany();
    $other = Customer::factory()->create();

    $response = $this->actingAs($user)->getJson(route('customers.show', $other));

    $response->assertNotFound();
});

it('cannot update a customer from another company', function () {
    $user = userWithCompany();
    $other = Customer::factory()->create();

    $response = $this->actingAs($user)->put(route('customers.update', $other), [
        'rut' => $other->rut,
        'razon_social' => 'Hackeado',
    ]);

    $response->assertForbidden();
    expect($other->fresh()->razon_social)->not->toBe('Hackeado');
});

it('cannot delete a customer from another company', function () {
    $user = userWithCompany();
    $other = Customer::factory()->create();

    $response = $this->actingAs($user)->delete(route('customers.destroy', $other));

    $response->assertNotFound();
    $this->assertModelExists($other);
});

it('requires authentication', function () {
    $response = $this->get(route('customers.index'));

    $response->assertRedirect(route('login'));
});
