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

        //TODO, load role relation
        return $this->model->query()
            ->select('id', 'first_name', 'middle_name', 'last_name', 'phone_number', 'user_type')
            ->whereFirstName(request('first_name'))
            ->whereMiddleName(request('middle_name'))
            ->whereLastName(request('last_name'))
            ->whereEmail(request('email'))
            ->whereGender(request('gender'))
            ->whereUserType(request('user_type'));
    }
}
