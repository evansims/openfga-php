# UserTypeFilter

Represents a filter for limiting users by type and optional relation. UserTypeFilter allows you to constrain authorization queries to specific user types, optionally including only users that have a particular relation. This is useful for filtering results when listing users or performing authorization checks on specific user categories. Use this when you need to limit authorization operations to specific types of users in your system.

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/UserTypeFilter.php)

## Implements
* [UserTypeFilterInterface](UserTypeFilterInterface.md)
* JsonSerializable
* [ModelInterface](ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;UserTypeFilter&#039;` |  |


## Methods
### getRelation


```php
public function getRelation(): ?string
```

Get the optional relation filter for limiting user types. When specified, this filter limits the results to users that have the specified relation to objects of the target type. This allows for more specific filtering beyond just the object type.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UserTypeFilter.php#L56)


#### Returns
?string

### getType


```php
public function getType(): string
```

Get the object type to filter by. This specifies the type of objects that users should be related to when filtering results. Only users connected to objects of this type will be included in the filtered results.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UserTypeFilter.php#L65)


#### Returns
string
 The object type to filter by

### jsonSerialize


```php
public function jsonSerialize(): array
```


[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UserTypeFilter.php#L74)


#### Returns
array

### schema

*<small>Implements Models\UserTypeFilterInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ModelInterface.php#L52)


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

