<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class SalePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function show(User $user, $sale)
    {
        return $user->store_id === $sale->store_id ? Response::allow() : Response::deny('Access denied');
    }
}
