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
    public function filterUser(array $params)
    {
        return $this->model->query()->filter($params);
    }

    public function searchUser()
    {
        return $this->model->query()->search();
    }


    public  function searchAndFilter(array $params){
        $query = $this->model->query()->search();
        return $query->filter($params,$query);
    }

    public function totalNumberOfUser(string $user_type = null)
    {
        $userTypes = User::ALL_USER_TYPES;
        if (!in_array($user_type, $userTypes) || $user_type == null) {
            return 0;
        }
        return  $this->model->where('user_type', $user_type)->count();
    }
}
