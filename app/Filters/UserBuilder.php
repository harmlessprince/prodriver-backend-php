<?php

namespace App\Filters;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserBuilder extends BaseModelBuilder
{

    protected function getModelClass(): string
    {
        return User::class;
    }

    // protected function getSearchableFields(): array
    // {
    //     return [
    //         'first_name',
    //         'last_name',
    //         'middle_name',
    //         'email',
    //         'user_type',
    //         'gender',
    //     ];
    // }
    public function whereFirstName(string $first_name = null): static
    {
        if ($first_name == null) return $this;
        $this->where('first_name', $first_name);
        return $this;
    }


    public function whereLastName(string $last_name = null): static
    {
        if ($last_name == null) return $this;
        $this->where('last_name', $last_name);
        return $this;
    }

    public function whereMiddleName(string $middle_name = null): static
    {
        if ($middle_name == null) return $this;
        $this->where('middle_name', $middle_name);
        return $this;
    }

    // public function whereLocation(string $location = null): static
    // {
    //     if ($location == null) return $this;
    //     $this->where('address', 'like', '%' . $location . '%');
    //     return $this;
    // }

    public function whereEmail(string $email = null): static
    {
        if ($email == null) return $this;
        $this->where('email', $email);
        return $this;
    }

    public function whereUserType(string $user_type = null): static
    {
        if ($user_type == null) return $this;
        $this->where('user_type', $user_type);
        return $this;
    }

    public function whereGender(string $gender = null): static
    {
        if ($gender == null) return $this;
        $this->where('gender', $gender);
        return $this;
    }
}
