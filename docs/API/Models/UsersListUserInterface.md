# UsersListUserInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
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



#### Returns
string

