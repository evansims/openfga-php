<?php

declare(strict_types=1);

namespace OpenFGA\Models\Enums;

use OpenFGA\{Messages, Translation\Translator};

/**
 * Consistency levels for OpenFGA authorization queries.
 *
 * This enum defines the available consistency levels that control the trade-off
 * between data consistency and query performance in OpenFGA operations. Different
 * consistency levels affect how fresh the data needs to be when processing
 * authorization checks.
 *
 * @see https://openfga.dev/docs/interacting/consistency OpenFGA Consistency Documentation
 */
enum Consistency: string
{
    /**
     * Prioritize data consistency over query performance.
     *
     * This option ensures that authorization checks are performed against
     * the most up-to-date data, potentially at the cost of increased latency.
     * Use when accuracy is more important than speed.
     */
    case HIGHER_CONSISTENCY = 'HIGHER_CONSISTENCY';

    /**
     * Prioritize query performance over data consistency.
     *
     * This option allows for faster authorization checks by potentially
     * using slightly stale data. Use when speed is more important than
     * having the absolute latest data.
     */
    case MINIMIZE_LATENCY = 'MINIMIZE_LATENCY';

    /**
     * Use the default consistency level determined by the OpenFGA server.
     *
     * This option delegates the consistency decision to the server's
     * configuration, which may change based on deployment settings.
     */
    case UNSPECIFIED = 'UNSPECIFIED';

    /**
     * Get a user-friendly description of this consistency level.
     *
     * Provides a descriptive explanation of what this consistency level means
     * for query behavior and performance characteristics.
     *
     * @return string A descriptive explanation of the consistency level
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::HIGHER_CONSISTENCY => Translator::trans(Messages::CONSISTENCY_HIGHER_CONSISTENCY_DESCRIPTION),
            self::MINIMIZE_LATENCY => Translator::trans(Messages::CONSISTENCY_MINIMIZE_LATENCY_DESCRIPTION),
            self::UNSPECIFIED => Translator::trans(Messages::CONSISTENCY_UNSPECIFIED_DESCRIPTION),
        };
    }

    /**
     * Check if this consistency level prioritizes data freshness.
     *
     * Useful for determining if a query will potentially have higher latency
     * in exchange for more up-to-date data.
     *
     * @return bool True if consistency is prioritized over performance, false otherwise
     */
    public function prioritizesConsistency(): bool
    {
        return match ($this) {
            self::HIGHER_CONSISTENCY => true,
            self::MINIMIZE_LATENCY, self::UNSPECIFIED => false,
        };
    }

    /**
     * Check if this consistency level prioritizes query performance.
     *
     * Useful for determining if a query will potentially use stale data
     * in exchange for better performance.
     *
     * @return bool True if performance is prioritized over consistency, false otherwise
     */
    public function prioritizesPerformance(): bool
    {
        return match ($this) {
            self::MINIMIZE_LATENCY => true,
            self::HIGHER_CONSISTENCY, self::UNSPECIFIED => false,
        };
    }
}
