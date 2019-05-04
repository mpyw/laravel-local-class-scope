<?php

namespace Mpyw\LaravelLocalClassScope;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Contracts\Foundation\Application;

/**
 * Class LaravelIdeHelperRewriter
 *
 * @codeCoverageIgnore
 */
class LaravelIdeHelperRewriter
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * Run only in local development environment.
     *
     * @param Application $app
     */
    public static function register(Application $app): void
    {
        if ($app->runningInConsole() && !$app->runningUnitTests()) {
            (new static($app))();
        }
    }

    /**
     * LaravelIdeHelperRewriter constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Invoke using Config Repository and Event Dispatcher.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __invoke(): void
    {
        $this->resolved(ConfigRepository::class, function (ConfigRepository $repository) {
            $this->resolved(EventDispatcher::class, function (EventDispatcher $events) use ($repository) {
                $this->listenGeneratorCommandIfEnabled($repository, $events);
            });
        });
    }

    /**
     * Run callback after when an instance is available.
     *
     * @param  string                                                     $abstract
     * @param  callable                                                   $callback
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function resolved(string $abstract, callable $callback)
    {
        $this->app->resolved($abstract)
            ? $callback($this->app->make($abstract))
            : $this->app->afterResolving($abstract, $callback);
    }

    /**
     * Register a command listener.
     *
     * @param ConfigRepository $repository
     * @param EventDispatcher  $events
     */
    public function listenGeneratorCommandIfEnabled(ConfigRepository $repository, EventDispatcher $events)
    {
        $config = $repository->get('laravel-local-class-scope.model_method_completion');

        if ($config['enabled'] ?? true && in_array($this->app->environment(), $config['environments'] ?? ['local'], true)) {
            $events->listen(CommandFinished::class, [$this, 'rewriteOnGeneratorCommandFinished']);
        }
    }

    /**
     * Rewrite on ide-helper:generate finished.
     *
     * @param CommandFinished $event
     */
    public function rewriteOnGeneratorCommandFinished(CommandFinished $event): void
    {
        if ($event->command === 'ide-helper:generate') {
            static::rewrite();
        }
    }

    /**
     * Rewrite \Eloquent section.
     */
    public function rewrite(): void
    {
        if (file_exists($path = "{$this->app->basePath()}/_ide_helper.php")) {
            $fp = fopen($path, 'c+b');
            flock($fp, LOCK_EX);

            $content = stream_get_contents($fp);
            rewind($fp);

            ftruncate($fp, fwrite($fp, preg_replace(
                static::pattern(),
                static::insertion(),
                $content
            )));

            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }

    /**
     * \Eloquent section detection pattern.
     *
     * @return string
     */
    public static function pattern(): string
    {
        return <<<'EOD'
/
    class \s+ Eloquent \s+ extends \s+ \\Illuminate\\Database\\Eloquent\\Model \s+ \{ \s+?
    \K
    (?=[ \t]+\S) 
/x
EOD;
    }

    /**
     * New methods for \Eloquent section.
     *
     * @return string
     */
    public static function insertion(): string
    {
        return <<<'EOD'

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


EOD;
    }
}
