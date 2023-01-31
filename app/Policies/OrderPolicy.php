<?php

namespace App\Policies;

use App\Models\AcceptedOrder;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user): Response|bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Order $order
     * @return bool
     */
    public function view(User $user, Order $order)
    {
        return $order->user_id == $user->id || $user->user_type == User::USER_TYPE_ADMIN || $user->user_type == User::USER_TYPE_TRANSPORTER;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->user_type == User::USER_TYPE_ADMIN || $user->user_type == User::USER_TYPE_CARGO_OWNER;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Order $order
     * @return bool
     */
    public function update(User $user, Order $order): bool
    {
        return $order->user_id == $user->id || $user->user_type == User::USER_TYPE_ADMIN || $user->user_type == User::USER_TYPE_CARGO_OWNER;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Order $order
     * @return Response|bool
     */
    public function delete(User $user, Order $order): Response|bool
    {
        return $user->user_type == User::USER_TYPE_ADMIN;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Order $order
     * @return Response|bool
     */
    public function restore(User $user, Order $order): Response|bool
    {
        return $user->user_type == User::USER_TYPE_ADMIN;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Order $order
     * @return Response|bool
     */
    public function forceDelete(User $user, Order $order): Response|bool
    {
        return $user->user_type == User::USER_TYPE_ADMIN;
    }

    public function accept(User $user, Order $order): bool
    {
        return $user->user_type == User::USER_TYPE_TRANSPORTER;
    }

    public function match(User $user, Order $order): bool
    {
        return $user->user_type == User::USER_TYPE_ADMIN;
    }

}
