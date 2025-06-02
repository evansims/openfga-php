# UserObjectInterface

Represents a user object in OpenFGA authorization model. User objects are typed entities that can be subjects in authorization relationships. They consist of a type (e.g., &#039;user&#039;, &#039;group&#039;) and a unique identifier within that type.

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/UserObjectInterface.php)

## Implements

* [`ModelInterface`](ModelInterface.md)
* `Stringable`
* `JsonSerializable`

## Related Classes

* [UserObject](Models/UserObject.md) (implementation)

## Methods

### List Operations

#### getId

```php
public function getId(): string

```

Get the unique identifier of the user object. The ID is unique within the context of the object type and represents the specific instance of the typed object.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UserObjectInterface.php#L36)

#### Returns

`string` — The object identifier

#### getType

```php
public function getType(): string

```

Get the type of the user object. The type defines the category or class of the object (e.g., &#039;user&#039;, &#039;group&#039;, &#039;organization&#039;) and must be defined in the authorization model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UserObjectInterface.php#L46)

#### Returns

`string` — The object type

### Other

#### jsonSerialize

```php
public function jsonSerialize(): array

```

Serialize the user object to its JSON representation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UserObjectInterface.php#L54)

#### Returns

`array`
