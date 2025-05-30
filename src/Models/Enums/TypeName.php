<?php

declare(strict_types=1);

namespace OpenFGA\Models\Enums;

/**
 * Data types supported in OpenFGA condition parameters.
 *
 * This enum defines the available data types that can be used for parameters
 * in OpenFGA authorization model conditions. These types enable type-safe
 * evaluation of conditional logic within authorization rules.
 *
 * @see https://openfga.dev/docs/modeling/conditions OpenFGA Conditions Documentation
 */
enum TypeName: string
{
    /**
     * Any type - accepts values of any supported data type.
     *
     * This type provides maximum flexibility by accepting any value,
     * useful for generic parameters or when the exact type is determined at runtime.
     */
    case ANY = 'TYPE_NAME_ANY';

    /**
     * Boolean type for true/false values.
     *
     * Used for parameters that represent binary states or flags
     * in authorization conditions.
     */
    case BOOL = 'TYPE_NAME_BOOL';

    /**
     * Double-precision floating-point number type.
     *
     * Used for parameters that require decimal precision,
     * such as monetary amounts or scientific calculations.
     */
    case DOUBLE = 'TYPE_NAME_DOUBLE';

    /**
     * Duration type for time spans.
     *
     * Used for parameters representing periods of time,
     * such as session timeouts or validity periods.
     */
    case DURATION = 'TYPE_NAME_DURATION';

    /**
     * Signed integer type for whole numbers.
     *
     * Used for parameters that represent counts, IDs,
     * or other whole number values that can be negative.
     */
    case INT = 'TYPE_NAME_INT';

    /**
     * IP address type for network addresses.
     *
     * Used for parameters representing IPv4 or IPv6 addresses
     * in network-based authorization conditions.
     */
    case IPADDRESS = 'TYPE_NAME_IPADDRESS';

    /**
     * List type for ordered collections of values.
     *
     * Used for parameters that contain multiple values
     * of the same or different types in a specific order.
     */
    case LIST = 'TYPE_NAME_LIST';

    /**
     * Map type for key-value collections.
     *
     * Used for parameters that represent associative arrays
     * or dictionary-like structures with named properties.
     */
    case MAP = 'TYPE_NAME_MAP';

    /**
     * String type for textual data.
     *
     * Used for parameters containing text values such as
     * names, descriptions, or other string-based identifiers.
     */
    case STRING = 'TYPE_NAME_STRING';

    /**
     * Timestamp type for specific points in time.
     *
     * Used for parameters representing exact moments,
     * such as creation dates or expiration times.
     */
    case TIMESTAMP = 'TYPE_NAME_TIMESTAMP';

    /**
     * Unsigned integer type for non-negative whole numbers.
     *
     * Used for parameters that represent counts, sizes,
     * or other whole number values that cannot be negative.
     */
    case UINT = 'TYPE_NAME_UINT';

    /**
     * Unspecified type - type is not explicitly defined.
     *
     * Used when the parameter type is determined by context
     * or when type checking is deferred to runtime.
     */
    case UNSPECIFIED = 'TYPE_NAME_UNSPECIFIED';

    /**
     * Get the corresponding PHP type for this OpenFGA type.
     *
     * Returns the equivalent PHP type name that would be used
     * for values of this type in PHP code.
     *
     * @return string The PHP type name
     */
    public function getPhpType(): string
    {
        return match ($this) {
            self::BOOL => 'bool',
            self::INT => 'int',
            self::UINT => 'int',
            self::DOUBLE => 'float',
            self::STRING => 'string',
            self::LIST => 'array',
            self::MAP => 'array',
            self::DURATION => 'string',
            self::TIMESTAMP => 'string',
            self::IPADDRESS => 'string',
            self::ANY => 'mixed',
            self::UNSPECIFIED => 'mixed',
        };
    }

    /**
     * Check if this type represents a collection of values.
     *
     * Useful for determining if iteration or collection-specific
     * operations can be performed on parameters of this type.
     *
     * @return bool True if the type is a collection, false otherwise
     */
    public function isCollection(): bool
    {
        return match ($this) {
            self::LIST, self::MAP => true,
            self::ANY, self::BOOL, self::DOUBLE, self::DURATION,
            self::INT, self::IPADDRESS, self::STRING, self::TIMESTAMP,
            self::UINT, self::UNSPECIFIED => false,
        };
    }

    /**
     * Check if this type accepts flexible or dynamic values.
     *
     * Useful for determining if runtime type checking is needed
     * or if strict type validation can be bypassed.
     *
     * @return bool True if the type is flexible, false otherwise
     */
    public function isFlexible(): bool
    {
        return match ($this) {
            self::ANY, self::UNSPECIFIED => true,
            self::BOOL, self::DOUBLE, self::DURATION, self::INT,
            self::IPADDRESS, self::LIST, self::MAP, self::STRING,
            self::TIMESTAMP, self::UINT => false,
        };
    }

    /**
     * Check if this type represents a numeric value.
     *
     * Useful for validation and type checking in condition parameter
     * processing where numeric operations are involved.
     *
     * @return bool True if the type is numeric, false otherwise
     */
    public function isNumeric(): bool
    {
        return match ($this) {
            self::INT, self::UINT, self::DOUBLE => true,
            self::ANY, self::BOOL, self::DURATION, self::IPADDRESS,
            self::LIST, self::MAP, self::STRING, self::TIMESTAMP,
            self::UNSPECIFIED => false,
        };
    }

    /**
     * Check if this type represents a temporal value.
     *
     * Useful for determining if time-based operations can be
     * performed on parameters of this type.
     *
     * @return bool True if the type is temporal, false otherwise
     */
    public function isTemporal(): bool
    {
        return match ($this) {
            self::DURATION, self::TIMESTAMP => true,
            self::ANY, self::BOOL, self::DOUBLE, self::INT,
            self::IPADDRESS, self::LIST, self::MAP, self::STRING,
            self::UINT, self::UNSPECIFIED => false,
        };
    }
}
