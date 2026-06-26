<?php

use App\Models\Company;
use App\Models\User;
use App\Support\Rut;
use Illuminate\Database\QueryException;

it('creates a company with a valid chilean rut', function () {
    $company = Company::factory()->create();

    expect(Rut::isValid($company->rut))->toBeTrue();
});

it('persists the company business fields', function () {
    $company = Company::factory()->create([
        'razon_social' => 'Ferreteria Don Pepe SpA',
        'giro' => 'Venta al por menor',
    ]);

    $this->assertDatabaseHas('companies', [
        'razon_social' => 'Ferreteria Don Pepe SpA',
        'giro' => 'Venta al por menor',
    ]);
});

it('enforces a unique rut', function () {
    $company = Company::factory()->create();

    expect(fn () => Company::factory()->create(['rut' => $company->rut]))
        ->toThrow(QueryException::class);
});

it('has many users', function () {
    $company = Company::factory()->create();
    $users = User::factory()->count(3)->for($company)->create();

    expect($company->users)->toHaveCount(3)
        ->and($users->first()->company->is($company))->toBeTrue();
});

it('keeps users when their company is deleted', function () {
    $company = Company::factory()->create();
    $user = User::factory()->for($company)->create();

    $company->delete();

    expect($user->fresh()->company_id)->toBeNull();
});
