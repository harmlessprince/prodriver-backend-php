<?php

namespace App\Filters;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class OrderBuilder extends BaseModelBuilder
{

    protected function getModelClass(): string
    {
        return User::class;
    }
    public function whereTonnageId(int $value = null): static
    {
        if ($value == null) return $this;
        $this->where('tonnage_id', $value);
        return $this;
    }

    public function whereCargoOwnerId(int $value = null): static
    {
        if ($value == null) return $this;
        $this->where('cargo_owner_id', $value);
        return $this;
    }

    public function whereCreatedBy(int $value = null): static
    {
        if ($value == null) return $this;
        $this->where('created_by', $value);
        return $this;
    }

    public function whereApprovedBy(int $value = null): static
    {
        if ($value == null) return $this;
        $this->where('approved_by', $value);
        return $this;
    }


    public function whereCancelledBy(int $value = null): static
    {
        if ($value == null) return $this;
        $this->where('cancelled_by', $value);
        return $this;
    }

    public function whereFinancialStatus(string $value = null): static
    {
        if ($value == null) return $this;
        $this->where('financial_status', $value);
        return $this;
    }

    public function whereStatus(string $value = null): static
    {
        if ($value == null) return $this;
        $this->where('status', $value);
        return $this;
    }
}
