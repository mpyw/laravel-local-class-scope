<?php

namespace Mpyw\LaravelLocalClassScope\Tests;

use InvalidArgumentException;
use Mpyw\LaravelLocalClassScope\LaravelLocalClassScopeServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class Test extends BaseTestCase
{
    /**
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            LaravelLocalClassScopeServiceProvider::class,
        ];
    }

    public function testString(): void
    {
        $query = Person::query()->scoped(AgeScope::class, '>=', 18);

        $this->assertSame('select * from `people` where `age` >= ?', $query->toSql());
        $this->assertSame([18], $query->getBindings());
    }

    public function testInstance(): void
    {
        $query = Person::query()->scoped(new AgeScope(18));

        $this->assertSame('select * from `people` where `age` = ?', $query->toSql());
        $this->assertSame([18], $query->getBindings());
    }

    public function testCallFromModel(): void
    {
        $query = Person::scoped(AgeScope::class, '<', 18);

        $this->assertSame('select * from `people` where `age` < ?', $query->toSql());
        $this->assertSame([18], $query->getBindings());
    }

    public function testInvalidScope(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$scope must be an instance of Scope');

        Person::query()->scoped(new Person());
    }
}
