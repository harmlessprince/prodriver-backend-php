<?php

namespace App\DataTransferObjects;

class MatchOrderDto
{
    public function __construct(
        public readonly int $order_id,
        public readonly int $truck_id,
        public readonly int $transporter_id,
        public readonly int $matched_by,
        public readonly float $amount
    )
    {
    }
}
