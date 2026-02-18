<?php

declare(strict_types=1);

use App\Models\Scopes\ActiveScope;
use App\Models\Scopes\AgeScope;
use App\Models\User;
use stdClass;

// 1. Static call: User::scoped(ActiveScope::class) -> No errors
User::scoped(ActiveScope::class);

// 2. Static call: User::scoped(new ActiveScope) -> No errors
User::scoped(new ActiveScope);

// 3. Instance call: $user->scoped(ActiveScope::class) -> No errors
$user = new User;
$user->scoped(ActiveScope::class);

// 4. Instance call: $user->scoped(new ActiveScope) -> No errors
$user = new User;
$user->scoped(new ActiveScope);

// 5. Query builder chaining: User::query()->scoped(ActiveScope::class) -> No errors
User::query()->scoped(ActiveScope::class);

// 6. Method chaining: User::scoped(ActiveScope::class)->scoped(AgeScope::class, '>=', 18) -> No errors
User::scoped(ActiveScope::class)->scoped(AgeScope::class, '>=', 18);

// 7. Invalid class: User::scoped(stdClass::class) -> argument.type error
User::scoped(stdClass::class);

// 8. Invalid class: User::scoped(new stdClass) -> argument.type error
User::scoped(new stdClass);

// 9. Invalid scalar: User::scoped('invalid scope name') -> argument.type error
User::scoped('invalid scope name');

// 10. Invalid scalar: User::scoped(123) -> argument.type error
User::scoped(123);

// 11. No arguments: User::scoped() -> arguments.count error
User::scoped();
