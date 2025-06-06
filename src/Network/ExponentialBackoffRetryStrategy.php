<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use OpenFGA\Exceptions\NetworkException;
use Override;
use Psr\Http\Client\{NetworkExceptionInterface, RequestExceptionInterface};
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

use function is_float;
use function is_int;
use function min;
use function random_int;
use function usleep;

/**
 * Exponential backoff retry strategy implementation.
 *
 * This strategy implements exponential backoff with jitter for retrying
 * failed operations. It increases the delay between retries exponentially
 * to reduce load on the server during failure scenarios, while adding
 * random jitter to prevent thundering herd problems.
 */
final readonly class ExponentialBackoffRetryStrategy implements RetryStrategyInterface
{
    private const float DEFAULT_BACKOFF_FACTOR = 2.0;

    private const int DEFAULT_INITIAL_DELAY_MS = 100;

    private const float DEFAULT_JITTER_FACTOR = 0.1;

    private const int DEFAULT_MAX_DELAY_MS = 5000;

    /**
     * Default configuration values.
     */
    private const int DEFAULT_MAX_RETRIES = 3;

    /**
     * Create a new exponential backoff retry strategy.
     *
     * @param int   $maxRetries     Maximum number of retry attempts
     * @param int   $initialDelayMs Initial delay in milliseconds
     * @param int   $maxDelayMs     Maximum delay in milliseconds
     * @param float $backoffFactor  Multiplication factor for each retry
     * @param float $jitterFactor   Random jitter factor (0.0 to 1.0)
     */
    public function __construct(
        private int $maxRetries = self::DEFAULT_MAX_RETRIES,
        private int $initialDelayMs = self::DEFAULT_INITIAL_DELAY_MS,
        private int $maxDelayMs = self::DEFAULT_MAX_DELAY_MS,
        private float $backoffFactor = self::DEFAULT_BACKOFF_FACTOR,
        private float $jitterFactor = self::DEFAULT_JITTER_FACTOR,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function execute(callable $operation, array $config = []): mixed
    {
        $maxRetries = $this->getConfigInt($config, 'max_retries', $this->maxRetries);
        $lastException = null;

        for ($attempt = 1; $attempt <= $maxRetries + 1; ++$attempt) {
            try {
                return $operation();
            } catch (Throwable $e) {
                $lastException = $e;

                // Don't retry if we've exhausted attempts or exception is not retryable
                if ($attempt > $maxRetries || ! $this->isRetryable($e)) {
                    throw $e;
                }

                // Wait before retrying
                $delay = $this->getRetryDelay($attempt, $config);

                if (0 < $delay) {
                    usleep($delay * 1000); // Convert milliseconds to microseconds
                }
            }
        }

        // This should never be reached, but just in case
        throw $lastException ?? new RuntimeException('Retry strategy failed without exception');
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getRetryDelay(int $attempt, array $config = []): int
    {
        $initialDelay = $this->getConfigInt($config, 'initial_delay_ms', $this->initialDelayMs);
        $maxDelay = $this->getConfigInt($config, 'max_delay_ms', $this->maxDelayMs);
        $backoffFactor = $this->getConfigFloat($config, 'backoff_factor', $this->backoffFactor);
        $jitterFactor = $this->getConfigFloat($config, 'jitter_factor', $this->jitterFactor);

        // Calculate exponential delay
        $delay = (int) ((float) $initialDelay * $backoffFactor ** (float) ($attempt - 1));

        // Cap at maximum delay
        $delay = min($delay, $maxDelay);

        // Add jitter to prevent thundering herd
        if (0 < $jitterFactor && 0 < $delay) {
            $jitterRange = (int) ((float) $delay * $jitterFactor);
            $jitter = random_int(-$jitterRange, $jitterRange);
            $delay = max(0, $delay + $jitter);
        }

        return $delay;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function isRetryable(Throwable $exception): bool
    {
        // Check for PSR network exceptions
        if ($exception instanceof NetworkExceptionInterface
            || $exception instanceof RequestExceptionInterface) {
            return true;
        }

        // Check for specific HTTP status codes if available
        if ($exception instanceof NetworkException) {
            $response = $exception->response();

            if ($response instanceof ResponseInterface) {
                $statusCode = $response->getStatusCode();

                // Retry on specific status codes
                return match ($statusCode) {
                    408, // Request Timeout
                    429, // Too Many Requests
                    502, // Bad Gateway
                    503, // Service Unavailable
                    504  // Gateway Timeout
                        => true,
                    default => false,
                };
            }

            // If no response, consider it retryable (network failure)
            return true;
        }

        return false;
    }

    /**
     * Get a float value from config with fallback.
     *
     * @param array<string, mixed> $config
     * @param string               $key
     * @param float                $default
     */
    private function getConfigFloat(array $config, string $key, float $default): float
    {
        /** @var mixed $value */
        $value = $config[$key] ?? null;

        return is_float($value) ? $value : $default;
    }

    /**
     * Get an integer value from config with fallback.
     *
     * @param array<string, mixed> $config
     * @param string               $key
     * @param int                  $default
     */
    private function getConfigInt(array $config, string $key, int $default): int
    {
        /** @var mixed $value */
        $value = $config[$key] ?? null;

        return is_int($value) ? $value : $default;
    }
}
