<?php

namespace App\Policies;

use App\Models\Truck;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TruckPolicy
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
        return  $user->user_type === User::USER_TYPE_ADMIN || $user->user_type === User::USER_TYPE_TRANSPORTER;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param  \App\Models\Truck  $truck
     * @return Response|bool
     */
    public function view(User $user, Truck $truck)
    {
        return  $user->user_type === User::USER_TYPE_ADMIN || ($user->user_type === User::USER_TYPE_TRANSPORTER && $user->id === $truck->truck_owner_id);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        return  $user->user_type === User::USER_TYPE_ADMIN || $user->user_type === User::USER_TYPE_TRANSPORTER;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param  \App\Models\Truck  $truck
     * @return Response|bool
     */
    public function update(User $user, Truck $truck)
    {
        return  $user->user_type === User::USER_TYPE_ADMIN || ($user->user_type === User::USER_TYPE_TRANSPORTER && $user->id === $truck->truck_owner_id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param  \App\Models\Truck  $truck
     * @return Response|bool
     */
    public function delete(User $user, Truck $truck)
    {
        return  $user->user_type === User::USER_TYPE_ADMIN;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param  \App\Models\Truck  $truck
     * @return Response|bool
     */
    public function restore(User $user, Truck $truck)
    {
        return  $user->user_type === User::USER_TYPE_ADMIN;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param  \App\Models\Truck  $truck
     * @return Response|bool
     */
    public function forceDelete(User $user, Truck $truck): Response|bool
    {
        return  $user->user_type === User::USER_TYPE_ADMIN;
    }
}
