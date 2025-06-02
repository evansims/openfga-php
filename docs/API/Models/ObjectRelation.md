# ObjectRelation

Represents a reference to a specific relation on an object. In authorization models, you often need to reference relationships between objects. An ObjectRelation identifies both the target object and the specific relation you&#039;re interested in, like &quot;the owner of document:budget&quot; or &quot;editors of folder:reports&quot;. This is commonly used in authorization rules where permissions depend on relationships with other objects in your system.

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/ObjectRelation.php)

## Implements
* [`ObjectRelationInterface`](ObjectRelationInterface.md)
* `JsonSerializable`
* [`ModelInterface`](ModelInterface.md)

## Related Classes
* [ObjectRelationInterface](Models/ObjectRelationInterface.md) (interface)

## Constants
| Name            | Value              | Description |
| --------------- | ------------------ | ----------- |
| `OPENAPI_MODEL` | `'ObjectRelation'` |             |

## Methods

### List Operations
#### getObject

```php
public function getObject(): ?string
```

Get the object identifier in an object-relation pair. The object represents the resource or entity being referenced, typically formatted as &quot;type:id&quot; where type describes the kind of resource.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ObjectRelation.php#L56)

#### Returns
`string` &#124; `null`
#### getRelation

```php
public function getRelation(): ?string
```

Get the relation name that defines the type of relationship to the object. The relation describes what kind of permission or relationship exists. Common examples include &quot;owner&quot;, &quot;viewer&quot;, &quot;editor&quot;, &quot;member&quot;.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ObjectRelation.php#L65)

#### Returns
`string` &#124; `null`
### Model Management
#### schema

*<small>Implements Models\ObjectRelationInterface</small>*

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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ObjectRelation.php#L74)

#### Returns
`array`
