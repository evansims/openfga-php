# UsersetUser


## Namespace
`OpenFGA\Models`

## Implements
* [UsersetUserInterface](Models/UsersetUserInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;UsersetUser&#039;` |  |


## Methods
### getId


```php
public function getId(): string
```

Get the object identifier in the userset reference. This represents the specific object instance that the userset refers to. For example, in &quot;group:eng#member&quot;, this would return &quot;eng&quot;.


#### Returns
string
 The object identifier

### getRelation


```php
public function getRelation(): string
```

Get the relation name in the userset reference. This represents the specific relation on the referenced object that defines the userset. For example, in &quot;group:eng#member&quot;, this would return &quot;member&quot;.


#### Returns
string
 The relation name

### getType


```php
public function getType(): string
```

Get the object type in the userset reference. This represents the type of object that the userset refers to. For example, in &quot;group:eng#member&quot;, this would return &quot;group&quot;.


#### Returns
string
 The object type

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\UsersetUserInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

