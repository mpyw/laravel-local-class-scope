<?php

declare(strict_types=1);

namespace Mpyw\LaravelLocalClassScope\PHPStan\Reflection;

use Illuminate\Database\Eloquent\Scope;
use Override;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\MethodReflection;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Generic\GenericClassStringType;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;

final class ScopedMethodReflection implements MethodReflection
{
    public function __construct(
        private readonly ClassReflection $declaringClass,
        private readonly Type $returnType,
        private readonly bool $isStatic,
    ) {}

    #[Override]
    public function getName(): string
    {
        return 'scoped';
    }

    #[Override]
    public function getPrototype(): MethodReflection
    {
        return $this;
    }

    #[Override]
    public function getVariants(): array
    {
        return [
            new FunctionVariant(
                TemplateTypeMap::createEmpty(),
                null,
                [
                    new ScopedParameterReflection(
                        'scope',
                        new UnionType([
                            new ObjectType(Scope::class),
                            new GenericClassStringType(
                                new ObjectType(Scope::class),
                            ),
                        ]),
                        false,
                        false,
                    ),
                    new ScopedParameterReflection(
                        'parameters',
                        new MixedType,
                        true,
                        true,
                    ),
                ],
                true,
                $this->returnType,
            ),
        ];
    }

    #[Override]
    public function isDeprecated(): TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }

    #[Override]
    public function getDeprecatedDescription(): ?string
    {
        return null;
    }

    #[Override]
    public function isFinal(): TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }

    #[Override]
    public function isInternal(): TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }

    #[Override]
    public function getThrowType(): ?Type
    {
        return null;
    }

    #[Override]
    public function hasSideEffects(): TrinaryLogic
    {
        return TrinaryLogic::createYes();
    }

    #[Override]
    public function getDeclaringClass(): ClassReflection
    {
        return $this->declaringClass;
    }

    #[Override]
    public function isStatic(): bool
    {
        return $this->isStatic;
    }

    #[Override]
    public function isPrivate(): bool
    {
        return false;
    }

    #[Override]
    public function isPublic(): bool
    {
        return true;
    }

    #[Override]
    public function getDocComment(): ?string
    {
        return null;
    }
}
