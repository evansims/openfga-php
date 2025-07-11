# TypeDefinitionInterface

Represents a type definition in an OpenFGA authorization model. Type definitions are the building blocks of authorization models that define the types of objects in your system and the relationships that can exist between them. Each type definition specifies: - The type name (for example &quot;document,&quot; &quot;user,&quot; &quot;organization&quot;) - The relations that objects of this type can have (for example &quot;viewer,&quot; &quot;editor,&quot; &quot;owner&quot;) - Optional metadata for additional context and configuration Type definitions form the schema that OpenFGA uses to understand your permission model and validate authorization queries.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`getMetadata()`](#getmetadata)
  - [`getRelations()`](#getrelations)
  - [`getType()`](#gettype)
  - [`jsonSerialize()`](#jsonserialize)

</details>

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/TypeDefinitionInterface.php)

## Implements

- [`ModelInterface`](ModelInterface.md)
- `JsonSerializable`

## Related Classes

- [TypeDefinition](Models/TypeDefinition.md) (implementation)

## Methods

### getMetadata

```php
public function getMetadata(): MetadataInterface|null

```

Get the metadata associated with this type definition. Metadata provides additional context, documentation, and configuration information for the type definition. This can include source file information, module details, and other development-time context.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TypeDefinitionInterface.php#L39)

#### Returns

[`MetadataInterface`](MetadataInterface.md) &#124; `null` — The metadata, or null if not specified

### getRelations

```php
public function getRelations(): TypeDefinitionRelationsInterface|null

```

Get the collection of relations defined for this type. Relations define the authorized relationships that can exist between objects of this type and other entities in the system.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TypeDefinitionInterface.php#L49)

#### Returns

[`TypeDefinitionRelationsInterface`](Models/Collections/TypeDefinitionRelationsInterface.md) &#124; `null` — The relations collection or null if no relations are defined

### getType

```php
public function getType(): string

```

Get the name of this type. The type name uniquely identifies this type definition within the authorization model. Common examples include &quot;user,&quot; &quot;document,&quot; &quot;folder,&quot; &quot;organization,&quot; etc.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TypeDefinitionInterface.php#L60)

#### Returns

`string` — The unique type name

### jsonSerialize

```php
public function jsonSerialize(): array<string, mixed>

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TypeDefinitionInterface.php#L66)

#### Returns

`array&lt;`string`, `mixed`&gt;`
