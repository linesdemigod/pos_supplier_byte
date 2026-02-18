<?php

namespace App\Policies;

use App\Models\TransferOrder;
use App\Models\User;
use Illuminate\Auth\Access\Response;
class TransferOrderPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function dispatched(User $user, TransferOrder $transferOrder)
    {

        return $status = $transferOrder->status === 'dispatched' ?
            Response::deny('This request has already been dispatched')
            : Response::allow();

        // $status = $transferOrder->status === 'dispatched';

        // return !$status;
    }

    public function delivered(User $user, TransferOrder $transfer)
    {

        return $status = $transfer->status === 'delivered' ?
            Response::deny('This request has been delivered')
            : Response::allow();

        // $status = $transferOrder->status === 'dispatched';

        // return !$status;
    }

    public function show(User $user, TransferOrder $transfer)
    {
        //only accessible to the warehouse and store in that transaction
        return $transfer->warehouse_id === $user->warehouse_id || $transfer->store_id ===
            $user->store_id ? Response::allow() : Response::deny('Access denied');
    }

    public function edit(User $user, TransferOrder $transfer)
    {
        return $transfer->warehouse_id === $user->warehouse_id ? Response::allow() : Response::deny('Access denied');
    }
}
