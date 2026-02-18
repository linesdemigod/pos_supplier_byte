<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToStoreOrWarehouse
{
    protected static function bootBelongsToStoreOrWarehouse(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            $user = Auth::user();

            if (!$user) {
                return;
            }

            $table = $builder->getModel()->getTable();

            // Apply tenant scoping
            if ($user->store_id) {
                $builder->where("{$table}.store_id", $user->store_id);
            }

            if ($user->warehouse_id) {
                $builder->where("{$table}.warehouse_id", $user->warehouse_id);
            }
        });

        static::creating(function ($model) {
            $user = Auth::user();

            if (!$user) {
                return;
            }

            if ($user->store_id) {
                $model->store_id = $user->store_id;
            }

            if ($user->warehouse_id) {
                $model->warehouse_id = $user->warehouse_id;
            }

        });
    }
}
