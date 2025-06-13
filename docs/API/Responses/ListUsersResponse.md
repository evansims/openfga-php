# ListUsersResponse

Response containing a list of users that have a specific relationship with an object. This response provides a collection of users (including user objects, usersets, and typed wildcards) that have the specified relationship with the target object. Use this to discover who has access to resources in your authorization system.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Implements](#implements)
* [Related Classes](#related-classes)
* [Methods](#methods)

* [List Operations](#list-operations)
    * [`getUsers()`](#getusers)
* [Model Management](#model-management)
    * [`schema()`](#schema)
* [Other](#other)
    * [`fromResponse()`](#fromresponse)

## Namespace

`OpenFGA\Responses`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListUsersResponse.php)

## Implements

* [`ListUsersResponseInterface`](ListUsersResponseInterface.md)
* [`ResponseInterface`](ResponseInterface.md)

## Related Classes

* [ListUsersResponseInterface](Responses/ListUsersResponseInterface.md) (interface)
* [ListUsersRequest](Requests/ListUsersRequest.md) (request)

## Methods

### List Operations

#### getUsers

```php
public function getUsers(): OpenFGA\Models\Collections\UsersInterface

```

Get the collection of users with the specified relationship. Returns a type-safe collection containing the user objects that have the queried relationship with the specified object. Each user represents an entity that has been granted the specified permission or relationship.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListUsersResponse.php#L97)

#### Returns

[`UsersInterface`](Models/Collections/UsersInterface.md) — The collection of users with the relationship

### Model Management

#### schema

*<small>Implements Responses\ListUsersResponseInterface</small>*

```php
public function schema(): SchemaInterface

```

Get the schema definition for this response. Returns the schema that defines the structure and validation rules for user listing response data, ensuring consistent parsing and validation of API responses.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListUsersResponseInterface.php#L35)

#### Returns

`SchemaInterface` — The schema definition for response validation

### Other

#### fromResponse

*<small>Implements Responses\ListUsersResponseInterface</small>*

```php
public function fromResponse(
    HttpResponseInterface $response,
    HttpRequestInterface $request,
    SchemaValidatorInterface $validator,
): static

```

Create a response instance from an HTTP response. This method transforms a raw HTTP response from the OpenFGA API into a structured response object, validating and parsing the response data according to the expected schema. It handles both successful responses by parsing and validating the data, and error responses by throwing appropriate exceptions.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ResponseInterface.php#L44)

#### Parameters

| Name         | Type                       | Description                                               |
| ------------ | -------------------------- | --------------------------------------------------------- |
| `$response`  | `HttpResponseInterface`    | The raw HTTP response from the OpenFGA API                |
| `$request`   | `HttpRequestInterface`     | The original HTTP request that generated this response    |
| `$validator` | `SchemaValidatorInterface` | Schema validator for parsing and validating response data |

#### Returns

`static` — The parsed and validated response instance containing the API response data
