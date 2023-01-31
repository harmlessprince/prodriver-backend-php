<?php

namespace App\DataTransferObjects;

use Carbon\Carbon;

class ApproveAcceptedOrderDto
{

    public readonly ?int $total_payout;
        public readonly ?int $advance_payout;
        public readonly ?float $margin_profit_amount;
        public readonly ?float $margin_profit_percentage;
        public readonly ?Carbon $delivery_date;
        public readonly ?Carbon $loading_date;
        public readonly ?int $trip_status_id;
        public readonly ?int $way_bill_status_id;
        public readonly ?int $way_bill_picture_id;
    public function __construct(
        public readonly int $trip_id,
        public readonly int $driver_id,
        public readonly int $truck_id,
        public readonly int $order_id,
        public readonly int $cargo_owner_id,
        public readonly int $transporter_id,
        public readonly int $account_manager_id,
        public readonly int $approved_by,

    )
    {
        $this->total_payout = null;
        $this->advance_payout = null;
        $this->margin_profit_amount = null;
        $this->margin_profit_percentage = null;
        $this->delivery_date = null;
        $this->loading_date = null;
        $this->trip_status_id = null;
        $this->way_bill_status_id = null;
        $this->way_bill_picture_id = null;
    }
}
