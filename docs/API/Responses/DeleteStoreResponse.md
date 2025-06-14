# DeleteStoreResponse

Response confirming successful deletion of a store. This response is returned when a store has been successfully deleted from the OpenFGA service. The response contains no additional data as the store has been permanently removed.

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [Other](#other)
  - [`fromResponse()`](#fromresponse)

## Namespace

`OpenFGA\Responses`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/DeleteStoreResponse.php)

## Implements

- [`DeleteStoreResponseInterface`](DeleteStoreResponseInterface.md)
- [`ResponseInterface`](ResponseInterface.md)

## Related Classes

- [DeleteStoreResponseInterface](Responses/DeleteStoreResponseInterface.md) (interface)
- [DeleteStoreRequest](Requests/DeleteStoreRequest.md) (request)

## Methods

#### fromResponse

*<small>Implements Responses\DeleteStoreResponseInterface</small>*

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
