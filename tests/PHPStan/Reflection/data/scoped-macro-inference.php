<?php

declare(strict_types=1);

use App\Models\Scopes\ActiveScope;
use App\Models\Scopes\AgeScope;
use App\Models\User;

use function PHPStan\Testing\assertType;

function test(): void
{
    // 1. Static call: User::scoped(ActiveScope::class) -> Builder<User>
    assertType(
        'Illuminate\Database\Eloquent\Builder<App\Models\User>',
        User::scoped(ActiveScope::class),
    );

    // 2. Static call: User::scoped(new ActiveScope) -> Builder<User>
    assertType(
        'Illuminate\Database\Eloquent\Builder<App\Models\User>',
        User::scoped(new ActiveScope),
    );

    // 3. Instance call: $user->scoped(ActiveScope::class) -> Builder<User>
    $user = new User;
    assertType(
        'Illuminate\Database\Eloquent\Builder<App\Models\User>',
        $user->scoped(ActiveScope::class),
    );

    // 4. Instance call: $user->scoped(new ActiveScope) -> Builder<User>
    $user = new User;
    assertType(
        'Illuminate\Database\Eloquent\Builder<App\Models\User>',
        $user->scoped(new ActiveScope),
    );

    // 5. Query builder chaining: User::query()->scoped(ActiveScope::class) -> Builder<User>
    assertType(
        'Illuminate\Database\Eloquent\Builder<App\Models\User>',
        User::query()->scoped(ActiveScope::class),
    );

    // 6. Method chaining: User::scoped(ActiveScope::class)->scoped(AgeScope::class, '>=', 18) -> Builder<User>
    assertType(
        'Illuminate\Database\Eloquent\Builder<App\Models\User>',
        User::scoped(ActiveScope::class)->scoped(AgeScope::class, '>=', 18),
    );

    // 7. Invalid class: User::scoped(stdClass::class) -> Builder<User>
    assertType(
        'Illuminate\Database\Eloquent\Builder<App\Models\User>',
        User::scoped(stdClass::class),
    );

    // 8. Invalid class: User::scoped(new stdClass) -> Builder<User>
    assertType(
        'Illuminate\Database\Eloquent\Builder<App\Models\User>',
        User::scoped(new stdClass),
    );

    // 9. Invalid scalar: User::scoped('invalid scope name') -> Builder<User>
    assertType(
        'Illuminate\Database\Eloquent\Builder<App\Models\User>',
        User::scoped('invalid scope name'),
    );

    // 10. Invalid scalar: User::scoped(123) -> Builder<User>
    assertType(
        'Illuminate\Database\Eloquent\Builder<App\Models\User>',
        User::scoped(123),
    );

    // 11. No arguments: User::scoped() -> Builder<User>
    assertType(
        'Illuminate\Database\Eloquent\Builder<App\Models\User>',
        User::scoped(),
    );
}
