# RelationMetadataInterface

Represents metadata associated with a relation in OpenFGA authorization models. Relation metadata provides additional context and constraints for relations defined in type definitions. This metadata helps with: - Type safety by defining which user types can be directly related - Development tooling by providing source file information - Model organization through module names - Validation and error reporting The metadata is particularly important for: - Ensuring that only appropriate user types can be assigned to relations - Providing helpful error messages when model validation fails - Supporting development tools that work with authorization models - Organizing complex models across multiple modules or files

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`getDirectlyRelatedUserTypes()`](#getdirectlyrelatedusertypes)
  - [`getModule()`](#getmodule)
  - [`getSourceInfo()`](#getsourceinfo)
  - [`jsonSerialize()`](#jsonserialize)

</details>

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationMetadataInterface.php)

## Implements

- [`ModelInterface`](ModelInterface.md)
- `JsonSerializable`

## Related Classes

- [RelationMetadata](Models/RelationMetadata.md) (implementation)

## Methods

### getDirectlyRelatedUserTypes

```php
public function getDirectlyRelatedUserTypes(): RelationReferencesInterface|null

```

Get the user types that can be directly related through this relation. This defines which types of users can have this relation to objects, providing type safety and helping with authorization model validation. For example, a &quot;member&quot; relation might allow &quot;user&quot; and &quot;group&quot; types.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationMetadataInterface.php#L41)

#### Returns

[`RelationReferencesInterface`](Models/Collections/RelationReferencesInterface.md) &#124; `null` — The directly related user types, or null if not specified

### getModule

```php
public function getModule(): string|null

```

Get the optional module name for organization. This provides organizational information about which module or namespace contains the relation definition, helping with model organization and debugging.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationMetadataInterface.php#L52)

#### Returns

`string` &#124; `null` — The module name, or null if not specified

### getSourceInfo

```php
public function getSourceInfo(): SourceInfoInterface|null

```

Get optional source file information for debugging and tooling. This provides information about the source file where the relation was originally defined, which is useful for development tools, debugging, and error reporting.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationMetadataInterface.php#L63)

#### Returns

[`SourceInfoInterface`](SourceInfoInterface.md) &#124; `null` — The source file information, or null if not available

### jsonSerialize

```php
public function jsonSerialize(): array

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationMetadataInterface.php#L69)

#### Returns

`array`
