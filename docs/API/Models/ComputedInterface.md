# ComputedInterface

Represents a computed userset in OpenFGA authorization models. Computed usersets allow you to define relationships that are calculated dynamically based on other relationships. Instead of storing direct relationships, computed usersets reference other relations that should be evaluated to determine the effective permissions. For example, if you want &quot;viewers&quot; of a document to include everyone who is an &quot;editor&quot; of that document, you could use a computed userset that references the &quot;editor&quot; relation. Common userset reference formats: - &quot;#relation&quot; - References a relation on the same object - &quot;object#relation&quot; - References a relation on a specific object

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/ComputedInterface.php)

## Implements
* [`ModelInterface`](ModelInterface.md)
* `JsonSerializable`

## Related Classes
* [Computed](Models/Computed.md) (implementation)



## Methods

                                                
### List Operations
#### getUserset


```php
public function getUserset(): string
```

Get the userset reference string that defines a computed relationship. This represents a reference to another userset that should be computed dynamically based on relationships. The userset string typically follows the format &quot;#relation&quot; to reference a relation on the same object type.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ComputedInterface.php#L39)


#### Returns
`string` — The userset reference string defining the computed relationship
### Other
#### jsonSerialize


```php
public function jsonSerialize(): array
```


[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ComputedInterface.php#L45)


#### Returns
`array`
