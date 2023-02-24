<?php

namespace App\Filters;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class DriverBuilder extends BaseModelBuilder
{
    protected function getModelClass(): string
    {
        return Driver::class;
    }
    public function whereUserId(string $value = null): static
    {
        if ($value == null) return $this;
        $this->where('user_id', $value);
        return $this;
    }

    public function wereOnTrip(bool $value = null): static
    {
        if ($value == null) return $this;
        $this->where('on_trip', $value);
        return $this;
    }
}
