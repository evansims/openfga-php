# UsersetTreeTupleToUserset

Represents a tuple-to-userset operation node in authorization evaluation trees. UsersetTreeTupleToUserset defines how to resolve users through tuple-to-userset mappings during authorization evaluation. It specifies which tuples to examine and how to compute the resulting usersets, enabling complex authorization patterns based on indirect relationships. Use this when working with authorization evaluation trees that involve tuple-to-userset relationship resolution.

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetTreeTupleToUserset.php)

## Implements
* [UsersetTreeTupleToUsersetInterface](UsersetTreeTupleToUsersetInterface.md)
* JsonSerializable
* [ModelInterface](ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;UsersetTree.TupleToUserset&#039;` |  |


## Methods
### getComputed


```php
public function getComputed(): array<int, ComputedInterface>
```

Get the array of computed usersets for the tuple-to-userset operation. This returns a collection of computed userset references that define how to resolve the users from the tuple-to-userset mapping in the tree expansion.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetTreeTupleToUserset.php#L58)


#### Returns
array&lt;int, ComputedInterface&gt;
 Array of computed userset references

### getTupleset


```php
public function getTupleset(): string
```

Get the tupleset string identifying which tuples to use for computation. This string identifies the specific tupleset that should be used to resolve users through the tuple-to-userset operation during tree expansion.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetTreeTupleToUserset.php#L67)


#### Returns
string
 The tupleset identifier string

### jsonSerialize


```php
public function jsonSerialize(): array
```


[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetTreeTupleToUserset.php#L76)


#### Returns
array

### schema

*<small>Implements Models\UsersetTreeTupleToUsersetInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ModelInterface.php#L52)


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

