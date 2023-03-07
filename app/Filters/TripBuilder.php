<?php

namespace App\Filters;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class TripBuilder extends BaseModelBuilder
{

    protected function getModelClass(): string
    {
        return Trip::class;
    }
    public function whereTripId(string $value = null): static
    {
        if ($value == null) return $this;
        $this->where('trip_id', $value);
        return $this;
    }

    public function whereTripStatusId(int $value = null): static
    {
        if ($value == null) return $this;
        $this->where('trip_status_id', $value);
        return $this;
    }

    public function whereWayBillStatusId(int $value = null): static
    {
        if ($value == null) return $this;
        $this->where('way_bill_status_id', $value);
        return $this;
    }

    public function wherePayoutStatus(string $value = null): static
    {
        if ($value == null) return $this;
        $this->where('payout_status', $value);
        return $this;
    }

    public function whereDeliveryStatus(string $value = null): static
    {
        if ($value == null) return $this;
        $this->where('delivery_status', $value);
        return $this;
    }


    public function whereFlagged(int $value = null): static
    {
        if ($value == null) return $this;
        $this->where('flagged', $value);
        return $this;
    }

    public function whereApprovedBy(int $value = null): static
    {
        if ($value == null) return $this;
        $this->where('approved_by', $value);
        return $this;
    }

    public function whereMatchedBy(int $value = null): static
    {
        if ($value == null) return $this;
        $this->where('matched_by', $value);
        return $this;
    }

    public function whereAccountManagerId(int $value = null): static
    {
        if ($value == null) return $this;
        $this->where('account_manager_id', $value);
        return $this;
    }

    public function whereAcceptedOrderId(int $value = null): static
    {
        if ($value == null) return $this;
        $this->where('accepted_order_id', $value);
        return $this;
    }
}
