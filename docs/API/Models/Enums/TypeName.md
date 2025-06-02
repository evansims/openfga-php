# TypeName

Data types supported in OpenFGA condition parameters. This enum defines the available data types that can be used for parameters in OpenFGA authorization model conditions. These types enable type-safe evaluation of conditional logic within authorization rules.

## Namespace

`OpenFGA\Models\Enums`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/TypeName.php)

## Implements

* `UnitEnum`

* `BackedEnum`

## Constants

| Name | Value | Description |

|------|-------|-------------|

| `ANY` | `\OpenFGA\Models\Enums\TypeName::ANY` | Any type - accepts values of any supported data type. This type provides maximum flexibility by accepting any value, useful for generic parameters or when the exact type is determined at runtime. |

| `BOOL` | `\OpenFGA\Models\Enums\TypeName::BOOL` | Boolean type for true/false values. Used for parameters that represent binary states or flags in authorization conditions. |

| `DOUBLE` | `\OpenFGA\Models\Enums\TypeName::DOUBLE` | Double-precision floating-point number type. Used for parameters that require decimal precision, such as monetary amounts or scientific calculations. |

| `DURATION` | `\OpenFGA\Models\Enums\TypeName::DURATION` | Duration type for time spans. Used for parameters representing periods of time, such as session timeouts or validity periods. |

| `INT` | `\OpenFGA\Models\Enums\TypeName::INT` | Signed integer type for whole numbers. Used for parameters that represent counts, IDs, or other whole number values that can be negative. |

| `IPADDRESS` | `\OpenFGA\Models\Enums\TypeName::IPADDRESS` | IP address type for network addresses. Used for parameters representing IPv4 or IPv6 addresses in network-based authorization conditions. |

| `LIST` | `\OpenFGA\Models\Enums\TypeName::LIST` | List type for ordered collections of values. Used for parameters that contain multiple values of the same or different types in a specific order. |

| `MAP` | `\OpenFGA\Models\Enums\TypeName::MAP` | Map type for key-value collections. Used for parameters that represent associative arrays or dictionary-like structures with named properties. |

| `STRING` | `\OpenFGA\Models\Enums\TypeName::STRING` | String type for textual data. Used for parameters containing text values such as names, descriptions, or other string-based identifiers. |

| `TIMESTAMP` | `\OpenFGA\Models\Enums\TypeName::TIMESTAMP` | Timestamp type for specific points in time. Used for parameters representing exact moments, such as creation dates or expiration times. |

| `UINT` | `\OpenFGA\Models\Enums\TypeName::UINT` | Unsigned integer type for non-negative whole numbers. Used for parameters that represent counts, sizes, or other whole number values that cannot be negative. |

| `UNSPECIFIED` | `\OpenFGA\Models\Enums\TypeName::UNSPECIFIED` | Unspecified type - type is not explicitly defined. Used when the parameter type is determined by context or when type checking is deferred to runtime. |

## Cases

| Name | Value | Description |

|------|-------|-------------|

| `ANY` | `TYPE_NAME_ANY` | Any type - accepts values of any supported data type. This type provides maximum flexibility by accepting any value, useful for generic parameters or when the exact type is determined at runtime. |

| `BOOL` | `TYPE_NAME_BOOL` | Boolean type for true/false values. Used for parameters that represent binary states or flags in authorization conditions. |

| `DOUBLE` | `TYPE_NAME_DOUBLE` | Double-precision floating-point number type. Used for parameters that require decimal precision, such as monetary amounts or scientific calculations. |

| `DURATION` | `TYPE_NAME_DURATION` | Duration type for time spans. Used for parameters representing periods of time, such as session timeouts or validity periods. |

| `INT` | `TYPE_NAME_INT` | Signed integer type for whole numbers. Used for parameters that represent counts, IDs, or other whole number values that can be negative. |

| `IPADDRESS` | `TYPE_NAME_IPADDRESS` | IP address type for network addresses. Used for parameters representing IPv4 or IPv6 addresses in network-based authorization conditions. |

| `LIST` | `TYPE_NAME_LIST` | List type for ordered collections of values. Used for parameters that contain multiple values of the same or different types in a specific order. |

| `MAP` | `TYPE_NAME_MAP` | Map type for key-value collections. Used for parameters that represent associative arrays or dictionary-like structures with named properties. |

| `STRING` | `TYPE_NAME_STRING` | String type for textual data. Used for parameters containing text values such as names, descriptions, or other string-based identifiers. |

| `TIMESTAMP` | `TYPE_NAME_TIMESTAMP` | Timestamp type for specific points in time. Used for parameters representing exact moments, such as creation dates or expiration times. |

| `UINT` | `TYPE_NAME_UINT` | Unsigned integer type for non-negative whole numbers. Used for parameters that represent counts, sizes, or other whole number values that cannot be negative. |

| `UNSPECIFIED` | `TYPE_NAME_UNSPECIFIED` | Unspecified type - type is not explicitly defined. Used when the parameter type is determined by context or when type checking is deferred to runtime. |

## Methods

### List Operations

#### getPhpType

```php
public function getPhpType(): string

```

Get the corresponding PHP type for this OpenFGA type. Returns the equivalent PHP type name that would be used for values of this type in PHP code.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/TypeName.php#L122)

#### Returns

`string` — The PHP type name

### Utility

#### isCollection

```php
public function isCollection(): bool

```

Check if this type represents a collection of values. Useful for determining if iteration or collection-specific operations can be performed on parameters of this type.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/TypeName.php#L148)

#### Returns

`bool` — True if the type is a collection, false otherwise

#### isFlexible

```php
public function isFlexible(): bool

```

Check if this type accepts flexible or dynamic values. Useful for determining if runtime type checking is needed or if strict type validation can be bypassed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/TypeName.php#L166)

#### Returns

`bool` — True if the type is flexible, false otherwise

#### isNumeric

```php
public function isNumeric(): bool

```

Check if this type represents a numeric value. Useful for validation and type checking in condition parameter processing where numeric operations are involved.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/TypeName.php#L184)

#### Returns

`bool` — True if the type is numeric, false otherwise

#### isTemporal

```php
public function isTemporal(): bool

```

Check if this type represents a temporal value. Useful for determining if time-based operations can be performed on parameters of this type.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/TypeName.php#L202)

#### Returns

`bool` — True if the type is temporal, false otherwise
