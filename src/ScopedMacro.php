<?php

namespace Mpyw\LaravelLocalClassScope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Scope;
use InvalidArgumentException;

/**
 * Class ScopedMacro
 */
class ScopedMacro
{
    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $query;

    /**
     * ScopedMacro constructor.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    /**
     * Apply Scope to Eloquent\Builder.
     *
     * @param  \Illuminate\Database\Eloquent\Scope|string $scope
     * @param  mixed                                      ...$parameters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function __invoke($scope, ...$parameters): Builder
    {
        if (is_string($scope) && class_exists($scope)) {
            $scope = new $scope(...$parameters);
        }
        if (!$scope instanceof Scope) {
            throw new InvalidArgumentException('$scope must be an instance of Scope');
        }

        $scope->apply($this->query, $this->query->getModel());

        return $this->query;
    }
}
