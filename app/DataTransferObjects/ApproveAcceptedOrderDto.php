<?php

namespace App\DataTransferObjects;

use Carbon\Carbon;
use DateTime;

class ApproveAcceptedOrderDto
{


    public ?float $margin_profit_amount;
    public ?float $margin_profit_percentage;
    public ?int $trip_status_id;
    public ?int $way_bill_status_id;
    public ?int $way_bill_picture_id;
    public ?float $balance_payout;
    public ?float $total_gtv;
    public ?string $delivery_status;
    public ?string $payout_status;
    public ?int $days_in_transit;
    public ?int $days_delivered;
    public ?DateTime $completed_date;
    public float|null $net_margin_profit_amount;
    public float|null $advance_gtv;
    public float|null $balance_gtv;
    public function __construct(
        public readonly int        $accepted_order_id,
        public readonly string     $trip_id,
        public readonly int        $driver_id,
        public readonly int        $truck_id,
        public readonly int        $order_id,
        public readonly int        $cargo_owner_id,
        public readonly int        $transporter_id,
        public readonly int        $account_manager_id,
        public readonly int        $approved_by,
        public readonly ?float     $total_payout = 0,
        public readonly ?float     $advance_payout = 0,
        public readonly ?\DateTime $loading_date = null,
        public readonly ?\DateTime $delivery_date = null,
    )
    {
        $this->margin_profit_amount = 0;
        $this->margin_profit_percentage = 0;
        $this->trip_status_id = null;
        $this->way_bill_status_id = null;
        $this->way_bill_picture_id = null;
        $this->balance_gtv = 0;
        $this->delivery_status = 'pending';
        $this->payout_status = 'pending';
    }
}
