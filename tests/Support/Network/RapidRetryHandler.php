<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Support\Network;

use OpenFGA\Network\AbstractRetryHandler;

/**
 * Custom RetryHandler for testing that uses shorter delays.
 */
final readonly class RapidRetryHandler extends AbstractRetryHandler
{
    private const int TEST_BASE_DELAY_MS = 10;         // Reduced from 100ms

    private const int TEST_FAST_RETRY_DELAY_MS = 5;     // Reduced from 50ms

    private const int TEST_MAINTENANCE_DELAY_MS = 100; // Reduced from 5000ms

    protected function getBaseDelayMs(): int
    {
        return self::TEST_BASE_DELAY_MS;
    }

    protected function getFastRetryDelayMs(): int
    {
        return self::TEST_FAST_RETRY_DELAY_MS;
    }

    protected function getMaintenanceDelayMs(): int
    {
        return self::TEST_MAINTENANCE_DELAY_MS;
    }

    protected function sleep(int $milliseconds): void
    {
        // Use reduced delays for testing to speed up test execution
        $factor = 0.02; // 2% of original time
        usleep((int) ($milliseconds * $factor * 1000));
    }
}
