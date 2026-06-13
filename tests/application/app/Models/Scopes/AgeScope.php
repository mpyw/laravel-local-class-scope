<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Override;

class AgeScope implements Scope
{
    /**
     * @var mixed[]
     */
    protected array $parameters;

    public function __construct(mixed ...$parameters)
    {
        $this->parameters = $parameters;
    }

    #[Override]
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('age', ...$this->parameters);
    }
}
