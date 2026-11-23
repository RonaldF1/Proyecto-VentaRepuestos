<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class LayerDependencyTest extends TestCase
{
    private const string SOURCE_PATH = __DIR__.'/../../src';

    /**
     * Namespaces each inner layer must never reference.
     *
     * @var array<string, list<string>>
     */
    private const array FORBIDDEN = [
        'Domain' => ['Illuminate\\', 'Laravel\\', '\\Application\\', '\\Infrastructure\\'],
        'Application' => ['Illuminate\\', 'Laravel\\', '\\Infrastructure\\'],
    ];

    /** Inner layers stay free of framework and outer-layer references. */
    #[DataProvider('innerLayerFiles')]
    public function test_inner_layers_do_not_depend_on_outer_layers(string $layer, string $path): void
    {
        $this->assertSame([], $this->forbiddenReferences($layer, $path), "Forbidden dependency in {$path}");
    }

    /**
     * Every PHP file that lives in a Domain or Application layer.
     *
     * @return iterable<string, array{string, string}>
     */
    public static function innerLayerFiles(): iterable
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(self::SOURCE_PATH));

        foreach ($files as $file) {
            $layer = $file instanceof SplFileInfo ? self::layerOf($file->getPathname()) : null;

            if ($layer !== null && $file->getExtension() === 'php') {
                yield substr($file->getPathname(), strlen(self::SOURCE_PATH) + 1) => [$layer, $file->getPathname()];
            }
        }
    }

    /** Resolve the inner layer a file belongs to, if any. */
    private static function layerOf(string $path): ?string
    {
        return array_find(
            array_keys(self::FORBIDDEN),
            fn (string $layer): bool => str_contains($path, DIRECTORY_SEPARATOR.$layer.DIRECTORY_SEPARATOR),
        );
    }

    /**
     * Imported or fully qualified names that break the dependency rule.
     *
     * @return list<string>
     */
    private function forbiddenReferences(string $layer, string $path): array
    {
        return array_values(array_filter(
            $this->referencesIn($path),
            fn (string $name): bool => array_any(
                self::FORBIDDEN[$layer],
                fn (string $needle): bool => str_contains("\\{$name}", $needle),
            ),
        ));
    }

    /**
     * Class names a file imports or writes fully qualified.
     *
     * @return list<string>
     */
    private function referencesIn(string $path): array
    {
        preg_match_all('/(?:^use\s+|\\\\)([A-Z][\w\\\\]+)/m', (string) file_get_contents($path), $matches);

        return $matches[1];
    }
}