<?php

declare(strict_types=1);

use OpenFGA\Messages;
use OpenFGA\Translation\Translator;
use PHPUnit\Framework\TestCase;

define('OPENFGA_TESTS_DIR', __DIR__);

require_once implode(DIRECTORY_SEPARATOR, [OPENFGA_TESTS_DIR, '..', 'vendor', 'autoload.php']);

pest()->extend(TestCase::class)->in(__DIR__);

// Global beforeEach to reset translator state
beforeEach(function (): void {
    // Reset translator to ensure consistent state for tests
    Translator::reset();
});

/**
 * Helper function to get translated messages in tests.
 *
 * @param  Messages             $message    The message enum case
 * @param  array<string, mixed> $parameters Parameters to substitute in the message
 * @param  string|null          $locale     Optional locale override
 * @return string               The translated message
 */
function trans(Messages $message, array $parameters = [], ?string $locale = null): string
{
    return Translator::trans($message, $parameters, $locale);
}
