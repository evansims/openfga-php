# MetadataInterface

Represents metadata associated with OpenFGA authorization model components. Metadata provides additional context and configuration information for authorization model elements. This includes module organization, relation-specific metadata, and source code information for debugging and development purposes. Metadata helps with model organization, documentation, and tooling support for complex authorization models.

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/MetadataInterface.php)

## Implements

* [`ModelInterface`](ModelInterface.md)

* `JsonSerializable`

## Related Classes

* [Metadata](Models/Metadata.md) (implementation)

## Methods

### List Operations

#### getModule

```php
public function getModule(): string|null

```

Get the module name for this metadata. Modules provide a way to organize and namespace authorization model components, similar to packages in programming languages. This helps with model organization and prevents naming conflicts in large authorization systems.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/MetadataInterface.php#L33)

#### Returns

`string` &#124; `null` — The module name, or null if not specified

#### getRelations

```php
public function getRelations(): RelationMetadataCollection|null

```

Get the collection of relation metadata. Relation metadata provides additional configuration and context for specific relations within a type definition. This can include documentation, constraints, or other relation-specific settings that enhance the authorization model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/MetadataInterface.php#L45)

#### Returns

[`RelationMetadataCollection`](Models/Collections/RelationMetadataCollection.md) &#124; `null` — The relation metadata collection, or null if not specified

#### getSourceInfo

```php
public function getSourceInfo(): SourceInfoInterface|null

```

Get the source code information for this metadata. Source information provides debugging and development context by tracking where authorization model elements were defined. This is particularly useful for development tools and error reporting.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/MetadataInterface.php#L56)

#### Returns

[`SourceInfoInterface`](SourceInfoInterface.md) &#124; `null` — The source information, or null if not available

### Other

#### jsonSerialize

```php
public function jsonSerialize(): array

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/MetadataInterface.php#L62)

#### Returns

`array`
