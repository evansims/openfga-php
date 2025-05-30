# ObjectRelation


## Namespace
`OpenFGA\Models`

## Implements
* [ObjectRelationInterface](Models/ObjectRelationInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;ObjectRelation&#039;` |  |


## Methods
### getObject


```php
public function getObject(): ?string
```

Get the object identifier in an object-relation pair. The object represents the resource or entity being referenced, typically formatted as &quot;type:id&quot; where type describes the kind of resource.


#### Returns
?string

### getRelation


```php
public function getRelation(): ?string
```

Get the relation name that defines the type of relationship to the object. The relation describes what kind of permission or relationship exists. Common examples include &quot;owner&quot;, &quot;viewer&quot;, &quot;editor&quot;, &quot;member&quot;.


#### Returns
?string

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\ObjectRelationInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

