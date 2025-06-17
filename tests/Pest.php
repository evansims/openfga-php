<?php

declare(strict_types=1);

use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenFGA\{Language, Messages};
use OpenFGA\Translation\Translator;
use PHPUnit\Framework\TestCase;

define('OPENFGA_TESTS_DIR', __DIR__);

require_once implode(DIRECTORY_SEPARATOR, [OPENFGA_TESTS_DIR, '..', 'vendor', 'autoload.php']);

require_once implode(DIRECTORY_SEPARATOR, [OPENFGA_TESTS_DIR, 'bootstrap.php']);

pest()->extend(TestCase::class)->in(__DIR__);

// Global beforeEach to reset translator state
beforeEach(function (): void {
    // Reset translator to ensure consistent state for tests
    Translator::reset();
});

// Setup integration test factories for all tests in Integration directory
uses()->beforeEach(function (): void {
    setupIntegrationTestFactories();
})->in('Integration');

/**
 * Helper function to get translated messages in tests.
 *
 * @param  Messages             $message    The message enum case
 * @param  array<string, mixed> $parameters Parameters to substitute in the message
 * @param  Language|null        $language   Optional language override
 * @return string               The translated message
 */
function trans(Messages $message, array $parameters = [], ?Language $language = null): string
{
    return Translator::trans($message, $parameters, $language);
}

/**
 * Helper function to get the OpenFGA API URL for tests.
 *
 * Uses the FGA_API_URL environment variable if set (assigned by docker-compose),
 * otherwise falls back to a local URL that works for local OpenFGA instances.
 *
 * @return string The OpenFGA API URL
 */
function getOpenFgaUrl(): string
{
    return getenv('FGA_API_URL') ?: 'http://127.0.0.1:8080';
}

/**
 * Helper function to get the OpenTelemetry Collector URL for tests.
 *
 * Uses the OTEL_COLLECTOR_URL environment variable if set (assigned by docker-compose),
 * otherwise falls back to a local URL that works for local OpenTelemetry Collector instances.
 *
 * @return string The OpenTelemetry Collector metrics URL
 */
function getOtelCollectorUrl(): string
{
    return getenv('OTEL_COLLECTOR_URL') ?: 'http://127.0.0.1:8889';
}

/**
 * Setup integration test PSR factories globally.
 *
 * This function defines constants that the RequestManager will check for
 * before falling back to auto-discovery. This ensures consistent PSR
 * implementations across all integration tests.
 */
function setupIntegrationTestFactories(): void
{
    if (! defined('OPENFGA_TEST_HTTP_CLIENT')) {
        $factory = new Psr17Factory;
        define('OPENFGA_TEST_HTTP_CLIENT', new FileGetContents($factory));
        define('OPENFGA_TEST_HTTP_REQUEST_FACTORY', $factory);
        define('OPENFGA_TEST_HTTP_RESPONSE_FACTORY', $factory);
        define('OPENFGA_TEST_HTTP_STREAM_FACTORY', $factory);
    }
}
