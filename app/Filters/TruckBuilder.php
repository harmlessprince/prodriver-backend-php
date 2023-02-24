<?php

namespace App\Filters;

use App\Models\Truck;

class TruckBuilder extends BaseModelBuilder
{

    protected function getModelClass(): string
    {
        return Truck::class;
    }


    public function whereTruckOwnerId(int $value = null): static
    {
        if ($value == null) return $this;
        $this->where('truck_owner_id', $value);
        return $this;
    }

    public function whereTruckTypeId(int $value = null): static
    {
        if ($value == null) return $this;
        $this->where('truck_type_id', $value);
        return $this;
    }

    public function whereDriverId(int $value = null): static
    {
        if ($value == null) return $this;
        $this->where('driver_id', $value);
        return $this;
    }

    public function whereRegistrationNumber(string $value = null): static
    {
        if ($value == null) return $this;
        $this->where('registration_number', $value);
        return $this;
    }

    public function whereChassisNumber(string $value = null): static
    {
        if ($value == null) return $this;
        $this->where('chassis_number', $value);
        return $this;
    }

    public function whereMaker(string $value = null): static
    {
        if ($value == null) return $this;
        $this->where('maker', $value);
        return $this;
    }

    public function whereModel(string $value = null): static
    {
        if ($value == null) return $this;
        $this->where('model', $value);
        return $this;
    }

    public function whereOnTrip(bool $value = null): static
    {
        if ($value == null) return $this;
        $this->where('on_trip', $value);
        return $this;
    }
    
}
