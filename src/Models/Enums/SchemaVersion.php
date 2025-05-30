<?php

declare(strict_types=1);

namespace OpenFGA\Models\Enums;

/**
 * OpenFGA authorization model schema versions.
 *
 * This enum defines the supported schema versions for authorization models in OpenFGA,
 * ensuring compatibility between client libraries and the OpenFGA service. Each schema
 * version represents a specific format and feature set for authorization models, with
 * newer versions introducing enhanced capabilities while maintaining backward compatibility
 * wherever possible.
 *
 * Schema versioning enables:
 * - Gradual migration between OpenFGA versions
 * - Feature availability validation
 * - Compatibility checking between clients and servers
 * - Forward and backward compatibility planning
 *
 * When creating authorization models, choose the appropriate schema version based on
 * the features you need and the OpenFGA service version you're targeting. Newer
 * schema versions provide access to the latest OpenFGA capabilities but may require
 * minimum service versions.
 *
 * @see https://openfga.dev/docs/modeling/getting-started OpenFGA Authorization Models
 * @see https://openfga.dev/docs/concepts#what-is-an-authorization-model Model Concepts
 */
enum SchemaVersion: string
{
    /**
     * Schema version 1.0 - Legacy authorization model format.
     *
     * This foundational schema version provides core relationship modeling capabilities
     * including basic type definitions, relations, and usersets. While still supported
     * for backward compatibility with existing deployments, this version has limitations
     * compared to newer schema versions.
     *
     * Features available in v1.0:
     * - Basic type definitions and relations
     * - Simple userset operations (direct, union, intersection)
     * - Fundamental relationship modeling
     *
     * Consider upgrading to v1.1 for access to advanced features like conditions
     * and enhanced relationship modeling capabilities.
     */
    case V1_0 = '1.0';

    /**
     * Schema version 1.1 - Current standard authorization model format.
     *
     * This is the recommended schema version for new OpenFGA deployments, providing
     * comprehensive authorization modeling capabilities including advanced features
     * that enable sophisticated access control patterns. This version represents
     * the current state of the art in OpenFGA authorization modeling.
     *
     * Enhanced features in v1.1:
     * - Conditional relationships with runtime parameter evaluation
     * - Advanced type definition metadata and configuration
     * - Improved userset operations and relationship inheritance
     * - Enhanced debugging and introspection capabilities
     * - Full compatibility with all current OpenFGA service features
     *
     * Use this version for new projects and when migrating from v1.0 to access
     * the latest OpenFGA capabilities and performance improvements.
     */
    case V1_1 = '1.1';

    /**
     * Compare this schema version with another version.
     *
     * Returns negative, zero, or positive value if this version is respectively
     * less than, equal to, or greater than the compared version.
     *
     * @param  SchemaVersion $other The version to compare against
     * @return int           Comparison result (-1, 0, or 1)
     *
     * @psalm-return -1|0|1
     */
    public function compareTo(SchemaVersion $other): int
    {
        return $this->getNumericVersion() <=> $other->getNumericVersion();
    }

    /**
     * Get the numeric version as a float for comparison operations.
     *
     * Useful for version comparison logic and feature detection.
     *
     * @return float The numeric representation of the schema version
     */
    public function getNumericVersion(): float
    {
        return (float) $this->value;
    }

    /**
     * Check if this is the latest schema version.
     *
     * Useful for determining if an authorization model is using the most
     * current feature set and capabilities.
     *
     * @return bool True if this is the latest schema version, false otherwise
     */
    public function isLatest(): bool
    {
        return self::V1_1 === $this;
    }

    /**
     * Check if this is a legacy schema version.
     *
     * Legacy versions are still supported but may lack features available
     * in newer versions. Consider upgrading for better functionality.
     *
     * @return bool True if this is a legacy version, false otherwise
     */
    public function isLegacy(): bool
    {
        return match ($this) {
            self::V1_0 => true,
            self::V1_1 => false,
        };
    }

    /**
     * Check if this schema version supports conditional relationships.
     *
     * Conditional relationships allow runtime parameter evaluation to determine
     * relationship validity, enabling context-aware authorization decisions.
     *
     * @return bool True if conditional relationships are supported, false otherwise
     */
    public function supportsConditions(): bool
    {
        return match ($this) {
            self::V1_1 => true,
            self::V1_0 => false,
        };
    }
}
