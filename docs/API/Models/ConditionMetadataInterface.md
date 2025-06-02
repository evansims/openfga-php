# ConditionMetadataInterface

Defines metadata information for conditions in OpenFGA authorization models. ConditionMetadata provides organizational and debugging information about conditions, including the module where they&#039;re defined and source file information. This helps with model analysis, debugging, and development tooling when working with complex authorization conditions. Use this interface when building tools that need to inspect or manipulate condition metadata in authorization models.

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/ConditionMetadataInterface.php)

## Implements

* [`ModelInterface`](ModelInterface.md)

* `JsonSerializable`

## Related Classes

* [ConditionMetadata](Models/ConditionMetadata.md) (implementation)

## Methods

### List Operations

#### getModule

```php
public function getModule(): string

```

Get the module name where the condition is defined. This provides organizational information about which module or namespace contains the condition definition, helping with debugging and understanding the model structure.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ConditionMetadataInterface.php#L31)

#### Returns

`string` — The module name containing the condition

#### getSourceInfo

```php
public function getSourceInfo(): SourceInfoInterface

```

Get source file information for debugging and tooling. This provides information about the source file where the condition was originally defined, which is useful for development tools, debugging, and error reporting.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ConditionMetadataInterface.php#L42)

#### Returns

[`SourceInfoInterface`](SourceInfoInterface.md) — The source file information

### Other

#### jsonSerialize

```php
public function jsonSerialize(): array

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ConditionMetadataInterface.php#L48)

#### Returns

`array`
