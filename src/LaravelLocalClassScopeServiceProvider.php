<?php

namespace Mpyw\LaravelLocalClassScope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;

class LaravelLocalClassScopeServiceProvider extends ServiceProvider
{
    /**
     * Register Eloquent\Builder::scoped() macro.
     */
    public function boot(): void
    {
        Builder::macro('scoped', function (...$args) {
            /** @var Builder $query */
            $query = $this;
            return (new ScopedMacro($query))(...$args);
        });
    }
}
