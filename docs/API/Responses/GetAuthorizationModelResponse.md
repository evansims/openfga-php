# GetAuthorizationModelResponse

Response containing a specific authorization model from the store. This response provides the complete authorization model including type definitions, relationships, and conditions. Use this to retrieve and examine the authorization schema that defines how permissions work in your application.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`fromResponse()`](#fromresponse)
  - [`getModel()`](#getmodel)
  - [`schema()`](#schema)

</details>

## Namespace

`OpenFGA\Responses`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/GetAuthorizationModelResponse.php)

## Implements

- [`GetAuthorizationModelResponseInterface`](GetAuthorizationModelResponseInterface.md)
- [`ResponseInterface`](ResponseInterface.md)

## Related Classes

- [GetAuthorizationModelResponseInterface](Responses/GetAuthorizationModelResponseInterface.md) (interface)
- [GetAuthorizationModelRequest](Requests/GetAuthorizationModelRequest.md) (request)

## Methods

### fromResponse

*<small>Implements Responses\GetAuthorizationModelResponseInterface</small>*

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

### getModel

```php
public function getModel(): ?OpenFGA\Models\AuthorizationModelInterface

```

Get the retrieved authorization model. Returns the complete authorization model including its type definitions, schema version, and any conditions. The model defines the relationship types and permission logic that govern authorization decisions within the store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/GetAuthorizationModelResponse.php#L101)

#### Returns

[`AuthorizationModelInterface`](Models/AuthorizationModelInterface.md) &#124; `null` — The authorization model, or null if not found

### schema

*<small>Implements Responses\GetAuthorizationModelResponseInterface</small>*

```php
public function schema(): SchemaInterface

```

Get the schema definition for this response. Returns the schema that defines the structure and validation rules for authorization model response data, ensuring consistent parsing and validation of API responses.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/GetAuthorizationModelResponseInterface.php#L33)

#### Returns

`SchemaInterface` — The schema definition for response validation
