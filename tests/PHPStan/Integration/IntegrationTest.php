<?php

declare(strict_types=1);

namespace Tests\Integration;

use Override;
use PHPStan\Analyser\Analyser;
use PHPStan\Analyser\Error;
use PHPStan\File\FileHelper;
use PHPStan\Testing\PHPStanTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Throwable;

final class IntegrationTest extends PHPStanTestCase
{
    /**
     * @return iterable<array{0: string, 1?: array<int, string[]>}>
     */
    public static function dataIntegrationTests(): iterable
    {
        self::getContainer();

        yield [
            __DIR__.'/data/scoped-usage.php',
            [
                11 => [],
                14 => [],
                18 => [],
                22 => [],
                25 => [],
                28 => [],
                31 => ['Parameter #1 $scope of static method App\Models\User::scoped() expects class-string<Illuminate\Database\Eloquent\Scope>|Illuminate\Database\Eloquent\Scope, string given.'],
                34 => ['Parameter #1 $scope of static method App\Models\User::scoped() expects class-string<Illuminate\Database\Eloquent\Scope>|Illuminate\Database\Eloquent\Scope, stdClass given.'],
                37 => ['Parameter #1 $scope of static method App\Models\User::scoped() expects class-string<Illuminate\Database\Eloquent\Scope>|Illuminate\Database\Eloquent\Scope, string given.'],
                40 => ['Parameter #1 $scope of static method App\Models\User::scoped() expects class-string<Illuminate\Database\Eloquent\Scope>|Illuminate\Database\Eloquent\Scope, int given.'],
                43 => ['Static method App\Models\User::scoped() invoked with 0 parameters, at least 1 required.'],
            ],
        ];
    }

    /**
     * @param  ?array<int, string[]>  $expectedErrors
     * @param  string[]  $additionalFiles
     *
     * @throws Throwable
     */
    #[DataProvider('dataIntegrationTests')]
    public function test_integration(string $file, ?array $expectedErrors = null, array $additionalFiles = []): void
    {
        $errors = $this->runAnalyse($file, additionalFiles: $additionalFiles);

        if ($expectedErrors === null) {
            $this->assertNoErrors($errors);
        } else {
            if (count($expectedErrors) > 0) {
                $this->assertNotEmpty($errors);
            }

            $this->assertSameErrorMessages($file, $expectedErrors, $errors);
        }
    }

    /**
     * @see https://github.com/phpstan/phpstan-src/blob/c9772621c0bd6eab7e02fdaa03714bea239b372d/tests/PHPStan/Analyser/AnalyserIntegrationTest.php#L604-L622
     * @see https://github.com/phpstan/phpstan/discussions/6888#discussioncomment-2423613
     *
     * @param  ?string[]  $allAnalysedFiles
     * @param  string[]  $additionalFiles
     * @return Error[]
     *
     * @throws Throwable
     */
    private function runAnalyse(string $file, ?array $allAnalysedFiles = null, array $additionalFiles = []): array
    {
        $files = array_map(
            fn (string $file): string => $this->getFileHelper()->normalizePath($file),
            [$file, ...$additionalFiles],
        );

        /** @var Analyser $analyser */
        // @phpstan-ignore phpstanApi.classConstant
        $analyser = self::getContainer()->getByType(Analyser::class);

        /** @var FileHelper $fileHelper */
        $fileHelper = self::getContainer()->getByType(FileHelper::class);

        // @phpstan-ignore phpstanApi.method, phpstanApi.method
        $errors = $analyser->analyse($files, null, null, true, $allAnalysedFiles)->getErrors();

        foreach ($errors as $error) {
            $this->assertSame($fileHelper->normalizePath($file), $error->getFilePath());
        }

        return $errors;
    }

    /**
     * @param  array<int, string[]>  $expectedErrors
     * @param  Error[]  $errors
     */
    private function assertSameErrorMessages(string $file, array $expectedErrors, array $errors): void
    {
        foreach ($errors as $error) {
            $errorLine = $error->getLine() ?? 0;

            $this->assertArrayHasKey(
                $errorLine,
                $expectedErrors,
                sprintf('File %s has unexpected error "%s" at line %d.', $file, $error->getMessage(), $errorLine),
            );
            $this->assertContains(
                $error->getMessage(),
                $expectedErrors[$errorLine],
                sprintf("File %s has unexpected error \"%s\" at line %d.\n\nExpected \"%s\"", $file, $error->getMessage(), $errorLine, implode("\n\t", $expectedErrors[$errorLine])),
            );
        }
    }

    #[Override]
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__.'/../../../phpstan.neon.dist',
        ];
    }
}
