<?php

namespace Mpyw\LaravelLocalClassScope\Tests;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class AgeScope implements Scope
{
    /**
     * @var array
     */
    protected $parameters;

    /**
     * AgeScope constructor.
     *
     * @param mixed ...$parameters
     */
    public function __construct(...$parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param Builder $builder
     * @param Model   $model
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('age', ...$this->parameters);
    }
}
