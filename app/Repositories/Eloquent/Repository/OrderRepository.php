<?php

namespace App\Repositories\Eloquent\Repository;

use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;

class OrderRepository extends BaseRepository
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }


    public function paginatedOrders(User $user,array $columns = ['*'], array $relations = []): Paginator
    {

        $ordersQuery =  $this->model->with($relations);
        if ($user->user_type == User::USER_TYPE_CARGO_OWNER) {
            $ordersQuery = $ordersQuery->where('cargo_owner_id', $user->id);
        }
        if ($user->user_type == User::USER_TYPE_TRANSPORTER) {
            $ordersQuery = $ordersQuery
                ->where('approved_by', null)
                ->where('matched_by', null)
                ->where('status', Order::PENDING);
        }
        return $ordersQuery->paginate();
    }


    public function acceptOrder(int $truck_id, int $amount)
    {

    }

}
