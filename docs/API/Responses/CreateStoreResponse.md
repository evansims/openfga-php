# CreateStoreResponse

Response confirming successful creation of a new store. This response provides the details of the newly created authorization store, including its unique identifier, name, and creation timestamps. Use the store ID for subsequent operations like managing authorization models and tuples.

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [CRUD Operations](#crud-operations)
  - [`getCreatedAt()`](#getcreatedat)
  - [`getUpdatedAt()`](#getupdatedat)
- [List Operations](#list-operations)
  - [`getId()`](#getid)
  - [`getName()`](#getname)
- [Model Management](#model-management)
  - [`schema()`](#schema)
- [Other](#other)
  - [`fromResponse()`](#fromresponse)

## Namespace

`OpenFGA\Responses`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/CreateStoreResponse.php)

## Implements

- [`CreateStoreResponseInterface`](CreateStoreResponseInterface.md)
- [`ResponseInterface`](ResponseInterface.md)

## Related Classes

- [CreateStoreResponseInterface](Responses/CreateStoreResponseInterface.md) (interface)
- [CreateStoreRequest](Requests/CreateStoreRequest.md) (request)

## Methods

### CRUD Operations

#### getCreatedAt

```php
public function getCreatedAt(): DateTimeImmutable

```

Get the timestamp when the store was created. Returns the exact moment when the store was successfully created in the OpenFGA system. This timestamp is immutable and set by the server upon store creation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/CreateStoreResponse.php#L97)

#### Returns

`DateTimeImmutable` — The creation timestamp of the store

#### getUpdatedAt

```php
public function getUpdatedAt(): DateTimeImmutable

```

Get the timestamp when the store was last updated. Returns the timestamp of the most recent modification to the store&#039;s metadata. For newly created stores, this will typically match the creation timestamp.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/CreateStoreResponse.php#L124)

#### Returns

`DateTimeImmutable` — The last update timestamp of the store

### List Operations

#### getId

```php
public function getId(): string

```

Get the unique identifier of the created store. Returns the system-generated unique identifier for the newly created store. This ID is used in all subsequent API operations to reference this specific store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/CreateStoreResponse.php#L106)

#### Returns

`string` — The unique store identifier

#### getName

```php
public function getName(): string

```

Get the human-readable name of the created store. Returns the descriptive name that was assigned to the store during creation. This name is used for identification and administrative purposes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/CreateStoreResponse.php#L115)

#### Returns

`string` — The descriptive name of the store

### Model Management

#### schema

*<small>Implements Responses\CreateStoreResponseInterface</small>*

```php
public function schema(): SchemaInterface

```

Get the schema definition for this response. Returns the schema that defines the structure and validation rules for store creation response data, ensuring consistent parsing and validation of API responses.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/CreateStoreResponseInterface.php#L32)

#### Returns

`SchemaInterface` — The schema definition for response validation

### Other

#### fromResponse

*<small>Implements Responses\CreateStoreResponseInterface</small>*

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
