# BatchCheckSingleResultInterface

Represents the result of a single check within a batch check response. Each result contains whether the check was allowed and any error information if the check failed to complete successfully.

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckSingleResultInterface.php)

## Implements
* [ModelInterface](ModelInterface.md)
* JsonSerializable



## Methods
### getAllowed


```php
public function getAllowed(): ?bool
```

Get whether this check was allowed. Returns true if the user has the specified relationship with the object, false if they don&#039;t, or null if the check encountered an error.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckSingleResultInterface.php#L25)


#### Returns
?bool

### getError


```php
public function getError(): ?object
```

Get any error that occurred during this check. Returns error information if the check failed to complete successfully, or null if the check completed without errors.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckSingleResultInterface.php#L35)


#### Returns
?object

### jsonSerialize


```php
public function jsonSerialize()
```





