<?php
namespace App\DataTransferObjects;
use App\Models\AcceptedOrder;

class AcceptOrderDto {
    public function __construct(
        public readonly int $order_id,
        public readonly int $truck_id,
        public readonly int $accepted_by,
        public readonly float $amount,
        public readonly int|null $approved_by = null,
        public readonly int|null $matched_by= null,
        public readonly ?int $id = null,
    )
    {
    }
   static public function fromModel(AcceptedOrder $acceptedOrder): AcceptOrderDto
   {
    return new  self(
        $acceptedOrder->order_id,
        $acceptedOrder->truck_id,
        $acceptedOrder->accepted_by,
        $acceptedOrder->amount,
        $acceptedOrder->approved_by,
        $acceptedOrder->matched_by,
        $acceptedOrder->id,
    );
   }
}
