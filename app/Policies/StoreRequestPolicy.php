<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StoreRequest;
use Illuminate\Auth\Access\Response;

class StoreRequestPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function approved(User $user, StoreRequest $storeRequest): Response
    {
        return $storeRequest->status === 'approved' || $storeRequest->status === 'cancelled' ?
            Response::deny('This request has already been approved or cancelled')
            : Response::allow();



        // return !$status;
    }

    public function allowed(User $user, $storeId)
    {
        return $user->store_id === $storeId ? Response::allow() : Response::deny('Access denied');
    }

    public function show(User $user, StoreRequest $item)
    {
        //only accessible to the warehouse and store in that transaction
        return $item->warehouse_id === $user->warehouse_id || $item->store_id ===
            $user->store_id ? Response::allow() : Response::deny('Access denied');
    }

    public function edit(User $user, StoreRequest $item)
    {
        return $item->store_id === $user->store_id && $item->status !== 'approved' ? Response::allow() : Response::deny('Access denied');
    }
}
