<?php

declare(strict_types=1);

namespace Semitexa\ThemeSky;

use Semitexa\Core\Attribute\Capability;

/**
 * What this package offers, for the capability catalog.
 *
 * The package ships no attributes of its own, so there is nothing for a
 * mechanism-level declaration to hang on — and without this the package is
 * invisible to anyone whose project has not installed it, which is precisely
 * the audience worth telling. The convention is one `Capabilities` class per
 * package: a definite place to look, and a definite place for a guard to check.
 *
 * Nothing reads this at runtime.
 */
#[Capability(
    id: 'theme.sky',
    summary: 'The Sky theme: a finished brand skin, base layout and header/footer partials built on the shared UI tokens.',
    useWhen: 'A project needs a coherent look immediately, and its brand can start from an existing palette rather than define one first.',
    avoidWhen: 'The brand is the product. Generate a skin instead of inheriting one that already looks like Semitexa.',
    replaces: [
        'a base layout and header/footer partials written from scratch in each project',
        'colours and spacing hard-coded into stylesheets where a token should have been read',
    ],
)]
final class Capabilities
{
}
