<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Customer;
use App\Support\Rut;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $body = (string) fake()->numberBetween(1000000, 25000000);

        return [
            'company_id' => Company::factory(),
            'rut' => Rut::format($body.Rut::verifierDigit($body)),
            'razon_social' => fake()->company(),
            'giro' => fake()->randomElement([
                'Venta al por menor',
                'Servicios profesionales',
                'Construccion',
                'Transporte',
                'Alimentos',
            ]),
            'direccion' => fake()->streetAddress(),
            'comuna' => fake()->randomElement([
                'Santiago', 'Providencia', 'Nunoa', 'Maipu', 'Vina del Mar', 'Temuco',
            ]),
            'email' => fake()->companyEmail(),
            'telefono' => '+569'.fake()->numberBetween(10000000, 99999999),
        ];
    }
}
