<?php

declare(strict_types=1);

namespace Semitexa\ThemeSky\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Source;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * The theme ships templates and a stylesheet, and nothing else that a PHP
 * test would ever touch. So these are the two ways it breaks: a template that
 * no longer parses, and an asset() it links that the package does not ship.
 */
final class ThemeSkyTemplatesTest extends TestCase
{
    private const ROOT = __DIR__ . '/../../src/Application';

    /** @return iterable<string, array{string}> */
    public static function templates(): iterable
    {
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(self::ROOT . '/View/templates', \FilesystemIterator::SKIP_DOTS));
        foreach ($files as $file) {
            if ($file instanceof \SplFileInfo && str_ends_with($file->getFilename(), '.twig')) {
                yield substr($file->getPathname(), strlen(self::ROOT) + 1) => [$file->getPathname()];
            }
        }
    }

    #[Test]
    #[DataProvider('templates')]
    public function the_template_parses(string $path): void
    {
        // The runtime's functions and filters are not under test here, only
        // the template's own syntax: any name it calls is accepted as a stub.
        $twig = new Environment(new ArrayLoader());
        $twig->registerUndefinedFunctionCallback(static fn (string $name): TwigFunction => new TwigFunction($name, static fn (): string => ''));
        $twig->registerUndefinedFilterCallback(static fn (string $name): TwigFilter => new TwigFilter($name, static fn (mixed $v): mixed => $v));

        $twig->parse($twig->tokenize(new Source(self::read($path), basename($path), $path)));
        $this->addToAssertionCount(1);
    }

    #[Test]
    #[DataProvider('templates')]
    public function every_theme_asset_it_links_is_shipped(string $path): void
    {
        $static = realpath(self::ROOT . '/Static');
        self::assertIsString($static, 'the theme ships a Static directory');

        // Every asset() call naming this theme must be read as a literal pair,
        // in either quote style and with any spacing; one this pattern cannot
        // read fails the test instead of passing unchecked.
        preg_match_all('/\basset\s*\(([^()]*)\)/', self::read($path), $calls);
        foreach ($calls[1] as $args) {
            if (!str_contains($args, 'theme-sky')) {
                continue;
            }
            self::assertSame(1, preg_match('/\A\s*([\'"])([^\'"]+)\1\s*,\s*([\'"])theme-sky\3\s*\z/', $args, $m), "{$path} links a theme asset this test cannot read: asset({$args})");
            $asset = $m[2];
            $resolved = realpath($static . '/' . $asset);
            self::assertTrue(
                $resolved !== false && str_starts_with($resolved, $static . DIRECTORY_SEPARATOR) && is_file($resolved),
                "{$path} links {$asset}, which is not a file inside Static",
            );
        }
    }

    private static function read(string $path): string
    {
        $text = file_get_contents($path);
        if ($text === false) {
            self::fail("{$path} cannot be read");
        }

        return $text;
    }
}
