<?php

namespace
{
    class Eloquent extends Illuminate\Database\Eloquent\Model
    {
        /**
         * Apply Scope to Eloquent\Builder.
         *
         * @param  Illuminate\Database\Eloquent\Scope|string          $scope
         * @param  mixed                                              ...$parameters
         * @return $this|\Illuminate\Database\Eloquent\Builder|static
         */
        public static function scoped($scope, ...$parameters)
        {
            return static::newInstance()->newQuery()->scoped($scope, ...$parameters);
        }
    }
}

namespace Illuminate\Database\Eloquent
{
    class Builder
    {
        /**
         * Apply Scope to Eloquent\Builder.
         *
         * @param  \Illuminate\Database\Eloquent\Scope|string         $scope
         * @param  mixed                                              ...$parameters
         * @return $this|\Illuminate\Database\Eloquent\Builder|static
         */
        public function scoped($scope, ...$parameters)
        {
            return (new class() extends Model {
            })
                ->newQuery()
                ->scoped($scope, ...$parameters);
        }
    }
}
