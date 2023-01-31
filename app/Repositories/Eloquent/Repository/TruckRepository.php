<?php

namespace App\Repositories\Eloquent\Repository;

use App\Models\Truck;
use Illuminate\Database\Eloquent\Model;

class TruckRepository extends BaseRepository
{
    public function __construct(Truck $model)
    {
        parent::__construct($model);
    }

     public function truckIsOnTrip(int $truckID): ?Model
     {
         return $this->model->query()->where('on_trip', true)->where('id', $truckID)->first();
     }

}

