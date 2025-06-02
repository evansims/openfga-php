# ListUsersResponseInterface

Interface for user listing response objects. This interface defines the contract for responses returned when listing users that have a specific relationship with an object in OpenFGA. This is the inverse of permission checking - instead of asking &quot;can this user access this object?&quot;, it asks &quot;which users can access this object?&quot;. User listing is particularly useful for building administrative interfaces, access reports, and user management features that need to display who has access to specific resources.

## Namespace

`OpenFGA\Responses`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListUsersResponseInterface.php)

## Implements

* [`ResponseInterface`](ResponseInterface.md)

## Related Classes

* [ListUsersResponse](Responses/ListUsersResponse.md) (implementation)

* [ListUsersRequestInterface](Requests/ListUsersRequestInterface.md) (request)

## Methods

#### getUsers

```php
public function getUsers(): UsersInterface<UserInterface>

```

Get the collection of users with the specified relationship. Returns a type-safe collection containing the user objects that have the queried relationship with the specified object. Each user represents an entity that has been granted the specified permission or relationship.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListUsersResponseInterface.php#L47)

#### Returns

[`UsersInterface`](Models/Collections/UsersInterface.md)&lt;[`UserInterface`](Models/UserInterface.md)&gt; — The collection of users with the relationship
