<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * @implements Scope<Model>
 */
class CompanyScope implements Scope
{
    /**
     * Constrain every query to the authenticated user's company.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (! Auth::check()) {
            return;
        }

        $builder->where(
            $model->getTable().'.company_id',
            Auth::user()->company_id,
        );
    }
}
