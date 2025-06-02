# Metadata

Contains metadata information about type definitions in your authorization model. Metadata provides additional context about how your authorization types behave, including module information, relation constraints, and source details. This information helps with model validation, debugging, and understanding the structure of your authorization system. Use this when you need insights into the properties and constraints of your authorization model&#039;s type definitions.

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/Metadata.php)

## Implements

* [`MetadataInterface`](MetadataInterface.md)

* `JsonSerializable`

* [`ModelInterface`](ModelInterface.md)

## Related Classes

* [MetadataInterface](Models/MetadataInterface.md) (interface)

## Constants

| Name | Value | Description |

|------|-------|-------------|

| `OPENAPI_MODEL` | `'Metadata'` |  |

## Methods

### List Operations

#### getModule

```php
public function getModule(): ?string

```

Get the module name for this metadata. Modules provide a way to organize and namespace authorization model components, similar to packages in programming languages. This helps with model organization and prevents naming conflicts in large authorization systems.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Metadata.php#L62)

#### Returns

`string` &#124; `null` — The module name, or null if not specified

#### getRelations

```php
public function getRelations(): ?OpenFGA\Models\Collections\RelationMetadataCollection

```

Get the collection of relation metadata. Relation metadata provides additional configuration and context for specific relations within a type definition. This can include documentation, constraints, or other relation-specific settings that enhance the authorization model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Metadata.php#L71)

#### Returns

[`RelationMetadataCollection`](Models/Collections/RelationMetadataCollection.md) &#124; `null` — The relation metadata collection, or null if not specified

#### getSourceInfo

```php
public function getSourceInfo(): ?OpenFGA\Models\SourceInfoInterface

```

Get the source code information for this metadata. Source information provides debugging and development context by tracking where authorization model elements were defined. This is particularly useful for development tools and error reporting.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Metadata.php#L80)

#### Returns

[`SourceInfoInterface`](SourceInfoInterface.md) &#124; `null` — The source information, or null if not available

### Model Management

#### schema

*<small>Implements Models\MetadataInterface</small>*

```php
public function schema(): SchemaInterface

```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ModelInterface.php#L52)

#### Returns

`SchemaInterface` — The schema definition containing validation rules and property specifications for this model

### Other

#### jsonSerialize

```php
public function jsonSerialize(): array

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Metadata.php#L89)

#### Returns

`array`
