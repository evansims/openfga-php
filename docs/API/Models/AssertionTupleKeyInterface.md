# AssertionTupleKeyInterface

Defines the contract for assertion tuple keys used in authorization model testing. An assertion tuple key specifies the user, relation, and object combination that should be tested in authorization model assertions. This is used to verify that your authorization model behaves correctly for specific scenarios. Use this when creating test cases to validate your authorization rules and ensure your permission model works as expected.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`getObject()`](#getobject)
  - [`getRelation()`](#getrelation)
  - [`getUser()`](#getuser)
  - [`jsonSerialize()`](#jsonserialize)

</details>

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/AssertionTupleKeyInterface.php)

## Implements

- [`ModelInterface`](ModelInterface.md)
- `JsonSerializable`

## Related Classes

- [AssertionTupleKey](Models/AssertionTupleKey.md) (implementation)

## Methods

### getObject

```php
public function getObject(): string

```

Get the object being tested in the assertion. This represents the resource or entity that the assertion is testing access to. In assertion testing, this is the object part of the tuple being validated against the authorization model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/AssertionTupleKeyInterface.php#L30)

#### Returns

`string` — The object identifier being tested

### getRelation

```php
public function getRelation(): string

```

Get the relation being tested in the assertion. This represents the type of relationship or permission being tested in the assertion. It defines what kind of access is being validated between the user and object.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/AssertionTupleKeyInterface.php#L41)

#### Returns

`string` — The relation name being tested

### getUser

```php
public function getUser(): string

```

Get the user being tested in the assertion. This represents the subject (user, group, role, etc.) whose access is being tested in the assertion. It&#039;s the entity for which we&#039;re validating whether they have the specified relation to the object.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/AssertionTupleKeyInterface.php#L52)

#### Returns

`string` — The user identifier being tested

### jsonSerialize

```php
public function jsonSerialize(): array

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/AssertionTupleKeyInterface.php#L62)

#### Returns

`array`
