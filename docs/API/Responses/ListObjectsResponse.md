# ListObjectsResponse

Response containing a list of objects that a user has a specific relationship with. This response provides an array of object identifiers that the specified user has the given relationship with. Use this to discover what resources a user can access in your authorization system.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`fromResponse()`](#fromresponse)
  - [`getObjects()`](#getobjects)
  - [`schema()`](#schema)

</details>

## Namespace

`OpenFGA\Responses`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListObjectsResponse.php)

## Implements

- [`ListObjectsResponseInterface`](ListObjectsResponseInterface.md)
- [`ResponseInterface`](ResponseInterface.md)

## Related Classes

- [ListObjectsResponseInterface](Responses/ListObjectsResponseInterface.md) (interface)
- [ListObjectsRequest](Requests/ListObjectsRequest.md) (request)

## Methods

### fromResponse

*<small>Implements Responses\ListObjectsResponseInterface</small>*

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

### getObjects

```php
public function getObjects(): array

```

Get the array of object identifiers the user has access to. Returns an array of object identifiers that the queried user has the specified relationship with. Each string represents an object ID of the requested type that the user can access through the specified relation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListObjectsResponse.php#L87)

#### Returns

`array` — Array of object identifiers the user has access to

### schema

*<small>Implements Responses\ListObjectsResponseInterface</small>*

```php
public function schema(): SchemaInterface

```

Get the schema definition for this response. Returns the schema that defines the structure and validation rules for object listing response data, ensuring consistent parsing and validation of API responses.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ListObjectsResponseInterface.php#L33)

#### Returns

`SchemaInterface` — The schema definition for response validation
