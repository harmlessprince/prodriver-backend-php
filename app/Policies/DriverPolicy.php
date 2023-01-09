<?php

namespace App\Policies;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class DriverPolicy
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
        return $user->user_type == User::USER_TYPE_ADMIN || $user->user_type === User::USER_TYPE_TRANSPORTER;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Driver $driver
     * @return Response|bool
     */
    public function view(User $user, Driver $driver): Response|bool
    {
        return $user->user_type === User::USER_TYPE_ADMIN ||
            ($user->user_type === User::USER_TYPE_TRANSPORTER && $user->id === $driver->user_id && $user->company->id === $driver->company_id);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        return $user->user_type === User::USER_TYPE_ADMIN || $user->user_type === User::USER_TYPE_TRANSPORTER;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Driver $driver
     * @return Response|bool
     */
    public function update(User $user, Driver $driver): Response|bool
    {
        return $user->user_type === User::USER_TYPE_ADMIN ||
            ($user->user_type === User::USER_TYPE_TRANSPORTER && $user->id === $driver->user_id && $user->company->id === $driver->company_id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Driver $driver
     * @return Response|bool
     */
    public function delete(User $user, Driver $driver): Response|bool
    {
        return $user->user_type === User::USER_TYPE_ADMIN;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Driver $driver
     * @return Response|bool
     */
    public function restore(User $user, Driver $driver)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Driver $driver
     * @return Response|bool
     */
    public function forceDelete(User $user, Driver $driver)
    {
        //
    }
}
