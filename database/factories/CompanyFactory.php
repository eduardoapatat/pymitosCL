<?php

namespace Database\Factories;

use App\Models\Company;
use App\Support\Rut;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $body = (string) fake()->numberBetween(60000000, 99999999);

        return [
            'rut' => Rut::format($body.Rut::verifierDigit($body)),
            'razon_social' => fake()->company(),
            'giro' => fake()->randomElement([
                'Venta al por menor',
                'Servicios de consultoria',
                'Construccion',
                'Transporte de carga',
                'Restaurantes',
            ]),
            'direccion' => fake()->streetAddress(),
            'comuna' => fake()->randomElement([
                'Santiago', 'Providencia', 'Las Condes', 'Maipu', 'Valparaiso', 'Concepcion',
            ]),
            'email' => fake()->companyEmail(),
            'telefono' => '+569'.fake()->numberBetween(10000000, 99999999),
        ];
    }
}
