# UsersetUserInterface

Defines the contract for userset user specifications. A userset user represents a reference to users through a userset relationship, typically in the format &quot;object#relation&quot; where object is the entity and relation defines which users are included. This allows dynamic user groups based on relationships rather than static user lists. Use this when you need to reference users through relationship-based groups in your authorization model.

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetUserInterface.php)

## Implements

* [`ModelInterface`](ModelInterface.md)
* `JsonSerializable`

## Related Classes

* [UsersetUser](Models/UsersetUser.md) (implementation)

## Methods

### List Operations

#### getId

```php
public function getId(): string

```

Get the object identifier in the userset reference. This represents the specific object instance that the userset refers to. For example, in &quot;group:eng#member&quot;, this would return &quot;eng&quot;.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetUserInterface.php#L30)

#### Returns

`string` — The object identifier

#### getRelation

```php
public function getRelation(): string

```

Get the relation name in the userset reference. This represents the specific relation on the referenced object that defines the userset. For example, in &quot;group:eng#member&quot;, this would return &quot;member&quot;.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetUserInterface.php#L40)

#### Returns

`string` — The relation name

#### getType

```php
public function getType(): string

```

Get the object type in the userset reference. This represents the type of object that the userset refers to. For example, in &quot;group:eng#member&quot;, this would return &quot;group&quot;.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetUserInterface.php#L50)

#### Returns

`string` — The object type

### Other

#### jsonSerialize

```php
public function jsonSerialize(): array

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetUserInterface.php#L56)

#### Returns

`array`
