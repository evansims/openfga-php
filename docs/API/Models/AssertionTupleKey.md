# AssertionTupleKey

Represents a tuple key used for testing authorization model assertions. An AssertionTupleKey defines the specific user, relation, and object combination that should be tested in authorization model assertions. This is used to verify that your authorization model behaves correctly by testing whether specific authorization questions return the expected results. Use this when creating test cases to validate your authorization rules and ensure your permission model works as intended.

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/AssertionTupleKey.php)

## Implements
* [`AssertionTupleKeyInterface`](AssertionTupleKeyInterface.md)
* `JsonSerializable`
* [`ModelInterface`](ModelInterface.md)

## Related Classes
* [AssertionTupleKeyInterface](Models/AssertionTupleKeyInterface.md) (interface)

## Constants
| Name            | Value                 | Description |
| --------------- | --------------------- | ----------- |
| `OPENAPI_MODEL` | `'AssertionTupleKey'` |             |

## Methods

### List Operations
#### getObject

```php
public function getObject(): string
```

Get the object being tested in the assertion. This represents the resource or entity that the assertion is testing access to. In assertion testing, this is the object part of the tuple being validated against the authorization model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/AssertionTupleKey.php#L59)

#### Returns
`string` — The object identifier being tested
#### getRelation

```php
public function getRelation(): string
```

Get the relation being tested in the assertion. This represents the type of relationship or permission being tested in the assertion. It defines what kind of access is being validated between the user and object.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/AssertionTupleKey.php#L68)

#### Returns
`string` — The relation name being tested
#### getUser

```php
public function getUser(): string
```

Get the user being tested in the assertion. This represents the subject (user, group, role, etc.) whose access is being tested in the assertion. It&#039;s the entity for which we&#039;re validating whether they have the specified relation to the object.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/AssertionTupleKey.php#L77)

#### Returns
`string` — The user identifier being tested
### Model Management
#### schema

*<small>Implements Models\AssertionTupleKeyInterface</small>*

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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/AssertionTupleKey.php#L86)

#### Returns
`array`
