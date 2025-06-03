<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use Override;

use function usleep;

/**
 * Concrete implementation of the retry handler using standard sleep delays.
 *
 * This final class provides the default implementation of the retry handler
 * that uses actual sleep delays for production use. For testing or custom
 * delay implementations, extend AbstractRetryHandler instead.
 */
final readonly class RetryHandler extends AbstractRetryHandler
{
    /**
     * @inheritDoc
     */
    #[Override]
    protected function sleep(int $delayMs): void
    {
        if (0 < $delayMs) {
            usleep($delayMs * 1000); // Convert to microseconds
        }
    }
}
