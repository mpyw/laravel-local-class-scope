<?php

declare(strict_types=1);

namespace Mpyw\LaravelLocalClassScope\Tests\PHPStan\Reflection;

use Override;
use PHPStan\Testing\TypeInferenceTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class ScopedMacroExtensionTest extends TypeInferenceTestCase
{
    /**
     * @return iterable<mixed>
     */
    public static function dataFileAsserts(): iterable
    {
        yield from self::gatherAssertTypes(__DIR__.'/data/scoped-macro-inference.php');
    }

    #[DataProvider('dataFileAsserts')]
    public function test_file_asserts(
        string $assertType,
        string $file,
        mixed ...$args,
    ): void {
        $this->assertFileAsserts($assertType, $file, ...$args);
    }

    #[Override]
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__.'/../../../extension.neon',
        ];
    }
}
