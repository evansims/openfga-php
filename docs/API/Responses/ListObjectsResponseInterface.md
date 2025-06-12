# ListObjectsResponseInterface

Interface for object listing response objects. This interface defines the contract for responses returned when listing objects that a user has access to in OpenFGA. This is the inverse of permission checking - instead of asking &quot;can this user access this object?,&quot; it asks &quot;what objects can this user access?&quot; Object listing is particularly useful for building user interfaces that need to display only the resources a user can access, such as file listings, document repositories, or administrative dashboards.

## Namespace

`OpenFGA\Responses`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListObjectsResponseInterface.php)

## Implements

* [`ResponseInterface`](ResponseInterface.md)

## Related Classes

* [ListObjectsResponse](Responses/ListObjectsResponse.md) (implementation)
* [ListObjectsRequestInterface](Requests/ListObjectsRequestInterface.md) (request)

## Methods

#### getObjects

```php
public function getObjects(): array<int, string>

```

Get the array of object identifiers the user has access to. Returns an array of object identifiers that the queried user has the specified relationship with. Each string represents an object ID of the requested type that the user can access through the specified relation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListObjectsResponseInterface.php#L44)

#### Returns

`array&lt;`int`, `string`&gt;` — Array of object identifiers the user has access to
