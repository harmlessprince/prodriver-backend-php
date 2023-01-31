<?php

namespace App\Policies;

use App\Models\AcceptedOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class AcceptedOrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param AcceptedOrder $acceptedOrder
     * @return Response|bool
     */
    public function view(User $user, AcceptedOrder $acceptedOrder)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param AcceptedOrder $acceptedOrder
     * @return Response|bool
     */
    public function update(User $user, AcceptedOrder $acceptedOrder): Response|bool
    {
        //
    }

    /**
     * Determine whether the user can approve the model.
     *
     * @param User $user
     * @param AcceptedOrder $acceptedOrder
     * @return Response|bool
     */
    public function approve(User $user, AcceptedOrder $acceptedOrder): Response|bool
    {
        return $user->user_type == User::USER_TYPE_ADMIN;
    }


    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param AcceptedOrder $acceptedOrder
     * @return Response|bool
     */
    public function delete(User $user, AcceptedOrder $acceptedOrder)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param AcceptedOrder $acceptedOrder
     * @return Response|bool
     */
    public function restore(User $user, AcceptedOrder $acceptedOrder)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param AcceptedOrder $acceptedOrder
     * @return Response|bool
     */
    public function forceDelete(User $user, AcceptedOrder $acceptedOrder)
    {
        //
    }
}
