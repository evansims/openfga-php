# WriteAssertionsResponse

Response confirming successful writing of test assertions. This response indicates that test assertions have been successfully stored for an authorization model. The assertions can now be used to validate that the model behaves correctly in various authorization scenarios.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`fromResponse()`](#fromresponse)

</details>

## Namespace

`OpenFGA\Responses`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteAssertionsResponse.php)

## Implements

- [`WriteAssertionsResponseInterface`](WriteAssertionsResponseInterface.md)
- [`ResponseInterface`](ResponseInterface.md)

## Related Classes

- [WriteAssertionsResponseInterface](Responses/WriteAssertionsResponseInterface.md) (interface)
- [WriteAssertionsRequest](Requests/WriteAssertionsRequest.md) (request)

## Methods

### fromResponse

*<small>Implements Responses\WriteAssertionsResponseInterface</small>*

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
