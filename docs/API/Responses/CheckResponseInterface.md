# CheckResponseInterface

Interface for permission check response objects. This interface defines the contract for responses returned when performing permission checks in OpenFGA. A check response indicates whether a specific user has a particular permission on a given object, based on the authorization model and current relationship data. Permission checking is the core operation of OpenFGA, allowing applications to make authorization decisions by evaluating user permissions against the defined relationship model and stored tuples.

## Namespace

`OpenFGA\Responses`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/CheckResponseInterface.php)

## Implements

* [`ResponseInterface`](ResponseInterface.md)

## Related Classes

* [CheckResponse](Responses/CheckResponse.md) (implementation)
* [CheckRequestInterface](Requests/CheckRequestInterface.md) (request)

## Methods

### Authorization

#### getAllowed

```php
public function getAllowed(): bool|null

```

Get whether the permission check was allowed. This is the primary result of the permission check operation, indicating whether the specified user has the requested permission on the given object according to the authorization model and current relationship data.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/CheckResponseInterface.php#L43)

#### Returns

`bool` &#124; `null` — True if permission is granted, false if denied, or null if the result is indeterminate

### List Operations

#### getResolution

```php
public function getResolution(): string|null

```

Get the resolution details for the permission decision. This provides additional information about how the permission decision was reached, which can be useful for understanding complex authorization logic or debugging permission issues.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/CheckResponseInterface.php#L54)

#### Returns

`string` &#124; `null` — The resolution details explaining the permission decision, or null if not provided
