# Laravel Local Class Scope [![Build Status](https://github.com/mpyw/laravel-local-class-scope/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/mpyw/laravel-local-class-scope/actions) [![Coverage Status](https://coveralls.io/repos/github/mpyw/laravel-local-class-scope/badge.svg?branch=master)](https://coveralls.io/github/mpyw/laravel-local-class-scope?branch=master)

A tiny macro that reuse a global scope class as a local scope.

The idea is from: [[Proposal] Local query scopes as classes · Issue #636 · laravel/ideas](https://github.com/laravel/ideas/issues/636)

## Requirements

- PHP: `^8.2`
- Laravel: `^11.0 || ^12.0`

> [!NOTE]
> Older versions have outdated dependency requirements. If you cannot prepare the latest environment, please refer to past releases.

## Installing

```bash
composer require mpyw/laravel-local-class-scope
```

## Usage

### Simple Scope

```php
class ActiveScope implements Scope
{
    public function apply(Builder $query, Model $model): void
    {
        $query->where('active', true);
    }
}
```

```php
User::scoped(ActiveScope::class)->get();
```

```php
User::scoped(new ActiveScope())->get();
```

### Scope that takes arguments

```php
class AgeScope implements Scope
{
    protected $parameters;

    public function __construct(...$parameters)
    {
        $this->parameters = $parameters;
    }

    public function apply(Builder $query, Model $model): void
    {
        $query->where('age', ...$this->parameters);
    }
}
```

```php
User::scoped(AgeScope::class, '>', 18)->get();
```

```php
User::scoped(new AgeScope('>', 18))->get();
```

### Combination

```php
User::scoped(ActiveScope::class)->scoped(AgeScope::class, '>', 18)->get();
```

### Re-define as a local method scope

```php
class User extends Model
{
    public function scopeActive(Builder $query): Builder
    {
        return $this->scoped(ActiveScope::class);
    }
}
```

### Share local method re-definition via trait

```php
trait ScopesActive
{
    public function scopeActive(Builder $query): Builder
    {
        return $this->scoped(ActiveScope::class);
    }    
}
```

```php
class User extends Model
{
    use ScopesActive;
}
```

```php
class Admin extends Model
{
    use ScopesActive;
}
```
