<?php

declare(strict_types=1);

namespace Mpyw\LaravelLocalClassScope\PHPStan\Reflection;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Override;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\Type\ObjectType;

final class ScopedMacroExtension implements MethodsClassReflectionExtension
{
    #[Override]
    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        if ($methodName !== 'scoped') {
            return false;
        }

        return $classReflection->is(Model::class) || $classReflection->is(Builder::class);
    }

    #[Override]
    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        if ($classReflection->is(Model::class)) {
            // Model::scoped()
            $modelType = new ObjectType($classReflection->getName());
            $isStatic  = true;
        } else {
            // Builder::scoped()
            $templateMap = $classReflection->getActiveTemplateTypeMap();
            $modelType   = $templateMap->getType('TModel');

            if ($modelType === null) {
                $modelType = new ObjectType(Model::class);
            }

            $isStatic = false;
        }

        $builderType = new GenericObjectType(
            Builder::class,
            [$modelType],
        );

        return new ScopedMethodReflection(
            $classReflection,
            $builderType,
            $isStatic,
        );
    }
}
