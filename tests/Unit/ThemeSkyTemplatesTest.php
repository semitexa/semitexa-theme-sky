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

        $twig->parse($twig->tokenize(new Source((string) file_get_contents($path), basename($path), $path)));
        $this->addToAssertionCount(1);
    }

    #[Test]
    #[DataProvider('templates')]
    public function every_theme_asset_it_links_is_shipped(string $path): void
    {
        preg_match_all("/asset\\('([^']+)',\\s*'theme-sky'\\)/", (string) file_get_contents($path), $m);
        foreach ($m[1] as $asset) {
            self::assertFileExists(self::ROOT . '/Static/' . $asset, "{$path} links {$asset}");
        }
        $this->addToAssertionCount(1);
    }
}
