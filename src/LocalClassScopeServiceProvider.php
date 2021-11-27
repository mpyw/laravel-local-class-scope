<?php

namespace Mpyw\LaravelLocalClassScope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;

class LocalClassScopeServiceProvider extends ServiceProvider
{
    /**
     * Register Eloquent\Builder::scoped() macro.
     */
    public function boot(): void
    {
        Builder::macro('scoped', function ($scope, ...$parameters) {
            $query = $this;
            \assert($query instanceof Builder);
            return (new ScopedMacro($query))($scope, ...$parameters);
        });
    }
}
