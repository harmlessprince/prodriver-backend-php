<?php

namespace App\Repositories\Eloquent\Repository;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email): ?Model
    {
        return $this->model->query()->where('email', $email)->first();
    }
    public function filter()
    {
        // TODO, load role relation
        return $this->model->query()
            ->whereFirstName(request('first_name'))
            ->whereMiddleName(request('middle_name'))
            ->whereLastName(request('last_name'))
            ->whereEmail(request('email'))
            ->whereGender(request('gender'))
            ->whereUserType(request('user_type'));
    }

    public function searchUser()
    {
        return $this->model->query()->search();
    }

    public function totalNumberOfUser(string $user_type)
    {
        $userTypes = User::ALL_USER_TYPES;
        if (!in_array($user_type, $userTypes)) {
            return 0;
        }
        return  $this->model->where('user_type', $user_type)->count();
    }
}
