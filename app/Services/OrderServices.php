<?php

namespace App\Services;

use App\DataTransferObjects\AcceptOrderDto;
use App\DataTransferObjects\ApproveAcceptedOrderDto;
use App\DataTransferObjects\MatchOrderDto;
use App\Models\AcceptedOrder;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OrderServices
{
    public function hasAcceptedOrderWithSameTruck(int $order_id, int $transporter_id, int $truck_id): bool
    {
        return AcceptedOrder::query()->where('order_id', $order_id)->where('accepted_by', $transporter_id)->where('truck_id', $truck_id)->exists();
    }

    public function acceptOrder(AcceptOrderDto $acceptOrderDto): void
    {
        AcceptedOrder::query()->create([
            'order_id' => $acceptOrderDto->order_id,
            'truck_id' => $acceptOrderDto->truck_id,
            'accepted_by' => $acceptOrderDto->accepted_by,
            'amount' => $acceptOrderDto->amount,
            'accepted_at' => Carbon::now()
        ]);
        return;
    }

    public function matchOrder(MatchOrderDto $dto): void
    {
        AcceptedOrder::query()->create([
            'order_id' => $dto->order_id,
            'truck_id' => $dto->truck_id,
            'accepted_by' => $dto->transporter_id,
            'matched_by' => $dto->matched_by,
            'amount' => $dto->amount,
            'accepted_at' => Carbon::now(),
            'matched_at' => Carbon::now()
        ]);
        return;
    }

    public function convertApprovedOrderToTrip(ApproveAcceptedOrderDto $dto): Model|Builder
    {
        // dd((array)$dto);
        return Trip::query()->create((array)$dto);
    }

}
