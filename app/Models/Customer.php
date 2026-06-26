<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $company_id
 * @property string $rut
 * @property string $razon_social
 * @property string|null $giro
 * @property string|null $direccion
 * @property string|null $comuna
 * @property string|null $email
 * @property string|null $telefono
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['rut', 'razon_social', 'giro', 'direccion', 'comuna', 'email', 'telefono'])]
class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope a query to only include customers of the given company.
     *
     * @param  Builder<Customer>  $query
     */
    #[Scope]
    protected function forCompany(Builder $query, int $companyId): void
    {
        $query->where('company_id', $companyId);
    }
}
