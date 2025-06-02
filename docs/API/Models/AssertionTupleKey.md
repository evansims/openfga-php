# AssertionTupleKey

Represents a tuple key used for testing authorization model assertions. An AssertionTupleKey defines the specific user, relation, and object combination that should be tested in authorization model assertions. This is used to verify that your authorization model behaves correctly by testing whether specific authorization questions return the expected results. Use this when creating test cases to validate your authorization rules and ensure your permission model works as intended.

## Namespace
`OpenFGA\Models`

## Implements
* [AssertionTupleKeyInterface](Models/AssertionTupleKeyInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;AssertionTupleKey&#039;` |  |


## Methods
### getObject


```php
public function getObject(): string
```

Get the object being tested in the assertion. This represents the resource or entity that the assertion is testing access to. In assertion testing, this is the object part of the tuple being validated against the authorization model.


#### Returns
string
 The object identifier being tested

### getRelation


```php
public function getRelation(): string
```

Get the relation being tested in the assertion. This represents the type of relationship or permission being tested in the assertion. It defines what kind of access is being validated between the user and object.


#### Returns
string
 The relation name being tested

### getUser


```php
public function getUser(): string
```

Get the user being tested in the assertion. This represents the subject (user, group, role, etc.) whose access is being tested in the assertion. It&#039;s the entity for which we&#039;re validating whether they have the specified relation to the object.


#### Returns
string
 The user identifier being tested

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\AssertionTupleKeyInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

