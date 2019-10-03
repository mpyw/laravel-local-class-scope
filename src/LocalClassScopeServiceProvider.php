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
        Builder::macro('scoped', function (...$args) {
            /** @var Builder $query */
            $query = $this;
            return (new ScopedMacro($query))(...$args);
        });

        $this->publishes([
            __DIR__ . '/../config/local-class-scope.php' => $this->app->configPath('local-class-scope.php'),
        ]);
    }

    /**
     * Register _ide_helper.php rewriter.
     */
    public function register(): void
    {
        IdeHelperRewriter::register($this->app);
    }
}
