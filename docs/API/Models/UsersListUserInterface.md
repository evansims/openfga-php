# UsersListUserInterface

Represents a user in a list context for authorization operations. UsersListUser provides a simple wrapper around user identifiers, ensuring they conform to the expected format and can be properly serialized for API operations. This is commonly used in list operations where user identifiers need to be processed in bulk. Use this interface when working with lists of users in authorization contexts, such as batch operations or user enumeration.

## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](ModelInterface.md)
* Stringable
* JsonSerializable



## Methods
### getUser


```php
public function getUser(): string
```

Get the user identifier string. This returns the user identifier in the format expected by OpenFGA, typically &quot;type:id&quot; where type describes the kind of user and id is the unique identifier for that user.


#### Returns
string
 The user identifier string

### jsonSerialize


```php
public function jsonSerialize(): string
```

Serialize the user to its JSON representation. Returns the user identifier as a string for API serialization. This differs from most models which serialize to arrays.


#### Returns
string
 The user identifier string

