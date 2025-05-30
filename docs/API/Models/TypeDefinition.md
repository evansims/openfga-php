# TypeDefinition


## Namespace
`OpenFGA\Models`

## Implements
* [TypeDefinitionInterface](Models/TypeDefinitionInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;TypeDefinition&#039;` |  |


## Methods
### getMetadata


```php
public function getMetadata(): ?OpenFGA\Models\MetadataInterface
```

Get the metadata associated with this type definition. Metadata provides additional context, documentation, and configuration information for the type definition. This can include source file information, module details, and other development-time context.


#### Returns
?[MetadataInterface](Models/MetadataInterface.md)
 The metadata, or null if not specified

### getRelations


```php
public function getRelations(): ?OpenFGA\Models\Collections\TypeDefinitionRelationsInterface
```

Get the collection of relations defined for this type. Relations define the authorized relationships that can exist between objects of this type and other entities in the system.


#### Returns
?[TypeDefinitionRelationsInterface](Models/Collections/TypeDefinitionRelationsInterface.md)

### getType


```php
public function getType(): string
```

Get the name of this type. The type name uniquely identifies this type definition within the authorization model. Common examples include &quot;user&quot;, &quot;document&quot;, &quot;folder&quot;, &quot;organization&quot;, etc.


#### Returns
string
 The unique type name

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\TypeDefinitionInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

