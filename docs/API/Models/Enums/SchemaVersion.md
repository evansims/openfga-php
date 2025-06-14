# SchemaVersion

OpenFGA authorization model schema versions. This enum defines the supported schema versions for authorization models in OpenFGA, ensuring compatibility between client libraries and the OpenFGA service. Each schema version represents a specific format and feature set for authorization models, with newer versions introducing enhanced capabilities while maintaining backward compatibility wherever possible. Schema versioning enables: - Gradual migration between OpenFGA versions - Feature availability validation - Compatibility checking between clients and servers - Forward and backward compatibility planning When creating authorization models, choose the appropriate schema version based on the features you need and the OpenFGA service version you&#039;re targeting. Newer schema versions provide access to the latest OpenFGA capabilities but may require minimum service versions.

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Constants](#constants)
- [Cases](#cases)
- [Methods](#methods)

- [List Operations](#list-operations)
  - [`getNumericVersion()`](#getnumericversion)
- [Utility](#utility)
  - [`isLatest()`](#islatest)
  - [`isLegacy()`](#islegacy)
- [Other](#other)
  - [`compareTo()`](#compareto)
  - [`supportsConditions()`](#supportsconditions)

## Namespace

`OpenFGA\Models\Enums`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/SchemaVersion.php)

## Implements

- `UnitEnum`
- `BackedEnum`

## Constants

| Name   | Value | Description                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  |
| ------ | ----- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `V1_0` | `1.0` | Schema version 1.0 - Legacy authorization model format. This foundational schema version provides core relationship modeling capabilities including basic type definitions, relations, and usersets. While still supported for backward compatibility with existing deployments, this version has limitations compared to newer schema versions. Features available in v1.0: - Basic type definitions and relations - Simple userset operations (direct, union, intersection) - Fundamental relationship modeling Consider upgrading to v1.1 for access to advanced features like conditions and enhanced relationship modeling capabilities.                                                                                                                                                                                                |
| `V1_1` | `1.1` | Schema version 1.1 - Current standard authorization model format. This is the recommended schema version for new OpenFGA deployments, providing comprehensive authorization modeling capabilities including advanced features that enable sophisticated access control patterns. This version represents the current state of the art in OpenFGA authorization modeling. Enhanced features in v1.1: - Conditional relationships with runtime parameter evaluation - Advanced type definition metadata and configuration - Improved userset operations and relationship inheritance - Enhanced debugging and introspection capabilities - Full compatibility with all current OpenFGA service features Use this version for new projects and when migrating from v1.0 to access the latest OpenFGA capabilities and performance improvements. |

## Cases

| Name   | Value | Description                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  |
| ------ | ----- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `V1_0` | `1.0` | Schema version 1.0 - Legacy authorization model format. This foundational schema version provides core relationship modeling capabilities including basic type definitions, relations, and usersets. While still supported for backward compatibility with existing deployments, this version has limitations compared to newer schema versions. Features available in v1.0: - Basic type definitions and relations - Simple userset operations (direct, union, intersection) - Fundamental relationship modeling Consider upgrading to v1.1 for access to advanced features like conditions and enhanced relationship modeling capabilities.                                                                                                                                                                                                |
| `V1_1` | `1.1` | Schema version 1.1 - Current standard authorization model format. This is the recommended schema version for new OpenFGA deployments, providing comprehensive authorization modeling capabilities including advanced features that enable sophisticated access control patterns. This version represents the current state of the art in OpenFGA authorization modeling. Enhanced features in v1.1: - Conditional relationships with runtime parameter evaluation - Advanced type definition metadata and configuration - Improved userset operations and relationship inheritance - Enhanced debugging and introspection capabilities - Full compatibility with all current OpenFGA service features Use this version for new projects and when migrating from v1.0 to access the latest OpenFGA capabilities and performance improvements. |

## Methods

### List Operations

#### getNumericVersion

```php
public function getNumericVersion(): float

```

Get the numeric version as a float for comparison operations. Useful for version comparison logic and feature detection.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/SchemaVersion.php#L93)

#### Returns

`float` — The numeric representation of the schema version

### Utility

#### isLatest

```php
public function isLatest(): bool

```

Check if this is the latest schema version. Useful for determining if an authorization model is using the most current feature set and capabilities.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/SchemaVersion.php#L106)

#### Returns

`bool` — True if this is the latest schema version, false otherwise

#### isLegacy

```php
public function isLegacy(): bool

```

Check if this is a legacy schema version. Legacy versions are still supported but may lack features available in newer versions. Consider upgrading for better functionality.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/SchemaVersion.php#L119)

#### Returns

`bool` — True if this is a legacy version, false otherwise

### Other

#### compareTo

```php
public function compareTo(SchemaVersion $other): int

```

Compare this schema version with another version. Returns negative, zero, or positive value if this version is respectively less than, equal to, or greater than the compared version.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/SchemaVersion.php#L81)

#### Parameters

| Name     | Type                                | Description                    |
| -------- | ----------------------------------- | ------------------------------ |
| `$other` | [`SchemaVersion`](SchemaVersion.md) | The version to compare against |

#### Returns

`int` — Comparison result (-1, 0, or 1)

#### supportsConditions

```php
public function supportsConditions(): bool

```

Check if this schema version supports conditional relationships. Conditional relationships allow runtime parameter evaluation to determine relationship validity, enabling context-aware authorization decisions.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/SchemaVersion.php#L135)

#### Returns

`bool` — True if conditional relationships are supported, false otherwise
