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

        $ordersQuery =  $this->model->with($relations)->latest('created_at');
        if ($user->user_type == User::USER_TYPE_CARGO_OWNER) {
            $ordersQuery = $ordersQuery->where('cargo_owner_id', $user->id);
        }
        if ($user->user_type == User::USER_TYPE_TRANSPORTER) {
            $ordersQuery = $ordersQuery
                ->where('approved_by', null)
                ->where('matched_by', null)
                ->where('status', Order::PENDING);
        }
        $ordersQuery = $ordersQuery->search();
        $ordersQuery = $ordersQuery->filter(request()->all(), $ordersQuery);
        return $ordersQuery->paginate(request('per_page', 15));
    }


    public function filterOrder(array $params)
    {
        return $this->model->query()->filter($params);
    }

    public function searchOrder()
    {
        return $this->model->query()->search();
    }


    public  function searchAndFilterOrder(array $params){
        $query = $this->model->query()->search();
        return $query->filter($params,$query);
    }

}
