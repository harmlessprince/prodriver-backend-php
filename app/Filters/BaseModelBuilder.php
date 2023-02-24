<?php

namespace App\Filters;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseModelBuilder extends Builder
{
    abstract protected function getModelClass(): string;
    // abstract protected function getSearchableFields(): array;

    public function filter(array $params, Builder $builder = null): Builder
    {
        $builder =  $builder === null ?  app($this->getModelClass())->query() : $builder;

        foreach ($params as $key => $value) {
            $methodName = 'where' . ucfirst(str_replace('_', '', ucwords($key, '_')));
            if (method_exists(static::class, $methodName)) {
                $builder->$methodName($value);
            }
        }

        return $builder;
    }
}
