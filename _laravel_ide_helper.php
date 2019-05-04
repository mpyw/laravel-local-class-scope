<?php

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
