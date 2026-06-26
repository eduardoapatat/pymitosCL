<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
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
#[ScopedBy([CompanyScope::class])]
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
}
