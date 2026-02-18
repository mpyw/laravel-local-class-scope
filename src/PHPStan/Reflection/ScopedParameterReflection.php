<?php

declare(strict_types=1);

namespace Mpyw\LaravelLocalClassScope\PHPStan\Reflection;

use Override;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\PassedByReference;
use PHPStan\Type\Type;

final class ScopedParameterReflection implements ParameterReflection
{
    public function __construct(
        private readonly string $name,
        private readonly Type $type,
        private readonly bool $isOptional,
        private readonly bool $isVariadic,
    ) {}

    #[Override]
    public function getName(): string
    {
        return $this->name;
    }

    #[Override]
    public function isOptional(): bool
    {
        return $this->isOptional;
    }

    #[Override]
    public function getType(): Type
    {
        return $this->type;
    }

    #[Override]
    public function passedByReference(): PassedByReference
    {
        return PassedByReference::createNo();
    }

    #[Override]
    public function isVariadic(): bool
    {
        return $this->isVariadic;
    }

    #[Override]
    public function getDefaultValue(): ?Type
    {
        return null;
    }
}
