# WriteTuplesResponse

Response confirming successful writing of relationship tuples. This response is returned when relationship tuples have been successfully written to the authorization store. The response contains no additional data as the operations have completed successfully.

## Namespace
`OpenFGA\Responses`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponse.php)

## Implements
* [`WriteTuplesResponseInterface`](WriteTuplesResponseInterface.md)
* [`ResponseInterface`](ResponseInterface.md)

## Related Classes
* [WriteTuplesResponseInterface](Responses/WriteTuplesResponseInterface.md) (interface)
* [WriteTuplesRequest](Requests/WriteTuplesRequest.md) (request)

## Methods

#### fromResponse

*<small>Implements Responses\WriteTuplesResponseInterface</small>*

```php
public function fromResponse(
    HttpResponseInterface $response,
    HttpRequestInterface $request,
    SchemaValidator $validator,
): static
```

Create a response instance from an HTTP response. This method transforms a raw HTTP response from the OpenFGA API into a structured response object, validating and parsing the response data according to the expected schema. It handles both successful responses by parsing and validating the data, and error responses by throwing appropriate exceptions.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ResponseInterface.php#L44)

#### Parameters
| Name         | Type                    | Description                                               |
| ------------ | ----------------------- | --------------------------------------------------------- |
| `$response`  | `HttpResponseInterface` | The raw HTTP response from the OpenFGA API                |
| `$request`   | `HttpRequestInterface`  | The original HTTP request that generated this response    |
| `$validator` | `SchemaValidator`       | Schema validator for parsing and validating response data |

#### Returns
`static` — The parsed and validated response instance containing the API response data
